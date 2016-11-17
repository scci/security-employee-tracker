<?php

namespace SET\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SET\Duty;
use SET\User;

class DutyToday extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $duty;
    public $user;

    /**
     * DutyToday constructor.
     *
     * @param Duty $duty
     * @param User $user
     */
    public function __construct(Duty $duty, User $user)
    {
        $this->duty = $duty;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.duty_today')
            ->subject('Reminder: You have '.$this->duty->name.' security check today.');
    }
}
