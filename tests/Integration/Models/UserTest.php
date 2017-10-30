<?php

namespace Tests\Integration\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Request;
use SET\Setting;
use SET\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    /*
     Test the User::getUserFullNameAttribute method
    */
    public function get_users_full_name_with_nickname()
    {
        // Create a user(by default status should be active)
        $createdUser = factory(User::class)->create();

        // Query the database using the scopeActive method in the user model and filter by the above created username
        $userFullName = $createdUser['last_name']
                        .', '.$createdUser['first_name']
                        .' ('.$createdUser['nickname'].')';

        $this->assertEquals($userFullName, $createdUser->userFullName);
    }

    /** @test */
    public function get_user_full_name_without_nickname()
    {
        $createdUser = factory(User::class)->create(['nickname' => null]);
        $userFullName = $createdUser['last_name'].', '.$createdUser['first_name'];

        $this->assertEquals($userFullName, $createdUser->userFullName);
    }

    /** @test */
    public function get_user_full_name_in_reverse_format()
    {
        Setting::set('full_name_format', 'first_last');
        $createdUser = factory(User::class)->create(['nickname' => null]);
        $userFullName = $createdUser['first_name'].' '.$createdUser['last_name'];

        $this->assertEquals($userFullName, $createdUser->userFullName);
    }

    /** @test */
    /*
     Test the User::scopeSearchUsers method
    */
    public function search_for_valid_user()
    {
        // Create a user(by default status should be active)
        $createdUser = factory(User::class)->create();

        // Query the database for the first 3 letters of the createdUser first_name using the scopeSearchUsers method in the user model
        $qInput = Request::input('q', str_limit($createdUser->first_name, 3, ''));
        $usersCollection = User::searchUsers($qInput)->get(['id', 'first_name', 'last_name', 'status', 'emp_num']);

        // Filter the obtained collection to retrieve a username named 'system'
        $foundUser = $usersCollection->filter(function ($item) use ($createdUser) {
            return $item->id == $createdUser->id;
        })->first();

        // Assert that the correct user is returned
        $this->assertEquals($foundUser->last_name, $createdUser->last_name);
        $this->assertEquals($foundUser->emp_num, $createdUser->emp_num);
        $this->assertEquals($foundUser->status, $createdUser->status);
    }

    /** @test */
    /*
     Test the User::scopeSearchUsers method
    */
    public function search_for_invalid_users()
    {
        // Query the database for a user with zzz(in the first_name, last_name or emp_num)
        // using the scopeSearchUsers method in the user model
        $qInput = Request::input('q', 'zzz');
        $usersCollection = User::searchUsers($qInput)->get(['id', 'first_name', 'last_name', 'status', 'emp_num']);

        // Ensure that the query returns an empty collection
        $this->assertEmpty($usersCollection);
    }

    /** @test */
    /*
     Test the User::scopeActive method for active user
    */
    public function test_user_is_active()
    {
        // Create a user(by default status should be active)
        $createdUser = factory(User::class)->create();

        // Query the database using the scopeActive method in the user model and filter by the above created username
        $activeUserCollection = User::active()->where('username', $createdUser->username)->get();

        // Ensure that only one record is retrieved(since usernames are unique)
        $this->assertEquals($activeUserCollection->count(), 1);

        // Retrieve the record from the collection
        $activeUser = $activeUserCollection->first();

        // Assert that the status is active and the first_name matches the first_name of created user.
        $this->assertEquals($activeUser->status, 'active');
        $this->assertEquals($activeUser->first_name, $createdUser->first_name);
    }

    /** @test */
    /*
     Test the User::scopeActive method for inactive user
    */
    public function test_user_is_inactive()
    {
        // Create a user whose status is not active
        $createdUser = factory(User::class)->create(['status' => 'deadman']);

        // Query the database using the scopeActive method in the user model and filter by the above created user id
        $inactiveUser = User::active()->where('id', $createdUser->id)->get();

        // Ensure that the query returns an empty collection since the above created user is not an active user
        $this->assertEmpty($inactiveUser);
    }

    /** @test */
    /*
     Test the User::scopeSkipSystem method
    */
    public function get_all_users_except_system_user()
    {
        // Query the database using the scopeSkipSystem method in the user model
        $usersCollection = User::skipSystem()->get();

        // Ensure that the collection does not contain a username named 'system'
        $this->assertNotContains('system', $usersCollection->pluck('username'));
    }

    /** @test */
    public function it_gets_the_destroyed_date_of_a_separated_user()
    {
        $date = (new User())->getDestroyDate('separated');
        $this->assertEquals($date, Carbon::today()->addYears(2)->startOfWeek());
    }

    /** @test */
    public function it_gets_the_destroyed_date_of_a_destroyed_user()
    {
        $date = (new User())->getDestroyDate('destroyed');
        $this->assertEquals($date, Carbon::today()->addWeek()->startOfWeek());
    }

    /** @test */
    public function it_gets_the_destroyed_date_of_an_active_user()
    {
        $date = (new User())->getDestroyDate('active');
        $this->assertNull($date);
    }
}
