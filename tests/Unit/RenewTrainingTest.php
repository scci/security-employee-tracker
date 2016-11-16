<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Console\Commands\RenewTraining;
use SET\Training;
use SET\TrainingUser;
use SET\User;

class ExpiringVisitsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_renews_training_when_it_is_time_to_renew()
    {
        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date' => Carbon::today()->subYear()->subMonth()->format('Y-m-d'),
        ]);

        (new RenewTraining())->handle();

        $trainingUser = TrainingUser::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->where('created_at', Carbon::now())
            ->get();

        $this->assertCount(1, $trainingUser);
    }

    /** @test */
    public function it_does_not_renew_training_if_before_renewal_date()
    {
        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subMonths(9)->format('Y-m-d'),
            'completed_date' => Carbon::today()->subMonths(9)->format('Y-m-d'),
        ]);

        (new RenewTraining())->handle();

        $trainingUser = TrainingUser::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->where('created_at', Carbon::now())
            ->get();

        $this->assertCount(0, $trainingUser);
    }

    /** @test */
    public function it_does_not_renew_training_when_there_exists_an_incomplete_training()
    {
        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYears(2)->format('Y-m-d'),
            'completed_date' => null,
        ]);

        (new RenewTraining())->handle();

        $trainingUser = TrainingUser::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->where('created_at', Carbon::now())
            ->get();

        $this->assertCount(0, $trainingUser);
    }
}
