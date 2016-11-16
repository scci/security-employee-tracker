<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use SET\Console\Commands\ProcessMonday;
use SET\Mail\EmailAdminSummary;

class ProcessMondayTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sends_a_summary_email_to_the_FSO()
    {
        Mail::fake();

        (new ProcessMonday())->handle();

        Mail::assertSent(EmailAdminSummary::class);
    }
}
