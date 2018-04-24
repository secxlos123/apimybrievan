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
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            \App\Http\Middleware\AuditRailHandler::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'   => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'        => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'throttle-custom'   => \App\Http\Middleware\ThrottleCustom::class,
        'api.access' => \App\Http\Middleware\VerifyApiAccess::class,
        'api.auth'   => \App\Http\Middleware\GetUserFromToken::class,
        'api.refresh'=> \Tymon\JWTAuth\Middleware\RefreshToken::class,
        'has.user.int' => \App\Http\Middleware\HasUserInternal::class,
        'has.user.dev' => \App\Http\Middleware\HasDeveloper::class,
        'property.access' => \App\Http\Middleware\PropertyTypeAccess::class,
        'property-type.access' => \App\Http\Middleware\PropertyItemAccess::class,
        'ipcheck' => \App\Http\Middleware\IpWhiteList::class,
        'file' => \App\Http\Middleware\FileMiddleware::class,
    ];
}
