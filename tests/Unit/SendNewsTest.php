<?php

use Tests\Testcase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use SET\Console\Commands\SendNews;
use SET\Mail\SendNewsEmail;
use SET\News;

class SendNewsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sends_out_news_on_its_publish_date()
    {
        Mail::fake();

        $send_news = factory(News::class)->create(['send_email' => 1, 'publish_date' => Carbon::today()]);
        $news = factory(News::class)->create();

        (new SendNews())->handle();

        Mail::assertQueued(SendNewsEmail::class, function ($mail) use ($send_news) {
            return $mail->news->id == $send_news->id;
        });

        Mail::assertNotQueued(SendNewsEmail::class, function ($mail) use ($news) {
            return $mail->news->id == $news->id;
        });
    }
}
