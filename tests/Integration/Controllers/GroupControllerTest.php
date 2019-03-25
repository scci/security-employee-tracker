<?php

namespace Tests\Integration\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Group;
use SET\Http\Controllers\GroupController;
use SET\Training;
use SET\User;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
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
        // Logged in as admin - Can access the groups page
        $response = $this->get('group');

        $response->assertStatus(200);
        $response->assertViewHas('groups');

        // Logged in as a user with role view - Can still access the groups page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('group');

        $response->assertStatus(200);
        $response->assertViewHas('groups');

        // Logged in as a regular user - Cannot access the groups page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('group');

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the group create page
        $response = $this->get('group/create');

        $response->assertStatus(200);
        $response->assertViewHas('users');
        $response->assertViewHas('training');
        $response->assertViewHas('selectedUsers');
        $response->assertViewHas('selectedTraining');

        // Logged in as a regular user - Cannot access the group create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/group/create');

        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the group create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('/group/create');

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_stores_the_group_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the group
        $request = ['name'     => 'A Test Group',
                 'closed_area' => '0',
                 'users'       => [factory(User::class)->create()->id, factory(User::class)->create()->id],
                 'trainings'   => [factory(Training::class)->create()->id], ];

        $response = $this->post('group', $request);
        $response->assertStatus(302);
        $response->assertRedirect('group');

        // Logged in as a regular user - Does not store the group
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->post('group', $request);
        $response->assertStatus(403);

        // Logged in as a user with role view - Does not store the group
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->post('group', $request);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_group_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $request = [];

        $response = $this->post('group', $request);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasErrors('name', 'The name field is required.');

        // Logged in as admin - Only description is entered.
        $request = ['users' => factory(User::class)->create()->id];
        $response = $this->post('group', $request);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasErrors('name', 'The name field is required.');
    }

    /**
     * @test
     */
    public function can_edit_group()
    {
        // Create a group object
        $createdGroup = factory(Group::class)->create();
        $createdGroupId = $createdGroup->id;

        // Logged in as admin - Can edit the group details
        $response = $this->get("group/$createdGroupId/edit");

        $response->assertStatus(200);
        $response->assertViewHas('group');
        $response->assertViewHas('users');
        $response->assertViewHas('training');
        $response->assertViewHas('selectedUsers');
        $response->assertViewHas('selectedTraining');

        // Logged in as a regular user - Cannot edit the group details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get("group/$createdGroupId/edit");
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the group create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get("group/$createdGroupId/edit");

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_updates_group()
    {
        // Create a group object
        $groupToCreate = factory(Group::class)->create();
        $createdGroupId = $groupToCreate->id;

        // Logged in as admin - Can update the group
        $request = ['name'     => 'Updated Group',
                 'closed_area' => '1',
                 'users'       => [factory(User::class)->create()->id],
                 'trainings'   => [factory(Training::class)->create()->id], ];

        $response = $this->patch("group/$createdGroupId", $request);

        $response->assertRedirect('group');

        $createdGroup = Group::find($groupToCreate->id);

        $this->assertNotEquals($createdGroup->name, $groupToCreate->name);
        $this->assertEquals($createdGroup->name, $request['name']);
        $this->assertEquals($createdGroup->closed_area, $request['closed_area']);
        $this->assertEquals([$createdGroup->users()->first()->id], $request['users']);
        $this->assertEquals([$createdGroup->trainings()->first()->id], $request['trainings']);

        // Logged in as admin - Ensure that you see error messages when required data is not provided.
        $request = ['name'     => '',
                 'closed_area' => '1',
                 'users'       => [factory(User::class)->create()->id],
                 'trainings'   => [factory(Training::class)->create()->id], ];

        $response = $this->patch("group/$createdGroupId", $request);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasErrors('name', 'The name field is required.');

        // Logged in as a regular user - Cannot update the group
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->patch("group/$createdGroupId", $request);
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot update the group
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->patch("group/$createdGroupId", $request);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_gets_user_ids_from_group()
    {
        $group = factory(Group::class)->create();
        $users = factory(User::class, 3)->create();
        $group->users()->attach($users);

        $request = ['groups' => $group];

        /*$usersInGroup = $this->json('POST', '/group-user-id', $request)
                ->seeJson($users->pluck('id')->toArray());*/

        // The ids in the response array are not in any order. So, ensure that
        // the first userid is present in the array
        $response = $this->post('/group-user-id', $request);

        $this->assertEquals(count($users->pluck('id')), count($response->getOriginalContent()));
        $this->assertContains($users->first()->id, $response->getOriginalContent());
    }

    /**
     * @test
     */
    public function it_deletes_the_group()
    {
        // Create a group object
        $groupToCreate = factory(Group::class)->create();
        $createdGroupId = $groupToCreate->id;

        // Ensure the created group is in the database
        $createdGroup = Group::find($createdGroupId);
        $this->assertNotNull($createdGroup);
        $this->assertEquals($createdGroup->id, $createdGroupId);

        // Delete the created group. Assert that a null object is returned.
        $response = $this->delete("group/$createdGroupId");
        $deletedGroup = Group::find($createdGroupId);
        $this->assertNull($deletedGroup);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete group page since the group with
        // the provided Id has already been deleted
        $response = $this->delete("group/$createdGroupId");
        $response->assertStatus(403);

        // Create a new group(Only user with edit permission can create)
        factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $groupToCreate = factory(Group::class)->create();
        $createdGroupId = $groupToCreate->id;

        // Try to delete as a regular user. Get forbidden status code
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->delete("group/$createdGroupId");
        $response->assertStatus(403);

        // Try to delete as a user with view permissions. Get forbidden status code
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);

        $response = $this->delete("group/$createdGroupId");
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_assigns_training()
    {
        $group = factory(Group::class)->create();
        $groupController = new GroupController();
        $training = factory(Training::class)->create();
        $group->trainings()->attach($training);

        // No users in the group - Hence no training assigned to group
        $users = null;
        $groupController->assignTraining($group, $users);
        $this->assertEmpty($training->users()->get());

        // Users attached to group
        $users = factory(User::class, 3)->create();
        $group->users()->attach($users);

        // Ensure that there are no trainings assigned before calling assignTraining
        $this->assertEmpty($training->users()->get());

        // Ensure that training is assigned after calling assignTraining
        $groupController->assignTraining($group, $users->pluck('id')->toArray());
        $this->assertNotEmpty($training->users()->get());

        foreach ($users as $user) {
            $this->assertEquals($training->id, $user->trainings()->first()->id);
        }
    }
}
