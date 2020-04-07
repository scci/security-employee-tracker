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
    public $swapDates;
    public $lastWorkedUser = null;
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
            ->insertFromDutySwap()
            ->combineListWithDates();
    }

    public function iterateList()
    {
        $today = Carbon::today()->format('Y-m-d');

        if (isset($this->lastWorkedUser->pivot->last_worked) && $today == $this->lastWorkedUser->pivot->last_worked) {
            return;
        }

        $this->readyToRecordNextEntry($today);
    }

    public function sortList()
    {
        if (!is_null($this->lastWorkedUser)) {
            while ($this->list->first()->id != $this->lastWorkedUser->id) {
                $this->list->push($this->list->shift());
            }
        }

        return $this;
    }

    /**
     * Check duty cycle. Then determine if time to record next entry.
     *
     * @param string $today
     */
    private function readyToRecordNextEntry($today)
    {
        switch ($this->duty->cycle) {
            case 'daily':
                $this->recordNextEntry();
                break;
            case 'monthly':
                if (Carbon::today()->startOfMonth()->format('Y-m-d') == $today) {
                    $this->recordNextEntry();
                }
                break;
            default: //weekly
                if (Carbon::today()->startOfWeek()->format('Y-m-d') == $today) {
                    $this->recordNextEntry();
                }
        }
    }
}
