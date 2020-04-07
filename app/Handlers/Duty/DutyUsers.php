<?php

namespace SET\Handlers\Duty;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use SET\Duty;
use SET\DutySwap;

class DutyUsers extends DutyHelper
{
    public function __construct(Duty $duty)
    {
        parent::__construct($duty);
    }

    /**
     * Generate a collection to be used for our view 'duty.show'.
     *
     * @return Collection
     */
    public function htmlOutput()
    {
        $newCollection = new Collection();

        foreach ($this->list as $entry) {
            $rowvalue = $entry['user']->userFullName;

            if (Gate::allows('view')) {
                $rowvalue = "<a href='".url('user', $entry['user']->id)."'>".$entry['user']->userFullName.'</a>';
            }

            $newCollection->push([
                'row'  => $rowvalue,
                'id'   => $entry['user']->id,
                'date' => $entry['date'],
            ]);
        }

        return $newCollection;
    }

    /**
     * Generate a collection to be used for emails. View is either emails.duty_future or emails.duty_today.
     *
     * @return \Illuminate\Support\Collection
     */
    public function emailOutput()
    {
        $collection = $this->list->map(function ($value) {
            return [
                'users' => new Collection([$value['user']]),
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

    /**
     * Grab the next user to work the duty roster and record them in our database so they are the current worker.
     */
    public function recordNextEntry()
    {
        if ($this->list->count() < 2) {
            return;
        }

        $nextUser = $this->list->toArray()[1]['user'];
        $this->duty->users()->updateExistingPivot($nextUser->id, ['last_worked' => Carbon::today()]);
    }

    /**
     * Get the current user in our database who is working the duty roster.
     *
     * @return DutyUsers
     */
    public function getLastWorked()
    {
        $this->lastWorkedUser = $this->duty->users()->orderBy('duty_user.last_worked', 'DESC')->orderBy('last_name')->first();

        return $this;
    }

    /*
     * Add new duty user/group to the bottom of the duty list by
     * setting the last_worked field to the oldest date
     */
    public function setLastWorkedDate()
    {
        // Get the new duty_user just added
        $newDutyUserID = $this->duty->users()->whereNull('duty_user.last_worked')->orderBy('last_name', 'DESC')->pluck('id');

        // Add the new users to the bottom of the duty list by assiging their last_worked dates
        // to the most recent weeks. This means that the last_worked dates for the other users
        // will need to move further down(depending on number of users added). Do not change the
        // last_worked date for the user for the current week.
        if ($newDutyUserID->isNotEmpty()) {
            $newDutyUsersCount = $newDutyUserID->count();

            DB::table('duty_user')
                ->whereNotIn('user_id', [$this->lastWorkedUser->id, $newDutyUserID])
                ->update(['last_worked' => DB::raw('DATE_SUB(last_worked, INTERVAL '.$newDutyUsersCount.' WEEK)')]);

            $lastWorkedDate = new Carbon($this->lastWorkedUser->pivot->last_worked);

            // Assign last_worked dates to the new users instead of null
            foreach ($newDutyUserID as $newUserID) {
                $this->duty->users()->updateExistingPivot($newUserID, ['last_worked' => $lastWorkedDate->subDays($newDutyUsersCount--)]);
            }
        }

        return $this;
    }

    /**
     * Get a list of all users for a specific duty sorted by the user's last_worked date.
     *
     * @return DutyUsers
     */
    public function queryList()
    {
        $this->list = $this->duty->users()->active()->orderBy('duty_user.last_worked', 'ASC')->get();

        return $this;
    }

    /**
     * Take our list of users and merge them with dates so that each user is assigned a duty date.
     *
     * @return DutyUsers
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
                    'date' => $newDatesList[$dateCounter++],
                    'user' => $this->list[$i]['user'],
                ];
            }
        }
        $this->list = $this->list->sortBy('date');

        return $this;
    }

    /**
     * Query a list of users who we swapped around and insert them into our current duty list of users.
     */
    public function insertFromDutySwap()
    {
        $dutySwaps = DutySwap::where('duty_id', $this->duty->id)
            ->where('imageable_type', 'SET\User')
            ->where('date', '>=', Carbon::now()->subDays(6)) //Omit really old records.
            ->orderBy('date', 'ASC')
            ->get();

        $this->convertListToCollection();

        $this->swapDates = new Collection();
        foreach ($dutySwaps as $swap) {
            $key = key($this->list->pluck('user')->where('id', $swap->imageable_id)->toArray());
            if (!is_null($key)) {
                $this->swapDates->push($swap->date);
                $this->list[$key] = [
                        'date' => $swap->date,
                        'user' => $swap->imageable()->first(),
                ];
            }
        }

        return $this;
    }

    /**
     * Convert the list of users into a collection of date and users.
     */
    private function convertListToCollection()
    {
        $count = $this->list->count();
        $newList = new Collection();
        for ($i = 0; $i < $count; $i++) {
            $newList->push([
                'date' => '',
                'user' => $this->list[$i],
            ]);
        }
        $this->list = $newList;
    }
}
