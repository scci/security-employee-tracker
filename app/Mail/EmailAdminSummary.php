<?php

namespace SET\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailAdminSummary extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $visits;
    public $records;
    public $destroyed;
    public $notes;
    public $monday;
    public $dutyLists;

    /**
     * EmailAdminSummary constructor.
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->notes = $array['notes'];
        $this->visits = $array['visits'];
        $this->records = $array['records'];
        $this->destroyed = $array['destroyed'];
        $this->dutyLists = $array['dutyLists'];
        $this->monday = Carbon::now()->startOfWeek();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.admin_reminder')
            ->subject('SET Weekly Report');
    }
}
