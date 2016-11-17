<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use SET\Console\Commands\SendReminders;
use SET\Events\TrainingAssigned;
use SET\Mail\EmailSupervisorReminder;
use SET\Training;
use SET\User;

class SendRemindersTest extends TestCase
{
    use DatabaseTransactions;

    protected $training;
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->training = factory(Training::class)->create(['renews_in' => 365]);
        $this->user = factory(User::class)->create([
            'supervisor_id' => factory(User::class)->create()->id,
        ]);
    }

    /** @test */
    public function it_sends_reminders_to_users_and_supervisors_when_training_is_past_due()
    {
        Mail::fake();
        $this->expectsEvents(TrainingAssigned::class);

        $this->training->users()->attach($this->user, [
            'author_id'      => $this->user->first()->id,
            'due_date'       => Carbon::yesterday()->format('Y-m-d'),
            'completed_date' => null,
        ]);

        (new SendReminders())->handle();

        Mail::assertSentTo([$this->user->supervisor()->first()], EmailSupervisorReminder::class);
    }

    /** @test */
    public function it_does_not_send_reminders_when_training_is_not_due_for_two_weeks()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $this->training->users()->attach($this->user, [
            'author_id'      => $this->user->first()->id,
            'due_date'       => Carbon::today()->addWeeks(2)->format('Y-m-d'),
            'completed_date' => null,
        ]);

        (new SendReminders())->handle();
    }

    /** @test */
    public function it_does_not_send_reminders_for_completed_trainings()
    {
        $this->doesntExpectEvents(TrainingAssigned::class);

        $this->training->users()->attach($this->user, [
            'author_id'      => $this->user->first()->id,
            'due_date'       => Carbon::yesterday()->format('Y-m-d'),
            'completed_date' => Carbon::today()->format('Y-m-d'),
        ]);

        (new SendReminders())->handle();
    }
}
