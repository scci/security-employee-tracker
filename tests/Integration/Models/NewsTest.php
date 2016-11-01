<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\News;

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
    $createdNews = factory(SET\News::class)->create(['publish_date' => Carbon::now()->format('Y-m-d')]);

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
        $createdNews1 = factory(SET\News::class)->create(
                        ['publish_date' => Carbon::tomorrow()->format('Y-m-d')]); //,
                        // 'expire_date'=>Carbon::yesterday()->format('Y-m-d')]);

    // Query the database using the scopePublishedNews method in the news model
    $newsPublished = News::publishedNews()->where('id', $createdNews1->id)->get();

    // Ensure that the query returns an empty collection
        $this->assertNotContains($createdNews1->title, $newsPublished->pluck('title'));

        $createdNews2 = factory(SET\News::class)->create(
                        ['publish_date' => Carbon::tomorrow()->format('Y-m-d'),
                         'expire_date'  => Carbon::yesterday()->format('Y-m-d'), ]);

        // Query the database using the scopePublishedNews method in the news model
    $newsPublished = News::publishedNews()->where('id', $createdNews2->id)->get();

    // Ensure that the query returns an empty collection
        $this->assertNotContains($createdNews2->title, $newsPublished->pluck('title'));
    }
}
