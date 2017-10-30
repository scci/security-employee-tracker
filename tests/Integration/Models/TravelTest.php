<?php

namespace Tests\Integration\Models;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Travel;

class TravelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sets_values_to_null_if_empty_string_is_passed()
    {
        $travel = factory(Travel::class)->create([
            'brief_date'   => '',
            'debrief_date' => '',
            'return_date'  => '',
            'comment'      => '',
        ]);

        $this->assertNull($travel->brief_date);
        $this->assertNull($travel->debrief_date);
        $this->assertNull($travel->return_date);
        $this->assertNull($travel->comment);
    }
}
