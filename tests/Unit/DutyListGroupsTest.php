<?php

use Tests\Testcase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Duty;
use SET\Group;
use SET\Handlers\Duty\DutyList;
use SET\User;

class DutyListGroupsTest extends TestCase
{
    use DatabaseTransactions;

    protected $duty;
    protected $groups;

    public function setUp()
    {
        parent::setUp();

        $this->duty = factory(Duty::class)->create(['has_groups' => 1]);
        $this->groups = factory(Group::class, 3)->create();
        foreach ($this->groups as $group) {
            $groups = factory(User::class, 2)->create();
            $group->users()->attach($groups);
        }
        $this->duty->groups()->attach($this->groups);
    }

    /** @test */
    public function it_outputs_a_user_list_ordered_by_last_name()
    {
        $html = (new DutyList($this->duty))->htmlOutput();

        $group = $this->duty->groups->sortBy('name');
        $this->assertEquals($group[0]->id, $html[0]['id']);
    }

    /** @test */
    public function it_outputs_a_group_list_starting_with_who_worked_last()
    {
        $group = $this->duty->groups->random();
        $this->duty->groups()->updateExistingPivot($group->id, ['last_worked' => Carbon::yesterday()]);

        $html = (new DutyList($this->duty))->htmlOutput();

        $this->assertEquals($group->id, $html[0]['id']);
    }

    /** @test */
    public function it_processes_group_swap_requests()
    {
        $htmlOne = ( new DutyList($this->duty) )->htmlOutput();

        $dates = [$htmlOne[0]['date'], $htmlOne[1]['date']];
        $IDs = [$htmlOne[0]['id'], $htmlOne[1]['id']];
        $type = 'SET\\Group';

        ( new DutyList($this->duty) )->processSwapRequest($dates, $IDs, $type);

        $htmlTwo = ( new DutyList($this->duty) )->htmlOutput();

        $this->assertEquals($htmlOne[0]['id'], $htmlTwo[1]['id']);
        $this->assertEquals($htmlOne[1]['id'], $htmlTwo[0]['id']);
        $this->assertEquals($htmlOne[0]['date'], $htmlTwo[0]['date']);
        $this->assertEquals($htmlOne[1]['date'], $htmlTwo[1]['date']);
    }
}
