<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use SET\Visit;

class ExpiringVisits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visits:expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'gets a list of visitation rights that will expire in the next week.';

    protected $visits;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->visits = Visit::with('user')
            ->whereBetween('expiration_date', [
                Carbon::today()->subDay()->toDateString(), Carbon::today()->addWeek()->toDateString(),
            ])
            ->activeUsers()
            ->orderBy('expiration_date')
            ->get();

        return $this;
    }

    public function getList()
    {
        return $this->visits;
    }
}
