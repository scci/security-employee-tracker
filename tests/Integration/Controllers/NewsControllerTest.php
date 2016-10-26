<?php

use Carbon\Carbon;
use SET\User;
use SET\News;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewsControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function setUp()
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
        $this->action('GET', 'NewsController@index');
                
        $this->seePageIs('news');
        $this->assertViewHas('allNews');       
        
        // Logged in as a regular user - Can still access the news page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);       
        $this->call('GET', '/news');
        
        $this->seePageIs('news');
        $this->assertViewHas('allNews');
       
    }
    
    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the news create page
        $this->call('GET', 'news/create');
        
        $this->seePageIs('news/create');
        
        // Logged in as a regular user - Cannot access the news create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser); //->visit('/news');        
        $this->call('GET', '/news/create');
        
        $this->seeStatusCode(403);
        
        // Logged in as a user with role view - Cannot access the news create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);        
        $this->call('GET', '/news/create');
        
        $this->seeStatusCode(403);       
    }
    
    /**
     * @test
     */
    public function it_stores_the_news_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the news
        $data = ['title' => 'My Title',
                 'description' => "A Description",
                 'publish_date' => "2016-10-28",
                 'send_email' => 0];
        
        $this->call('POST', 'news', $data);
        $this->assertRedirectedToRoute('news.index');
        
        // Logged in as a regular user - Does not store the news
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('POST', 'news', $data);
        $this->seeStatusCode(403);
        
        // Logged in as a user with role view - Does not store the news
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('POST', 'news', $data);
        $this->seeStatusCode(403);     
    }
    
    /**
     * @test
     */
    public function it_does_not_store_news_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = [];
        
        $this->call('POST', 'news', $data);
        
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(array('title', 'description', 'publish_date'));
        $this->assertSessionHasErrors('title', "The title field is required.");
        $this->assertSessionHasErrors('description', "The description field is required.");
        $this->assertSessionHasErrors('publish_date', "The publish_date field is required.");
        
        // Logged in as admin - Only publish_date is entered.
        $data = ['publish_date' => "2016-10-28"];
        $this->call('POST', 'news', $data);
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(array('title', 'description'));
        
        // Logged in as admin - Invalid expire_date is provided
        $data = ['title' => 'My Title',
                 'description' => "A Description",
                 'publish_date' => "2016-10-28",
                 'expire_date' => "2016-10-22"];
        $this->call('POST', 'news', $data);
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors('expire_date', "The expire date must be a date after publish date.");

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
        $this->call('GET', "news/$createdNewsId");
        $this->seePageIs('/news/' . $createdNewsId);
    }

    /** @test */

    public function it_does_not_show_unpublished_news_to_regular_users()
    {
        $createdNews = factory(News::class)->create(['publish_date' => Carbon::tomorrow()]);
        $createdNewsId = $createdNews->id;

        // Logged in as a regular user - Cannot see the news details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', "news/$createdNewsId");
        $this->seeStatusCode(403);
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
        $this->call('GET', "news/$createdNewsId/edit");
        $this->seePageIs('/news/'.$createdNewsId.'/edit');
        
        // Logged in as a regular user - Cannot edit the news details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', "news/$createdNewsId/edit");
        $this->seeStatusCode(403);
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
        $data = ['title' => 'My Title',
                 'description' => "A Description",
                 'publish_date' => "2016-10-28"];
        
        $this->call('PATCH', "news/$createdNewsId", $data);
        
        $this->assertRedirectedToRoute('news.index');
        
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
        $this->call('PATCH', "news/$createdNewsId", $data);
        $this->seeStatusCode(403);
        
        // Logged in as a user with role view - Cannot update the news
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('PATCH', "news/$createdNewsId", $data);
        $this->seeStatusCode(403);
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
        $this->call('DELETE', "news/$createdNewsId");
        $deletedNews = News::find($createdNewsId);
        $this->assertNull($deletedNews);
        
        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        
        // Cannot access the delete news page since the news with 
        // the provided Id has already been deleted
        $this->call('DELETE', "news/$createdNewsId");
        $this->seeStatusCode(404);
        
        // Create a new news and try to delete. Get forbidden status code
        $newsToCreate = factory(News::class)->create();
        $createdNewsId = $newsToCreate->id;
        $this->call('DELETE', "news/$createdNewsId");
        $this->seeStatusCode(403);
    }
 }
