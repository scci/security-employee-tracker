<?php

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

        $this->call('GET', "/user/$userId/travel/create");
        $this->seePageIs("/user/$userId/travel/create");

        // Create a regular user - Still logged in as admin
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        // Admin can access another user's travel create page
        $this->call('GET', "/user/$userId/travel/create");
        $this->seePageIs("/user/$userId/travel/create");

        // Logged in as a regular user - Cannot access the travel create page
        $this->actingAs($newuser);
        $this->call('GET', "/user/$userId/travel/create");
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot access the travel create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('GET', "/user/$userId/travel/create");

        $this->seeStatusCode(403);
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

        $this->call('POST', "/user/$userId/travel/", $data);
        $this->assertRedirectedToRoute('user.show', $userId);

        // Logged in as a regular user - Does not store the travel
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('POST', "/user/$userId/travel/", $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Does not store the travel
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('POST', "/user/$userId/travel/", $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_travel_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $userId = $this->user->id;
        $data = [];

        $this->call('POST', "/user/$userId/travel/", $data);

        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['location', 'leave_date', 'return_date']);
        $this->assertSessionHasErrors('location', 'The location field is required.');
        $this->assertSessionHasErrors('leave_date', 'The leave date field is required.');
        $this->assertSessionHasErrors('return_date', 'The return date field is required.');

        // Logged in as admin - Only comment is entered.
        $data = ['comment' => 'Travel Notes'];
        $this->call('POST', "/user/$userId/travel/", $data);
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors('location', 'The location field is required.');
        $this->assertSessionHasErrors('leave_date', 'The leave date field is required.');
        $this->assertSessionHasErrors('return_date', 'The return date field is required.');
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
        $this->call('GET', "/user/$userId/travel/$createdTravelId/edit");
        $this->seePageIs("/user/$userId/travel/$createdTravelId/edit");
        $this->assertViewHas('user');
        $this->assertViewHas('travel');

        // Logged in as a regular user - Can edit the own's travel page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('GET', "/user/$userId/travel/$createdTravelId/edit");
        $this->seePageIs("/user/$userId/travel/$createdTravelId/edit");
        $this->assertViewHas('user');
        $this->assertViewHas('travel');
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

        $this->call('PATCH', "/user/$userId/travel/$createdTravelId", $data);

        $this->assertRedirectedToRoute('user.show', $userId);

        $createdTravel = Travel::find($createdTravelId);
        $this->assertNotEquals($createdTravel->location, $travelToCreate->location);
        $this->assertEquals($createdTravel->location, $data['location']);
        $this->assertEquals($createdTravel->comment, $data['comment']);
        $this->assertEquals($createdTravel->leave_date, $data['leave_date']);
        $this->assertEquals($createdTravel->return_date, $data['return_date']);

        // Logged in as a regular user - Still logged in as admin
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        $this->call('PATCH', "/user/$userId/travel/$createdTravelId", $data);
        $this->assertRedirectedToRoute('user.show', $userId);

        // Logged in as new user. User should be able to edit own travel
        $this->actingAs($newuser);
        $this->call('PATCH', "/user/$userId/travel/$createdTravelId", $data);
        $this->assertRedirectedToRoute('user.show', $userId);

        // Logged in as a user with role view - Cannot update the travel for previous user
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('PATCH', "/user/$userId/travel/$createdTravelId", $data);
        $this->seeStatusCode(403);

        // Get userId for user with role view and try to update travel for the same user
        $userId = $newuser->id;
        $this->call('PATCH', "/user/$userId/travel/$createdTravelId", $data);
        $this->assertRedirectedToRoute('user.show', $userId);
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
        $this->call('DELETE', "/user/$userId/travel/$createdTravelId");
        $deletedTravel = Travel::find($createdTravelId);
        $this->assertNull($deletedTravel);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete travel page since the travel with
        // the provided Id has already been deleted
        $this->call('DELETE', "/user/$userId/travel/$createdTravelId");
        $this->seeStatusCode(403);

        // Create a new training user and try to delete. Get forbidden status code
        $travelToCreate = factory(Travel::class)->create();
        $createdTravelId = $travelToCreate->id;
        $this->call('DELETE', "/user/$userId/travel/$createdTravelId");
        $this->seeStatusCode(403);
    }
}
