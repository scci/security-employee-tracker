<?php

namespace SET\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use SET\Events\TrainingAssigned;
use Setting;

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
        return Carbon::createFromFormat('Y-m-d', $date)->toFormattedDateString();
    }

    /**
     * @param $user
     * @param $training
     * @param string $dueDate
     * @param $trainingUser
     */
    private function sendEmail($user, $training, $dueDate, $trainingUser)
    {
        $reportAddress = Setting::get('sender_address', 'set@yourcompany.com');

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
