<?php

namespace SET\Handlers\Duty;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use SET\Duty;
use SET\DutySwap;

class DutyGroups extends DutyHelper
{

    public function __construct(Duty $duty)
    {
        parent::__construct($duty); ;
    }

    public function HTMLOutput()
    {
        $newCollection = new Collection();

        foreach ($this->list as $entry) {

            $row = $this->buildHTMLUserRow($entry);

            $newCollection->push([
                'row' => $row,
                'id' => $entry['id'],
                'date' => $entry['date']
            ]);
        }

        return $newCollection;
    }

    public function emailOutput()
    {
        $collection = $this->list->map(function($value, $key) {
            return [
                'users' => $value['group'],
                'date' => $value['date']
            ];
        });

        return $collection;
    }

    public function recordNextEntry()
    {
        $nextGroupID = $this->list->toArray()[1]['id'];
        $this->duty->groups()->updateExistingPivot($nextGroupID, ['last_worked' => Carbon::today()]);
    }

    public function getLastWorked()
    {
        $this->lastWorked = $this->duty->groups()->orderBy('duty_group.last_worked', 'DESC')->first();
        return $this;
    }

    public function queryList()
    {

        $this->list = $this->duty->groups()->orderBy('name')->get();
        return $this;
    }

    public function combineListWithDates()
    {
        $dates = (new DutyDates($this->duty))->getDates();
        $newList = new Collection();
        $count = $this->list->count();

        for ($i = 0; $i < $count; $i++)
        {
            $newList->push([
                'date' => $dates[$i],
                'group' => $this->list[$i]->users()->get(),
                'id'   => $this->list[$i]->id,
            ]);
        }

        $this->list = $newList;
        return $this;
    }

    public function insertFromDutySwap()
    {
        $dutySwaps = DutySwap::where('duty_id', $this->duty->id)
            ->where('imageable_type', 'SET\Group')
            ->where('date', '>=', Carbon::now()->subMonth()) //Omit really old records.
            ->orderBy('date', 'ASC')
            ->get();

        foreach ($dutySwaps as $swap)
        {
            foreach ($this->list as $key => $entry)
            {
                if ($swap->date == $entry['date']) {
                    $this->list[$key] = [
                        'group' => $swap->imageable()->first()->users()->get(),
                        'id'   => $swap->imageable()->first()->id,
                        'date' => $entry['date']
                    ];
                }
            }
        }
    }

    /**
     * @param $entry
     * @return string
     */
    private function buildHTMLUserRow($entry)
    {
        $row = '';
        foreach ($entry['group'] as $user) {
            if(Gate::allows('view')) {
                $row .= "<a href='" . url('user', $user->id) . "'>" . $user->userFullName . "</a> & ";
            } else {
                $row .= $user->userFullName . " & ";
            }
        }
        $row = rtrim($row, '& ');
        return $row;
    }
}
