<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\ValidateSignature;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // The application keeps its own subclasses of several framework
        // middleware. config/sanctum.php and the aliases below refer to them by
        // name, so they are swapped in for the framework defaults rather than
        // dropped.
        $middleware->replace(\Illuminate\Http\Middleware\TrustProxies::class, TrustProxies::class);
        $middleware->replace(\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class, PreventRequestsDuringMaintenance::class);
        $middleware->replace(\Illuminate\Foundation\Http\Middleware\TrimStrings::class, TrimStrings::class);

        $middleware->replaceInGroup('web', \Illuminate\Cookie\Middleware\EncryptCookies::class, EncryptCookies::class);
        $middleware->replaceInGroup('web', \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, VerifyCsrfToken::class);

        $middleware->throttleApi();

        $middleware->alias([
            'auth'   => Authenticate::class,
            'guest'  => RedirectIfAuthenticated::class,
            'signed' => ValidateSignature::class,
        ]);

        // This application has no route named "login". Filament's own auth
        // middleware attaches its panel login URL to the AuthenticationException
        // and that still takes precedence; this only covers the guards that
        // don't supply one, which is what used to 500 on /admin.
        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:notify-yesterday-unhonored-slots')->dailyAt('03:00');
        $schedule->command('app:send-booking-reminders')->hourlyAt(5);
    })
    ->create();
