<?php

namespace Tests\Integration\Controllers;
use Tests\TestCase;

use Illuminate\Support\Facades\Event;
use SET\Training;
use SET\TrainingUser;
use SET\User;
use SET\Events\TrainingAssigned;

class TrainingUserControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->signIn();
        $this->withoutEvents();
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the training user create page
        $userId = $this->user->id;
        $response = $this->get("/user/$userId/training/create");
        $response->assertStatus(200);

        $response->assertSeeText("Assign Training");
        $response->assertViewHas('user');
        $response->assertViewHas('training');
        $response->assertViewHas('disabled');

        // Logged in as a regular user - Cannot access the training user create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;

        $response = $this->get("/user/$userId/training/create");
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the training user create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;

        $response = $this->get("/user/$userId/training/create");
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_stores_the_training_user_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the training user
        Event::fake();
        $userId = $this->user->id;
        $data = ['training_id'      => factory(Training::class)->create()->id,
                 'completed_date'   => '',
                 'due_date'         => '2017-01-23',
                 'encrypt'          => '',
                 'comment'          => 'Training user notes', ];

        $response = $this->post("/user/$userId/training/", $data);
        $response->assertStatus(302);
        $response->assertRedirect('/user/'.$userId);        
        //$this->expectsEvents(SET\Events\TrainingAssigned::class);
        Event::assertDispatched(TrainingAssigned::class);

        // Logged in as a regular user - Cannot not store the training user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;

        $response = $this->post("/user/$userId/training/", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot not store the training user
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;

        $response = $this->post("/user/$userId/training/", $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_training_user_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $userId = $this->user->id;
        $data = [];

        $response = $this->post("/user/$userId/training/", $data);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['training_id', 'due_date']);
        $response->assertSessionHasErrors('training_id', 'Please select a training.');
        $response->assertSessionHasErrors('due_date', 'The due date field is required.');

        // Logged in as admin - Only due_date is entered.
        $data = ['due_date' => '2017-01-23'];
        $response = $this->post("/user/$userId/training/", $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors('training_id', 'Please select a training.');
    }

    /**
     * @test
     */
    public function it_shows_the_training_user()
    {
        // Logged in as admin
        $userId = $this->user->id;

        // Create a traininguser object
        $trainingUser = factory(TrainingUser::class)->create();
        $trainingUserId = $trainingUser->id;

        // Create a traininguser object without the completed date
        $trainingUserNoCompletedDate = factory(TrainingUser::class)->create(['completed_date' => null]);
        $trainingUserNoCompletedDateId = $trainingUserNoCompletedDate->id;

        //Can see the training user details
        $response = $this->get("/user/$userId/training/$trainingUserId");
        $response->assertRedirect('user/'.$userId);

        $response = $this->get("/user/$userId/training/$trainingUserNoCompletedDateId");
        $response->assertSee("/user/$userId/training/$trainingUserNoCompletedDateId");

        // Logged in as regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;

        // Can see the training user details for the logged in user
        $response = $this->get("/user/$userId/training/$trainingUserId");
        $response->assertRedirect('user/'.$userId);

        // Can see the training user details for the logged in user
        $response = $this->get("/user/$userId/training/$trainingUserNoCompletedDateId");
        $response->assertSee("/user/$userId/training/$trainingUserNoCompletedDateId");

        // Create another new user - But try to access the training user page for the previous user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot see the training user details for the previously created user
        $response = $this->get("/user/$userId/training/$trainingUserId");
        $response->assertStatus(403);

        // Cannot see the training user details for the previously created user
        $response = $this->get("/user/$userId/training/$trainingUserNoCompletedDateId");
        $response->assertStatus(403);

        // Logged in as user with view permissions
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;

        // Can see the training user details for the logged in user
        $response = $this->get("/user/$userId/training/$trainingUserId");
        $response->assertRedirect('user/'.$userId);

        // Can see the training user details for the logged in user
        $response = $this->get("/user/$userId/training/$trainingUserNoCompletedDateId");
        $response->assertSee("/user/$userId/training/$trainingUserNoCompletedDateId");
    }

    /**
     * @test
     */
    public function can_edit_the_training_user()
    {
        // Create a traininguser object
        $userId = $this->user->id;
        $trainingUserToCreate = factory(TrainingUser::class)->create();
        $trainingUserId = $trainingUserToCreate->id;

        // Logged in as admin - Can edit the training
        $response = $this->get("/user/$userId/training/$trainingUserId/edit");
        $response->assertStatus(200);
        $response->assertSeeText("Update Training");
        $response->assertViewHas('user');
        $response->assertViewHas('trainingUser');
        $response->assertViewHas('training');
        $response->assertViewHas('disabled');

        // Create a new user - Still logged in as an admin
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        // Able to edit the new user's training
        $response = $this->get("/user/$userId/training/$trainingUserId/edit");
        $response->assertStatus(200);
        $response->assertSeeText("Update Training");
        $response->assertViewHas('user');
        $response->assertViewHas('trainingUser');
        $response->assertViewHas('training');
        $response->assertViewHas('disabled');

        // Log in as a regular user - User should be able to edit their own training page
        $this->actingAs($newuser);

        $response = $this->get("/user/$userId/training/$trainingUserId/edit");
        $response->assertStatus(200);
        $response->assertSeeText("Update Training");
        $response->assertViewHas('user');
        $response->assertViewHas('trainingUser');
        $response->assertViewHas('training');
        // Ensure the due by field is disabled since user is not an admin.
        $response->assertViewHas('disabled', 'disabled');

        // Create a new user
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        // Still logged in as previous user - Cannot edit the newuser's training
        $response = $this->get("/user/$userId/training/$trainingUserId/edit");
        $response->assertStatus(403);

        // Create a new user with view permissions
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);

        // Cannot edit the previous user's training
        $response = $this->get("/user/$userId/training/$trainingUserId/edit");
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_updates_the_training_user()
    {
        // Get the admin user id
        $userId = $this->user->id;

        // Create a traininguser object
        $trainingUserToCreate = factory(TrainingUser::class)->create();
        $createdTrainingUserId = $trainingUserToCreate->id;

        // Logged in as admin - Can update the training user
        $data = ['training_id'      => $createdTrainingUserId,
                 'completed_date'   => '',
                 'due_date'         => '2016-12-29',
                 'comment'          => 'Training User Notes',
                ];

        $response = $this->patch("/user/$userId/training/$createdTrainingUserId", $data);

        $response->assertRedirect('user/'.$userId);

        // Ensure the training user is updated with the provided data
        $updatedTrainingUser = TrainingUser::find($createdTrainingUserId);
        $this->assertEquals($updatedTrainingUser->completed_date, $data['completed_date']);
        $this->assertEquals($updatedTrainingUser->comment, $data['comment']);
        $this->assertEquals($updatedTrainingUser->due_date, $data['due_date']);

        // Create new user - still logged in as admin
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        $response = $this->patch("/user/$userId/training/$createdTrainingUserId", $data);
        $response->assertRedirect('user/'.$userId);

        // Logged in as new user. User should be able to edit own training
        $this->actingAs($newuser);
        $response = $this->patch("/user/$userId/training/$createdTrainingUserId", $data);
        $response->assertRedirect('user/'.$userId);

        // Logged in as a user with role view - Cannot update another user's training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);

        $response = $this->patch("/user/$userId/training/$createdTrainingUserId", $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_deletes_the_training_user()
    {
        // Create a training user object
        $userId = $this->user->id;
        $trainingUserToCreate = factory(TrainingUser::class)->create();
        $createdTrainingUserId = $trainingUserToCreate->id;

        // Ensure the created training user is in the database
        $createdTrainingUser = TrainingUser::find($createdTrainingUserId);
        $this->assertNotNull($createdTrainingUser);
        $this->assertEquals($createdTrainingUser->id, $createdTrainingUserId);

        // Delete the created training user. Assert that a null object is returned.
        $response = $this->delete("/user/$userId/training/$createdTrainingUserId");
        $deletedTrainingUser = TrainingUser::find($createdTrainingUserId);
        $this->assertNull($deletedTrainingUser);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete training user page since the training user with
        // the provided Id has already been deleted
        $response = $this->delete("/user/$userId/training/$createdTrainingUserId");
        $response->assertStatus(403);

        // Create a new training user and try to delete. Get forbidden status code
        $trainingUserToCreate = factory(TrainingUser::class)->create();
        $createdTrainingUserId = $trainingUserToCreate->id;
        $response = $this->delete("/user/$userId/training/$createdTrainingUserId");
        $response->assertStatus(403);
    }
}
