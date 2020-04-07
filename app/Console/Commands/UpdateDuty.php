<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use SET\Duty;
use SET\Handlers\Duty\DutyList;
use SET\Mail\DutyToday;
use SET\Mail\DutyUpcoming;

/**
 * Processes the emails for upcoming employees working security checks. Also notifies FSO
 * Class UpdateDuty.
 */
class UpdateDuty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duty:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the duty roster and sends out email notifications to users.';

    /**
     * Execute the console command.
     *
     * @return UpdateDuty
     */
    public function handle()
    {
        $duties = Duty::all();
        foreach ($duties as $duty) {
            $emailList = (new DutyList($duty))->scheduledUpdate();
            $this->processEmailList($emailList, $duty);
        }

        return $this;
    }

    private function processEmailList($emailList, $duty)
    {
        foreach ($emailList as $usersDateArray) {
            if ($usersDateArray['date'] == Carbon::today()->format('Y-m-d')) {
                foreach ($usersDateArray['users'] as $user) {
                    //Mail::to($user)->send(new DutyToday($duty, $user));
                }
            }

            if ($this->isReadyForNotification($duty, $usersDateArray)) {
                $this->sendUsersUpcomingEmailNotification($duty, $usersDateArray);
            }
        }
    }

    /**
     * @param $duty
     * @param $usersDateArray
     *
     * @return bool
     */
    private function isReadyForNotification($duty, $usersDateArray)
    {
        //return ($duty->cycle == 'daily' && $usersDateArray['date'] == Carbon::today()->addWeeks(1)->format('Y-m-d'));
        return ($duty->cycle == 'weekly' && $usersDateArray['date'] == Carbon::today()->addWeeks(2)->format('Y-m-d'))
            || ($duty->cycle == 'daily' && $usersDateArray['date'] == Carbon::today()->addWeeks(1)->format('Y-m-d'));
        
    }

    /**
     * @param $duty
     * @param $usersDateArray
     *
     * @return UpdateDuty
     */
    private function sendUsersUpcomingEmailNotification($duty, $usersDateArray)
    {
        foreach ($usersDateArray['users'] as $user) {
            //Mail::to($user)->send(new DutyUpcoming($duty, $user, $usersDateArray['date']));
        }

        return $this;
    }
}
