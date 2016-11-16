<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use League\Flysystem\Exception;
use SET\User;

class SyncLdap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync users table with LDAP';

    /**
     * Execute the console command.
     *
     * @return Exception|null
     */
    public function handle()
    {
        if (config('auth.providers.users.driver') != 'adldap') {
            return new Exception('LDAP not setup. Syncing users will not work.');
        }

        Artisan::call('adldap:import');
        $this->activateUsers();
    }

    private function activateUsers()
    {
        User::whereBetween('created_at', [Carbon::now()->subMinutes(1), Carbon::now()])
            ->update(['status' => 'active']);
    }
}
