<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use SET\Training;
use SET\TrainingType;
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

        $this->call('GET', 'training');
        $this->seeStatusCode(200);

        // Logged in as a regular user - Cannot access the training page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/training');

        $this->seeStatusCode(403);

        // Logged in as a user with role view - Can access the training page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', '/training');

        $this->seeStatusCode(200); // OK status code
        $this->seePageIs('training');
        $this->assertViewHas('trainings');
        $this->assertViewHas('isTrainingType');
        $this->assertViewHas('hasTrainingType');

        // Verify trainingtype page components do not appear (tests views\layouts\_navbar.blade.php)
        $this->see('/training">Trainings</a>'); // Navbar item
        $this->dontSee('<th>Type</th>'); // Table column
        $this->dontSee('data-tooltip="Manage Training Types"'); // Training Type button (tests views\training\index.blade.php)
    }

    /**
     * @test Passing of the Training Type to the Training index
     * web.php Route::get('/training/trainingtype/{trainingTypeID}', ['uses' => 'TrainingController@index']);
     */
    public function it_shows_the_index_page_for_specific_trainingtype()
    {
        // MIMIC call when there are no training types
        // Logged in as admin - Can access the training page
        $response = $this->call('GET', 'training/');
        $this->seeStatusCode(200); // OK status code
        $this->seePageIs('training/'); // Route '/training/trainingtype/{trainingTypeID}'
        //  Verify page components when no Training Types (views\layouts\_navbar.blade.php)
        $this->see('/training">Trainings</a>'); // Navbar item
        $this->dontSee('<th>Type</th>'); // Table column
        $this->see('data-tooltip="Manage Training Types"'); // Training Type button (tests views\training\index.blade.php)

        // MIMIC call when there are training types
        // Create a trainingtype object
        $createdTrainingType = factory(TrainingType::class)->create([]);
        $createdTrainingTypeId = $createdTrainingType->id;

        // Create a training object
        $createdTraining = factory(Training::class)->create([]);
        $createdTrainingId = $createdTraining->id;

        // Associating trainingtype to a Training
        $createdTraining->trainingType()->associate($createdTrainingType);
        $createdTraining->save();
        // Ensure trainingtype is associated with training
        $this->assertEquals($createdTrainingType->id, $createdTraining->training_type_id);

        // Logged in as admin - Can access the training page
        $response = $this->call('GET', "training/trainingtype/$createdTrainingTypeId");

        $this->seeStatusCode(200); // OK status code
        $this->seePageIs('training/trainingtype/'.$createdTrainingTypeId); // Route '/training/trainingtype/{trainingTypeID}'
        $this->assertViewHas('trainings');
        $this->assertViewHas('isTrainingType');
        $this->assertViewHas('hasTrainingType');

        // Verify page components
        $this->see('<title>SET - Training Directory</title>'); // Page Title
        $this->see('Training, Credentials and Briefings</span>'); // Block Title
        $this->see('<th>Name</th>'); // Table header
        $this->see('<th>Type</th>'); // Table header
        $this->see('<th>Incomplete</th>'); // Table header
        $this->see('<th>Completed</th>'); // Table header

        // Verify page components when there are Training Types (views\layouts\_navbar.blade.php)
        $this->see('data-activates="training-lists2">Trainings<'); // Navbar item
        $this->see('/training">All</a>'); // Navbar menu item link
        $this->see($createdTrainingType->name.'</a>'); // Navbar menu item link (app\Providers\ComposerServiceProvider.php)
        $this->see('<th>Type</th>'); // Table column
        // (tests views\training\index.blade.php)
        $this->dontSee('data-tooltip="Manage Training Types"'); // Training Type button

        // Verify page components when there are no active Training Types
        $createdTrainingType->status = 0;
        $createdTrainingType->save();
        // Logged in as admin - Can access the training page
        $response = $this->call('GET', "training/trainingtype/$createdTrainingTypeId");
        $this->seeStatusCode(200); // OK status code
        $this->seePageIs('training/trainingtype/'.$createdTrainingTypeId);
        // Verify page components for inactive training types (app\Providers\ComposerServiceProvider.php)
        $this->see('/training">Trainings</a>'); // Navbar item displays if no active training types
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

        //  Verify page components
        $this->see('Auto Renew'); // Block title
        $this->see('Attachments'); // Block title
        $this->see('Description'); // Block title
        $this->see('Administrative Files'); // Block title
        $this->see('Assign training to users');
        $this->see('Bulk Update Training');
        // When there are no training types (views\layouts\_new_training.blade.php)
        $this->dontSee('Training Type'); // Block title

        // MIMIC call when there are training types
        // Create trainingtype object
        $createdTrainingType = factory(TrainingType::class)->create([]);
        // Associating trainingtype to a Training
        $createdTraining->trainingType()->associate($createdTrainingType);
        $createdTraining->save();
        // Ensure trainingtype is associated with training
        $this->assertEquals($createdTrainingType->id, $createdTraining->training_type_id);

        // Logged in as admin - Can access the training page
        $this->call('GET', "training/$createdTrainingId");
        $this->seePageIs('/training/'.$createdTrainingId);
        $this->seeStatusCode(200); // OK status code
        // Verify page components - When there are no training types (views\layouts\_new_training.blade.php)
        $this->see('Training Type'); // Block title
        $this->see('Assign training to users');
        $this->see('Bulk Update Training');
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

        $this->call('GET', "training/$createdTrainingId/assign");
        $this->seeStatusCode(200); // OK status code
        $this->seePageIs("training/$createdTrainingId/assign");
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
    public function it_bulk_updates_a_training()
    {
        $training = factory(Training::class)->create();
        $createdTrainingId = $training->id;

        // Create multiple training users for the same training with completed_date null.
        $trainingUsers = factory(TrainingUser::class, 5)->create(
                            ['training_id'    => $createdTrainingId,
                             'completed_date' => null, ]);

        Storage::fake('local');
        $data = ['users' => [
                      0 => $trainingUsers[0]->user_id,
                      1 => $trainingUsers[2]->user_id,
                      2 => $trainingUsers[3]->user_id,
                    ],
                 'training_id'      => $createdTrainingId,
                 'completed_date'   => '2016-12-29',
                 'comment'          => 'Completed training offered by company',
                 'encrypt'          => '1',
                 'admin_only'       => '1',
                 'files'            => [
                      0 => UploadedFile::fake()->create('document.pdf', 26112),
                    ],
                ];
        $this->call('POST', "/training/$createdTrainingId/bulkupdate/", $data);
        $this->seeStatusCode(302); // Redirection status code
        $this->assertRedirectedTo("/training/$createdTrainingId");

        // Tried to test notification message. But none of the following worked.
        //It may be easier to test notifications if we moved to using
        //https://laravel.com/docs/5.6/notifications instead of Krucas\Notification\Facades\Notification
        /*$this->see("Training was updated for the users.");
        $notifications= \Krucas\Notification\Facades\Notification::container()->all();
        $notifications = \Krucas\Notification\Facades\Notification($this->getSessionStore(), 'notifications');
        $notifications= \Krucas\Notification\Facades\Notification::container()->get('success')->first();
        Log::Info($notifications);*/

        // Retrieve the training user just updated
        $updatedTrainingUser = SET\TrainingUser::where('training_id', $createdTrainingId)->get();
        $this->assertEquals($updatedTrainingUser[0]->completed_date, $data['completed_date']);
        $this->assertEquals($updatedTrainingUser[0]->comment, $data['comment']);

        $this->assertEquals($updatedTrainingUser[2]->completed_date, $data['completed_date']);
        $this->assertEquals($updatedTrainingUser[2]->comment, $data['comment']);

        $this->assertEquals($updatedTrainingUser[3]->completed_date, $data['completed_date']);
        $this->assertEquals($updatedTrainingUser[3]->comment, $data['comment']);

        $this->assertEquals($updatedTrainingUser[1]->completed_date, null);
        $this->assertEquals($updatedTrainingUser[4]->completed_date, null);
        $this->assertNotEquals($updatedTrainingUser[1]->completed_date, $data['completed_date']);
        $this->assertNotEquals($updatedTrainingUser[1]->comment, $data['comment']);
        $this->assertNotEquals($updatedTrainingUser[4]->completed_date, $data['completed_date']);
        $this->assertNotEquals($updatedTrainingUser[4]->comment, $data['comment']);

        // Retrieve the uploaded file for the created training
        // Assert the file was stored...
        $fileURL = Storage::url('local');
        $this->assertEquals($fileURL.'/'.$data['files'][0]->name, '/storage/local/document.pdf');
    }

    /**
     * @test
     */
    public function it_does_not_bulk_update_if_no_users_or_completed_date()
    {
        $training = factory(Training::class)->create();
        $createdTrainingId = $training->id;

        // Error when no users are specified
        $data1 = ['training_id'     => $createdTrainingId,
                 'comment'          => 'Completed training offered by company',
                ];

        $this->call('POST', "/training/$createdTrainingId/bulkupdate/", $data1);
        $this->assertSessionHasErrors('users', 'The users field is required');
        $this->assertSessionHasErrors('completed_date', 'The completed_date field is required');
    }

    /**
     * @test
     */
    public function it_sends_reminders()
    {
        $trainingUser = factory(TrainingUser::class)->create();
        $trainingUserId = $trainingUser->id;

        $response = $this->call('GET', "training/reminder/$trainingUserId");
        $this->expectsEvents(SET\Events\TrainingAssigned::class);
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $this->seeStatusCode(302); // Redirection status code
        $this->assertRedirectedTo('/'); // Only check that you're redirecting to a specific URI
    }
}
