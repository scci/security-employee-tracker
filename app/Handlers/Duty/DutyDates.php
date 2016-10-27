<?php

namespace SET\Handlers\Duty;

use Carbon\Carbon;
use SET\Duty;
use SET\Log;

class DutyDates
{
    private $duty;
    
    public function __construct(Duty $duty)
    {
        $this->duty = $duty;
    }

    public function getDates()
    {
        return $this->buildDateArray();
    }

    private function buildDateArray()
    {
        $array = [];
        $storeDate = $date = $this->getStartDate($this->duty->cycle);

        $count = $this->duty->has_groups ? $this->duty->groups()->get()->count() : $this->duty->users()->get()->count();

        for ($i = 0; $i < $count; $i++)
        {
            $array[$i] = $storeDate->format('Y-m-d');
            $storeDate = $this->nextDate($this->duty->cycle, $date);
        }

        return $array;
    }

    private function getStartDate($cycle)
    {
        if ($cycle == 'monthly') {
            return Carbon::now()->startOfMonth();
        } else if ($cycle == 'weekly') {
            return Carbon::now()->startOfWeek();
        } else if ($cycle == 'daily') {
            return Carbon::now()->startOfDay();
        } else {
            Log::error('Duty has an invalid cycle name. Must use monthly, weekly or daily');
            return null;
        }
    }

    private function nextDate($cycle, Carbon $date)
    {
        if ($cycle == 'monthly') {
            return $date->addMonth();
        } else if ($cycle == 'weekly') {
            return $date->addWeek();
        } else if ($cycle == 'daily') {
            return $date->addDay();
        } else {
            Log::error('Duty has an invalid cycle name. Must use monthly, weekly or daily');
            return null;
        }
    }
}
