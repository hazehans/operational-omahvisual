<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production') && ! in_array(request()->getHost(), ['127.0.0.1', 'localhost'])) {
            URL::forceScheme('https');
        }

        // Implicitly grant 'Superuser' and 'Owner' all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasAnyRole(['Superuser', 'Owner']) ? true : null;
        });
    }
}
