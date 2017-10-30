<?php

namespace Tests\Integration\Models;
use Tests\TestCase;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Request;
use SET\Training;
use SET\User;
use SET\TrainingType;

class TrainingTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    /*
     Test the Training::scopeSearchTraining method
    */
    public function search_for_valid_training()
    {
        // Create a training
        $createdTraining = factory(Training::class)->create();

        // Query the database for the first 3 letters of the createdTraining name
        // using the scopeSearchTraining method in the training model
        $qInput = Request::input('q', str_limit($createdTraining->name, 3, ''));
        $trainingCollection = Training::searchTraining($qInput)->get(['id', 'name', 'renews_in', 'description']);

        // Filter the obtained collection to retrieve the just created training matching the id.
        $foundTraining = $trainingCollection->filter(function ($item) use ($createdTraining) {
            return $item->id == $createdTraining->id;
        })->first();

        // Assert that the correct training is returned
        $this->assertEquals($foundTraining->name, $createdTraining->name);
        $this->assertEquals($foundTraining->renews_in, $createdTraining->renews_in);
        $this->assertEquals($foundTraining->description, $createdTraining->description);
    }

    /** @test */
    /*
     Test the Training::scopeSearchTraining method
    */
    public function search_for_invalid_training()
    {
        // Query the database for a training with yyy in the name
        // using the scopeSearchTraining method in the training model
        $qInput = Request::input('q', 'yyy');
        $trainingCollection = Training::searchTraining($qInput)->get(['id', 'name', 'renews_in', 'description']);

        // Ensure that the query returns an empty collection
        $this->assertEmpty($trainingCollection);
    }

    /** @test */
    public function it_can_list_the_number_of_incompleted_assignments()
    {
        $training = factory(Training::class)->create();
        $users = factory(User::class, 3)->create();
        $training->users()->attach($users[0], ['completed_date' => Carbon::today(), 'author_id' => 189794, 'due_date' => Carbon::today()]);
        $training->users()->attach($users[1], ['completed_date' => null, 'author_id' => 1, 'due_date' => Carbon::today()]);
        $training->users()->attach($users[2], ['completed_date' => null, 'author_id' => 1, 'due_date' => Carbon::today()]);

        $query = Training::findOrFail($training->id);

        $this->assertEquals($query->incompleted, 2);
    }

    /** @test */
    /*
     Test the Training::scopeSearchTrainingByType method
    */
    public function search_for_valid_training_by_type()
    {
        // Create a trainingtype object
        $createdTrainingType = factory(TrainingType::class)->create();

        // Create a training
        $createdTraining = factory(Training::class)->create(['training_type_id' => $createdTrainingType->id]);

        // Query the database for a training with the created training type
        // using the scopeSearchTrainingByType method in the training model
        $qInput = Request::input('q', $createdTrainingType->name);
        $trainingCollection = Training::trainingByType($qInput)->get();

         // Filter the obtained collection to retrieve the just created training matching the id.
         $foundTraining = $trainingCollection->filter(function ($item) use ($createdTraining) {
             return $item->id == $createdTraining->id;
         })->first();

        // Assert that the correct training is returned
        $this->assertNotEmpty($trainingCollection);
        $this->assertEquals($foundTraining->training_type_id, $createdTrainingType->id);
    }

    /** @test */
    /*
     Test the Training::scopeSearchTrainingByType method
    */
    public function search_for_invalid_training_by_type()
    {
        // Query the database for a training with training type sometype
        // using the scopeSearchTrainingByType method in the training model
        $qInput = Request::input('q', 'sometype');
        $trainingCollection = Training::trainingByType($qInput)->get(['id', 'name', 'renews_in', 'description']);

        // Ensure that the query returns an empty collection
        $this->assertEmpty($trainingCollection);
    }
}
