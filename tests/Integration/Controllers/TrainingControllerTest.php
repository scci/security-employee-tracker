<?php

namespace Tests\Integration\Controllers;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use SET\Training;
use SET\TrainingType;
use SET\TrainingUser;
use SET\User;
use SET\Note;
use SET\Events\TrainingAssigned;

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
        $response = $this->get('training');
        $response->assertStatus(200);

        // Logged in as a regular user - Cannot access the training page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('training');
        $response->assertStatus(403);


        // Logged in as a user with role view - Can access the training page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('training');
        $response->assertStatus(200); // OK status code
        $response->assertSee("Training, Credentials and Briefings");        
        $response->assertViewHas('trainings');
        $response->assertViewHas('isTrainingType');
        $response->assertViewHas('hasTrainingType');

        // Verify trainingtype page components do not appear (tests views\layouts\_navbar.blade.php)
        $response->assertSee('/training">Trainings</a>'); // Navbar item
        $response->assertDontSee('<th>Type</th>'); // Table column
        $response->assertDontSee('data-tooltip="Manage Training Types"'); // Training Type button (tests views\training\index.blade.php)
    }

    /**
     * @test Passing of the Training Type to the Training index
     * web.php Route::get('/training/trainingtype/{trainingTypeID}', ['uses' => 'TrainingController@index']);
     */
    public function it_shows_the_index_page_for_specific_trainingtype()
    {
        // MIMIC call when there are no training types
        // Logged in as admin - Can access the training page
        $response = $this->get('training');
        $response->assertStatus(200); // OK status code
        $response->assertSee("Training, Credentials and Briefings");        
        $response->assertViewHas('trainings');
        $response->assertViewHas('isTrainingType');
        $response->assertViewHas('hasTrainingType');
        
        //  Verify page components when no Training Types (views\layouts\_navbar.blade.php)
        $response->assertSee('/training">Trainings</a>'); // Navbar item
        $response->assertDontSee('<th>Type</th>'); // Table column
        $response->assertSee('data-tooltip="Manage Training Types"'); // Training Type button (tests views\training\index.blade.php)

        // MIMIC call when there are training types
        // Create a trainingtype object
        $createdTrainingType = factory(TrainingType::class)->create([]);
        $createdTrainingTypeId = $createdTrainingType->id;

        // Create a training object
        $createdTraining = factory(Training::class)->create([]);        

        // Associating trainingtype to a Training
        $createdTraining->trainingType()->associate($createdTrainingType);
        $createdTraining->save();
        // Ensure trainingtype is associated with training
        $this->assertEquals($createdTrainingType->id, $createdTraining->training_type_id);

        // Logged in as admin - Can access the training page
        $response = $this->get("training/trainingtype/$createdTrainingTypeId");

        $response->assertStatus(200); // OK status code
        $response->assertViewHas('trainings');
        $response->assertViewHas('isTrainingType');
        $response->assertViewHas('hasTrainingType');

        // Verify page components
        $response->assertSee('<title>SET - Training Directory</title>'); // Page Title
        $response->assertSee('Training, Credentials and Briefings</span>'); // Block Title
        $response->assertSee('<th>Name</th>'); // Table header
        $response->assertSee('<th>Type</th>'); // Table header
        $response->assertSee('<th>Incomplete</th>'); // Table header
        $response->assertSee('<th>Completed</th>'); // Table header

        // Verify page components when there are Training Types (views\layouts\_navbar.blade.php)
        $response->assertSee('data-activates="training-lists2">Trainings<'); // Navbar item
        $response->assertSee('/training">All</a>'); // Navbar menu item link
        $response->assertSee($createdTrainingType->name.'</a>'); // Navbar menu item link (app\Providers\ComposerServiceProvider.php)
        $response->assertSee('<th>Type</th>'); // Table column
        // (tests views\training\index.blade.php)
        $response->assertDontSee('data-tooltip="Manage Training Types"'); // Training Type button

        // Verify page components when there are no active Training Types
        $createdTrainingType->status = 0;
        $createdTrainingType->save();
        // Logged in as admin - Can access the training page
        $response = $this->get("training/trainingtype/$createdTrainingTypeId");
        $response->assertStatus(200); // OK status code
        
        // Verify page components for inactive training types (app\Providers\ComposerServiceProvider.php)
        $response->assertSee('/training">Trainings</a>'); // Navbar item displays if no active training types
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the training create page
        $response = $this->get('training/create');
        $response->assertStatus(200);
        
        $response->assertViewHas('users');
        $response->assertViewHas('groups');
        $response->assertViewHas('training_types');

        // Logged in as a regular user - Cannot access the training create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/training/create');

        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the training create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('/training/create');

        $response->assertStatus(403);
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

        $response = $this->post('training', $data);
        $response->assertRedirect('training');

        // Retrieve the training note created by this user
        $userNote = Note::where('user_id', $this->user->id)->get();

        // Ensure that the note is created  - tests the private method createTrainingNote
        $this->assertNotNull($userNote);

        // Logged in as a regular user - Does not store the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->post('training', $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Does not store the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->post('training', $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_training_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = [];

        $response = $this->post('training', $data);

        $response->assertSessionHasErrors();

        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasErrors('name', 'The name field is required.');

        // Logged in as admin - Only assign value is entered.
        $data = ['assign' => 'due_date'];
        $response = $this->post('training', $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['name', 'due_date']);
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
        $response = $this->get("training/$createdTrainingId");
        $response->assertStatus(200);
        $response->assertViewHas('notes');
        $response->assertViewHas('training');
        $response->assertViewHas('showAll');

        //  Verify page components
        $response->assertSee('Auto Renew'); // Block title
        $response->assertSee('Attachments'); // Block title
        $response->assertSee('Description'); // Block title
        // When there are no training types (views\layouts\_new_training.blade.php)
        $response->assertDontSee('Training Type'); // Block title

        // MIMIC call when there are training types
        // Create trainingtype object
        $createdTrainingType = factory(TrainingType::class)->create([]);        
        // Associating trainingtype to a Training
        $createdTraining->trainingType()->associate($createdTrainingType);                
        $createdTraining->save();
        // Ensure trainingtype is associated with training
        $this->assertEquals($createdTrainingType->id, $createdTraining->training_type_id);        
        
        // Logged in as admin - Can access the training page
        $response= $this->get("training/$createdTrainingId");
        
        $response->assertStatus(200); // OK status code
        $response->assertViewHas('training');
        
        // Verify page components - When there are no training types (views\layouts\_new_training.blade.php)
        $response->assertSee('Auto Renew'); // Block title
        $response->assertSee('Attachments'); // Block title
        $response->assertSee('Description'); // Block title
        $response->assertSee('Training Type'); // Block title
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
        $response= $this->get("training/$createdTrainingId/edit");

        $response->assertStatus(200);
        $response->assertViewHas('training');
        $response->assertViewHas('users');
        $response->assertViewHas('groups');
        $response->assertViewHas('training_types');

        // Logged in as a regular user - Cannot edit the training details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response= $this->get("training/$createdTrainingId/edit");
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot edit the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response= $this->get("training/$createdTrainingId/edit");
        $response->assertStatus(403);
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

        $response= $this->patch("training/$createdTrainingId", $data);

        $response->assertRedirect("/training/$createdTrainingId");

        $createdTraining = Training::find($trainingToCreate->id);
        $this->assertNotEquals($createdTraining->name, $trainingToCreate->name);
        $this->assertEquals($createdTraining->name, $data['name']);
        $this->assertEquals($createdTraining->description, $data['description']);
        $this->assertEquals($createdTraining->renews_in, $data['renews_in']);
        $this->assertEquals($createdTraining->due_date, $trainingToCreate->due_date);
        $this->assertEquals($createdTraining->assign, $trainingToCreate->assign);

        // Retrieve the training note created by this user
        $userNote = Note::where('user_id', $this->user->id)->get();

        // Ensure that the note is created - tests the private method createTrainingNote
        $this->assertNotNull($userNote);

        // Logged in as a regular user - Cannot update the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response= $this->patch("training/$createdTrainingId", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot update the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response= $this->patch("training/$createdTrainingId", $data);
        $response->assertStatus(403);
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
        $response = $this->delete("training/$createdTrainingId");
        $deletedTraining = Training::find($createdTrainingId);
        $this->assertNull($deletedTraining);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete training page since the training with
        // the provided Id has already been deleted
        $response = $this->delete("training/$createdTrainingId");
        $response->assertStatus(403);

        // Create a new training(Only user with edit permission can create)
        factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $trainingToCreate = factory(Training::class)->create();
        $createdTrainingId = $trainingToCreate->id;

        // Try to delete as a regular user. Get forbidden status code
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->delete("training/$createdTrainingId");
        $response->assertStatus(403);

        // Try to delete as a user with view permissions. Get forbidden status code
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);

        $response = $this->delete("training/$createdTrainingId");
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_assigns_training()
    {
        $training = factory(Training::class)->create();
        $createdTrainingId = $training->id;

        //$response = $this->get('TrainingController@assignForm', $createdTrainingId);
        $response = $this->get('training/'.$createdTrainingId.'/assign');

        $response->assertStatus(200);        
        $response->assertViewHas('training');
        $response->assertViewHas('users');
        $response->assertViewHas('groups');
        $response->assertSee('Assign users to');
        $response->assertSee('Choose Group');
        $response->assertSee('Choose User');
        $response->assertSee('Due date');
        $response->assertSee('Completed date');

        // Logged in as a regular user - Cannot assign the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        //$response = $this->get('TrainingController@assignForm', $createdTrainingId);
        $response = $this->get('training/'.$createdTrainingId.'/assign');
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot assign the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('training/'.$createdTrainingId.'/assign');
        $response->assertStatus(403);
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

        $response = $this->post("/training/$createdTrainingId/assign/", $data);
        $response->assertRedirect("/training/$createdTrainingId");

        // Retrieve the training note created by this user
        $userNote = Note::where('user_id', $this->user->id)->get();

        // Ensure that the note is created - tests the private method createTrainingNote
        $this->assertNotNull($userNote);

        // Logged in as a regular user - Cannot assign users to training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->post("/training/$createdTrainingId/assign/", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot assign users to training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->post("/training/$createdTrainingId/assign/", $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_sends_reminders()
    {
        Event::fake();
        Notification::fake();
        
        $training = factory(Training::class)->create(['renews_in' => 365]);
        $createdTrainingId = $training->id;
        
        $trainingUser = factory(TrainingUser::class)->create(
                     ['training_id'    => $createdTrainingId,
                     'user_id'        => $this->user->id,
                     'due_date'       => Carbon::today()->subday(365)->format('Y-m-d'),
                     'author_id'      => $this->user->id,
                     'completed_date' => Carbon::today()->subday(336)->format('Y-m-d'),]);
        $trainingUserId = $trainingUser->id;
        
        $response = $this->actingAs($this->user)->get('training/reminder/'.$trainingUserId);
        $response->assertStatus(302);
        
        Event::shouldReceive('fire')->with(new TrainingAssigned($trainingUser));        
        Notification::shouldReceive('Reminder sent to');
    }
}
