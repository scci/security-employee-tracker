<?php

namespace SET\Handlers\Duty;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;
use SET\Duty;
use SET\DutySwap;

class DutyUsers extends DutyHelper
{

    public function __construct(Duty $duty)
    {
        parent::__construct($duty);
    }

    /**
     * Generate a collection to be used for our view 'duty.show'
     * @return Collection
     */
    public function HTMLOutput()
    {
        $newCollection = new Collection();

        foreach ($this->list as $entry) {
            if (Gate::allows('view')) {
                $rowvalue = "<a href='" . url('user', $entry['user']->id) . "'>" . $entry['user']->userFullName . "</a>";
            } else {
                $rowvalue = $entry['user']->userFullName;
            }
            $newCollection->push([
                'row' => $rowvalue,
                'id' => $entry['user']->id,
                'date' => $entry['date']
            ]);
        }

        return $newCollection;
    }

    /**
     * Generate a collection to be used for emails. View is either emails.duty_future or emails.duty_today
     * @return \Illuminate\Support\Collection
     */
    public function emailOutput()
    {
        $collection = $this->list->map(function($value) {
            return [
                'users' => new Collection([$value['user']]),
                'date' => $value['date'],
            ];
        });

        return $collection;
    }

    /**
     * Get function for the list. List is stored on the helper class.
     * @return Collection
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Grab the next user to work the duty roster and record them in our database so they are the current worker.
     */
    public function recordNextEntry()
    {
        $nextUser = $this->list->toArray()[1]['user'];
        $this->duty->users()->updateExistingPivot($nextUser->id, ['last_worked' => Carbon::today()]);
    }

    /**
     * Get the current user in our database who is working the duty roster
     * @return DutyUsers
     */
    public function getLastWorked()
    {
        $this->lastWorked = $this->duty->users()->orderBy('duty_user.last_worked', 'DESC')->orderBy('last_name')->first();
        return $this;
    }


    /**
     * Get a list of all users for a specific duty sorted by the user's last name
     * @return DutyUsers
     */
    public function queryList()
    {
        $this->list = $this->duty->users()->orderBy('last_name')->get();
        return $this;
    }

    /**
     * Take our list of users and merge them with dates so that each user is assigned a duty date.
     * @return DutyUsers
     */
    public function combineListWithDates()
    {
        $dates = (new DutyDates($this->duty))->getDates();
        $count = $this->list->count();
        $newList = new Collection();

        for ($i = 0; $i < $count; $i++)
        {
            $newList->push([
                'date' => $dates[$i],
                'user' => $this->list[$i]
            ]);
        }
        $this->list = $newList;
        return $this;
    }

    /**
     * Query a list of users who we swapped around and insert them into our current duty list of users
     */
    public function insertFromDutySwap()
    {
        $dutySwaps = DutySwap::where('duty_id', $this->duty->id)
            ->where('imageable_type', 'SET\User')
            ->where('date', '>=', Carbon::now()->subMonth())  //Omit really old records.
            ->orderBy('date', 'ASC')
            ->get();

        foreach ($dutySwaps as $swap)
        {
            foreach ($this->list as $key => $entry)
            {
                if ($swap->date == $entry['date']) {
                    $this->list[$key] = [
                        'user' => $swap->imageable()->first(),
                        'date' => $entry['date']
                    ];
                }
            }
        }

        return $this;
    }
}
