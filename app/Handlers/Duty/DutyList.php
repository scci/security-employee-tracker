<?php

namespace SET\Handlers\Duty;

use SET\Duty;
use SET\DutySwap;

class DutyList
{
    private $duty;

    public function __construct($duty)
    {
        if (is_int($duty)) {
            $duty = Duty::findOrFail($duty);
        }
        $this->duty = $duty;
    }

    public function htmlOutput()
    {
        return $this->processedList()->htmlOutput();
    }

    public function scheduledUpdate()
    {
        //build the list and write into the database the next record.
        $this->processedList()->iterateList();
        //build the list again (with the new record on top) and generate our email output.
        return $this->processedList()->emailOutput();
    }

    public function emailOutput()
    {
        $userGroup = $this->processedList();

        return $userGroup->emailOutput();
    }

    /**
     * @param array  $dateArray
     * @param array  $IDArray
     * @param string $type
     */
    public function processSwapRequest(array $dateArray, array $IDArray, $type)
    {
        for ($i = 0; $i < 2; $i++) {
            $futureSwap = DutySwap::where('date', $dateArray[$i])
                ->where('duty_id', $this->duty->id)->first();
            $flippedI = ($i == 0 ? 1 : 0);

            if (is_null($futureSwap)) {
                $this->createDutySwap($dateArray[$i], $IDArray[$flippedI], $type);
            } else {
                $futureSwap->imageable_id = $IDArray[$flippedI];
                $futureSwap->save();
            }
        }
    }

    private function processedList()
    {
        if ($this->duty->has_groups) {
            return new DutyGroups($this->duty);
        } else {
            return new DutyUsers($this->duty);
        }
    }

    /**
     * @param $date
     * @param $ID
     * @param $type
     */
    private function createDutySwap($date, $ID, $type)
    {
        DutySwap::create([
            'imageable_id'   => $ID,
            'imageable_type' => $type,
            'duty_id'        => $this->duty->id,
            'date'           => $date,
        ]);
    }
}
