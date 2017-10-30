<?php

use Carbon\Carbon;
use SET\Handlers\DateFormat;
use Tests\Testcase;

class DateFormatTest extends TestCase
{
    /** @test */
    public function it_returns_the_correct_date_format_when_given_a_carbon_instance()
    {
        $mock = $this->getMockForTrait(DateFormat::class);

        $date = Carbon::today();

        $this->assertEquals($mock->dateFormat($date), Carbon::today()->format('Y-m-d'));
    }

    /** @test */
    public function it_returns_the_same_format_when_given_the_correct_format()
    {
        $mock = $this->getMockForTrait(DateFormat::class);

        $date = date('Y-m-d');

        $this->assertEquals($mock->dateFormat($date), $date);
    }

    /** @test */
    public function it_returns_the_correct_format_when_given_a_date_with_time()
    {
        $mock = $this->getMockForTrait(DateFormat::class);

        $date = date('Y-m-d H:i:s');

        $this->assertEquals($mock->dateFormat($date), Carbon::today()->format('Y-m-d'));
    }

    /** @test */
    public function it_returns_the_correct_format_when_giving_a_JPAS_datetime()
    {
        $mock = $this->getMockForTrait(DateFormat::class);

        $date = date('n/j/Y G:i');

        $this->assertEquals($mock->dateFormat($date), Carbon::today()->format('Y-m-d'));
    }
}
