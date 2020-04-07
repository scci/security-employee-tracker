<?php

namespace SET\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SET\Duty;
use SET\Setting;
use SET\User;

class DutyUpcoming extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $date;
    public $duty;

    /**
     * DutyUpcoming constructor.
     *
     * @param Duty $duty
     * @param User $user
     * @param $date
     */
    public function __construct(Duty $duty, User $user, $date)
    {
        $this->duty = $duty;
        $this->user = $user;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filename = $this->generateICS();

        if ($this->duty->cycle == 'daily') {
            $subject = 'You have '.$this->duty->name." security check on $this->date.";
        } else {
            $subject = 'You have '.$this->duty->name." security check starting $this->date.";
        }

        return $this->view('emails.duty_future')
            ->subject($subject)
            ->attach($filename, ['mime' => 'text/calendar']);
    }

    /**
     * Make our ICS file.
     *
     * @return string
     */
    private function generateICS()
    {
        $date = Carbon::createFromFormat('Y-m-d', $this->date);

        $reportAddress = Setting::get('summary_recipient', null);
        $recipientEmails = User::whereIn('id', $reportAddress)->get()->pluck('email');

        $rrule = '';
        if ($this->duty->cycle == 'weekly') {
            $rrule = 'RRULE:FREQ=DAILY;COUNT=5;INTERVAL=1;';
        } elseif ($this->duty->cycle == 'daily') {
            $rrule = 'RRULE:FREQ=DAILY;COUNT=1;INTERVAL=1;';
        }

        $filename = 'schedule.ics';
        $meetingDuration = (1800); // 30 minutes
        $time = 'T15:30:00.00';
        $meetingstamp = strtotime($date->toFormattedDateString().$time);
        $dtstart = gmdate('Ymd\THis\Z', $meetingstamp);
        $dtend = gmdate('Ymd\THis\Z', $meetingstamp + $meetingDuration);
        $todaystamp = gmdate('Ymd\THis\Z');
        $title = "You have $this->duty->name security check";
        $organizer = 'MAILTO:'.$recipientEmails;

        // ICS
        $mail = [];
        $mail[0] = 'BEGIN:VCALENDAR';
        $mail[1] = 'PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN';
        $mail[2] = 'VERSION:2.0';
        $mail[3] = 'CALSCALE:GREGORIAN';
        $mail[4] = 'METHOD:REQUEST';
        $mail[5] = 'BEGIN:VEVENT';
        $mail[6] = 'DTSTART;TZID=America/Chicago:'.$dtstart;
        $mail[7] = 'DTEND;TZID=America/Chicago:'.$dtend;
        $mail[8] = 'DTSTAMP;TZID=America/Chicago:'.$todaystamp;
        $mail[9] = 'UID:'.date('Ymd').'T'.date('His').'-'.rand().'@teamscci.com';
        $mail[10] = 'ORGANIZER;'.$organizer;
        $mail[11] = 'CREATED:'.$todaystamp;
        $mail[12] = 'LAST-MODIFIED:'.$todaystamp;
        $mail[14] = 'SEQUENCE:0';
        $mail[15] = 'STATUS:CONFIRMED';
        $mail[16] = 'SUMMARY:'.$title;
        $mail[17] = 'TRANSP:OPAQUE';
        $mail[18] = 'X-MICROSOFT-CDO-IMPORTANTCE:1';
        $mail[19] = $rrule;
        $mail[20] = 'END:VEVENT';
        $mail[21] = 'END:VCALENDAR';

        $mail = implode("\r\n", $mail);
        header('text/calendar');
        file_put_contents($filename, $mail);

        return $filename;
    }
}
