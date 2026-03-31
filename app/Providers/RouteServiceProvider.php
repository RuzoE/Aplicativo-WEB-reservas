<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Model binding personalizado para MaintenanceOrder
        Route::model('order', \App\Models\MaintenanceOrder::class);

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth-login', function (Request $request) {
            $email = (string) $request->input('email', 'guest');
            $key = strtolower($email) . '|' . $request->ip();

            return [
                Limit::perMinute(5)->by($key),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        RateLimiter::for('auth-register', function (Request $request) {
            $email = (string) $request->input('email', 'guest');
            $key = strtolower($email) . '|' . $request->ip();

            return [
                Limit::perMinutes(10, 3)->by($key),
                Limit::perMinutes(10, 10)->by($request->ip()),
            ];
        });

        RateLimiter::for('password-recovery', function (Request $request) {
            $email = (string) $request->input('email', 'guest');
            $key = strtolower($email) . '|' . $request->ip();

            return [
                Limit::perMinutes(10, 3)->by($key),
                Limit::perHour(10)->by($request->ip()),
            ];
        });

        RateLimiter::for('reception-sensitive', function (Request $request) {
            return [
                Limit::perMinute(30)->by(($request->user()?->id ?: 'guest') . '|' . $request->route()?->getName()),
                Limit::perMinute(60)->by($request->ip()),
            ];
        });
    }
}
