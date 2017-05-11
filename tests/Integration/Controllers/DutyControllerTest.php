<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Duty;
use SET\User;

class DutyControllerTest extends TestCase
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
        // Logged in as admin - Can access the duty page
        $this->action('GET', 'DutyController@index');

        $this->assertEquals('duty', Route::getCurrentRoute()->getPath());
        $this->assertViewHas('duties');

        // Logged in as a regular user - Can still access the duty page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/duty');

        $this->seePageIs('duty');
        $this->assertViewHas('duties');
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the duty create page
        $this->call('GET', 'duty/create');

        $this->seePageIs('duty/create');
        $this->assertViewHas('users');
        $this->assertViewHas('groups');

        // Logged in as a regular user - Cannot access the duty create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/duty/create');

        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot access the duty create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', '/duty/create');

        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_stores_the_duty_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the duty
        $data = ['name'        => 'Some Duty',
                 'cycle'       => 'weekly',
                 'description' => 'A Description',
                 'users'       => [factory(User::class)->create()->id],
                 'has_groups'  => 0, ];

        $this->call('POST', 'duty', $data);
        $this->assertRedirectedToRoute('duty.index');

        // Logged in as a regular user - Does not store the duty
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('POST', 'duty', $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Does not store the duty
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('POST', 'duty', $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_duty_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = [];

        $this->call('POST', 'duty', $data);

        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['name', 'users']);
        $this->assertSessionHasErrors('name', 'The name field is required.');
        $this->assertSessionHasErrors('users', 'You must have at least one user.');

        // Logged in as admin - Only description is entered.
        $data = ['has_groups' => 1];
        $this->call('POST', 'duty', $data);

        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['name', 'groups']);
        $this->assertSessionHasErrors('name', 'The name field is required.');
        $this->assertSessionHasErrors('groups', 'You must have at least one group.');
    }

    /**
     * @test
     */
    public function it_shows_the_duty()
    {
        // Create a duty object
        $createdDuty = factory(Duty::class)->create([]);
        $createdDutyId = $createdDuty->id;

        // Logged in as admin - Can see the duty details
        $this->call('GET', "duty/$createdDutyId");
        $this->seePageIs('/duty/'.$createdDutyId);
        $this->assertViewHas('duty');
        $this->assertViewHas('list');
    }

    /**
     * @test
     */
    public function can_edit_duty()
    {
        // Create a duty object
        $createdDuty = factory(Duty::class)->create();
        $createdDutyId = $createdDuty->id;

        // Logged in as admin - Can edit the duty details
        $this->call('GET', "duty/$createdDutyId/edit");

        $this->seePageIs('/duty/'.$createdDutyId.'/edit');
        $this->assertViewHas('duty');
        $this->assertViewHas('users');
        $this->assertViewHas('groups');

        // Logged in as a regular user - Cannot edit the duty details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', "duty/$createdDutyId/edit");
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_updates_duty()
    {
        // Create a duty object
        $dutyToCreate = factory(Duty::class)->create([]);
        $createdDutyId = $dutyToCreate->id;

        // Logged in as admin - Can update the duty
        $data = ['name'        => 'Updated Duty',
                 'cycle'       => 'weekly',
                 'description' => 'Updated Description',
                 'users'       => [factory(User::class)->create()->id],
                 'has_groups'  => $dutyToCreate->has_groups, ];

        $this->call('PATCH', "duty/$createdDutyId", $data);

        $this->assertRedirectedToRoute('duty.index');

        $createdDuty = Duty::find($dutyToCreate->id);
        $this->assertNotEquals($createdDuty->name, $dutyToCreate->name);
        $this->assertEquals($createdDuty->name, $data['name']);
        $this->assertEquals($createdDuty->description, $data['description']);
        $this->assertEquals($createdDuty->cycle, $data['cycle']);
        $this->assertEquals($createdDuty->has_groups, $dutyToCreate->has_groups);

        // Logged in as admin - Ensure that you see error messages when required data is not provided.
        $data = ['name'        => '',
                 'cycle'       => 'weekly',
                 'description' => 'Updated Description',
                 'users'       => [factory(User::class)->create()->id],
                 'has_groups'  => $dutyToCreate->has_groups, ];

        $this->call('PATCH', "duty/$createdDutyId", $data);
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['name']);
        $this->assertSessionHasErrors('name', 'The name field is required.');

        // Logged in as a regular user - Cannot update the duty
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('PATCH', "duty/$createdDutyId", $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot update the duty
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('PATCH', "duty/$createdDutyId", $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_deletes_the_duty()
    {
        // Create a duty object
        $dutyToCreate = factory(Duty::class)->create([]);
        $createdDutyId = $dutyToCreate->id;

        // Ensure the created duty is in the database
        $createdDuty = Duty::find($dutyToCreate->id);
        $this->assertNotNull($createdDuty);
        $this->assertEquals($createdDuty->id, $createdDutyId);

        // Delete the created duty. Assert that a null object is returned.
        $this->call('DELETE', "duty/$createdDutyId");
        $deletedDuty = Duty::find($createdDutyId);
        $this->assertNull($deletedDuty);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete the duty since the duty with
        // the provided Id has already been deleted
        $this->call('DELETE', "duty/$createdDutyId");
        $this->seeStatusCode(404);

        // Create a new duty and try to delete. Get forbidden status code
        $dutyToCreate = factory(Duty::class)->create([]);
        $createdDutyId = $dutyToCreate->id;
        $this->call('DELETE', "duty/$createdDutyId");
        $this->seeStatusCode(403);
    }
}
