<?php

namespace SET\Handlers\Calendar;

use Illuminate\Database\Eloquent\Collection;
use SET\TrainingUser;

class TrainingUsers extends CollectionList
{
    public function getList() : Collection
    {
        return TrainingUser::with('user', 'training')
            ->whereBetween('due_date', [$this->start, $this->end])
            ->whereNull('completed_date')
            ->orderBy('training_id')
            ->get();
    }
}
