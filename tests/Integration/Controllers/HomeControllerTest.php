<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Training;
use SET\User;

class HomeControllerTest extends TestCase
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
        // Logged in as admin - Can access the home page and display calendar, duties, etc
        $this->action('GET', 'HomeController@index');

        $this->assertEquals('/', Route::getCurrentRoute()->getPath());
        $this->assertViewHas('trainingUser');
        // $this->assertViewHas('log');
        $this->assertViewHas('calendar');
        $this->assertViewHas('duties');

        // Logged in as a user with role view - Can access the home page and
        // display calendar, duties, etc
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->action('GET', 'HomeController@index');

        $this->assertEquals('/', Route::getCurrentRoute()->getPath());
        $this->assertViewHas('trainingUser');
        // $this->assertViewHas('log');
        $this->assertViewHas('calendar');
        $this->assertViewHas('duties');

        // Logged in as a regular user - User redirected to user's home page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->action('GET', 'HomeController@index');

        $this->assertRedirectedTo('/user/'.$newuser->id);
    }

    /**
     * @test
     */
    public function it_searches_users_and_trainings()
    {
        // Logged in as admin - Can search users and trainings
        $training = factory(Training::class)->create();
        $this->action('GET', 'HomeController@search');
        $this->seeJsonStructure(['status',
                                'error',
                                'data' => ['user', 'training'],
                                ]);

        $this->assertContains('"status":true', $this->response->getContent());
        $this->assertContains('"error":null', $this->response->getContent());
        $this->assertContains($this->user->last_name, $this->response->getContent());
        $this->assertContains($training->name, $this->response->getContent());

        // Logged in as a regular user - User redirected to user's home page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->action('GET', 'HomeController@search');
        $this->seeStatusCode(403);
    }
}
