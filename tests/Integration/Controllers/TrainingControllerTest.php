<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Http\Controllers\TrainingController;
use SET\Training;
use SET\TrainingUser;
use SET\User;

class TrainingControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->signIn();
        $this->withoutEvents();
    }

    /**
     * @test
     */
    public function it_shows_the_index_page()
    {
        // Logged in as admin - Can access the training page
        $this->action('GET', 'TrainingController@index');

        $this->seePageIs('training');
        $this->assertViewHas('trainings');

        // Logged in as a regular user - Cannot access the training page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/training');

        $this->seeStatusCode(403);

        // Logged in as a user with role view - Can access the training page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', '/training');

        $this->seePageIs('training');
        $this->assertViewHas('trainings');
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the training create page
        $this->call('GET', 'training/create');

        $this->seePageIs('training/create');
        $this->assertViewHas('users');
        $this->assertViewHas('groups');
        $this->assertViewHas('training_types');

        $this->see('Training Type');

        // Logged in as a regular user - Cannot access the training create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/training/create');

        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot access the training create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', '/training/create');

        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_stores_the_training_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the training
        $data = ['name'        => 'A Training',
                 'renews_in'   => '15',
                 'description' => 'A Description',
                 'assign'      => 'None',
                 'due_date'    => '2016-11-28', ];

        $this->call('POST', 'training', $data);
        $this->assertRedirectedToRoute('training.index');

        // Retrieve the training note created by this user
        $userNote = SET\Note::where('user_id', $this->user->id)->get();

        // Ensure that the note is created  - tests the private method createTrainingNote
        $this->assertNotNull($userNote);

        // Logged in as a regular user - Does not store the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('POST', 'training', $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Does not store the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('POST', 'training', $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_training_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = [];

        $this->call('POST', 'training', $data);

        $this->assertSessionHasErrors();

        $this->assertSessionHasErrors(['name']);
        $this->assertSessionHasErrors('name', 'The name field is required.');

        // Logged in as admin - Only assign value is entered.
        $data = ['assign' => 'due_date'];
        $this->call('POST', 'training', $data);
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['name', 'due_date']);
    }

    /**
     * @test
     */
    public function it_shows_the_training()
    {
        // Create a training object
        $createdTraining = factory(Training::class)->create([]);
        $createdTrainingId = $createdTraining->id;

        // Logged in as admin - Can see the training details
        $this->call('GET', "training/$createdTrainingId");
        $this->seePageIs('/training/'.$createdTrainingId);
        $this->assertViewHas('notes');
        $this->assertViewHas('training');
        $this->assertViewHas('showAll');
    }

    /**
     * @test
     */
    public function can_edit_training()
    {
        // Create a training object
        $createdTraining = factory(Training::class)->create();
        $createdTrainingId = $createdTraining->id;

        // Logged in as admin - Can edit the training details
        $this->call('GET', "training/$createdTrainingId/edit");

        $this->seePageIs('/training/'.$createdTrainingId.'/edit');
        $this->assertViewHas('training');
        $this->assertViewHas('users');
        $this->assertViewHas('groups');
        $this->assertViewHas('training_types');
        $this->see('Training Type');

        // Logged in as a regular user - Cannot edit the training details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', "training/$createdTrainingId/edit");
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot edit the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', "training/$createdTrainingId/edit");
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_updates_the_training()
    {
        // Create a training object
        $trainingToCreate = factory(Training::class)->create();
        $createdTrainingId = $trainingToCreate->id;

        // Logged in as admin - Can update the training
        $data = ['name'        => 'A Training',
                 'renews_in'   => '15',
                 'description' => 'A Description',
                 'assign'      => 'None',
                 'due_date'    => '2016-11-28', ];

        $this->call('PATCH', "training/$createdTrainingId", $data);

        $this->assertRedirectedTo("/training/$createdTrainingId");

        $createdTraining = Training::find($trainingToCreate->id);
        $this->assertNotEquals($createdTraining->name, $trainingToCreate->name);
        $this->assertEquals($createdTraining->name, $data['name']);
        $this->assertEquals($createdTraining->description, $data['description']);
        $this->assertEquals($createdTraining->renews_in, $data['renews_in']);
        $this->assertEquals($createdTraining->due_date, $trainingToCreate->due_date);
        $this->assertEquals($createdTraining->assign, $trainingToCreate->assign);

        // Retrieve the training note created by this user
        $userNote = SET\Note::where('user_id', $this->user->id)->get();

        // Ensure that the note is created - tests the private method createTrainingNote
        $this->assertNotNull($userNote);

        // Logged in as a regular user - Cannot update the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('PATCH', "training/$createdTrainingId", $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot update the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('PATCH', "training/$createdTrainingId", $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_deletes_the_training()
    {
        // Create a training object
        $trainingToCreate = factory(Training::class)->create();
        $createdTrainingId = $trainingToCreate->id;

        // Ensure the created training is in the database
        $createdTraining = Training::find($createdTrainingId);
        $this->assertNotNull($createdTraining);
        $this->assertEquals($createdTraining->id, $createdTrainingId);

        // Delete the created training. Assert that a null object is returned.
        $this->call('DELETE', "training/$createdTrainingId");
        $deletedTraining = Training::find($createdTrainingId);
        $this->assertNull($deletedTraining);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete training page since the training with
        // the provided Id has already been deleted
        $this->call('DELETE', "training/$createdTrainingId");
        $this->seeStatusCode(403);

        // Create a new training(Only user with edit permission can create)
        factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $trainingToCreate = factory(Training::class)->create();
        $createdTrainingId = $trainingToCreate->id;

        // Try to delete as a regular user. Get forbidden status code
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('DELETE', "training/$createdTrainingId");
        $this->seeStatusCode(403);

        // Try to delete as a user with view permissions. Get forbidden status code
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);

        $this->call('DELETE', "training/$createdTrainingId");
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_assigns_training()
    {
        $training = factory(Training::class)->create();
        $createdTrainingId = $training->id;

        $this->action('GET', 'TrainingController@assignForm', $createdTrainingId);

        $this->seePageIs("training/{$createdTrainingId}/assign");
        $this->assertViewHas('training');
        $this->assertViewHas('users');
        $this->assertViewHas('groups');

        // Logged in as a regular user - Cannot assign the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->action('GET', 'TrainingController@assignForm', $createdTrainingId);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot assign the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->action('GET', 'TrainingController@assignForm', $createdTrainingId);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_assigns_users_to_training()
    {
        $training = factory(Training::class)->create();
        $createdTrainingId = $training->id;

        $data = ['name'        => 'A Training',
                 'renews_in'   => '15',
                 'description' => 'A Description',
                 'assign'      => 'None',
                 'due_date'    => '2016-11-28',
                 'users'       => [$this->user->id], ];

        $this->call('POST', "/training/$createdTrainingId/assign/", $data);
        $this->assertRedirectedTo("/training/$createdTrainingId");

        // Retrieve the training note created by this user
        $userNote = SET\Note::where('user_id', $this->user->id)->get();

        // Ensure that the note is created - tests the private method createTrainingNote
        $this->assertNotNull($userNote);

        // Logged in as a regular user - Cannot assign users to training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('POST', "/training/$createdTrainingId/assign/", $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot assign users to training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('POST', "/training/$createdTrainingId/assign/", $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_sends_reminders()
    {
        $trainingUser = factory(TrainingUser::class)->create();
        $trainingUserId = $trainingUser->id;

        $this->action('GET', 'TrainingController@sendReminder', $trainingUserId);
        $this->expectsEvents(SET\Events\TrainingAssigned::class);
    }
}
