<?php

namespace SET\Handlers\Duty;

use Carbon\Carbon;
use SET\Duty;

/**
 * Class DutyBase.
 */
class DutyHelper
{
    public $list;
    public $lastWorked = null;
    public $duty;

    public function __construct(Duty $duty)
    {
        $this->duty = $duty;
        $this->generateList();
    }

    public function generateList()
    {
        $this->queryList()
            ->getLastWorked()
            ->sortList()
            ->combineListWithDates()
            ->insertFromDutySwap();
    }

    public function getList()
    {
        return $this->list;
    }

    public function iterateList()
    {
        $today = Carbon::today()->format('Y-m-d');

        if ($today == $this->lastWorked->pivot->last_worked) {
            return;
        }

        switch ($this->duty->cycle) {
            case 'daily':
                $this->recordNextEntry();
                break;
            case 'weekly':
                if (Carbon::today()->startOfWeek()->format('Y-m-d') == $today) {
                    $this->recordNextEntry();
                }
                break;
            case 'monthly':
                if (Carbon::today()->startOfMonth()->format('Y-m-d') == $today) {
                    $this->recordNextEntry();
                }
                break;
        }
    }

    public function sortList()
    {
        if (!is_null($this->lastWorked)) {
            while ($this->list->first()->id != $this->lastWorked->id) {
                $this->list->push($this->list->shift());
            }
        }

        return $this;
    }
}
