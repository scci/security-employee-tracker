<?php

namespace Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\User;
use SET\Visit;
use Tests\TestCase;

class VisitTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_pull_all_records_with_active_users()
    {
        $visits = [];
        for ($i = 0; $i < 5; $i++) {
            $user = factory(User::class)->create();
            $visit = factory(Visit::class)->create();

            $visit->user_id = $user->id;
            $visit->save();
            $visits[] = $visit->id;
        }
        for ($i = 0; $i < 5; $i++) {
            $visit = factory(Visit::class)->create();
            $user = factory(User::class)->create(['status' => 'separated']);
            $visit->user_id = $user->id;
            $visit->save();
            $visits[] = $visit->id;
        }

        $this->assertEquals(5, Visit::whereIn('id', $visits)->activeUsers()->get()->count());
    }
}
