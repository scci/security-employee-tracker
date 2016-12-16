<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Duty;
use SET\Handlers\Duty\DutyList;
use SET\User;

class DutyListUsersTest extends TestCase
{
    use DatabaseTransactions;

    protected $duty;
    protected $users;
    protected $done;

    public function setUp()
    {
        parent::setUp();

        $this->duty = factory(Duty::class)->create();
        $this->users = factory(User::class, 4)->create();
        $this->duty->users()->attach($this->users);
    }

    /** @test */
    public function it_outputs_a_user_list_ordered_by_last_name()
    {
        $html = (new DutyList($this->duty))->htmlOutput();

        $user = $this->users->sortBy('last_name')->first();

        $this->assertEquals($user->id, $html[0]['id']);
    }

    /** @test */
    public function it_outputs_a_user_list_starting_with_who_worked_last()
    {
        $user = $this->users->random();
        $this->duty->users()->updateExistingPivot($user->id, ['last_worked' => Carbon::yesterday()]);

        $html = (new DutyList($this->duty))->htmlOutput();

        $this->assertEquals($user->id, $html[0]['id']);
    }

    /** @test */
    public function it_outputs_an_array_for_emailing_users()
    {
        $email = (new DutyList($this->duty))->scheduledUpdate();

        if (Carbon::today()->startOfWeek() != Carbon::today()) {
            $this->assertEquals($email[0]['users'][0]->id, $this->users->sortBy('last_name')->first()->id);
        }
        $this->assertEquals($email[0]['date'], Carbon::today()->startOfWeek()->format('Y-m-d'));
    }

    /** @test */
    public function it_iterates_the_user_list_and_outputs_an_array_for_emailing_users()
    {
        $user = $this->users->random();
        $this->duty->users()->updateExistingPivot($user->id, ['last_worked' => Carbon::today()->subWeek()->startOfWeek()]);

        $email = (new DutyList($this->duty))->scheduledUpdate();

        if (Carbon::today()->startOfWeek() == Carbon::today()) {
            $this->assertEquals($email[0]['date'], Carbon::today()->startOfWeek()->format('Y-m-d'));
            $this->assertEquals($email[3]['users'][0]->id, $user->id);
        } else {
            $this->assertEquals($email[0]['users'][0]->id, $user->id);
        }
    }

    /** @test */
    public function it_does_not_iterates_the_user_list_if_it_already_has_been_iterated()
    {
        $user = $this->users->random();
        $this->duty->users()->updateExistingPivot($user->id, ['last_worked' => Carbon::today()->startOfWeek()]);

        $email = (new DutyList($this->duty))->scheduledUpdate();

        $this->assertEquals($email[0]['users'][0]->id, $user->id);
    }

    /** @test */
    public function it_processes_user_swap_requests()
    {
        $htmlOne = ( new DutyList($this->duty) )->htmlOutput();

        $dates = [$htmlOne[0]['date'], $htmlOne[1]['date']];
        $IDs = [$htmlOne[0]['id'], $htmlOne[1]['id']];
        $type = 'SET\\User';

        ( new DutyList($this->duty) )->processSwapRequest($dates, $IDs, $type);

        $htmlTwo = ( new DutyList($this->duty) )->htmlOutput();

        $this->assertEquals($htmlOne[0]['id'], $htmlTwo[1]['id']);
        $this->assertEquals($htmlOne[1]['id'], $htmlTwo[0]['id']);
        $this->assertEquals($htmlOne[0]['date'], $htmlTwo[0]['date']);
        $this->assertEquals($htmlOne[1]['date'], $htmlTwo[1]['date']);

        //Do a swap of a swap.
        $dates = [$htmlTwo[0]['date'], $htmlTwo[1]['date']];
        $IDs = [$htmlTwo[0]['id'], $htmlTwo[1]['id']];
        $type = 'SET\\User';

        ( new DutyList($this->duty) )->processSwapRequest($dates, $IDs, $type);

        $htmlThree = ( new DutyList($this->duty) )->htmlOutput();

        $this->assertEquals($htmlTwo[0]['id'], $htmlThree[1]['id']);
        $this->assertEquals($htmlTwo[1]['id'], $htmlThree[0]['id']);
        $this->assertEquals($htmlTwo[0]['date'], $htmlThree[0]['date']);
        $this->assertEquals($htmlTwo[1]['date'], $htmlThree[1]['date']);
    }
}
