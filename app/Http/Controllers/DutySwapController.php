<?php

namespace SET\Http\Controllers;

use Illuminate\Http\Request;

use SET\Duty;
use SET\DutySwap;
use SET\Handlers\Duty\DutyList;
use SET\Http\Requests;

class DutySwapController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $dates = explode(',', $data['date']);
        $IDs = explode(',', $data['id']);
        $type = 'SET\\' . $data['type'];
        $dutyID = intval($data['duty']);

        ( new DutyList($dutyID) )->processSwapRequest($dates, $IDs, $type);

        return redirect()->action('DutyController@show', $dutyID);

    }



}
