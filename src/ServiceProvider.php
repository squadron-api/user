<?php

namespace Squadron\User;

use Laravel\Passport\Passport;
use Squadron\User\Console\Commands\BanUser;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole())
        {
            $this->publishes([
                __DIR__.'/../config/user.php' => config_path('squadron/user.php'),
            ]);

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations-passport');
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->commands([
                BanUser::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        Passport::enableImplicitGrant();
    }

    public function register(): void
    {
        Passport::ignoreMigrations();

        $this->app->singleton('auth.password', function ($app) {
            return new Services\PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });
    }
}
