<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Console\Commands\RenewTraining;
use SET\Events\TrainingAssigned;
use SET\Training;
use SET\User;

class RenewTrainingTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_renews_training_when_it_is_time_to_renew()
    {
        $this->expectsEvents(TrainingAssigned::class);

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date' => Carbon::today()->subYear()->subMonth()->format('Y-m-d'),
        ]);

        $trainingUser = (new RenewTraining())->handle()->getList();

        $this->assertCount(1, $trainingUser);
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
}
