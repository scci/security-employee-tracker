<?php

namespace SET\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Krucas\Notification\Facades\Notification;
use SET\Attachment;
use SET\Events\TrainingAssigned;
use SET\Http\Requests\TrainingUserRequest;
use SET\Training;
use SET\TrainingUser;
use SET\User;

class TrainingUserController extends Controller
{
    public function create(User $user)
    {
        $this->authorize('edit');

        $training = Training::all()->pluck('name', 'id')->toArray();
        $disabled = '';

        return view('traininguser.create', compact('user', 'training', 'disabled'));
    }

    public function store(TrainingUserRequest $request, User $user)
    {
        $this->authorize('edit');

        $data = $request->all();
        $data['author_id'] = Auth::user()->id;
        $data['user_id'] = $user->id;

        $trainingUser = TrainingUser::create($data);

        if ($request->hasFile('files')) {
            Attachment::upload($trainingUser, $request->file('files'), $data['encrypt']);
        }

        Event::fire(new TrainingAssigned($trainingUser));

        Notification::container()->success('Training successfully assigned');

        return redirect()->action('UserController@show', $user->id);
    }

    public function show(User $user, $trainingUserID)
    {
        $trainingUser = TrainingUser::with('training')->findOrFail($trainingUserID);
        $this->authorize('show_user', $user);

        if (isset($trainingUser->completed_date)) {
            return redirect()->action('UserController@show', $user->id);
        }

        return view('traininguser.show', compact('trainingUser', 'user'));
    }

    public function edit(User $user, $trainingUserID)
    {
        $this->authorize('edit_training_user', $user);
        $training = Training::all()->pluck('name', 'id')->toArray();
        $trainingUser = TrainingUser::findOrFail($trainingUserID);

        //disable the due by field unless admin.
        $disabled = '';
        if (Gate::denies('edit')) {
            $disabled = 'disabled';
        }

        return view('traininguser.edit', compact('user', 'trainingUser', 'training', 'disabled'));
    }

    public function update(Request $request, User $user, $trainingUserID)
    {
        $this->authorize('edit_training_user', $user);
        $trainingUser = TrainingUser::findOrFail($trainingUserID);
        $data = $request->all();
        $trainingUser->update($data);

        if ($request->hasFile('files')) {
            Attachment::upload($trainingUser, $request->file('files'), $data['encrypt']);
        }

        Notification::container()->success('Training successfully updated');

        return redirect()->action('UserController@show', $user->id);
    }

    public function destroy($userID, $trainingUserID)
    {
        $this->authorize('edit');
        TrainingUser::findOrFail($trainingUserID)->delete();
        Storage::deleteDirectory('traininguser_'.$trainingUserID);

        return Redirect::back();
    }
}
