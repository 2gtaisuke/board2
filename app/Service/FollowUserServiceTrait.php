<?php

namespace App\Service;

use App\Exceptions\FollowUserException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait FollowUserServiceTrait
{
    /**
     * $following_userが$followed_userをフォローする
     *
     * @param User $following_user
     * @param User $followed_user
     * @throws FollowUserException
     */
    public function followUser(User $following_user, User $followed_user)
    {
        if($following_user->isFollowing($followed_user)) {
            throw new FollowUserException('既にフォローしています');
        } elseif ($following_user->id === $followed_user->id) {
            throw new FollowUserException('自分自身はフォローできません');
        }

        $following_user->follow($followed_user);
    }


    /**
     * $following_userが$followed_userをアンフォローする
     *
     * @param User $following_user
     * @param User $followed_user
     * @throws FollowUserException
     */
    public function unfollowUser(User $following_user, User $followed_user)
    {
        if(!$following_user->isFollowing($followed_user)) {
            throw new FollowUserException('まだフォローしていません');
        } elseif ($following_user->id === $followed_user->id) {
            throw new FollowUserException('自分自身はアンフォローできません');
        }

        $following_user->unfollow($followed_user);
    }

    /**
     * フォロワーを取得する
     *
     * @param User $user
     * @return Collection
     */
    public function getFollower(User $user): Collection
    {
        return $user->follower()->get()->map(function ($follower) {
            $follower->profile_image = $this->getUserProfileImage($follower);
            return $follower;
        });
    }

}