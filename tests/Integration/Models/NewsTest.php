<?php

namespace Tests\Integration\Models;
use Tests\TestCase;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use SET\Mail\SendNewsEmail;
use SET\News;
use SET\User;

class NewsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    /*
     Test the News::scopePublishedNews method
    */
    public function get_published_news()
    {
        // Create a news
        $createdNews = factory(News::class)->create(['publish_date' => Carbon::now()->format('Y-m-d')]);

        // Query the database using the scopePublishedNews method in the news model
        $newsPublished = News::publishedNews()->where('id', $createdNews->id)->get();

        // Assert that the correct news is returned
        $this->assertEquals($newsPublished->first()->title, $createdNews->title);
        $this->assertEquals($newsPublished->first()->description, $createdNews->description);
        $this->assertEquals($newsPublished->first()->author_id, $createdNews->author_id);
        $this->assertEquals($newsPublished->first()->publish_date, $createdNews->publish_date);
    }

    /** @test */
    /*
     Test the News::scopePublishedNews method
    */
    public function get_unpublished_news()
    {
        $createdNews1 = factory(News::class)->create(
                        ['publish_date' => Carbon::tomorrow()->format('Y-m-d')]); //,
                        // 'expire_date'=>Carbon::yesterday()->format('Y-m-d')]);

        // Query the database using the scopePublishedNews method in the news model
        $newsPublished = News::publishedNews()->where('id', $createdNews1->id)->get();

        // Ensure that the query returns an empty collection
        $this->assertNotContains($createdNews1->title, $newsPublished->pluck('title'));

        $createdNews2 = factory(News::class)->create(
                        ['publish_date' => Carbon::tomorrow()->format('Y-m-d'),
                         'expire_date'  => Carbon::yesterday()->format('Y-m-d'), ]);

        // Query the database using the scopePublishedNews method in the news model
        $newsPublished = News::publishedNews()->where('id', $createdNews2->id)->get();

        // Ensure that the query returns an empty collection
        $this->assertNotContains($createdNews2->title, $newsPublished->pluck('title'));
    }

    /** @test */
    public function it_sends_out_emails_to_all_users()
    {
        Mail::fake();

        $news = factory(News::class)->create(['publish_date' => Carbon::now(), 'send_email' => 1]);

        $news->emailNews();

        $users = User::skipSystem()->active()->get();
        Mail::assertQueued(SendNewsEmail::class);
    }

    /** @test */
    public function it_does_not_send_out_emails_if_the_publish_date_is_not_today()
    {
        Mail::fake();

        $news = factory(News::class)->create(['publish_date' => Carbon::tomorrow(), 'send_email' => 1]);

        $news->emailNews();

        Mail::assertNotQueued(SendNewsEmail::class);
    }

    /** @test */
    public function it_does_not_send_out_emails_if_not_flagged_to_send_email()
    {
        Mail::fake();

        $news = factory(News::class)->create(['publish_date' => Carbon::today(), 'send_email' => 0]);

        $news->emailNews();

        Mail::assertNotQueued(SendNewsEmail::class);
    }

    /** @test */
    public function it_returns_the_expiration_date_in_the_correct_format()
    {
        $news = factory(News::class)->create(['expire_date' => Carbon::today()]);

        $this->assertEquals($news->expirationDate, Carbon::today()->format('Y-m-d'));
    }
}
