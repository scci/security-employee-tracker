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

    public function __construct()
    {
        $this->start = Carbon::today()->subWeeks(1);
        $this->end =  Carbon::today()->addMonths(2);
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
        $i = 0;

        $separatedList = $this->separatedList();
        $travelList = $this->travelsList();
        $trainingUsersList = $this->trainingUsersList();
        $newUserList = $this->newUserList();

        while ($date <= $this->end) {

            $currentDate = $date->format('Y-m-d');

            $separatedArray = $this->pushToArray($separatedList, ['destroyed_date'], $currentDate);
            $travelsArray = $this->pushToArray($travelList, ['leave_date','return_date'], $currentDate);
            $trainingUsersArray = $this->pushToArray($trainingUsersList, ['due_date'], $currentDate);
            $newUserArray = $this->pushToArray($newUserList, ['created_at'], $currentDate);
            $trainingUsersArray = $this->groupUsersForTraining($trainingUsersArray);

            if ( (!empty($separatedArray) || !empty($travelsArray) || !empty($trainingUsersArray) || !empty($newUserArray)) || $currentDate == Carbon::today()->format('Y-m-d') ) {
                $this->calendarArray[$i]['date'] = $currentDate;
                $this->calendarArray[$i]['separated'] = $separatedArray;
                $this->calendarArray[$i]['travel'] = $travelsArray;
                $this->calendarArray[$i]['trainingUser'] = $trainingUsersArray;
                $this->calendarArray[$i]['newUser'] = $newUserArray;
            }

            $date->addDay();
            $i++;
        }
    }

    private function pushToArray($list, array $columnName, $date) {
        $array = [];



        foreach($list as $item) {
            foreach ($columnName as $column) {
                $dbDate = $this->testForCarbonObject($item[$column]);
                if ($date == $dbDate) {
                    array_push($array, $item);
                }
            }
        }
        return $array;
    }

    /**
     * If a carbon object, convert it to our date string. Otherwise leave it alone.
     * @param date
     * @return mixed
     */
    private function testForCarbonObject($date)
    {
        //check if format is YYYY-MM-DD
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
            return $date;
        }else if (get_class($date) == 'Carbon\Carbon') {
            //dd($date->format('Y-m-d'));
            return $date->format('Y-m-d');
        }else {
            return "Something went horribly wrong with testForCarbonObject.";
        }
    }

    private function groupUsersForTraining($trainingUsers)
    {
        $array = array();
        foreach($trainingUsers as $key => $item) {
            $array[$item['training_id']][$key] = $item;
        }
        ksort($array, SORT_NUMERIC);

        foreach($array as $training) {
            foreach($training as $trainingUser) {
                $trainingUser['userLink'] = "<a href='" . url('user', $trainingUser->user_id) ."'>". $trainingUser->user->userFullName ."</a>";
            }
        }

        return $array;
    }

    /**
     * @return mixed
     */
    private function separatedList()
    {
        return User::where( function ($q) {
                $q->where('status', 'separated')->orWhere('status', 'destroyed');
            })
            ->whereBetween('destroyed_date', [$this->start,$this->end])
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