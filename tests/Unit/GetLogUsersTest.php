<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use SET\User;
use Tests\Testcase;

class GetLogUsersTest extends TestCase
{
    /**
     * The activityLog object stores changes, so the first entry will be the last array position
     * Note: This unit test executes all changes at the same machine time, so the cases
     *  must sort them before obtaining the record for comparison.
     */
    public function setUp()
    {
        parent::setUp();

        // Create sample specific user
        factory(User::class)->create([
          'username'       => 'ssample', 'emp_num' => '123',
          'first_name'     => 'Susan', 'nickname' => 'Susie', 'last_name' => 'Sample',
          'email'          => 'ssample@gmail.com', 'phone' => '555-123-4567',
          'status'         => 'active', 'clearance' => 'S',
          'elig_date'      => Carbon::create(2001, 1, 1, 0, 0, 0),
          'inv'            => 'ABCD', 'inv_close' => Carbon::create(2010, 10, 10, 0, 0, 0),
          'destroyed_date' => Carbon::create(2012, 12, 12, 0, 0, 0),
          'supervisor_id'  => '86', 'access_level' => 'S',
          'password'       => Hash::make('plain-text'),
        ]);
        // Create second specific user
        factory(User::class)->create([
          'username'       => 'ayahoo', 'emp_num' => '234',
          'first_name'     => 'Apple', 'nickname' => 'None', 'last_name' => 'Yahoo',
          'email'          => 'ayahooe@gmail.com', 'phone' => '555-123-4567',
          'status'         => 'active', 'clearance' => 'S',
          'elig_date'      => Carbon::create(2001, 1, 1, 0, 0, 0),
          'inv'            => 'LMNOP', 'inv_close' => Carbon::create(2010, 10, 10, 0, 0, 0),
          'destroyed_date' => Carbon::create(2012, 12, 12, 0, 0, 0),
          'supervisor_id'  => '86', 'access_level' => 'S',
          'password'       => Hash::make('plain-text'),
        ]);
    }

    /**
     * Test that the returned array includes expected keys.
     *
     * @return void
     */
    public function test_returns_expected_keys()
    {
        $user = User::where('username', 'ssample')->first();
        $changes = $user->getUserLog($user)->last();  // Sample one record

        // ensure the expected array keys are returned
        $this->assertArrayHasKey('comment', $changes);
        $this->assertArrayHasKey('user_fullname', $changes);
        $this->assertArrayHasKey('updated_at', $changes);
    }

    /**
     * Test that the first log by a specific user is a created action.
     *
     * @return void
     */
    public function test_first_log_entry_is_created()
    {
        usleep(1000000); // delay to allow update_at to differ
        $user1 = User::where('username', 'ssample')->first();
        $user1->inv = 'XYZ';
        $user1->save();

        $user1 = User::where('username', 'ssample')->first();

        $obj = new SET\User();
        $changes = $obj->getUserLog($user1)->last();  // Sample first record

        $this->assertStringStartsWith("created user 'Sample", $changes['comment']);
    }

    /**
     * Test that the last log by a specific user is an updated action.
     *
     * @return void
     */
    public function test_last_log_entry_is_updated()
    {
        usleep(1000000); // delay to allow update_at to differ
        $user1 = User::where('username', 'ssample')->first();

        $obj = new SET\User();
        $user1->inv = 'XYZ';
        $user1->save();
        $user1 = User::where('username', 'ssample')->first();

        $changes = $obj->getUserLog($user1)->first(); // Sample last entry for user

        // Test against ssample investigation changes
        $this->assertStringStartsWith('Sample, Susan', $changes['user_fullname']);
        $this->assertStringStartsWith("Inv updated from 'ABCD' to 'XYZ'.", $changes['comment']);
    }

    /**
     * Test calling method without passing user parameter.
     *
     * @return void
     */
    public function test_method_without_passing_parameter()
    {
        usleep(1000000); // delay to allow update_at to differ
        $user2 = User::where('username', 'ayahoo')->first();
        $user2->access_level = 'Unclass';
        $user2->save();

        $obj = new SET\User();
        $changes = $obj->getUserLog()->first(); // Sample last entry

        // Test against most recent eexample access level changes
        $this->assertStringStartsWith('Yahoo, Apple', $changes['user_fullname']);
        $this->assertStringStartsWith("Access_level updated from 'S' to 'Unclass'.",
        $changes['comment']);
    }

    /**
     * Test when a user is deleted.
     *
     * @return void
     */
    public function test_deleted_action()
    {
        usleep(1000000); // delay to allow update_at to differ
        User::where('username', 'ayahoo')->first()->delete();

        $obj = new SET\User();
        $changes = $obj->getUserLog()->first(); // Sample last entry

        // Test against user ayahoo deletion
        $this->assertStringStartsWith('Yahoo, Apple', $changes['user_fullname']);
        $this->assertStringStartsWith("deleted user 'Yahoo, Apple'.", $changes['comment']);
    }

    /**
     * Test log counts make sense.
     *
     * @return void
     */
    public function test_user_log_counts()
    {
        $user1 = User::where('username', 'ssample')->first();
        $user1->inv = 'XYZ';
        $user1->save();
        $user2 = User::where('username', 'ayahoo')->first();
        $user2->nickname = 'YoYo';
        $user2->save();

        $user1 = User::where('username', 'ssample')->first();
        $user2 = User::where('username', 'ayahoo')->first();
        $obj = new SET\User();

        // Test user logs are equal
        $this->assertEquals(count($obj->getUserLog($user1)), 2);
        $this->assertEquals(count($obj->getUserLog($user2)), 2);
        $this->assertEquals(count($obj->getUserLog($user1)), count($obj->getUserLog($user2)));

        // Test two changes are logged
        $user1 = User::where('username', 'ssample')->first();
        $user1->access_level = 'Conditional';
        $user1->save();
        $user1->nickname = 'Nicky';
        $user1->save();

        // Test user logs are unequal
        $this->assertEquals(count($obj->getUserLog($user1)), 4);
        $this->assertGreaterThan(count($obj->getUserLog($user2)), count($obj->getUserLog($user1)));

        // Test that all logs are greater than single user log
        $this->assertGreaterThan(count($obj->getUserLog($user1)), count($obj->getUserLog()));
    }
}
