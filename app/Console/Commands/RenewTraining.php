<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use SET\Events\TrainingAssigned;
use SET\TrainingUser;

class RenewTraining extends Command
{
    /**
     * The number of days to renew a training before it expires.
     *
     * @var int
     */
    protected $offset = 14;


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
            ->orderBy('completed_date', 'desc')
            ->get()
            ->unique('user_id', 'training_id');

        foreach ($trainingUsers as $trainingUser) {
            if (!$this->renewedAlready($trainingUser) && $this->timeToRenew($trainingUser)) {
                $this->processRenewal($trainingUser);
            }
        }

        return $this;
    }

    public function getTrainingAdminRecord()
    {
        return $this->trainingAdminRecord->sortBy('userFullName');
    }

    /**
     * Check if training note has been renewed already.
     *
     * @param $trainingUser
     *
     * @return bool
     */
    private function renewedAlready($trainingUser)
    {
        $trainingRecord = TrainingUser::where('training_id', $trainingUser->training_id)
            ->where('user_id', $trainingUser->user_id)
            ->where('due_date', '>', Carbon::today())
            ->get();

        return !$trainingRecord->isEmpty();
    }

    /**
     * Check if the training is past the renews_in value.
     *
     * @param $trainingUser
     *
     * @return bool
     */
    private function timeToRenew($trainingUser)
    {
        if ($trainingUser->training->renews_in == 0) {
            return false;
        }

        $today = Carbon::today();

        $renewalDate = Carbon::createFromFormat('Y-m-d', $trainingUser->completed_date)
            ->addDays($trainingUser->training->renews_in)
            ->subDays($this->offset);

        if ($renewalDate >= $today) {
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
        $dueDate = Carbon::createFromFormat('Y-m-d', $trainingUser->completed_date)
            ->addDays($trainingUser->training->renews_in);

        $assignedTraining = $this->createRecord($trainingUser, $dueDate);

        //Email user of new training is due
        Event::fire(new TrainingAssigned($assignedTraining));

        //
        $this->trainingAdminRecord->push([
            'name'     => $assignedTraining->user->userFullName,
            'training' => $assignedTraining->training->name,
            'due_date' => $dueDate->toDateString(),
        ]);
    }

    /**
     * @param $trainingUser
     * @param Carbon $dueDate
     *
     * @return TrainingUser
     */
    private function createRecord($trainingUser, $dueDate)
    {
        $newNote = TrainingUser::create([
            'user_id'        => $trainingUser->user_id,
            'author_id'      => 1,
            'training_id'    => $trainingUser->training_id,
            'due_date'       => $dueDate->toDateString(),
            'completed_date' => null,
        ]);

        return $newNote;
    }
}
