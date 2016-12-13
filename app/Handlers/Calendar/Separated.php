<?php

namespace SET\Handlers\Calendar;

use Illuminate\Database\Eloquent\Collection;
use SET\User;

class Separated extends CollectionList
{
    public function getList() : Collection
    {
        return User::where(function ($q) {
            $q->where('status', 'separated')->orWhere('status', 'destroyed');
        })
            ->whereBetween('destroyed_date', [$this->start, $this->end])
            ->get();
    }
}
