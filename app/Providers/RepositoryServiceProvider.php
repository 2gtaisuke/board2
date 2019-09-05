<?php

namespace App\Providers;

use App\Repositories\SocialAccountRepository;
use App\Repositories\SocialAccountRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SocialAccountRepositoryInterface::class, SocialAccountRepository::class
        );
        $this->app->bind(
            UserRepositoryInterface::class, UserRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
