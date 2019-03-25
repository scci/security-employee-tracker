<?php

namespace SET\Http\Controllers;

use SET\Duty;
use SET\Group;
use SET\Handlers\Duty\DutyList;
use SET\Http\Requests\DutyRequest;
use SET\User;

class DutyController extends Controller
{
    public function index()
    {
        $duties = Duty::all();

        return view('duty.index', compact('duties'));
    }

    public function create()
    {
        $this->authorize('edit');

        $users = User::active()->skipSystem()->get()->sortBy('userFullName')->pluck('userFullName', 'id');
        $groups = Group::all()->pluck('name', 'id');

        return view('duty.create', compact('users', 'groups'));
    }

    public function store(DutyRequest $request)
    {
        $this->authorize('edit');
        $data = $request->all();
        $duty = Duty::create($data);

        isset($data['groups'])
            ? $duty->groups()->attach($data['groups'])
            : $duty->users()->attach($data['users']);

        return redirect()->action('DutyController@index');
    }

    public function show($dutyID)
    {
        $duty = Duty::findOrFail($dutyID);
        $list = (new DutyList($duty))->htmlOutput();

        return view('duty.show', compact('duty', 'list'));
    }

    public function edit($dutyID)
    {
        $this->authorize('edit');

        $duty = Duty::with('users', 'groups')->findOrFail($dutyID);
        $users = User::active()->skipSystem()->orderBy('last_name', 'ASC')->get()->pluck('userFullName', 'id');
        $groups = Group::orderBy('name', 'ASC')->get()->pluck('name', 'id');

        return view('duty.edit', compact('duty', 'users', 'groups'));
    }

    public function update(DutyRequest $request, $dutyID)
    {
        $data = $request->all();
        $duty = Duty::findOrFail($dutyID);
        $duty->update($data);

        isset($data['groups'])
            ? $duty->groups()->sync($data['groups'])
            : $duty->users()->sync($data['users']);

        return redirect()->action('DutyController@index');
    }

    public function destroy(Duty $duty)
    {
        $this->authorize('edit');

        $duty->has_groups ? $duty->groups()->detach() : $duty->users()->detach();

        $duty->delete();

        return redirect()->action('DutyController@index');
    }
}
