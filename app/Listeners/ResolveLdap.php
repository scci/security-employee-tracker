<?php

namespace SET\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class ResolveLdap implements ShouldQueue
{
    /**
     * Build the event handler.
     */
    public function __construct()
    {
        //
    }

    /**
     * Call to add missing users.
     */
    public function handle()
    {
        if (config('auth.providers.users.driver') == 'adldap') {
            Artisan::call('adldap:import');
        }
    }
}
