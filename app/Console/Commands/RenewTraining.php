<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use SET\TrainingUser;
use SET\Events\TrainingAssigned;

class RenewTraining extends Command
{
    /**
     * The number of days to renew a training before it expires.
     *
     * @var int
     */
    protected $offset = 30;


    /**
     * List of all renewed notes.
     *
     * @var Collection
     */
    protected $trainingAdminRecord;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'training:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renews training before it expires.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->trainingAdminRecord = new Collection();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $trainingUsers = TrainingUser::with('user', 'training')
            ->whereNotNull('completed_date')
            ->where('due_date', '<', Carbon::today())
            ->activeUsers()
            ->get();

        foreach ($trainingUsers as $trainingUser) {
            if (!$this->renewedAlready($trainingUser) && $trainingUser->training->renews_in > 0) {
                $this->processRenewal($trainingUser);
            }
        }
    }

    public function getTrainingAdminRecord()
    {
        return $this->trainingAdminRecord->sortBy('userFullName');
    }

    /**
     * Check if training note has been renewed already.
     *
     * @param $trainingUser
     * @return bool
     */
    private function renewedAlready($trainingUser)
    {
        $trainingRecord = TrainingUser::where('training_id', $trainingUser->training_id)
            ->where('user_id', $trainingUser->user_id)
            ->where('due_date', '>', Carbon::today())
            ->get();

        if ($trainingRecord->isEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * Generate a new Training note that will be due $this->offset days from now.
     *
     * @param $trainingUser
     */
    private function processRenewal($trainingUser)
    {
        $today = Carbon::today();

        $renewalDate = Carbon::createFromFormat('Y-m-d', $trainingUser->completed_date)
            ->addDays($trainingUser->training->renews_in)
            ->subDays($this->offset);

        $dueDate = Carbon::createFromFormat('Y-m-d', $trainingUser->completed_date)
            ->addDays($trainingUser->training->renews_in);

        //check if the renewalDate is past today and if the dueDate is still in the future,
        if ($today >= $renewalDate && $today < $dueDate) {
            $this->createRecord($trainingUser, $dueDate);

            //Email user of new training is due
            Event::fire(new TrainingAssigned($trainingUser));

            //
            $this->trainingAdminRecord->push([
                'name' => $trainingUser->user->userFullName,
                'training' => $trainingUser->training->name,
                'due_date' => $dueDate->toDateString(),
            ]);
        }
    }

    /**
     * @param $trainingUser
     * @param Carbon $dueDate
     * @return TrainingUser
     */
    private function createRecord($trainingUser, $dueDate)
    {
        $newNote = TrainingUser::create([
            'user_id' => $trainingUser->user_id,
            'author_id' => 1,
            'training_id' => $trainingUser->training_id,
            'due_date' => $dueDate->toDateString()
        ]);
        return $newNote;
    }
}
