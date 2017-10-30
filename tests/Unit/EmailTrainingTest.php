<?php

namespace SET\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use SET\Events\TrainingAssigned;
use SET\Training;
use SET\User;

/**
 * Class EmailTraining.
 */
class EmailTrainingTest implements ShouldQueue
{
    /** @test */
    public function it_sends_an_email()
    {
        Mail::fake();

        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create();
        $trainingUser = $training->users()->attach($user, ['author_id' => $user->first()->id,
            'due_date'                                                 => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date'                                           => Carbon::today()->subYear()->subMonth()->format('Y-m-d'), ]);

        (new EmailTraining())->handle(new TrainingAssigned($trainingUser));

        Mail::assertSent();
    }
}
