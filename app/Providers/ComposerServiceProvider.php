<?php

namespace SET\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SET\Duty;
use SET\Setting;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::share('app_name', Setting::get('app_name', 'SET'));

        // Pass the logged in user's full name to the layouts._header view.
        view()->composer(['layouts._navbar', 'home._last_login'], function ($view) {
            $view->with('logged_in_user', Auth::user());
            $view->with('duties', Duty::all());
        });

        // Pass our action items if we have the sidebar.
        view()->composer('layouts._sidebar_action_items', 'SET\Http\ViewComposers\ActionItemsComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
