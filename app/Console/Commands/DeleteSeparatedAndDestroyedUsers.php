<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use SET\User;

class DeleteSeparatedAndDestroyedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:destroyed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Changes deadman users to destroyed users after 2 years.';

    protected $destroyed;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->destroyed = new Collection();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setDestroyed();
    }

    /**
     * Return list of users who were destroyed.
     *
     * @return Collection
     */
    public function getDestroyed()
    {
        return $this->destroyed;
    }

    public function setDestroyed()
    {
        $deadmanList = User::where(function ($q) {
            $q->where('status', 'separated')->orWhere('status', 'destroyed');
        })
            ->whereBetween('destroyed_date', [Carbon::today(), Carbon::today()->addDays(6)])
            ->get();

        foreach ($deadmanList as $user) {
            Storage::deleteDirectory('user_'.$user->id);
            $user->delete();
        }

        $this->destroyed = $deadmanList;
    }
}
