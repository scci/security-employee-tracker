<?php

namespace Tests\Integration\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Duty;
use SET\User;
use Tests\TestCase;

class DutySwapControllerTest extends TestCase
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
    public function it_stores_the_dutyswap_by_testing_each_user_role()
    {
        // Create a duty object
        $createdDuty = factory(Duty::class)->create([]);
        $createdDutyId = $createdDuty->id;

        // Logged in as admin - Can store the dutyswap
        $data = ['id'   => '65,32',
                 'date' => '2016-01-23, 2016-02-12',
                 'duty' => $createdDutyId,
                 'type' => 'User', ];
        $response = $this->post('duty-swap', $data);
        $response->assertRedirect('/duty/'.$data['duty']);

        // Logged in as a regular user - Does not store the dutyswap
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->post('duty-swap', $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Does not store the dutyswap
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->post('duty-swap', $data);
        $response->assertStatus(403);
    }
}
