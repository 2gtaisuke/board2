<?php


namespace App\Models\Traits;


use App\Models\User;

trait FollowTrait
{
    /**
     * フォローしてるユーザーを返す
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(
            self::class,
            'follow_users',
            'user_id',
            'followed_user_id'
        );
    }

    /**
     * 自身のフォロワーを返す
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function follower()
    {
        return $this->belongsToMany(
            self::class,
            'follow_users',
            'followed_user_id',
            'user_id'
        );
    }

    /**
     * $userをフォローする
     *
     * @param User $user
     * @throws \Exception
     */
    public function follow(User $user): void
    {
        if ($this->isFollowing($user)) {
            throw new \Exception('既にフォローしています');
        }
        $this->following()->attach($user->id);
    }

    /**
     * $userをアンフォローする
     *
     * @param User $user
     */
    public function unfollow(User $user): void
    {
        if (!$this->isFollowing($user)) {
            throw new \Exception('まだフォローしていません');
        }

        $this->following()->detach($user->id);
    }

    /**
     * $userをフォローしているかどうかを返す
     *
     * @param User $user
     * @return bool
     */
    public function isFollowing(User $user): bool
    {
        return !is_null($this->load('following')->following->find($user->id));
    }
}