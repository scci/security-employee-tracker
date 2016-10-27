<?php

namespace SET\Console\Commands;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use SET\TrainingUser;
use Carbon\Carbon;
use SET\Events\TrainingAssigned;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use SET\Visit;

/**
 * Class SendReminders
 * @package SET\Console\Commands
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
     * @var
     */
    protected $visits;

    /**
     * Execute the console command.
     * Sends out reminder emails to all employees. Then sends a reminder to their supervisor.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setTrainingUsers();
        $this->setVisits();

        foreach ($this->trainingUsers as $trainingUser) {
            Event::fire(new TrainingAssigned($trainingUser));
        }

        $this->emailSupervisor();

    }

    /**
     * Email the employee's supervisor if a user has training that isn't complete yet.
     */
    public function emailSupervisor()
    {
        $supervisors = $this->getSupervisors();

        foreach ($supervisors as $supervisor) {

            $newNotes = new Collection();

            $this->trainingUsers->each(function($item, $key) use ($supervisor, $newNotes) {
                if ($item->user->supervisor_id == $supervisor->id) {
                    $newNotes->push($item);
                }
            });

            if (!$newNotes->isEmpty()) {
                Mail::send('emails.supervisor_reminder', ['notes' => $newNotes], function($m) use ($supervisor) {
                    $m->to($supervisor->email, $supervisor->userFullName)
                        ->subject($supervisor->email . ' Training that your employees need to complete.');
                });
            }

        }
    }

    /**
     * Stores a list of incomplete training.
     */
    public function setTrainingUsers()
    {
        $this->trainingUsers = TrainingUser::with(['training', 'training.attachments', 'user', 'user.supervisor'])
            ->where('due_date', '<=', Carbon::today()->addWeek(2))
            ->whereNull('completed_date')
            ->activeUsers()
            ->orderBy('due_date')
            ->get();

        return $this->trainingUsers;
    }

    /**
     *  Stores a list of visits that are due to expire sometime between yesterday and the next week.
     */
    public function setVisits()
    {
        $this->visits = Visit::with('user')
            ->whereBetween('expiration_date', [
                Carbon::today()->subDay()->toDateString(), Carbon::today()->addWeek()->toDateString()
            ])
            ->activeUsers()
            ->orderBy('expiration_date')
            ->get();
    }

    /**
     * Get our Notes list
     * @return mixed
     */
    public function gettrainingUsers()
    {
        return $this->trainingUsers;
    }

    /**
     * Get our Visits list
     * @return mixed
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Take a list of notes and return a list of supervisors related to those notes.
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
