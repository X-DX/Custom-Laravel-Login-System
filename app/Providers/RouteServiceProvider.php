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
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Existing API Rate Limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // our custom "login" rate limiter : This defines a rate limit rule for a named action — here we call it 'login'.
        RateLimiter::for('login', function(Request $request) {  //The name 'login' is just a label; you’ll refer to it later when applying limits.
            // The function inside tells Laravel how to identify unique users and how often to allow actions.
            $email = (string) $request->input('email'); // We extract the email input from the login request. Casting to (string) ensures that if the field is null, it becomes an empty string — this prevents errors when combining it later.

            return[
                // Limit: 5 attempts per minute per email + IP
                Limit::perMinute(2)->by($email . $request->ip()) // This defines how Laravel uniquely identifies who is making requests. The by() method is used to define the key by which the rate limit is applied. This key determines how requests are grouped for the purpose of counting and limiting.
                // Together they form a unique “key” (like "john@example.com127.0.0.1").
                ->response(function() {  // This defines what happens when someone exceeds the limit.
                    return response()->json([
                        'massage' => 'Too many login attempts. PLease try again later.'
                    ], 429); // The callback returns a response with a 429 HTTP status code (Too Many Requests).
                }),
            ];
        });

        // Route registration — keeps your routes connected
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
