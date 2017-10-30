<?php

namespace Tests\Integration\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Training;
use SET\User;
use Tests\TestCase;

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
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('trainingUser');
        $response->assertViewHas('calendar');
        $response->assertViewHas('duties');

        // Logged in as a user with role view - Can access the home page and
        // display calendar, duties, etc
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('trainingUser');
        $response->assertViewHas('activityLog');
        $response->assertViewHas('calendar');
        $response->assertViewHas('duties');

        // Logged in as a regular user - User redirected to user's home page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/');

        $response->assertRedirect('/user/'.$newuser->id);
    }

    /**
     * @test
     */
    public function it_searches_users_and_trainings()
    {
        // Logged in as admin - Can search users and trainings
        $training = factory(Training::class)->create();
        $response = $this->get('/search');
        $response->assertJsonStructure(['status',
                                'error',
                                'data' => ['user', 'training'],
                                ]);

        $response->assertSee('"status":true');
        $response->assertSee('"error":null');
        $response->assertSee($this->user->last_name);
        $response->assertSee($training->name);

        // Logged in as a regular user - User redirected to user's home page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/search');
        $response->assertStatus(403);
    }
}
