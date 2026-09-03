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

        if (!$this->app->runningInConsole()) {
            try {
                $req = $this->app->make('request');
                if ($req && ($req->server('HTTP_X_FORWARDED_PROTO') === 'https' || $req->isSecure() || str_contains($req->getHttpHost(), 'trycloudflare.com') || str_contains($req->getHttpHost(), 'ngrok-free.dev') || str_contains($req->getHttpHost(), 'ngrok.app') || str_contains($req->getHttpHost(), 'ngrok.io'))) {
                    \Illuminate\Support\Facades\URL::forceScheme('https');
                }
            } catch (\Throwable $e) {
                // Ignore if request is not yet bound
            }
        }
    }
}
