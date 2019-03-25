<?php

namespace SET\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
        $this->authorize('edit');

        $data = $request->all();
        $data['author_id'] = Auth::user()->id;

        $travel = $user->travels()->create($data);

        if ($request->hasFile('files')) {
            Attachment::upload($travel, $request->file('files'), $data['encrypt']);
        }

        return redirect()->action('UserController@show', $user->id)->with('status', 'Travel successfully created');
    }

    public function edit(User $user, Travel $travel)
    {
        $this->authorize('edit_training_user', $user);

        return view('travel.edit', compact('user', 'travel'));
    }

    public function update(TravelRequest $request, User $user, Travel $travel)
    {
        $this->authorize('edit_training_user', $user);
        $data = $request->all();
        $travel->update($data);

        if ($request->hasFile('files')) {
            Attachment::upload($travel, $request->file('files'), $data['encrypt']);
        }

        return redirect()->action('UserController@show', $user->id)->with('status', 'Travel successfully updated');
    }

    public function destroy($userID, $travelID)
    {
        $this->authorize('edit');
        Travel::findOrFail($travelID)->delete();
        Storage::deleteDirectory('travel_'.$travelID);

        return Redirect::back();
    }
}
