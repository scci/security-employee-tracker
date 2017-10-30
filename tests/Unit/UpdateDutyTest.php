<?php

use Tests\Testcase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use SET\Console\Commands\UpdateDuty;
use SET\Duty;
use SET\Mail\DutyToday;
use SET\User;

class UpdateDutyTest extends TestCase
{
    use DatabaseTransactions;

    private $duty;
    private $users;

    public function setUp()
    {
        parent::setUp();

        $this->duty = factory(Duty::class)->create(['cycle' => 'daily']);
        $users = factory(User::class, 4)->create();
        $this->users = $users->sortBy('last_name');
        $this->duty->users()->attach($this->users);

        $this->duty->users()->updateExistingPivot($this->users[0]->id, ['last_worked' => Carbon::yesterday()]);
    }

    /** @test */
    public function it_updates_the_duty_roster_and_notifies_users()
    {
        Mail::fake();

        (new UpdateDuty())->handle();

        $users = $this->duty->users()->orderBy('duty_user.last_worked', 'DESC')->orderBy('last_name')->get();

        Mail::assertQueued(DutyToday::class);
    }
}
