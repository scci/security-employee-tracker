<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use SET\User;

class DeleteUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes users on their destroyed date.';

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

        return $this;
    }

    /**
     * Return list of users who were destroyed.
     *
     * @return Collection
     */
    public function getList()
    {
        return $this->destroyed;
    }

    public function setDestroyed()
    {
        $deadmanList = User::where('status', '!=', 'active')
            ->where('destroyed_date', '<=', Carbon::today())
            ->get();

        foreach ($deadmanList as $user) {
            Storage::deleteDirectory('user_'.$user->id);
            $user->delete();
        }

        $this->destroyed = $deadmanList;
    }
}
