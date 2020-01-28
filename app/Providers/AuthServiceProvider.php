<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // Main ..
        \App\Models\Main\Site::class => \App\Policies\Main\SitePolicy::class,

        // Temp ..
        \App\Models\Temp\Link::class => \App\Policies\Temp\LinkPolicy::class,
        \App\Models\Temp\Site::class => \App\Policies\Temp\SitePolicy::class,

        // Admin ..
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Role::class => \App\Policies\RolePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
    }
}
