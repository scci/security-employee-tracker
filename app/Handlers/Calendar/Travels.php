<?php

namespace SET\Handlers\Calendar;

use Illuminate\Database\Eloquent\Collection;
use SET\Travel;

class Travels extends CollectionList
{

    public function getList() : Collection
    {
        return Travel::with('user')
            ->where(function ($query) {
                $query->whereBetween('leave_date', [$this->start, $this->end])
                    ->orWhereBetween('return_date', [$this->start, $this->end]);
            })
            ->get();
    }
}