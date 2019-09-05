<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /** @var UserService */
    private $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    public function show(User $user)
    {
        # TODO: ユーザーのプロフィール画像をどこから持ってくるか
        $user_profile_path = $this->user_service->getUserProfileImage($user);

        $followers = $this->user_service->getFollower($user);

        $likes = $this->user_service->retrieveLikes($user->id);

        return view('user.show', compact('user', 'user_profile_path', 'followers', 'likes'));
    }
}
