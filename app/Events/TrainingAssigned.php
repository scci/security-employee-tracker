<?php

namespace SET\Events;

use Illuminate\Queue\SerializesModels;

class TrainingAssigned extends Event
{
    use SerializesModels;

    private $trainingUser;

    /**
     * Builds our trainingAssigned Class.
     *
     * @param $trainingUser
     */
    public function __construct($trainingUser)
    {
        $this->trainingUser = $trainingUser;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    public function getTrainingUser()
    {
        return $this->trainingUser;
    }
}
