<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isSuperAdmin', function($user){
            return $user->type == 'super admin';
        });

        Gate::define('isAdmin', function($user){
            return $user->type == 'admin' || $user->type == 'super admin';
        });

        Gate::define('isUser', function($user){
            return $user->type == 'user' || $user->type == 'admin' || $user->type == 'super admin';
        });

        Gate::define('isRegistrar', function($user){
            return $user->type == 'registrar' || $user->type == 'admin' || $user->type == 'super admin';
        });

        Gate::define('isAdministrative', function($user){
            return $user->type == 'administrative' || $user->type == 'admin' || $user->type == 'super admin';
        });

        Passport::routes();
        //
    }
}
