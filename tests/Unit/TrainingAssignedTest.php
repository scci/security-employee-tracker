<?php


use SET\Events\TrainingAssigned;

class TrainingAssignedTest extends TestCase
{
    /** @test */
    public function it_returns_the_constructed_value()
    {
        $returned = (new TrainingAssigned('TrainingUser'))->getTrainingUser();
        $this->assertEquals($returned, 'TrainingUser');
    }

    /** @test */
    public function it_broadcasts_on_nothing()
    {
        $returned = (new TrainingAssigned('TrainingUser'))->broadcastOn();
        $this->assertEmpty($returned);
    }
}
