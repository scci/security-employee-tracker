<?php

namespace SET\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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

        return redirect()->action('UserController@show', $user->id)->with('status', 'Visit successfully created');
    }

    public function edit(User $user, Visit $visit)
    {
        $this->authorize('edit');

        return view('visit.edit', compact('user', 'visit'));
    }

    public function update(VisitRequest $request, User $user, Visit $visit)
    {
        $visit->update($request->all());

        return redirect()->action('UserController@show', $user->id)->with('status', 'Visit successfully updated');
    }

    public function destroy($userID, Visit $visit)
    {
        $this->authorize('edit');
        $visit->delete();

        return redirect()->action('UserController@show', $userID);
    }
}
