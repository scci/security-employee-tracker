<?php

namespace SET\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use SET\Events\TrainingAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class EmailTraining
 * @package SET\Listeners
 */
class EmailTraining implements ShouldQueue
{
    /**
     * Create the event handler.
     */
    public function __construct()
    {
        //
    }

    /**
     * Email the employee about the training. Then return a notification to the user that an email was sent.
     *
     * @param  TrainingAssigned $event
     * @return void
     */
    public function handle(TrainingAssigned $event)
    {
        $trainingUser = $event->getTrainingUser();
        $user = $trainingUser->user;
        $training = $trainingUser->training;
        $dueDate = $this->makeDueDatePretty($trainingUser->due_date);

        if (is_null($trainingUser->completed_date)) {
            $this->sendEmail($user, $training, $dueDate, $trainingUser);
        }
    }

    /**
     * @param $due_date
     * @return mixed
     */
    private function makeDueDatePretty($due_date)
    {
        return Carbon::createFromFormat('Y-m-d', $due_date)->toFormattedDateString();
    }

    /**
     * @param $user
     * @param $training
     * @param $dueDate
     * @param $trainingUser
     */
    private function sendEmail($user, $training, $dueDate, $trainingUser)
    {
        Mail::send(
            'emails.training',
            ['user' => $user, 'training' => $training, 'due_date' => $dueDate, 'trainingUser' => $trainingUser],
            function ($m) use ($user, $training) {
                $m->to($user->email, $user->userFullName)->subject($training->name . " was assigned to you.");

                //ATTACH FILES
                foreach ($training->attachments as $file) {
                    $path = 'app/training_' . $file->imageable_id . '/' . $file->filename;
                    $m->attach(storage_path($path), ['as' => $file->filename, 'mime' => $file->mime]);
                }
            } // end $m function
        );
    }
}
