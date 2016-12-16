<?php

namespace SET\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Krucas\Notification\Facades\Notification;
use SET\Http\Requests\VisitRequest;
use SET\User;
use SET\Visit;

class VisitController extends Controller
{
    public function create(User $user)
    {
        $this->authorize('edit');

        return view('visit.create', compact('user'));
    }

    public function store(VisitRequest $request, User $user)
    {
        $data = $request->all();
        $data['author_id'] = Auth::user()->id;

        $user->visits()->create($data);

        Notification::container()->success('Visit successfully created');

        return redirect()->action('UserController@show', $user->id);
    }

    public function edit(User $user, Visit $visit)
    {
        $this->authorize('edit');

        return view('visit.edit', compact('user', 'visit'));
    }

    public function update(VisitRequest $request, User $user, Visit $visit)
    {
        $visit->update($request->all());

        Notification::container()->success('Visit successfully updated');

        return redirect()->action('UserController@show', $user->id);
    }

    public function destroy($userID, Visit $visit)
    {
        $this->authorize('edit');
        $visit->delete();

        return redirect()->action('UserController@show', $userID);
    }
}
