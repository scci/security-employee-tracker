<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TrainingUserTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function it_can_get_all_records_with_active_users()
    {
        $activeUsers = factory(SET\User::class, 4)->create();
        $inactiveUsers = factory(SET\User::class, 3)->create(['status' => 'separated']);
        $training = factory(SET\Training::class)->create();

        $training->users()->attach($activeUsers, ['author_id' => $activeUsers->first()->id, 'due_date' => Carbon::today()]);
        $training->users()->attach($inactiveUsers, ['author_id' => $activeUsers->first()->id, 'due_date' => Carbon::today()]);

        $trainingUsers = SET\TrainingUser::where('training_id', $training->id)->activeUsers()->get();

        $this->assertEquals(4, $trainingUsers->count());
    }

    public function it_sets_values_to_null_if_empty_string_is_passed()
    {
        $note = factory(SET\TrainingUser::class)->create([
            'due_date' => '',
            'completed_date' => ''
        ]);

        $this->assertNull($note->due_date);
        $this->assertNull($note->completed_date);

    }
}
