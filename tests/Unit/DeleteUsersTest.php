<?php

use Tests\Testcase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Console\Commands\DeleteUsers;
use SET\User;

class DeleteUsersTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_deletes_inactive_users_whose_destroyed_date_has_passed()
    {
        factory(User::class, 2)->create();
        factory(User::class, 2)->create(['destroyed_date' => Carbon::today()]);
        factory(User::class, 2)->create(['destroyed_date' => Carbon::today(), 'status' => 'separated']);

        $users = (new DeleteUsers())->handle()->getList();

        $this->assertCount(2, $users);
    }
}
