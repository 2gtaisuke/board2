<?php


namespace App\Service;


use App\Models\SocialAccount;
use App\Repositories\SocialAccountRepositoryInterface;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialUser;

class SocialAccountService
{
    /** @var SocialAccountRepositoryInterface */
    private $social_account_repo;

    /** @var UserService */
    private $user_service;

    /** @var DatabaseManager */
    private $db;

    public function __construct(
        SocialAccountRepositoryInterface $social_account_repo, UserService $user_service, DatabaseManager $db
    )
    {
        $this->social_account_repo = $social_account_repo;
        $this->user_service = $user_service;
        $this->db = $db;
    }

    /**
     *
     *
     * @param array $provided_user_info
     * @param string $provider
     * @return SocialAccount
     * @throws \Exception
     */
    public function findOrCreateWithUser(array $provided_user_info, string $provider): SocialAccount
    {
        try {
            $this->db->beginTransaction();

            $social_account = $this->social_account_repo->firstOrNew([
                'provider_id'   => $provided_user_info['id'],
                'provider_name' => $provider,
            ]);

            if ($social_account->exists) {
                $this->db->commit();
                return $social_account;
            }

            $user = $this->user_service->store([
                'name' => $provided_user_info['name'],
                'image_path' => $provided_user_info['image_path'],
                'api_token' => Str::random(60),
            ]);

            $user->accounts()->save($social_account);

            $this->db->commit();
            return $social_account;

        } catch(\Exception $e) {
            $this->db->rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}