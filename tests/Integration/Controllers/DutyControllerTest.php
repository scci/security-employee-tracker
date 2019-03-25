<?php

namespace Tests\Integration\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SET\Duty;
use SET\User;
use Tests\TestCase;

class DutyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
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
        $response = $this->get('/duty');
        $response->assertStatus(200);
        $response->assertViewHas('duties');

        // Logged in as a regular user - Can still access the duty page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/duty');

        $response->assertSee('duty');
        $response->assertViewHas('duties');
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the duty create page
        $response = $this->get('duty/create');

        $response->assertStatus(200);
        $response->assertViewHas('users');
        $response->assertViewHas('groups');

        // Logged in as a regular user - Cannot access the duty create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/duty/create');
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the duty create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('/duty/create');
        $response->assertStatus(403);
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

        $response = $this->post('/duty/', $data);

        $response->assertRedirect('duty');

        // Logged in as a regular user - Does not store the duty
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->post('duty', $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Does not store the duty
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->post('duty', $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_duty_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = [];

        $response = $this->post('duty', $data);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['name', 'users']);
        $response->assertSessionHasErrors('name', 'The name field is required.');
        $response->assertSessionHasErrors('users', 'You must have at least one user.');

        // Logged in as admin - Only description is entered.
        $data = ['has_groups' => 1];
        $response = $this->post('duty', $data);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['name', 'groups']);
        $response->assertSessionHasErrors('name', 'The name field is required.');
        $response->assertSessionHasErrors('groups', 'You must have at least one group.');
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
        $response = $this->get("duty/$createdDutyId");
        $response->assertStatus(200);
        $response->assertSee('/duty/'.$createdDutyId);
        $response->assertViewHas('duty');
        $response->assertViewHas('list');
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
        $response = $this->get("duty/$createdDutyId/edit");
        $response->assertStatus(200);
        $response->assertViewHas('duty');
        $response->assertViewHas('users');
        $response->assertViewHas('groups');

        // Logged in as a regular user - Cannot edit the duty details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get("duty/$createdDutyId/edit");
        $response->assertStatus(403);
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

        $response = $this->patch("duty/$createdDutyId", $data);

        $response->assertRedirect('duty');

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

        $response = $this->patch("duty/$createdDutyId", $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasErrors('name', 'The name field is required.');

        // Logged in as a regular user - Cannot update the duty
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->patch("duty/$createdDutyId", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot update the duty
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->patch("duty/$createdDutyId", $data);
        $response->assertStatus(403);
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
        $response = $this->delete("duty/$createdDutyId");
        $deletedDuty = Duty::find($createdDutyId);
        $this->assertNull($deletedDuty);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete the duty since the duty with
        // the provided Id has already been deleted
        $response = $this->delete("duty/$createdDutyId");
        $response->assertStatus(404);

        // Create a new duty and try to delete. Get forbidden status code
        $dutyToCreate = factory(Duty::class)->create([]);
        $createdDutyId = $dutyToCreate->id;
        $response = $this->delete("duty/$createdDutyId");
        $response->assertStatus(403);
    }
}
