<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // check for maintenance mode
        // see https://learnku.com/docs/laravel/5.7/configuration#maintenance-mode
        \App\Http\Middleware\CheckForMaintenanceMode::class,

        // check to see if form post size has exceeded limit
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // use PHP trim() on request parameters
        \App\Http\Middleware\TrimStrings::class,

        // convert empty string request parameters to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

        // server params after proxies
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        // Web middleware group, used for routes/web.php
        // defined inside RouteServiceProvider
        'web' => [
            // Cookie encryption/decryption
            \App\Http\Middleware\EncryptCookies::class,

            // Add cookie to response
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

            // Start session
            \Illuminate\Session\Middleware\StartSession::class,

            // \Illuminate\Session\Middleware\AuthenticateSession::class,

            // Add system error information into $errors array inside views
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // Verify CSRF
            // see https://learnku.com/docs/laravel/5.7/csrf
            \App\Http\Middleware\VerifyCsrfToken::class,

            // manage route model bindings
            // see https://learnku.com/docs/laravel/5.7/routing#route-model-binding
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Enforce user email verification
            \App\Http\Middleware\EnsureEmailIsVerified::class,

            // Record user's last active time
            \App\Http\Middleware\RecordLastActivedTime::class,
        ],

        // API middleware group, used for routes/api.php
        // defined inside RouteServiceProvider
        'api' => [
            // use alias for middleware usage
            // see https://learnku.com/docs/laravel/5.7/middleware#为路由分配中间件
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * Aliases for middleware names (eg. api middleware usage above)
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // Only authenticated users' requests can pass, used most frequently
        'auth' => \App\Http\Middleware\Authenticate::class,

        // HTTP Basic Authentication
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,

        // Manage route model binding
        // See https://learnku.com/docs/laravel/5.7/routing#route-model-binding
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,

        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,

        // User authorization policies
        'can' => \Illuminate\Auth\Middleware\Authorize::class,

        // Only unauthorized (guests) users can access (eg. register and login pages)
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

        // Signature validation, mentioned in recovering password section
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,

        // Request limit, mainly used in APIs (eg. max 10 requests per minute)
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        // Laravel-specific email verification mechanism
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * Eg. StartSession always execute first, since only then can we use
     * other middleware such as Auth.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
