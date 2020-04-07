<?php

namespace SET\Handlers\Duty;

use Carbon\Carbon;
use SET\Duty;
use Illuminate\Support\Facades\Log;

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
        //Log::Info("In DutyHelper __construct");
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
        Log::Info("In DutyHelper iterateList");
        $today = Carbon::today()->format('Y-m-d');

        if (isset($this->lastWorkedUser->pivot->last_worked) && $today == $this->lastWorkedUser->pivot->last_worked) {
            return;
        }

        $this->readyToRecordNextEntry($today);
    }

    public function sortList()
    {
        Log::Info("In DutyHelper sortList");
        Log::Info($this->lastWorkedUser->id);
        Log::Info($this->list->first()->id);
        if (!is_null($this->lastWorkedUser)) {
            while ($this->list->first()->id != $this->lastWorkedUser->id) {
                //Log::Info("DutyHelper sortList - shift list");
                //Log::Info($this->list->shift());
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
        Log::Info("In DutyHelper readyToRecordNextEntry");
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
                //if (Carbon::today()->startOfWeek()->format('Y-m-d') == $today) {
                Log::Info("In DutyHelper call weekly recordNextEntry");
                    $this->recordNextEntry();
                //}
        }
    }
}
