<?php

namespace SET\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use SET\Console\Commands\ProcessMonday;
use SET\Console\Commands\RenewTraining;
use SET\Console\Commands\SendNews;
use SET\Console\Commands\SyncLdap;
use SET\Console\Commands\UpdateDuty;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateDuty::class,
        ProcessMonday::class,
        SendNews::class,
        SyncLdap::class,
        RenewTraining::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('duty:update')->withoutOverlapping()
            ->daily()->at('6:00');
        $schedule->command('emails:monday')->withoutOverlapping()
            ->weekly()->mondays()->at('6:01');
        $schedule->command('emails:news')->withoutOverlapping()
            ->daily()->at('6:00');

        if (config('auth.providers.users.driver') == 'adldap') {
            $schedule->command('users:sync')->withoutOverlapping()->hourly();
        }
    }
}
