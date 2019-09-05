<?php

namespace App\Models;

use App\Models\Traits\FollowTrait;
use App\Models\Traits\LikeTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use Notifiable, CanResetPasswordTrait, FollowTrait, LikeTrait;

    protected $fillable = [
        'name', 'email', 'password', 'image_path', 'api_token'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->belongsToMany(
            Board::class,
            'likes'
        );
    }

    /**
     * コメント数を返す
     *
     * @return int
     */
    public function commentsCount(): int
    {
        return $this->comments()->count();
    }

    /**
     * $compared_userが自分自身かどうかを返す
     *
     * @param User $compared_user
     * @return bool
     */
    public function isMyself(User $compared_user)
    {
        return intval($this->id) === intval($compared_user->id);
    }
}