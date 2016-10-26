<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\User;
use SET\Training;
use SET\Travel;
use SET\TrainingUser;
use SET\Duty;
use Carbon\Carbon;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function it_loads_on_admin_login()
    {
        $this->visit('/')->see('Calendar')->seePageIs('/');
    }

    /** @test */
    public function it_shows_accounts_created_in_the_last_week()
    {
        $newUser = factory(User::class)->create();
        $this->visit('/')->see("$newUser->userFullName's</a> account was created.");
    }
    
    /** @test */
    public function it_shows_accounts_that_will_be_deleted_soon() 
    {
        factory(User::class)->create(['status' => 'separated', 'destroyed_date' => Carbon::today()->addWeek()->format('Y-m-d')]);
        $this->visit('/')->see("records will be deleted");
    }
    
    /** @test */
    public function it_shows_users_who_are_traveling() 
    {
        $user = factory(User::class)->create();
        $visit = factory(Travel::class)->create([
            'user_id' => $user->id,
            'leave_date' => Carbon::today()->format('Y-m-d'),
            'return_date' => Carbon::today()->addWeek()->format('Y-m-d')
        ]);

        $this->visit('/')
            ->see("$user->userFullName</a> leaves for $visit->location.")
            ->see("$user->userFullName</a> returns from $visit->location.");
    }
    
    /** @test */
    public function it_groups_multiple_training_users()
    {
        $users = factory(User::class, 5)->create();
        $training = factory(Training::class)->create();
        foreach ($users as $user) {
            $training->users()->attach($user, ['due_date' => Carbon::today()->format('Y-m-d'), 'author_id' => $this->user->id]);
        }
        $this->visit('/')->see("5 people.");

    }

    /** @test */
    public function it_lists_users_who_have_upcoming_training()
    {
        $users = factory(User::class, 2)->create();
        $training = factory(Training::class)->create();
        foreach ($users as $user) {
            $training->users()->attach($user, ['due_date' => Carbon::today()->format('Y-m-d'), 'author_id' => $this->user->id]);
        }
        $this->visit('/')->see(implode('; ', array_map(function($a) {return '<a href="'. url('user', $a['id']) .'">'.$a['last_name']. ', '. $a['first_name']. ' (' .$a['nickname'] . ')</a>';}, $users->toArray()) ));

    }
    
    /** @test */
    public function it_shows_who_is_currently_working_security_checks() 
    {
        $duty = factory(Duty::class)->create(['has_groups' => 0]);
        $users = factory(User::class, 5)->create();
        $duty->users()->attach($users);

        $this->visit('/')->see($users->sortBy('last_name')->first()->userFullName);
    }
    
    /** @test */
    public function it_shows_when_a_user_has_completed_a_training() 
    {
        $user = factory(User::class)->create();
        $training = factory(Training::class)->create();
        $training->users()->attach($user, ['due_date' => Carbon::today()->format('Y-m-d'), 'completed_date' => Carbon::today()->format('Y-m-d'), 'author_id' => $this->user->id]);
        $trainingUser = TrainingUser::where('training_id', $training->id)->where('user_id', $user->id)->get()->first();

        $this->visit('/')->see($trainingUser->completed_date)->see($user->userFullName)->see($training->name);
    }
    
    /** @test */
    public function it_shows_when_changes_are_made_to_a_users_profile() 
    {
        $emp_num = $this->user->emp_num;
        $this->user->update(['emp_num' => 995]);
        $this->visit('/')->see("Emp_num changed from '" . $emp_num . "' to '995'.");
    }
}