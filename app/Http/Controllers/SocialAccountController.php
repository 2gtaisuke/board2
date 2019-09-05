<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Service\SocialAccountService;
use App\Service\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Psr\Log\LoggerInterface;
use Illuminate\Http\RedirectResponse;

class SocialAccountController extends Controller
{
    /** @var Socialite */
    private $social_manager;

    /** @var AuthManager */
    private $auth;

    /** @var SocialAccountService */
    private $social_account_service;

    /** @var UserService */
    private $user_service;

    /** @var LoggerInterface */
    private $logger;

    private $scopes = [
        'github' => 'read:user',
        'google' => 'profile',
    ];

    public function __construct(
        Socialite $social_manager,
        AuthManager $auth,
        SocialAccountService $social_account_service,
        UserService $user_service,
        LoggerInterface $logger
    )
    {
        $this->social_manager = $social_manager;
        $this->auth = $auth;
        $this->social_account_service = $social_account_service;
        $this->user_service = $user_service;
        $this->logger = $logger;
    }

    /**
     * GitHubの認証ページヘユーザーをリダイレクト
     * @param string $provider
     * @return RedirectResponse
     */
    public function redirectToProvider(string $provider)
    {
        return $this->social_manager->driver($provider)->scopes($this->scopes[$provider])->redirect();
    }

    /**
     * GitHubからユーザー情報を取得
     *
     * @param string social provider
     * @return RedirectResponse
     */
    public function handleProviderCallback(string $provider)
    {
        // ソーシャル情報を取得
        try {
            $provided_user_info = $this->social_manager->driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login');
        }

        try {
            # 画像保存
            $local_avatar_path = $this->user_service->storeImage($provided_user_info->getAvatar());

            $social_account = $this->social_account_service->findOrCreateWithUser([
                'name' => $provided_user_info->getName(),
                'id'   => $provided_user_info->getId(),
                'image_path' => $local_avatar_path,
                'api_token' => Str::random(60),
            ], $provider);

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            # 画像を削除
            if(isset($local_avatar_path)) {
                $this->user_service->deleteImage($local_avatar_path);
            }

            return redirect()->route('login');
        }

        $this->auth->guard()->login($social_account->user()->get()->first());
        return redirect('/');
    }
}