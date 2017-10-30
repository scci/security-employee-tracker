<?php

namespace Tests\Integration\Models;
use Tests\TestCase;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Training;
use SET\User;
use SET\TrainingUser;

class TrainingUserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_get_all_records_with_active_users()
    {
        $activeUsers = factory(User::class, 4)->create();
        $inactiveUsers = factory(User::class, 3)->create(['status' => 'separated']);
        $training = factory(Training::class)->create();

        $training->users()->attach($activeUsers, ['author_id' => $activeUsers->first()->id, 'due_date' => Carbon::today()]);
        $training->users()->attach($inactiveUsers, ['author_id' => $activeUsers->first()->id, 'due_date' => Carbon::today()]);

        $trainingUsers = TrainingUser::where('training_id', $training->id)->activeUsers()->get();

        $this->assertEquals(4, $trainingUsers->count());
    }

    public function it_sets_values_to_null_if_empty_string_is_passed()
    {
        $note = factory(TrainingUser::class)->create([
            'due_date'       => '',
            'completed_date' => '',
        ]);

        $this->assertNull($note->due_date);
        $this->assertNull($note->completed_date);
    }
}
