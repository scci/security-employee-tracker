<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Http\Controllers\UserController;
use SET\User;

/**
 * Class UserControllerTest.
 */
class UserControllerTest extends TestCase
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
        // Logged in as admin - Can access the user page
        $this->action('GET', 'UserController@index');

        $this->seePageIs('user');
        $this->assertViewHas('users');

        // Logged in as a regular user - Cannot access the user page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        $this->call('GET', '/user');
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the visit create page
        $this->call('GET', '/user/create');
        $this->seePageIs('/user/create');
        $this->assertViewHas('supervisors');
        $this->assertViewHas('groups');

        // Create a regular user - Cannot access the visit create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/user/create');
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot access the visit create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', '/user/create');
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_stores_the_user_by_testing_each_user_role()
    {
        $data = ['first_name'       => 'John',
                 'nickname'         => 'Johnny',
                 'last_name'        => 'Smith',
                 'email'            => 'jsmith@test.com',
                 'username'         => 'jsmith',
                 'phone'            => '1234567890',
                 'emp_num'          => '321',
                 'supervisor_id'    => '21',
                 'access_level'     => 'secret',
                 'clearance'        => 'interim',
                 'elig_date'        => '2016-12-20',
                 'inv'              => 'investigation',
                 'inv_close'        => '2016-12-29',
                 'status'           => 'active', ];

        $this->call('POST', 'user', $data);
        $this->assertRedirectedToRoute('user.index');

        // Retrieve the created  user and ensure that the user is created
        $createdUser = SET\User::where('email', $data['email'])->get();
        $this->assertNotNull($createdUser);

        // Logged in as a regular user - Cannot store the user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('POST', 'user', $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot store the user
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('POST', 'user', $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_user_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = [];

        $this->call('POST', 'user', $data);
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['first_name', 'last_name', 'email']);
        $this->assertSessionHasErrors('first_name', 'The first_name field is required.');
        $this->assertSessionHasErrors('last_name', 'The last_name field is required.');
        $this->assertSessionHasErrors('email', 'The email field is required.');

        $data = ['first_name'   => 'Jane',
                 'last_name'    => 'Doe', ];

        $this->call('POST', 'user', $data);
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['email']);
        $this->assertSessionHasErrors('email', 'The email field is required.');
    }

    /**
     * @test
     */
    public function it_shows_the_user()
    {
        // Logged in as admin - Can see the admin's user page
        $userId = $this->user->id;
        $this->call('GET', "user/$userId");
        $this->seePageIs('/user/'.$userId);
        $this->assertViewHas('user');
        $this->assertViewHas('duties');
        $this->assertViewHas('previous');
        $this->assertViewHas('next');
        $this->assertViewHas('trainings');
        $this->assertViewHas('activityLog');
        $this->assertViewHas('training_blocks');
        $this->assertViewHas('training_user_types');

        // Create a user object
        $createdUser = factory(User::class)->create([]);
        $createdUserId = $createdUser->id;

        // Logged in as admin - Can see the user page for the created user
        $this->call('GET', "user/$createdUserId");
        $this->seePageIs('/user/'.$createdUserId);
        $this->assertViewHas('user');
        $this->assertViewHas('duties');
        $this->assertViewHas('previous');
        $this->assertViewHas('next');
        $this->assertViewHas('trainings');
        $this->assertViewHas('activityLog');

        // Logged in as the created user - Can see the created user's page
        $this->actingAs($createdUser);
        $this->call('GET', "user/$createdUserId");
        $this->seePageIs('/user/'.$createdUserId);
        $this->assertViewHas('user');
        $this->assertViewHas('duties');
        $this->assertViewHas('previous');
        $this->assertViewHas('next');
        $this->assertViewHas('trainings');
        $this->assertViewHas('activityLog');

        // Create another user object
        $newUser = factory(User::class)->create([]);

        // Logged in as the newuser - Cannot see the previously created user's page
        $this->actingAs($newUser);
        $this->call('GET', "user/$createdUserId");
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Can see the previously created user's page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', "user/$createdUserId");
        $this->seePageIs('/user/'.$createdUserId);
        $this->assertViewHas('user');
        $this->assertViewHas('duties');
        $this->assertViewHas('previous');
        $this->assertViewHas('next');
        $this->assertViewHas('trainings');
        $this->assertViewHas('activityLog');
    }

    /**
     * @test
     */
    public function it_shows_the_user_trainings_by_blocktype()
    {
        // Create trainingtype objects
        $createdTrainingTypes = factory(SET\TrainingType::class, 5)->create();

        // Create a non-admin user object
        $createdUser = factory(User::class)->create([]);
        $createdUserId = $createdUser->id;

        // Logged in as the created user
        $this->actingAs($createdUser);

        // Access the user page - No trainings have been assigned to user yet
        $this->call('GET', "user/$createdUserId");
        $this->seeStatusCode(200); // OK status code

        // Verify page components (views\user\show.blade.php)
        $this->seePageIs('/user/'.$createdUserId)
             ->dontSee('Scheduled Training') // Block Title
             ->dontSee('Due Date: '.Carbon::tomorrow()->format('Y-m-d')) // Field
             ->dontSee('ADD TRAINING') // button
             ->dontSee('SHOW ALL') // button
             ->dontsee('Completed: '.Carbon::today()->format('Y-m-d'));

        // For each training type, create a training and add the current user to it.
        foreach ($createdTrainingTypes as $trainingType) {
            $createdTraining = factory(SET\Training::class)->create(['training_type_id' => $trainingType->id]);
            $trainingUser = factory(SET\TrainingUser::class)->create(
                    ['training_id'    => $createdTraining->id,
                     'user_id'        => $createdUserId,
                     'due_date'       => Carbon::tomorrow()->format('Y-m-d'),
                     'author_id'      => $this->user->id,
                     'completed_date' => null, ]);
        }

        $this->call('GET', "user/$createdUserId");
        $this->seeStatusCode(200); // OK status code

        // Verify page components (views\user\show.blade.php)
        $this->seePageIs('/user/'.$createdUserId)
             ->see('Scheduled Training') // Block Title
             ->see('Due Date: '.Carbon::tomorrow()->format('Y-m-d')) // Field
             ->dontSee('ADD TRAINING') // button
             ->dontSee('SHOW ALL') // button
             ->dontsee('Completed: '.Carbon::today()->format('Y-m-d'));

        // Ensure the training type blocks are not displayed since the completed date is set to null.
        // Only Scheduled Training block is displayed in this case
        foreach ($createdTrainingTypes as $createdTrainingType) {
            $this->dontSee($createdTrainingType->name.' Training');
        }

        // Ensure completed date is set for all trainings for all users
        foreach ($createdUser->assignedTrainings as $traininguser) {
            $this->see($traininguser->training->name);
            $traininguser->completed_date = Carbon::today()->format('Y-m-d');
            $createdUser->assignedTrainings()->save($traininguser);
        }

        // Reload the the page reflecting completed training
        $this->call('GET', "user/$createdUserId");
        $this->seeStatusCode(200); // OK status code

        $this->seePageIs('/user/'.$createdUserId)
             ->dontSee('Scheduled Training') // Block Title
             ->dontSee('ADD TRAINING') // button
             ->see('SHOW ALL') // Button
             ->see('Completed: '.Carbon::today()->format('Y-m-d')); // Field

        // Ensure the training type blocks are displayed since all trainings are marked completed
        foreach ($createdTrainingTypes as $createdTrainingType) {
            $this->see($createdTrainingType->name.' Training');
        }
    }

    /**
     * @test
     */
    public function it_shows_the_user_trainings_by_blocktype_for_admin()
    {
        // Create trainingtype objects
        $createdTrainingTypes = factory(SET\TrainingType::class, 5)->create();

        // For each training type, create a training and add the admin user to it.
        foreach ($createdTrainingTypes as $trainingType) {
            $createdTraining = factory(SET\Training::class)->create(['training_type_id' => $trainingType->id]);
            $trainingUser = factory(SET\TrainingUser::class)->create(
                    ['training_id'    => $createdTraining->id,
                     'user_id'        => $this->user->id,
                     'due_date'       => Carbon::tomorrow()->format('Y-m-d'),
                     'author_id'      => $this->user->id,
                     'completed_date' => null, ]);
        }

        // Logged in as admin - Check that every scheduled training is listed
        $this->actingAs($this->user);
        $userId = $this->user->id;
        $this->call('GET', "user/$userId");
        $this->seeStatusCode(200); // OK status code

        // Verify page components (views\user\show.blade.php)
        $this->seePageIs('/user/'.$userId)
             ->see('Scheduled Training') // Block Title
             ->see('Due Date: '.Carbon::tomorrow()->format('Y-m-d')) // Field
             ->see('ADD TRAINING') // button
             ->dontSee('SHOW ALL') // button
             ->dontsee('Completed: '.Carbon::today()->format('Y-m-d'));

        // Ensure the training type blocks are not displayed since the completed date is set to null.
        // Only Scheduled Training block is displayed in this case
        foreach ($createdTrainingTypes as $createdTrainingType) {
            $this->dontSee($createdTrainingType->name.' Training');
        }

        // Ensure all the training names are displayed and then set the completed date to today
        foreach ($this->user->assignedTrainings as $traininguser) {
            $this->see($traininguser->training->name);
            $traininguser->completed_date = Carbon::today()->format('Y-m-d');
            $this->user->assignedTrainings()->save($traininguser);
        }

        // Ensure completed date is set for all trainings for all users
        foreach ($this->user->assignedTrainings as $traininguser) {
            $this->assertEquals($traininguser->completed_date, Carbon::today()->format('Y-m-d'));
        }

        // Reload the the page reflecting completed training
        $this->call('GET', "user/$userId");
        $this->seeStatusCode(200); // OK status code

        // Verify page components (views\user\show.blade.php)
        $this->seePageIs('/user/'.$userId)
             ->dontSee('Scheduled Training') // Block Title
             ->dontSee('ADD TRAINING') // button
             ->see('SHOW ALL') // Button
             ->see('Completed: '.Carbon::today()->format('Y-m-d')); // Field

        // Ensure the training type blocks are displayed since all trainings are marked completed
        foreach ($createdTrainingTypes as $createdTrainingType) {
            $this->see($createdTrainingType->name.' Training');
        }

        // Add another set of trainings to the user with completed date set different past dates
        for ($i = 1; $i < 4; $i++) {
            $trainingUser = factory(SET\TrainingUser::class)->create(
                        ['training_id'    => $createdTraining->id,
                         'user_id'        => $this->user->id,
                         'due_date'       => Carbon::tomorrow()->format('Y-m-d'),
                         'author_id'      => $this->user->id,
                         'completed_date' => Carbon::today()->subYear($i)->format('Y-m-d'), ]);
        }

        // Reload the the page to see all the completed trainings for the specified training block
        $trainingBlock = $createdTraining->trainingType->name;
        $this->call('GET', "user/$userId/$trainingBlock/show");
        $this->seeStatusCode(200); // OK status code
        $this->seePageIs('/user/'.$userId.'/'.$trainingBlock.'/show')
             ->see('SHOW ALL') // Button
             ->see('Completed: '.Carbon::today()->format('Y-m-d'))
             ->see('Completed: '.Carbon::today()->subYear(1)->format('Y-m-d'))
             ->see('Completed: '.Carbon::today()->subYear(2)->format('Y-m-d'))
             ->see('Completed: '.Carbon::today()->subYear(3)->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function can_edit_user()
    {
        // Logged in as admin - Can edit the admin's user page
        $userId = $this->user->id;
        $this->call('GET', "user/$userId/edit");

        $this->seePageIs('/user/'.$userId.'/edit');
        $this->assertViewHas('user');
        $this->assertViewHas('supervisors');
        $this->assertViewHas('groups');

        // Create a user object
        $createdUser = factory(User::class)->create();
        $createdUserId = $createdUser->id;

        // Logged in as admin - Can edit the user details
        $this->call('GET', "user/$createdUserId/edit");

        $this->seePageIs('/user/'.$createdUserId.'/edit');
        $this->assertViewHas('user');
        $this->assertViewHas('supervisors');
        $this->assertViewHas('groups');

        // Logged in as a regular user - Cannot edit the user details
        $newuser = factory(User::class)->create();
        $newuserId = $newuser->id;
        $this->actingAs($newuser);
        $this->call('GET', "user/$newuserId/edit");
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot edit the user details
        $newuser = factory(User::class)->create(['role' => 'view']);
        $newuserId = $newuser->id;
        $this->actingAs($newuser);
        $this->call('GET', "user/$newuserId/edit");
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_updates_the_user()
    {
        // Create a user object
        $createdUser = factory(User::class)->create();
        $createdUserId = $createdUser->id;

        // Logged in as admin - Can update the user
        $data = ['first_name'       => 'John',
                 'last_name'        => 'Doe',
                 'nickname'         => 'Johnny',
                 'email'            => $createdUser->email,
                 'username'         => $createdUser->username,
                 'phone'            => $createdUser->phone,
                 'emp_num'          => $createdUser->emp_num,
                 'supervisor_id'    => $createdUser->supervisor_id,
                 'access_level'     => $createdUser->access_level,
                 'clearance'        => $createdUser->clearance,
                 'elig_date'        => $createdUser->elig_date,
                 'inv'              => $createdUser->inv,
                 'inv_close'        => $createdUser->inv_close,
                 'status'           => $createdUser->status, ];

        $this->call('PATCH', "/user/$createdUserId", $data);

        $this->assertRedirectedToRoute('user.show', $createdUserId);

        $newlyCreatedUser = User::find($createdUser->id);
        $this->assertNotEquals($newlyCreatedUser->first_name, $createdUser->first_name);
        $this->assertNotEquals($newlyCreatedUser->last_name, $createdUser->last_name);
        $this->assertNotEquals($newlyCreatedUser->nickname, $createdUser->nickname);
        $this->assertEquals($newlyCreatedUser->first_name, $data['first_name']);
        $this->assertEquals($newlyCreatedUser->last_name, $data['last_name']);
        $this->assertEquals($newlyCreatedUser->nickname, $data['nickname']);

        // Logged in as a regular user - Cannot update the user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('PATCH', "/user/$createdUserId", $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot update the user
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('PATCH', "/user/$createdUserId", $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_deletes_the_user()
    {
        // Create a user object
        $userToCreate = factory(User::class)->create();
        $createdUserId = $userToCreate->id;

        // Ensure the created user is in the database
        $createdUser = User::find($createdUserId);
        $this->assertNotNull($createdUser);
        $this->assertEquals($createdUser->id, $createdUserId);

        // Delete the created user. Assert that a null object is returned.
        $this->call('DELETE', "user/$createdUserId");
        $deletedUser = User::find($createdUserId);
        $this->assertNull($deletedUser);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete user page since the user with
        // the provided Id has already been deleted
        $this->call('DELETE', "user/$createdUserId");
        $this->seeStatusCode(403);

        // Create a new user(Only user with edit permission can create)
        factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $userToCreate = factory(User::class)->create();
        $createdUserId = $userToCreate->id;

        // Try to delete as a regular user. Get forbidden status code
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('DELETE', "user/$createdUserId");
        $this->seeStatusCode(403);

        // Try to delete as a user with view permissions. Get forbidden status code
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);

        $this->call('DELETE', "user/$createdUserId");
        $this->seeStatusCode(403);
    }

    /** @test Call getUserTrainingTypes() without an argument */
    public function it_gets_the_users_trainingTypes_with_no_trainingUser()
    {
        //$createdUser = factory(SET\User::class)->create();

        $user_training_types = with(new UserController())->getUserTrainingTypes();
        $training_user_types = $user_training_types[0]; // List of the user's training types
      $training_blocks = $user_training_types[1]; // List of training block titles for user

      $this->assertTrue(is_array($training_user_types));
        $this->assertTrue(is_array($training_blocks));
        $this->assertTrue(empty($training_user_types));
        $this->assertTrue(empty($training_blocks));
    }

    /** @test Call getUserTrainingTypes() with an argument */
    public function it_gets_the_users_trainingTypes()
    {
        // Create users, trainings, and training types
        $createdUsers = factory(SET\User::class, 2)->create();
        $createdTrainingTypes = factory(SET\TrainingType::class, 5)->create([]);
        $createdTrainings = factory(SET\Training::class, 25)->create([]);
        $n = 0;
        // Create User Trainings (completed and incomplete) Trainings with types
        foreach ($createdUsers as $createdUser) {
            foreach ($createdTrainings as $createdTraining) {
                // Assign users to trainings (both incomplete and completed)
                if ($createdTraining->id % 2 == 0) {
                    $createdTraining->users()->attach($createdUser, ['due_date' => Carbon::tomorrow()->format('Y-m-d'),
                      'author_id'                                               => 1, 'completed_date'=>null, ]);
                } else {
                    $createdTraining->users()->attach($createdUser, ['due_date' => Carbon::tomorrow()->format('Y-m-d'),
                      'author_id'                                               => 1, 'completed_date'=>Carbon::yesterday()->format('Y-m-d'), ]);
                }
                // Associating trainingtype to 2/3 Trainings
                if ($createdTraining->id % 3 != 0) {
                    if (++$n > $createdTrainingTypes->count()) {
                        $n = 1;
                    }
                    // Associating trainingtype to a Training
                    $createdTraining->trainingType()->associate($createdTrainingTypes->where('id', $n)->first());
                    $createdTraining->save();
                }
            }
        }
        // Make method call and evaluate returned values
        foreach ($createdUsers as $createdUser) {
            $trainings = $createdUser->assignedTrainings()->get();
            $this->assertEquals($trainings->count(), $createdTrainings->count());
            // Make method call
            $user_training_types = with(new UserController())->getUserTrainingTypes($trainings);

            $this->assertEquals(gettype($user_training_types), 'array');
            $training_user_types = $user_training_types[0]; // List of the user's training types
            $training_blocks = $user_training_types[1]; // List of training block titles for user
            $this->assertTrue(is_array($training_user_types));
            $this->assertTrue(is_array($training_blocks));
            // Ensure each user training has has proper type
            $this->assertEquals(count($training_user_types), $createdTrainings->count());
            $this->assertEquals(count(array_unique($training_user_types)), $createdTrainingTypes->count() + 2);
            foreach ($createdTrainingTypes as $createdTrainingType) {
                $this->assertTrue(is_int(array_search($createdTrainingType->name, $training_user_types)));
            }
            $this->assertTrue(is_int(array_search('Scheduled', $training_user_types)));
            $this->assertTrue(is_int(array_search('Miscellaneous', $training_user_types)));
            // Ensure training block titles exists (Scheduled, XXXs, Miscellaneous)
            $this->assertEquals(count($training_blocks), $createdTrainingTypes->count() + 2);
            $this->assertEquals(array_search('Scheduled', $training_blocks), 'AAA', true);
            $this->assertEquals(array_search('Miscellaneous', $training_blocks), '999', true);
            $this->assertEquals(array_pop($training_blocks), 'Miscellaneous');  // last array element
            $this->assertEquals(array_shift($training_blocks), 'Scheduled');  // first array element
        }
    }

    /**
     * @test
     */
    public function it_gets_the_user_trainings()
    {
        // Create a user object
        $createdUser = factory(User::class)->create([]);
        $createdUserId = $createdUser->id;

        // Create trainingtype objects
        $createdTrainingTypes = factory(SET\TrainingType::class, 2)->create();

        // For each training type, create a training and add the created user to it.
        foreach ($createdTrainingTypes as $trainingType) {
            $createdTraining = factory(SET\Training::class)->create(['training_type_id' => $trainingType->id]);

            // Schedule created Training
            $trainingUser = factory(SET\TrainingUser::class)->create(
                    ['training_id'    => $createdTraining->id,
                     'user_id'        => $createdUserId,
                     'due_date'       => Carbon::tomorrow()->format('Y-m-d'),
                     'author_id'      => $createdUserId,
                     'completed_date' => null, ]);
            // Created Training completed today
            $trainingUser = factory(SET\TrainingUser::class)->create(
                    ['training_id'    => $createdTraining->id,
                     'user_id'        => $createdUserId,
                     'due_date'       => Carbon::tomorrow()->format('Y-m-d'),
                     'author_id'      => $createdUserId,
                     'completed_date' => Carbon::today()->format('Y-m-d'), ]);
            // Created Training completed a year ago
            $trainingUser = factory(SET\TrainingUser::class)->create(
                    ['training_id'    => $createdTraining->id,
                     'user_id'        => $createdUserId,
                     'due_date'       => Carbon::tomorrow()->format('Y-m-d'),
                     'author_id'      => $createdUserId,
                     'completed_date' => Carbon::today()->subYear(1)->format('Y-m-d'), ]);
        }

        // GetUserTrainings when training type is null
        $userController = new UserController();
        $trainings = $this->invokeMethod($userController, 'getUserTrainings', [$createdUser, null]);
        $this->assertNotNull($trainings);
        $this->assertCount(4, $trainings);

        // GetUserTrainings when training type is of the first created training type
        $trainings = $this->invokeMethod($userController, 'getUserTrainings', [$createdUser, $createdTrainingTypes->first()->name]);
        $this->assertNotNull($trainings);
        $this->assertCount(5, $trainings);
        Log::Info($trainings);

        // GetUserTrainings when training type is an invalid value
        $userController = new UserController();
        $trainings = $this->invokeMethod($userController, 'getUserTrainings', [$createdUser, 'sometype']);
        $this->assertNotNull($trainings);
        $this->assertCount(4, $trainings);
    }
}
