<?php

namespace SET\Handlers\Calendar;

use Carbon\Carbon;
use SET\Handlers\DateFormat;
use SET\TrainingUser;
use SET\Travel;
use SET\User;

class Calendar
{
    use DateFormat;

    private $start;
    private $end;
    private $calendarArray;
    private $test;

    /**
     * Name of function that generates a collection followed by an array of dates
     * we want to mark in our calendar/agenda.
     *
     * @var array
     */
    private $lists = [
        Separated::class     => ['destroyed_date'],
        Travels::class       => ['leave_date', 'return_date'],
        TrainingUsers::class => ['due_date'],
        NewUser::class       => ['created_at'],
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

    private function generateCalendarItems()
    {

        //Let's build that calendar.
        $date = $this->start;
        $iterator = 0;

        $list = $this->callClass();

        while ($date <= $this->end) {
            $currentDate = $date->format('Y-m-d');
            $this->test = false;
            $list2 = [];

            foreach ($this->lists as $functionName => $columns) {
                $list2[$functionName] = $this->buildArrayByDate($list[$functionName], $columns, $currentDate);
            }

            if ($this->test || $currentDate == Carbon::today()->format('Y-m-d')) {
                $this->calendarArray[$iterator] = [
                    'date'         => $currentDate,
                    'separated'    => $list2[Separated::class],
                    'travel'       => $list2[Travels::class],
                    'trainingUser' => $this->groupUsersForTraining($list2[TrainingUsers::class]),
                    'newUser'      => $list2[NewUser::class],
                ];
            }

            $date->addDay();
            $iterator++;
        }
    }

    private function buildArrayByDate($list, array $columnName, $date)
    {
        $array = [];

        foreach ($list as $item) {
            foreach ($columnName as $column) {
                $dbDate = $this->dateFormat($item[$column]);
                if ($date == $dbDate) {
                    $this->test = true;
                    array_push($array, $item);
                }
            }
        }

        return $array;
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
    private function callClass()
    {
        $array = [];
        foreach ($this->lists as $class => $columns) {
            $array[$class] = (new $class($this->start, $this->end))->getList();
        }

        return $array;
    }
}
