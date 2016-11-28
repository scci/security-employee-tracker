<?php

namespace SET\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use SET\Events\TrainingAssigned;
use SET\Setting;

/**
 * Class EmailTraining.
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
     * @param TrainingAssigned $event
     *
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
     * @param string $date
     *
     * @return string
     */
    private function makeDueDatePretty($date)
    {
        //check if format is YYYY-MM-DD
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
            return Carbon::createFromFormat('Y-m-d', $date)->toFormattedDateString();
        }

        //Otherwise it is YYYY-MM-DD HH:MM:SS
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->toFormattedDateString();
    }

    /**
     * @param $user
     * @param $training
     * @param string $dueDate
     * @param $trainingUser
     */
    private function sendEmail($user, $training, $dueDate, $trainingUser)
    {
        $reportAddress = Setting::where('name', 'report_address')->first();

        Mail::send(
            'emails.training',
            [
                'user'          => $user,
                'training'      => $training,
                'due_date'      => $dueDate,
                'trainingUser'  => $trainingUser,
                'reportAddress' => $reportAddress,
            ],
            function ($m) use ($user, $training) {
                $m->to($user->email, $user->userFullName)->subject($training->name.' was assigned to you.');

                //ATTACH FILES
                foreach ($training->attachments as $file) {
                    $path = 'app/training_'.$file->imageable_id.'/'.$file->filename;
                    $m->attach(storage_path($path), ['as' => $file->filename, 'mime' => $file->mime]);
                }
            } // end $m function
        );
    }
}
