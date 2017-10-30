<?php

namespace Tests\Integration\Controllers;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Travel;
use SET\User;

class TravelControllerTest extends TestCase
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
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the travel create page
        $userId = $this->user->id;

        $response = $this->get("/user/$userId/travel/create");
        $response->assertStatus(200);
        $response->assertSeeText("Add a Travel");

        // Create a regular user - Still logged in as admin
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        // Admin can access another user's travel create page
        $response = $this->get("/user/$userId/travel/create");
        $response->assertStatus(200);
        $response->assertSeeText("Add a Travel");

         // Logged in as a regular user - Cannot access the travel create page
        $this->actingAs($newuser);
        $response = $this->get("/user/$userId/travel/create");
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the travel create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->get("/user/$userId/travel/create");

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_stores_the_travel_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the travel
        $userId = $this->user->id;
        $data = ['location'      => 'Test travel',
                 'leave_date'    => '2016-12-10',
                 'return_date'   => '2016-12-20',
                 'brief_date'    => '2016-12-08',
                 'debrief_date'  => '2016-12-21',
                 'comment'       => 'Description For travel',
                 'encrypt'       => '', ];

        $response = $this->post("/user/$userId/travel/", $data);
        $response->assertStatus(302);
        $response->assertRedirect('user/'.$userId);

        // Logged in as a regular user - Does not store the travel
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->post("/user/$userId/travel/", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Does not store the travel
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->post("/user/$userId/travel/", $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_travel_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $userId = $this->user->id;
        $data = [];

        $response = $this->post("/user/$userId/travel/", $data);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['location', 'leave_date', 'return_date']);
        $response->assertSessionHasErrors('location', 'The location field is required.');
        $response->assertSessionHasErrors('leave_date', 'The leave date field is required.');
        $response->assertSessionHasErrors('return_date', 'The return date field is required.');

        // Logged in as admin - Only comment is entered.
        $data = ['comment' => 'Travel Notes'];
        $response = $this->post("/user/$userId/travel/", $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors('location', 'The location field is required.');
        $response->assertSessionHasErrors('leave_date', 'The leave date field is required.');
        $response->assertSessionHasErrors('return_date', 'The return date field is required.');
    }

    /**
     * @test
     */
    public function can_edit_the_travel()
    {
        // Create a travel object
        $userId = $this->user->id;
        $travelToCreate = factory(Travel::class)->create();
        $createdTravelId = $travelToCreate->id;

        // Logged in as admin - Can edit the travel
        $response = $this->get("/user/$userId/travel/$createdTravelId/edit");
        $response->assertStatus(200);
        $response->assertViewHas('user');
        $response->assertViewHas('travel');

        // Logged in as a regular user - Can edit the own's travel page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->get("/user/$userId/travel/$createdTravelId/edit");
        $response->assertStatus(200);
        $response->assertViewHas('user');
        $response->assertViewHas('travel');
    }

    /**
     * @test
     */
    public function it_updates_the_travel()
    {
        // Create a travel object
        $userId = $this->user->id;
        $travelToCreate = factory(Travel::class)->create();
        $createdTravelId = $travelToCreate->id;

        // Logged in as admin - Can update the travel
        $data = ['location'     => 'Travel to this place',
                 'comment'      => 'This is a travel comment',
                 'leave_date'   => '2016-12-10',
                 'return_date'  => '2016-12-20', ];

        $response = $this->patch("/user/$userId/travel/$createdTravelId", $data);

        $response->assertRedirect('user/'.$userId);

        $createdTravel = Travel::find($createdTravelId);
        $this->assertNotEquals($createdTravel->location, $travelToCreate->location);
        $this->assertEquals($createdTravel->location, $data['location']);
        $this->assertEquals($createdTravel->comment, $data['comment']);
        $this->assertEquals($createdTravel->leave_date, $data['leave_date']);
        $this->assertEquals($createdTravel->return_date, $data['return_date']);

        // Logged in as a regular user - Still logged in as admin
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        $response = $this->patch("/user/$userId/travel/$createdTravelId", $data);
        $response->assertRedirect('user/'.$userId);

        // Logged in as new user. User should be able to edit own travel
        $this->actingAs($newuser);
        $response = $this->patch("/user/$userId/travel/$createdTravelId", $data);
        $response->assertRedirect('user/'.$userId);

        // Logged in as a user with role view - Cannot update the travel for previous user
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->patch("/user/$userId/travel/$createdTravelId", $data);
        $response->assertStatus(403);

        // Get userId for user with role view and try to update travel for the same user
        $userId = $newuser->id;
        $response = $this->patch("/user/$userId/travel/$createdTravelId", $data);
        $response->assertRedirect('user/'.$userId);
    }

    /**
     * @test
     */
    public function it_deletes_the_travel()
    {
        // Create a travel object
        $userId = $this->user->id;
        $travelToCreate = factory(Travel::class)->create();
        $createdTravelId = $travelToCreate->id;

        // Ensure the created travel is in the database
        $createdTravel = Travel::find($createdTravelId);
        $this->assertNotNull($createdTravel);
        $this->assertEquals($createdTravel->id, $createdTravelId);

        // Delete the created travel. Assert that a null object is returned.
        $response = $this->delete("/user/$userId/travel/$createdTravelId");
        $deletedTravel = Travel::find($createdTravelId);
        $this->assertNull($deletedTravel);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete travel page since the travel with
        // the provided Id has already been deleted
        $response = $this->delete("/user/$userId/travel/$createdTravelId");
        $response->assertStatus(403);

        // Create a new training user and try to delete. Get forbidden status code
        $travelToCreate = factory(Travel::class)->create();
        $createdTravelId = $travelToCreate->id;
        $response = $this->delete("/user/$userId/travel/$createdTravelId");
        $response->assertStatus(403);
    }
}
