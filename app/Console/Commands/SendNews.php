<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use SET\News;

class SendNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send News Emails on Published Date';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $newsToPublish = News::where('publish_date', Carbon::today())
            ->where('send_email', 1)
            ->get();

        foreach ($newsToPublish as $news) {
            $news->emailNews();
        }
    }
}
