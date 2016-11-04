<?php

namespace SET\Handlers\Calendar;

use Carbon\Carbon;
use SET\TrainingUser;
use SET\Travel;
use SET\User;

class Calendar
{
    private $start;
    private $end;
    private $calendarArray;
    private $test;

    /**
     * Name of function that generates a collection followed by an array of dates
     * we want to mark in our calendar/agenda.
     * @var array
     */
    private $lists = [
        'separatedList'     => ['destroyed_date'],
        'travelsList'       => ['leave_date', 'return_date'],
        'trainingUsersList' => ['due_date'],
        'newUserList'       => ['created_at']
    ];

    public function __construct()
    {
        $this->start = Carbon::today()->subWeeks(1);
        $this->end = Carbon::today()->addMonths(2);
        $this->calendarArray = [];

        $this->generateCalendarItems();
    }

    public function getCalendar()
    {
        return $this->calendarArray;
    }

    public function generateCalendarItems()
    {

        //Let's build that calendar.
        $date = $this->start;
        $i = 0;

        $list = $this->callFunctionsFromLookup();

        while ($date <= $this->end) {
            $currentDate = $date->format('Y-m-d');
            $this->test = false;

            foreach($this->lists as $functionName => $columns) {
                $list[$functionName] = $this->buildArrayByDate($list[$functionName], $columns, $currentDate);
            }

            if ($this->test || $currentDate == Carbon::today()->format('Y-m-d')) {
                $this->calendarArray[$i] = [
                    'date' => $currentDate,
                    'separated' => $list['separatedList'],
                    'travel' => $list['travelsList'],
                    'trainingUser' => $this->groupUsersForTraining($list['trainingUsersList']),
                    'newUser' => $list['newUserList']
                ];
            }

            $date->addDay();
            $i++;
        }
    }

    private function buildArrayByDate($list, array $columnName, $date)
    {
        $array = [];

        foreach ($list as $item) {
            foreach ($columnName as $column) {
                $dbDate = $this->testForCarbonObject($item[$column]);
                if ($date == $dbDate) {
                    $this->test = true;
                    array_push($array, $item);
                }
            }
        }

        return $array;
    }

    /**
     * If a carbon object, convert it to our date string. Otherwise leave it alone.
     *
     * @param date
     *
     * @return mixed
     */
    private function testForCarbonObject($date)
    {
        //check if format is YYYY-MM-DD
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
            return $date;
        } elseif (get_class($date) == 'Carbon\Carbon') {
            return $date->format('Y-m-d');
        } else {
            return null;
        }
    }

    private function groupUsersForTraining($trainingUsers)
    {
        $array = [];
        foreach ($trainingUsers as $key => $item) {
            $array[$item['training_id']][$key] = $item;
        }
        ksort($array, SORT_NUMERIC);

        foreach ($array as $training) {
            foreach ($training as $trainingUser) {
                $trainingUser['userLink'] = "<a href='".url('user', $trainingUser->user_id)."'>".$trainingUser->user->userFullName.'</a>';
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    private function callFunctionsFromLookup()
    {
        $array = [];
        foreach ($this->lists as $functionName => $columns) {
            $array[$functionName] = $this->$functionName();
        }
        return $array;
    }

    /**
     * @return mixed
     */
    private function separatedList()
    {
        return User::where(function ($q) {
            $q->where('status', 'separated')->orWhere('status', 'destroyed');
        })
            ->whereBetween('destroyed_date', [$this->start, $this->end])
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function travelsList()
    {
        return Travel::with('user')
            ->where(function ($query) {
                $query->whereBetween('leave_date', [$this->start, $this->end])
                    ->orWhereBetween('return_date', [$this->start, $this->end]);
            })
            ->get();
    }

    /**
     * @return mixed
     */
    private function trainingUsersList()
    {
        return TrainingUser::with('user', 'training')
            ->whereBetween('due_date', [$this->start, $this->end])
            ->whereNull('completed_date')
            ->orderBy('training_id')
            ->get();
    }

    private function newUserList()
    {
        return User::whereBetween('created_at', [$this->start, $this->end])->get();
    }

}
