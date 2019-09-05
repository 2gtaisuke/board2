<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name'
    ];

    public function boards(): BelongsToMany
    {
        return $this->belongsToMany(
            Board::class,
            'board_tag'
        );
    }
}
