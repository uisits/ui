<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use StudentAffairsUwm\Shibboleth\Entitlement;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes(function ($router) {
            $router->forAccessTokens();
            $router->forPersonalAccessTokens();
            $router->forTransientTokens();
        });

        Passport::tokensExpireIn(now()->addMinutes(60));
        Passport::refreshTokensExpireIn(now()->addDays(1));

        Gate::define('is-admin', function() {
            return Entitlement::has(env('APP_AD_AUTHORIZE_GROUP'));
        });
    }
}
