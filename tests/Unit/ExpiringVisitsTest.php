<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Console\Commands\ExpiringVisits;
use SET\Visit;

class ExpiringVisitsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_generates_a_list_of_expiring_visitation_rights()
    {
        factory(Visit::class, 5)->create();
        factory(Visit::class)->create(['expiration_date' => Carbon::tomorrow()]);

        $visits = (new ExpiringVisits())->handle()->getList();

        $this->assertCount(1, $visits);
    }
}
