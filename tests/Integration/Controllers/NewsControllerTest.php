<?php

namespace Tests\Integration\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\News;
use SET\User;
use Tests\TestCase;

class NewsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signIn();
    }

    /**
     * @test
     */
    public function it_shows_the_index_page()
    {
        // Logged in as admin - Can access the news page
        $response = $this->get('/news');
        $response->assertStatus(200);
        $response->assertViewHas('allNews');

        // Logged in as a regular user - Can still access the news page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->call('GET', '/news');
        $response->assertViewHas('allNews');
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the news create page
        $response = $this->get('/news/create');
        $response->assertSee('Add News');
        $response->assertSee('Publish Date');
        $response->assertSee('Expires On:');

        // Logged in as a regular user - Cannot access the news create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser); //->visit('/news');
        $response = $this->get('/news/create');
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the news create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('/news/create');
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_stores_the_news_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the news
        $data = ['title'        => 'My Title',
                 'description'  => 'A Description',
                 'publish_date' => '2016-10-28',
                 'send_email'   => 0, ];

        $response = $this->post('news', $data);
        //$this->call('POST', 'news', $data);
        $response->assertRedirect('news');

        // Logged in as a regular user - Does not store the news
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->post('news', $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Does not store the news
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->post('news', $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_news_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = [];

        $response = $this->post('news', $data);

        //$this->assertSessionHasErrors();
        $response->assertSessionHasErrors(['title', 'description', 'publish_date']);
        $response->assertSessionHasErrors('title', 'The title field is required.');
        $response->assertSessionHasErrors('description', 'The description field is required.');
        $response->assertSessionHasErrors('publish_date', 'The publish_date field is required.');

        // Logged in as admin - Only publish_date is entered.
        $data = ['publish_date' => '2016-10-28'];
        $response = $this->post('news', $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['title', 'description']);

        // Logged in as admin - Invalid expire_date is provided
        $data = ['title'        => 'My Title',
                 'description'  => 'A Description',
                 'publish_date' => '2016-10-28',
                 'expire_date'  => '2016-10-22', ];
        $response = $this->post('news', $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors('expire_date', 'The expire date must be a date after publish date.');
    }

    /**
     * @test
     */
    public function it_shows_the_news()
    {
        // Create a news object
        $createdNews = factory(News::class)->create([]);
        $createdNewsId = $createdNews->id;

        // Logged in as admin - Can see the news details
        $response = $this->get("news/$createdNewsId");
        $response->assertStatus(200);
        $response->assertSee('/news/'.$createdNewsId);
        $response->assertSee($createdNews->title);
    }

    /** @test */
    public function it_does_not_show_unpublished_news_to_regular_users()
    {
        $createdNews = factory(News::class)->create(['publish_date' => Carbon::tomorrow()]);
        $createdNewsId = $createdNews->id;

        // Logged in as a regular user - Cannot see the news details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get("news/$createdNewsId");
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function can_edit_the_news()
    {
        // Create a news object
        $createdNews = factory(News::class)->create();
        $createdNewsId = $createdNews->id;

        // Logged in as admin - Can edit the news details
        $response = $this->get("news/$createdNewsId/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit News');
        $response->assertSee('Title');
        $response->assertSee('Publish Date:');
        $response->assertSee($createdNews->publish_date);
        $response->assertSee('Expires On:');

        // Logged in as a regular user - Cannot edit the news details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get("news/$createdNewsId/edit");
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_updates_the_news_user_roles()
    {
        // Create a news object
        $newsToCreate = factory(News::class)->create();
        $createdNewsId = $newsToCreate->id;

        // Logged in as admin - Can update the news
        $data = ['title'        => 'My Title',
                 'description'  => 'A Description',
                 'publish_date' => '2016-10-28', ];

        $response = $this->patch("news/$createdNewsId", $data);

        $response->assertRedirect('news');

        $createdNews = News::find($newsToCreate->id);
        $this->assertNotEquals($createdNews->title, $newsToCreate->title);
        $this->assertEquals($createdNews->title, $data['title']);
        $this->assertEquals($createdNews->description, $data['description']);
        $this->assertEquals($createdNews->publish_date, $data['publish_date']);
        $this->assertEquals($createdNews->expire_date, $newsToCreate->expire_date);
        $this->assertEquals($createdNews->author_id, $newsToCreate->author_id);

        // Logged in as a regular user - Cannot update the news
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->patch("news/$createdNewsId", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot update the news
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->patch("news/$createdNewsId", $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_deletes_the_news()
    {
        // Create a news object
        $newsToCreate = factory(News::class)->create();
        $createdNewsId = $newsToCreate->id;

        // Ensure the created news is in the database
        $createdNews = News::find($createdNewsId);
        $this->assertNotNull($createdNews);
        $this->assertEquals($createdNews->id, $createdNewsId);

        // Delete the created news. Assert that a null object is returned.
        $response = $this->delete("news/$createdNewsId");
        $deletedNews = News::find($createdNewsId);
        $this->assertNull($deletedNews);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete news page since the news with
        // the provided Id has already been deleted
        $response = $this->delete("news/$createdNewsId");
        $response->assertStatus(404);

        // Create a new news and try to delete. Get forbidden status code
        $newsToCreate = factory(News::class)->create();
        $createdNewsId = $newsToCreate->id;
        $response = $this->delete("news/$createdNewsId");
        $response->assertStatus(403);
    }
}
