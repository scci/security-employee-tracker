<?php

namespace SET\Providers;

use SET\Events\TrainingAssigned;
use SET\Listeners\ResolveLdap;
use SET\Listeners\LogUserAccess;
use SET\Listeners\EmailTraining;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Login::class => [
            ResolveLdap::class,
            LogUserAccess::class,
        ],
        TrainingAssigned::class => [
            EmailTraining::class,
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}
