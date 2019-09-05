<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    protected $fillable = [
        'title', 'user_id'
    ];

    protected $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->belongsToMany(
            User::class,
            'likes'
        );
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'board_tag'
        );
    }

    /**
     * commentsをeagerロードした最新順
     *
     * @param int|null $user_id
     * @return Board|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestWithRelation($user_id)
    {
        return $this->with([
            'comments' => function($query) {
                $query->latest();
            },
            'likes' => function($query) use ($user_id) {
                $query->where('users.id', $user_id);
            }
        ])->latest();
    }
}