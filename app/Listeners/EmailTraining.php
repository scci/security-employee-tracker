<?php

namespace SET\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use SET\Events\TrainingAssigned;
use SET\Setting;
use Illuminate\Support\Facades\Log;

/**
 * Class EmailTraining.
 */
class EmailTraining implements ShouldQueue
{
    /**
     * Email the employee about the training. Then return a notification to the user that an email was sent.
     *
     * @param TrainingAssigned $event
     *
     * @return void
     */
    public function handle(TrainingAssigned $event)
    {
        //Log::Info("Handle EmailTraining");
        $trainingUser = $event->getTrainingUser();
        $user = $trainingUser->user;
        $training = $trainingUser->training;
        $dueDate = $this->makeDueDatePretty($trainingUser->due_date);

        if (is_null($trainingUser->completed_date) and $training->administrative == 0) {
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
        //Log::Info("EmailTraining - sendEmail");
        //Log::Info("Training Users are :");
        //Log::Info($trainingUser);
        $reportAddress = Setting::get('mail_from_address', 'set@yourcompany.com');
        $reportName = Setting::get('mail_from_name', 'SET-yourcompany');

//        Mail::send(
//            'emails.training',
//            [
//                'user'          => $user,
//                'training'      => $training,
//                'due_date'      => $dueDate,
//                'trainingUser'  => $trainingUser,
//                'reportAddress' => $reportAddress,
//                'reportName'    => $reportName,
//            ],
//            function ($m) use ($user, $training, $reportAddress, $reportName) {
//                $m->from($reportAddress, $reportName);
//                $m->to($user->email, $user->userFullName)->subject($training->name.' was assigned to you.');
//
//                //ATTACH FILES
//                foreach ($training->attachments as $file) {
//                    if (!$file->admin_only) {
//                        $path = 'app/training_'.$file->imageable_id.'/'.$file->filename;
//                        $m->attach(storage_path($path), ['as' => $file->filename, 'mime' => $file->mime]);
//                    }
//                }
//            } // end $m function
//        );
    }
}
