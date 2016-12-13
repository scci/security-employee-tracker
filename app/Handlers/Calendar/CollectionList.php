<?php

namespace SET\Handlers\Calendar;

use Illuminate\Database\Eloquent\Collection;

abstract class CollectionList
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public abstract function getList() : Collection;
}