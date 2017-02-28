<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Console\Commands\RenewTraining;
use SET\Events\TrainingAssigned;
use SET\Training;
use SET\TrainingUser;
use SET\User;

class RenewTrainingTest extends TestCase
{
    use DatabaseTransactions;

    /** @test Test a False timeToRenew() call */
    public function it_doesnt_renew_before_timeToRenew()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no pre-existing user trainings');
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subday(365)->format('Y-m-d'),
            'completed_date' => Carbon::today()->subday(335)->format('Y-m-d'), // at with offset
        ]);
        $this->assertCount(1, TrainingUser::all(), 'Should only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'Should be no new user training');
        $this->assertCount(1, TrainingUser::all(), 'Should still only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'Should be no new user training');
        $this->assertCount(1, TrainingUser::all(), 'Should still only be one user training');
    }

    /** @test Test a True timeToRenew() call */
    public function it_renews_if_timeToRenew()
    {
        $this->expectsEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no pre-existing user trainings');
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subday(365)->format('Y-m-d'),
            'completed_date' => Carbon::today()->subday(336)->format('Y-m-d'),  // Withing the offset
        ]);
        $this->assertCount(1, TrainingUser::all(), 'Should only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(1, $trainingUser, 'Should be new user training');
        $this->assertCount(2, TrainingUser::all(), 'Should be two user trainings');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'No new user training');
        $this->assertCount(2, TrainingUser::all(), 'Should be two user trainings');
    }

    /** @test Test renewedAlready() method with a null completed_date */
    public function it_doesnt_renew_if_renewedAlready_training_still_outstanding()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no pre-existing user trainings');
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subday(365)->format('Y-m-d'),
            'completed_date' => null,
        ]);
        $this->assertCount(1, TrainingUser::all(), 'Should only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'Should be no new user training');
        $this->assertCount(1, TrainingUser::all(), 'Should still only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'Should be no new user training');
        $this->assertCount(1, TrainingUser::all(), 'Should still only be one user training');
    }

    /** @test Test renewedAlready() method with a valid completed_date value*/
    public function it_renews_if_renewedAlready_has_completed_date()
    {
        $this->expectsEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no pre-existing user trainings');
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subday(365)->format('Y-m-d'),
            'completed_date' => Carbon::today()->subday(500)->format('Y-m-d'), // way before offset
        ]);
        $this->assertCount(1, TrainingUser::all(), 'Should only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(1, $trainingUser, 'Should be new user training');
        $this->assertCount(2, TrainingUser::all(), 'Should be two user trainings');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'No new user training');
        $this->assertCount(2, TrainingUser::all(), 'Should be two user trainings');
    }

    /** @test Test renewedAlready() method with a past completed_date value*/
    public function it_renews_if_renewedAlready_past_completed_date_offset()
    {
        $this->expectsEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no pre-existing user trainings');
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subday(365)->format('Y-m-d'),
            'completed_date' => Carbon::today()->subday(336)->format('Y-m-d'), // day before offset
        ]);
        $this->assertCount(1, TrainingUser::all(), 'Should only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(1, $trainingUser, 'Should be new user training');
        $this->assertCount(2, TrainingUser::all(), 'Should be two user trainings');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'No new user training');
        $this->assertCount(2, TrainingUser::all(), 'Should be two user trainings');
    }

    /** @test Test renewedAlready() method with a even completed_date value*/
    public function it_doesnt_renew_if_renewedAlready_match_completed_date_offset()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no pre-existing user trainings');
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subday(365)->format('Y-m-d'),
            'completed_date' => Carbon::today()->subday(335)->format('Y-m-d'), // day of offset
        ]);
        $this->assertCount(1, TrainingUser::all(), 'Should only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'No new user training');
        $this->assertCount(1, TrainingUser::all(), 'Still user trainings');

        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'No new user training');
        $this->assertCount(1, TrainingUser::all(), 'Still user trainings');
    }

    /** @test */
    public function it_renews_training_when_it_is_time_to_renew()
    {
        $this->expectsEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no pre-existing user trainings');
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date' => Carbon::today()->subYear()->subMonth()->format('Y-m-d'),
        ]);
        $this->assertCount(1, TrainingUser::all(), 'Should only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();

        $this->assertCount(1, $trainingUser);
        $this->assertCount(2, TrainingUser::all(), 'Should be a new second user training');
    }

    /** @test */
    public function it_does_not_renew_training_with_a_0_renews_in_value()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 0]);
        $user = factory(User::class)->create();
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date' => Carbon::today()->subYear()->format('Y-m-d'),
        ]);

        $trainingUser = (new RenewTraining())->handle()->getList();

        $this->assertCount(0, $trainingUser);
    }

    /** @test */
    public function it_does_not_renew_training_if_before_renewal_date()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subMonths(9)->format('Y-m-d'),
            'completed_date' => Carbon::today()->subMonths(9)->format('Y-m-d'),
        ]);

        $trainingUser = (new RenewTraining())->handle()->getList();

        $this->assertCount(0, $trainingUser);
    }

    /** @test */
    public function it_does_not_renew_training_when_there_exists_an_incomplete_training()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYears(2)->format('Y-m-d'),
            'completed_date' => null,
        ]);

        $trainingUser = (new RenewTraining())->handle()->getList();

        $this->assertCount(0, $trainingUser);
    }

    /** @test */
    public function it_does_not_renew_if_it_has_already_been_renewed()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();

        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date' => Carbon::today()->subYear()->subMonth()->format('Y-m-d'),
        ]);

        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subWeek()->format('Y-m-d'),
            'completed_date' => null,
        ]);

        $trainingUser = (new RenewTraining())->handle()->getList();

        $this->assertCount(0, $trainingUser);
    }

    /** @test */
    public function it_does_not_renew_if_incomplete_but_past_due()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();

        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date' => Carbon::today()->subYear()->subMonth()->format('Y-m-d'),
        ]);

        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subWeek()->format('Y-m-d'),
            'completed_date' => null,
        ]);

        $trainingUser = (new RenewTraining())->handle()->getList();

        $this->assertCount(0, $trainingUser);
    }

    /** @test
     * Issue appeared creating a new renewed training even though the renewed
     * training had already been completed.
     * However this is/was because of conditions
     *    1. The stop_renewal on the evaluated Training User is 'null'
     *    2. The due_date on the evaluated Training User is in the past
     */
    public function it_does_not_renew_if_completed_with_stop_renewal_as_null()
    {
        $this->expectsEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no existing user trainings');

        // Create Prior user training; ensure completed date causes new due_date for today/past (over 365)
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subDays(365)->format('Y-m-d'),
            'completed_date' => Carbon::today()->subDays(365)->format('Y-m-d'),
        ]);

        // Create new renewal
        $this->assertCount(1, TrainingUser::all(), 'Should only be the initial user training.');
        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(1, $trainingUser, 'User training should be renewed.');
        $this->assertCount(2, TrainingUser::all(), 'Creates an additional user training.');

        // Now mimic user completing current course
        $latestTrainingUser = TrainingUser::where('id', TrainingUser::count())->first();
        $latestTrainingUser->completed_date = Carbon::today()->subWeek(1)->format('Y-m-d');
        $latestTrainingUser->stop_renewal = null; // Mimic problematic samples
        $latestTrainingUser->save();

        // The latest Training User now has two components that caused the false renewal problem
        //   1. The stop_renewal = null
        //   2. The due_date (prior completed_date + 365) is today or in past

        // Subsequent RenewTraining should not create new training
        $trainingUser = (new RenewTraining())->handle()->getList();
        $this->assertCount(0, $trainingUser, 'User training should NOT be renewed.');
        $this->assertCount(2, TrainingUser::all(), 'No change in user training.');
    }

    /** @test */
    public function it_does_not_renew_if_renews_in_is_null()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => null]);
        $user = factory(User::class)->create();
        $this->assertCount(0, TrainingUser::all(), 'Should be no pre-existing user trainings');
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date' => Carbon::today()->subYear()->subMonth()->format('Y-m-d'),
        ]);
        $this->assertCount(1, TrainingUser::all(), 'Should only be one user training');

        $trainingUser = (new RenewTraining())->handle()->getList();

        $this->assertCount(0, $trainingUser, 'Should not be renewed.');
        $this->assertCount(1, TrainingUser::all(), 'Should not be a new user training');
    }
}
