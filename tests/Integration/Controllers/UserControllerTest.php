<?php

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
        $this->assertViewHas('logs');

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
        $this->assertViewHas('logs');

        // Logged in as the created user - Can see the created user's page
        $this->actingAs($createdUser);
        $this->call('GET', "user/$createdUserId");
        $this->seePageIs('/user/'.$createdUserId);
        $this->assertViewHas('user');
        $this->assertViewHas('duties');
        $this->assertViewHas('previous');
        $this->assertViewHas('next');
        $this->assertViewHas('trainings');
        // $this->assertViewHas('logs');

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
        // $this->assertViewHas('logs');
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
}
