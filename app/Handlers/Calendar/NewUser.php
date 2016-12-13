<?php

namespace SET\Handlers\Calendar;

use Illuminate\Database\Eloquent\Collection;
use SET\User;

class NewUser extends CollectionList
{

    public function getList() : Collection
    {
        return User::whereBetween('created_at', [$this->start, $this->end])->get();
    }
}