<?php

namespace SET\Handlers\Duty;

use Carbon\Carbon;
use SET\Duty;

/**
 * Class DutyBase
 * @package SET\Handlers\Duty
 *
 * Please refer to DutyGroups and DutyUsers.
 * Just implementing DRY principles by pulling out functions present in both classes.
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

        switch ($this->duty->cycle) {
            case 'daily':
                if ($today != $this->lastWorked) {
                    $this->recordNextEntry();
                }
                break;
            case 'weekly':
                if (Carbon::today()->startOfWeek()->format('Y-m-d') == $today && $today != $this->lastWorked->pivot->last_worked) {
                    $this->recordNextEntry();
                }
                break;
            case 'monthly':
                if (Carbon::today()->startOfMonth()->format('Y-m-d') == $today && $today != $this->lastWorked->pivot->last_worked) {
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
