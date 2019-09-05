<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function store(array $attributes): User;
}