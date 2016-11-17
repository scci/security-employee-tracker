<?php

namespace SET\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SET\Events\TrainingAssigned;
use SET\Listeners\EmailTraining;
use SET\Listeners\LogUserAccess;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Login::class => [
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
