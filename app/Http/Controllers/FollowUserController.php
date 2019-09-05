<?php

namespace App\Http\Controllers;

use App\Exceptions\FollowUserException;
use App\Models\User;
use App\Service\UserService;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class FollowUserController extends Controller
{
    private $user_service;
    private $logger;

    public function __construct(
        UserService $user_service,
        LoggerInterface $logger
    )
    {
        $this->user_service = $user_service;
        $this->logger = $logger;
    }

    // TODO: apiに書き換える。
    /**
     * ユーザーをフォローする
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function followUser(User $user, Request $request)
    {
//        TODO: APIにする
        try {
            $this->user_service->followUser($request->user(), $user);
            return back();
        } catch (FollowUserException $e) {
            return back();
        }
    }

    /**
     * ユーザーをアンフォローする
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unfollowUser(User $user, Request $request)
    {
        try {
            $this->user_service->unfollowUser($request->user(), $user);
            return back();
        } catch (FollowUserException $e) {
            return back();
        }
    }
}