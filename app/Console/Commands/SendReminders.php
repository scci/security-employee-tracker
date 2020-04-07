<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use SET\Events\TrainingAssigned;
use SET\Mail\EmailSupervisorReminder;
use SET\TrainingUser;
use Illuminate\Support\Facades\Log;

/**
 * Class SendReminders.
 */
class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends out email reminders a week before they are due.';

    /**
     * @var
     */
    protected $trainingUsers;

    /**
     * Execute the console command.
     * Sends out reminder emails to all employees. Then sends a reminder to their supervisor.
     *
     * @return SendReminders
     */
    public function handle()
    {
        $this->setTrainingUsers();

        Log::Info("***** Get Each Training Users Training Due *****");
        foreach ($this->trainingUsers as $trainingUser) {
            Log::Info($trainingUser->user->userFullName . " - " . $trainingUser->training->name . " - " . $trainingUser->due_date);
            //Event::fire(new TrainingAssigned($trainingUser));
        }

        Log::Info("***** --------- *****");
        Log::Info("***** --------- *****");
       // $this->emailSupervisor();

        return $this;
    }

    /**
     * Email the employee's supervisor if a user has training that isn't complete yet.
     */
    public function emailSupervisor()
    {
        $supervisors = $this->getSupervisors();

        foreach ($supervisors as $supervisor) {
            $newNotes = new Collection();

            $this->trainingUsers->each(function ($item) use ($supervisor, $newNotes) {
                if ($item->user->supervisor_id == $supervisor->id) {
                    $newNotes->push($item);
                }
            });

            if (!$newNotes->isEmpty()) {
                //Mail::to($supervisor)->send(new EmailSupervisorReminder($newNotes));
            }
        }
    }

    /**
     * Stores a list of incomplete training.
     */
    public function setTrainingUsers()
    {
        Log::Info("***** Setting Incomplete Training Users *****");
        $this->trainingUsers = TrainingUser::with([
            'training', 'training.attachments', 'user', 'user.supervisor',
        ])
            ->where('due_date', '<=', Carbon::today()->addWeek())
            ->whereNull('completed_date')
            ->activeUsers()
            ->orderBy('due_date')
            ->get();

        //Log::Info($this->trainingUsers);
        Log::Info("***** Done setting Incomplete Training Users *****");
        return $this->trainingUsers;
    }

    /**
     * Get our Notes list.
     *
     * @return mixed
     */
    public function getList()
    {
        return $this->trainingUsers;
    }

    /**
     * Take a list of notes and return a list of supervisors related to those notes.
     *
     * @return Collection
     */
    private function getSupervisors()
    {
        $supervisors = new Collection();
        foreach ($this->trainingUsers as $trainingUser) {
            if ($trainingUser->user->supervisor) {
                $supervisors->push($trainingUser->user->supervisor);
            }
        }

        return $supervisors->unique();
    }
}
