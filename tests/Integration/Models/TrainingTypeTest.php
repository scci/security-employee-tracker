<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use SET\TrainingType;
use SET\Training;

class TrainingTypeTest extends TestCase
{
    use DatabaseMigrations;  // Reset The Database After Each Test

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_it_can_be_populated()
    {
        // Populate table
        factory(TrainingType::class,4)->create();
        $this->assertCount(4,SET\TrainingType::all());

        // Set records with values
        factory(TrainingType::class)->create(['name'=>'Information Systems',
            'sidebar'=>'1', 'status'=>'1', 'description'=>'description 1']);
        factory(TrainingType::class)->create(['name'=>'Security',
            'sidebar'=>'2', 'status'=>'0', 'description'=>'description 2']);
        $this->assertCount(6,SET\TrainingType::all());
        // $training_types = SET\TrainingType::all();
        // foreach ($training_types as $key => $value) {
        //     $result1 = $value->name;
        //     $result2 = $value->description;
        //     $result3 = $value->sidebar;
        //     $result4 = $value->status;
        //     dump($result1.'  '.$result2.'  '.$result3.'  '.$result4);
        // }
      }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_it_handles_relationships()
    {

      // Create Training Types
      $type1 = factory(TrainingType::class)->create(['name'=>'Information Systems',
          'sidebar'=>'1', 'status'=>'1', 'description'=>'Type 1']);
      $type2 = factory(TrainingType::class)->create(['name'=>'Security',
          'sidebar'=>'2', 'status'=>'1', 'description'=>'Type 2']);
      $type3 = factory(TrainingType::class)->create(['name'=>'Tertiary Training',
          'sidebar'=>'2', 'status'=>'0', 'description'=>'Type 3']);
      // Create Trainings
      $training1 = factory(Training::class)->create(['name'=>'Training #1',
          'description'=>'Number 1']);
      $training2 = factory(Training::class)->create(['name'=>'Training #2',
          'description'=>'Number 2']);

      // Check record
      $this->assertCount(3, TrainingType::all());
      $this->assertCount(2, TrainingType::whereStatus(true)->get());
      $this->assertCount(1, TrainingType::whereStatus(false)->get());

      $trainingType = TrainingType::where('description', 'Type 1')->first();
      $this->assertEquals('Information Systems',$trainingType->name );
      // Change an Save
      $trainingType->name = 'Info Sys Type';
      $trainingType->save();
      $trainingType = TrainingType::where('description', 'Type 1')->first();
      $this->assertEquals('Info Sys Type',$trainingType->name );

      // Test associations and disassociations
      // Evaluate Training types that have training(s)
      $this->assertEquals(TrainingType::has('trainings')->count(), 0);
      $this->assertEquals(TrainingType::doesntHave('trainings')->count(), 3);
      $this->assertEquals(Training::has('trainingType')->count(), 0);
      $this->assertEquals(Training::doesntHave('trainingType')->count(), 2);

      // Associating traingingtype to a TrainingType
      $trainings=Training::all();
      $trainingType = TrainingType::where('name','Info Sys Type')->first();
      $training = $trainings->where('description', 'Number 2')->first();
      $training->trainingType()->associate($trainingType);
      $training->save();
      $this->assertEquals(TrainingType::where('name','Info Sys Type')->first()->id,
          Training::where('description', 'Number 2')->first()->training_type_id);

      // Evaluate Training types that have training(s)
      $this->assertEquals(TrainingType::has('trainings')->get()->first()->name,'Info Sys Type');
      $this->assertEquals(TrainingType::has('trainings')->count(), 1);
      $this->assertEquals(TrainingType::doesntHave('trainings')->count(), 2);
      $this->assertEquals(Training::has('trainingType')->count(), 1);
      $this->assertEquals(Training::doesntHave('trainingType')->count(), 1);

      // Associating traingingtype to a TrainingType
      $trainingType = TrainingType::where('name','Security')->first();
      $training = $trainings->where('description', 'Number 1')->first();
      $training->trainingType()->associate($trainingType);
      $training->save();
      $this->assertEquals(TrainingType::where('name','Security')->first()->id,
          Training::where('description', 'Number 1')->first()->training_type_id);
      // Evaluate Training types that have training(s)
      $this->assertEquals(TrainingType::has('trainings')->count(), 2);
      $this->assertEquals(TrainingType::doesntHave('trainings')->count(), 1);
      $this->assertEquals(Training::has('trainingType')->count(), 2);
      $this->assertEquals(Training::doesntHave('trainingType')->count(), 0);

      // Test dissociate a Training
      $this->assertEquals(TrainingType::has('trainings')->get()->last()->name,'Security');
      $trainingType = TrainingType::where('name','Security')->first();
      $training = $trainings->where('description', 'Number 1')->first();
      $training->trainingType()->dissociate();
      $training->save();
      $this->assertEquals(Training::where('description', 'Number 1')->first()->training_type_id, null);
      // Evaluate Training types that have training(s)
      $this->assertEquals(TrainingType::has('trainings')->count(), 1);
      $this->assertEquals(TrainingType::doesntHave('trainings')->count(), 2);
      $this->assertEquals(Training::has('trainingType')->count(), 1);
      $this->assertEquals(Training::doesntHave('trainingType')->count(), 1);

      // // try accessing via method
      // $trainingTypes = SET\TrainingType::all();
      // foreach ($trainingTypes as $trainingType) {
      //   foreach ($trainingType->trainings as  $value) {
      //     dump($value->name.' has training_type_id = ' . $value->training_type_id);
      //   }
      // }
  }
}
