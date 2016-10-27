<?php

namespace SET\Http\Controllers;

use Illuminate\Http\Request;

use SET\Handlers\Duty\DutyList;

class DutySwapController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $dates = explode(',', $data['date']);
        $IDs = explode(',', $data['id']);
        $type = 'SET\\' . $data['type'];
        $dutyID = intval($data['duty']);

        (new DutyList($dutyID))->processSwapRequest($dates, $IDs, $type);

        return redirect()->action('DutyController@show', $dutyID);

    }



}
