<?php

namespace App\Providers;

use App\Infrastructure\Session\MemberDatabaseSessionHandler;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Session::extend('database', function ($app) {
            $config = $app->make('config')->get('session');

            return new MemberDatabaseSessionHandler(
                $app->make('db')->connection($config['connection'] ?? null),
                $config['table'],
                $config['lifetime'],
                $app,
            );
        });
    }
}
