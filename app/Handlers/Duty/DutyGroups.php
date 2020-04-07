<?php

namespace SET\Handlers\Duty;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use SET\Duty;
use SET\DutySwap;

class DutyGroups extends DutyHelper
{
    public function __construct(Duty $duty)
    {
        parent::__construct($duty);
    }

    public function htmlOutput()
    {
        $newCollection = new Collection();

        foreach ($this->list as $entry) {
            $row = $this->buildHTMLUserRow($entry);

            $newCollection->push([
                'row'  => $row,
                'id'   => $entry['id'],
                'date' => $entry['date'],
            ]);
        }

        return $newCollection;
    }

    public function emailOutput()
    {
        $collection = $this->list->map(function ($value) {
            return [
                'users' => $value['group'],
                'date'  => $value['date'],
            ];
        });

        return $collection;
    }

    /**
     * Get function for the list. List is stored on the helper class.
     *
     * @return Collection
     */
    public function getList()
    {
        return $this->list;
    }

    public function recordNextEntry()
    {
        if ($this->list->count() < 2) {
            return;
        }

        $nextGroupID = $this->list->toArray()[1]['id'];
        $this->duty->groups()->updateExistingPivot($nextGroupID, ['last_worked' => Carbon::today()]);
    }

    public function getLastWorked()
    {
        $this->lastWorkedUser = $this->duty->groups()->orderBy('duty_group.last_worked', 'DESC')->first();

        return $this;
    }

    public function queryList()
    {
        $this->list = $this->duty->groups()->orderBy('name')->get();

        return $this;
    }

    /**
     * Take our list of groups and merge them with dates so that each group is assigned a duty date.
     *
     * @return DutyGroups
     */
    public function combineListWithDates()
    {
        $dates = (new DutyDates($this->duty))->getDates();
        $newDatesList = array_values(array_diff($dates, $this->swapDates->toArray()));
        $count = $this->list->count();
        $dateCounter = 0;

        for ($i = 0; $i < $count; $i++) {
            // Does list[i] already have date assigned? Is yes, skip assignment
            if (!empty($this->list[$i]['date'])) {
                continue;
            } else {
                $this->list[$i] = [
                    'group' => $this->list[$i]['group'],
                    'id'    => $this->list[$i]['id'],
                    'date'  => $newDatesList[$dateCounter++],
                ];
            }
        }
        $this->list = $this->list->sortBy('date');

        return $this;
    }

    /**
     * Query a list of groups who we swapped around and insert them into our current duty list of groups.
     */
    public function insertFromDutySwap()
    {
        $dutySwaps = DutySwap::where('duty_id', $this->duty->id)
            ->where('imageable_type', 'SET\Group')
            ->where('date', '>=', Carbon::now()->subDays(6)) //Omit really old records.
            ->orderBy('date', 'ASC')
            ->get();

        $this->convertListToCollection();
        $this->swapDates = new Collection();

        foreach ($dutySwaps as $swap) {
            $key = key($this->list->where('id', $swap->imageable_id)->toArray());
            if (!is_null($key)) {
                $this->swapDates->push($swap->date);
                $this->list[$key] = [
                    'group' => $swap->imageable()->first()->users()->active()->get(),
                    'id'    => $swap->imageable()->first()->id,
                    'date'  => $swap->date,
                ];
            }
        }

        return $this;
    }

    /**
     * Convert the list of groups into a collection of group users, group id and date.
     */
    private function convertListToCollection()
    {
        $count = $this->list->count();
        $newList = new Collection();
        for ($i = 0; $i < $count; $i++) {
            $newList->push([
                'group' => $this->list[$i]->users()->active()->get(),
                'id'    => $this->list[$i]->id,
                'date'  => '',
            ]);
        }
        $this->list = $newList;
    }

    /**
     * @param $entry
     *
     * @return string
     */
    private function buildHTMLUserRow($entry)
    {
        $row = '';
        foreach ($entry['group'] as $user) {
            if (Gate::allows('view')) {
                $row .= "<a href='".url('user', $user->id)."'>".$user->userFullName.'</a> & ';
            } else {
                $row .= $user->userFullName.' & ';
            }
        }
        $row = rtrim($row, '& ');

        return $row;
    }
}
