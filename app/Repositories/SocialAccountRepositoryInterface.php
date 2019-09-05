<?php


namespace App\Repositories;


use App\Models\SocialAccount;

interface SocialAccountRepositoryInterface
{
    public function firstOrNew(array $attributes): SocialAccount;
}