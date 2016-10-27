<?php

namespace SET\Mail;

use SET\News;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendNewsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $news;
    
    /**
     * Create a new message instance.
     *
     * @return Mailable
     */
    public function __construct(News $news)
    {
        $this->news = $news;
    }

    /**
     * Build the message.
     *
     * @return SendNewsEmail
     */
    public function build()
    {
        $mailer = $this->view('emails.published_news')
                ->subject($this->news->title);
        
        //ATTACH FILES
        foreach ($this->news->attachments as $file) {
            $path = 'app/news_' . $file->imageable_id . '/' . $file->filename;
            $mailer->attach(storage_path($path), ['as' => $file->filename, 'mime' => $file->mime]);
        }
        return $mailer;
    }
}
