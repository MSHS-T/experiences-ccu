<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        if (App::isLocal() && !App::runningInConsole()) {
            // $this->app['auth']->setUser(User::role('administrator')->first());
            // $this->app['auth']->setUser(User::role('plateau_manager')->first());
            // $this->app['auth']->setUser(User::role('manipulation_manager')->first());
        }
    }
}
