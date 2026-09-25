<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('photo-uploads', function (Request $request): array {
            $ip = $request->ip() ?? 'unknown';

            return [
                Limit::perMinute(10)->by($ip),
                Limit::perHour(500)->by($ip),
            ];
        });
    }
}
