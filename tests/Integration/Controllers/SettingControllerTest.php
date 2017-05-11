<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\User;

class SettingControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->signIn();
    }

    /** @test */
    public function it_loads()
    {
        return true;
    }

    /**
     * @test
     */
    public function it_shows_the_index_page()
    {
        // Logged in as admin - Can access the settings page
        $this->action('GET', 'SettingController@index');

        $this->assertEquals('settings', Route::getCurrentRoute()->getPath());
        $this->assertViewHas('userList');
        $this->assertViewHas('admins');
        $this->assertViewHas('configAdmins');
        $this->assertViewHas('viewers');

        // Logged in as a regular user - Cannot access the settings page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/settings');

        $this->seeStatusCode(403);
    }
}
