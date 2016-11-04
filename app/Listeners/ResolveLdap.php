<?php

namespace SET\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use SET\User;

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
            $this->activateUsers();
        }
    }

    private function activateUsers()
    {
        User::whereBetween('created_at', [Carbon::now()->subMinutes(3), Carbon::now()])
        ->update(['status' => 'active']);
    }
}
