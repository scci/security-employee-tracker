<?php

namespace Tests\Integration\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\User;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use DatabaseTransactions;

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
        // Logged in as admin - Can access the settings page
        $response = $this->get('settings');

        $response->assertStatus(200);
        $response->assertViewHas('userList');
        $response->assertViewHas('admins');
        $response->assertViewHas('configAdmins');
        $response->assertViewHas('viewers');

        // Logged in as a regular user - Cannot access the settings page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/settings');

        $response->assertStatus(403);
    }
}
