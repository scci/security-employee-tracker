<?php

namespace SET\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailSupervisorReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $notes;

    /**
     * Create a new message instance.
     *
     * @param Collection $note
     */
    public function __construct(Collection $note)
    {
        $this->notes = $note;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.supervisor_reminder')
            ->subject('Training that your employees need to complete.');
    }
}
