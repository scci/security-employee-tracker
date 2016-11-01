<?php

namespace SET\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Krucas\Notification\Facades\Notification;
use SET\Attachment;
use SET\Http\Requests\TravelRequest;
use SET\Travel;
use SET\User;

class TravelController extends Controller
{
    public function create(User $user)
    {
        $this->authorize('edit');

        return view('travel.create', compact('user'));
    }

    public function store(TravelRequest $request, User $user)
    {
        $data = $request->all();
        $data['author_id'] = Auth::user()->id;

        $travel = $user->travels()->create($data);

        if ($request->hasFile('files')) {
            Attachment::upload($travel, $request->file('files'), $data['encrypt']);
        }

        Notification::container()->success('Travel successfully created');

        return redirect()->action('UserController@show', $user->id);
    }

    public function edit(User $user, Travel $travel)
    {
        $this->authorize('edit');

        return view('travel.edit', compact('user', 'travel'));
    }

    public function update(TravelRequest $request, User $user, Travel $travel)
    {
        $data = $request->all();
        $travel->update($data);

        if ($request->hasFile('files')) {
            Attachment::upload($travel, $request->file('files'), $data['encrypt']);
        }

        Notification::container()->success('Travel successfully updated');

        return redirect()->action('UserController@show', $user->id);
    }

    public function destroy($userID, $travelID)
    {
        Travel::findOrFail($travelID)->delete();
        Storage::deleteDirectory('travel_'.$travelID);

        return Redirect::back();
    }
}
