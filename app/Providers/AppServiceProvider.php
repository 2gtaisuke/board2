<?php

namespace App\Providers;

use App\Http\View\Composers\LoginUserComposer;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View as ViewFacades;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ClientInterface::class,
            Client::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        # アプリケーション名
        ViewFacades::composer('layouts.app', function(View $view) {
            $view->with('app_name', config('app.name'));
        });

        # ログインユーザーを$login_userに定義
        ViewFacades::composer(
            '*', LoginUserComposer::class
        );

        # sqlクエリをログに出力
        if (config('app.env') !== 'production') {
            DB::listen(function ($query) {
                Log::info("Query Time:{$query->time}s] $query->sql");
            });
        }
    }
}
