<?php

namespace App\Providers;

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
        \Illuminate\Pagination\Paginator::defaultView('partials.pagination');

        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || request()->isSecure() || str_contains(request()->getHttpHost(), 'trycloudflare.com')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
