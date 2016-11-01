<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class TravelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sets_values_to_null_if_empty_string_is_passed()
    {
        $travel = factory(SET\Travel::class)->create([
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
