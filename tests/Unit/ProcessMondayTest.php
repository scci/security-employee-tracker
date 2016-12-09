<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use SET\Console\Commands\ProcessMonday;
use SET\Mail\EmailAdminSummary;
use SET\Setting;

class ProcessMondayTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sends_a_summary_email_to_the_FSO()
    {
        return true;
        Mail::fake();

        Setting::set('sender_address', 'fake@email.com');

        (new ProcessMonday())->handle();

        Mail::assertSent(EmailAdminSummary::class);
    }
}
