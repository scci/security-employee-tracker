<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Console\Commands\ProcessMonday;
use SET\Duty;
use SET\Mail\EmailAdminSummary;
use SET\Setting;
use SET\Training;
use SET\User;
use SET\Visit;

class ProcessMondayTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sends_out_a_summary_email_to_the_summary_recipient()
    {
        Mail::fake();

        $this->setupForTrainings();
        $this->setupForExpiringVisits();
        $this->setupForDeletingUsers();
        $this->setupDutyList();

        Setting::set('summary_recipient', null);
        (new ProcessMonday())->handle();
        Mail::assertNotSent(EmailAdminSummary::class);

        Setting::set('summary_recipient', 'fake@email.com');
        (new ProcessMonday())->handle();
        Mail::assertSentTo(['fake@email.com'], EmailAdminSummary::class);
    }

    private function setupForTrainings()
    {
        $training = factory(Training::class)->create(['renews_in' => 365]);
        $user = factory(User::class)->create([
            'supervisor_id' => factory(User::class)->create()->id,
        ]);
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::yesterday()->format('Y-m-d'),
            'completed_date' => null,
        ]);
        $training->users()->attach($user, [
            'author_id'      => $user->first()->id,
            'due_date'       => Carbon::today()->subYear()->format('Y-m-d'),
            'completed_date' => Carbon::today()->subYear()->subMonth()->format('Y-m-d'),
        ]);
    }

    private function setupForExpiringVisits()
    {
        factory(Visit::class, 5)->create();
        factory(Visit::class)->create(['expiration_date' => Carbon::tomorrow()]);
    }

    private function setupForDeletingUsers()
    {
        factory(User::class, 2)->create(['destroyed_date' => Carbon::today(), 'status' => 'separated']);
    }

    private function setupDutyList()
    {
        $duty = factory(Duty::class)->create();
        $users = factory(User::class, 4)->create();
        $duty->users()->attach($users);
    }
}
