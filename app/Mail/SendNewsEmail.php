<?php

namespace SET\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SET\News;
use SET\Setting;

class SendNewsEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $news;
    public $reportAddress;

    /**
     * SendNewsEmail constructor.
     *
     * @param News $news
     */
    public function __construct(News $news)
    {
        $this->news = $news;
        $this->reportAddress = Setting::where('name', 'report_address')->first();
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
            $path = 'app/news_'.$file->imageable_id.'/'.$file->filename;
            $mailer->attach(storage_path($path), ['as' => $file->filename, 'mime' => $file->mime]);
        }

        return $mailer;
    }
}
