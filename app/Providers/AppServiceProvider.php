<?php

namespace SET\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use SET\Handlers\DBConfigs\DBConfigs;
use SET\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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

        if(env('APP_DEBUG')) {
            DB::listen(function($query) {
                File::append(
                    storage_path('/logs/query.log'),
                    $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
                );
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
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
