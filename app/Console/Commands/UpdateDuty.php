<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use SET\Duty;
use SET\Handlers\Duty\DutyList;
use SET\Setting;

/**
 * Processes the emails for upcoming employees working security checks. Also notifies FSO
 * Class UpdateDuty
 * @package SET\Console\Commands
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
     * @return mixed
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
        foreach($emailList as $usersDateArray) {
            if ($usersDateArray['date'] == Carbon::today()->format('Y-m-d')){
                foreach ($usersDateArray['users'] as $user) {
                    $this->sendTodaysNotification($user, $duty);
                }
            }

            if ($this->isReadyForNotification($duty, $usersDateArray)) {
                $this->sendUsersUpcomingEmailNotification($duty, $usersDateArray);
            }
        }

    }

    /**
     * Send out a reminder for people working next week
     * @param $user
     * @param $duty
     */
    private function sendTodaysNotification($user, $duty)
    {
        Mail::send('emails.duty_today', [
            'user' => $user,
            'duty' => $duty
        ], function ($m) use ($user, $duty) {
            $m->to($user->email, $user->userFullName)
                ->subject('Reminder: You have ' . $duty->name . ' security check today.');
        });
    }


    private function sendNotificationWithICS($user, $duty, $date)
    {
        Mail::send('emails.duty_future', [
            'user' => $user,
            'date' => $date,
            'duty' => $duty
        ], function ($m) use ($user, $duty, $date) {

            $filename = $this->generateICS($duty, Carbon::createFromFormat('Y-m-d',$date));

            if ($duty->cycle = 'daily') {
                $subject = "You have $duty->name security check on $date.";
            } else {
                $subject = "You have $duty->name security check starting $date.";
            }
            $m->to($user->email, $user->userFullName)
                ->subject($subject)
                ->attach($filename, array('mime' => "text/calendar"));
        });
    }

    /**
     * @param $duty
     * @param $usersDateArray
     * @return bool
     */
    private function isReadyForNotification($duty, $usersDateArray)
    {
        return ( $duty->cycle == 'weekly' && $usersDateArray['date'] == Carbon::today()->addWeeks(2)->format('Y-m-d') )
            || ( $duty->cycle == 'daily'  && $usersDateArray['date'] == Carbon::today()->addWeeks(1)->format('Y-m-d') );
    }

    /**
     * @param $duty
     * @param $date
     * @return string
     */
    private function generateICS($duty, $date)
    {
        $reportAddress = Setting::where('name', 'report_address')->first();

        $rrule = '';
        if ($duty->cycle == 'weekly') {
            $rrule = "RRULE:FREQ=DAILY;COUNT=5;INTERVAL=1;";
        } else if ($duty->cycle == 'daily') {
            $rrule = "RRULE:FREQ=DAILY;COUNT=1;INTERVAL=1;";
        }

        $filename = "schedule.ics";
        $meetingDuration = (1800); // 30 minutes
        $time = "T15:30:00.00";
        $meetingstamp = strtotime($date->toFormattedDateString() . $time);
        $dtstart = gmdate('Ymd\THis\Z', $meetingstamp);
        $dtend = gmdate('Ymd\THis\Z', $meetingstamp + $meetingDuration);
        $todaystamp = gmdate('Ymd\THis\Z');
        $title = "You have $duty->name security check";
        $organizer = "MAILTO:" . $reportAddress->secondary;

        // ICS
        $mail[0] = "BEGIN:VCALENDAR";
        $mail[1] = "PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN";
        $mail[2] = "VERSION:2.0";
        $mail[3] = "CALSCALE:GREGORIAN";
        $mail[4] = "METHOD:REQUEST";
        $mail[5] = "BEGIN:VEVENT";
        $mail[6] = "DTSTART;TZID=America/Chicago:" . $dtstart;
        $mail[7] = "DTEND;TZID=America/Chicago:" . $dtend;
        $mail[8] = "DTSTAMP;TZID=America/Chicago:" . $todaystamp;
        $mail[9] = "UID:" . date('Ymd') . 'T' . date('His') . '-' . rand() . '@teamscci.com';
        $mail[10] = "ORGANIZER;" . $organizer;
        $mail[11] = "CREATED:" . $todaystamp;
        $mail[12] = "LAST-MODIFIED:" . $todaystamp;
        $mail[14] = "SEQUENCE:0";
        $mail[15] = "STATUS:CONFIRMED";
        $mail[16] = "SUMMARY:" . $title;
        $mail[17] = "TRANSP:OPAQUE";
        $mail[18] = "X-MICROSOFT-CDO-IMPORTANTCE:1";
        $mail[19] = $rrule;
        $mail[20] = "END:VEVENT";
        $mail[21] = "END:VCALENDAR";

        $mail = implode("\r\n", $mail);
        header("text/calendar");
        file_put_contents($filename, $mail);
        return $filename;
    }

    /**
     * @param $duty
     * @param $usersDateArray
     * @return mixed
     */
    private function sendUsersUpcomingEmailNotification($duty, $usersDateArray)
    {
        foreach ($usersDateArray['users'] as $user) {
            $this->sendNotificationWithICS($user, $duty, $usersDateArray['date']);
        }

        return $this;
    }


}
