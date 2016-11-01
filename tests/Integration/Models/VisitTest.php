<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class VisitTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_pull_all_records_with_active_users()
    {
        $visits = [];
        for ($i = 0; $i < 5; $i++) {
            $visit = factory(SET\Visit::class)->create();
            $user = factory(SET\User::class)->create();
            $visit->user_id = $user->id;
            $visit->save();
            $visits[] = $visit->id;
        }
        for ($i = 0; $i < 5; $i++) {
            $visit = factory(SET\Visit::class)->create();
            $user = factory(SET\User::class)->create(['status' => 'separated']);
            $visit->user_id = $user->id;
            $visit->save();
            $visits[] = $visit->id;
        }

        $this->assertEquals(5, SET\Visit::whereIn('id', $visits)->activeUsers()->get()->count());
    }
}
