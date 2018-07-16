<?php

namespace SET\Http\Controllers;

use SET\Http\Requests\DutySwapRequest;
use SET\Handlers\Duty\DutyList;

class DutySwapController extends Controller
{
    public function store(DutySwapRequest $request)
    {
        $this->authorize('edit');

        $data = $request->all();
        $dates = explode(',', $data['date']);
        $IDs = explode(',', $data['id']);
        $type = 'SET\\'.$data['type'];
        $dutyID = intval($data['duty']);

        (new DutyList($dutyID))->processSwapRequest($dates, $IDs, $type);

        return redirect()->action('DutyController@show', ['id' => $dutyID]);
    }
}
