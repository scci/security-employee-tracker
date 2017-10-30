<?php

namespace SET\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use SET\Handlers\DBConfigs\DBConfigs;
use SET\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Ensure installation is not in progress
        if (!strpos(url()->current(), 'install')) {
            DBConfigs::execute();
            $this->setupLdap();

            // Force SSL Secure Routes
            if (!$this->app->environment('local')) {
                if (app()::VERSION >= 5.4) {
                    \URL::forceScheme('https');    //# Method changed in Laravel 5.4
                } else {
                    \URL::forceSchema('https');
                }
            }
        }

        Setting::saving(function ($setting) {
            Cache::forever($setting->key, $setting->value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
            $this->app->register(DuskServiceProvider::class);
        }
    }

    private function setupLdap()
    {
        if (config('auth.providers.users.driver') == 'adldap') {
            $this->app->register('Adldap\Laravel\AdldapServiceProvider');
            $this->app->register('Adldap\Laravel\AdldapAuthServiceProvider');
        }
    }
}
