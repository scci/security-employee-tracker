<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Setting;
use SET\User;

class SettingControllerTest extends TestCase
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
        // Logged in as admin - Can access the settings page
        $this->action('GET', 'SettingController@index');

        $this->seePageIs('settings');
        $this->assertViewHas('report');
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

    /**
     * @test
     */
    public function it_updates_the_setting()
    {
        // Currently there is only one row for setting. Get the first setting.
        $currentSetting = Setting::get()->first();

        // Logged in as admin - Can update the news
        $data = ['report_address-primary'      => 'FSO-Primary',
                 'report_address-secondary'    => 'FSO-Secondary',
                 'admin'                       => $currentSetting->admin,
                 'viewer'                      => $currentSetting->viewer, ];

        $this->call('PATCH', 'settings/none', $data);

        $this->assertRedirectedToRoute('settings.index');

        $updatedSetting = Setting::get()->first();
        $this->assertNotEquals($currentSetting->primary, $updatedSetting->primary);
        $this->assertEquals($updatedSetting->primary, $data['report_address-primary']);
        $this->assertEquals($updatedSetting->secondary, $data['report_address-secondary']);

        // Logged in as a regular user - Cannot update settings
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('PATCH', 'settings/none', $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot update settings
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('PATCH', 'settings/none', $data);
        $this->seeStatusCode(403);
    }
}
