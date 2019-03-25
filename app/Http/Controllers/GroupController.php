<?php

namespace SET\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use SET\Events\TrainingAssigned;
use SET\Group;
use SET\Http\Requests\GroupRequest;
use SET\Training;
use SET\TrainingUser;
use SET\User;

/**
 * Class GroupController.
 */
class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view');

        $groups = Group::with([
            'users' => function ($q) {
                $q->orderBy('last_name');
            },
            'trainings',
        ])->get()->sortBy('name');

        return view('group.index', compact('groups'));
    }

    public function create()
    {
        $this->authorize('edit');

        $training = Training::orderBy('name')->get()->pluck('name', 'id')->toArray();
        $users = User::skipSystem()->active()->orderBy('last_name')->get()->pluck('UserFullName', 'id')->toArray();
        $selectedTraining = $selectedUsers = [];

        return view('group.create', compact('users', 'training', 'selectedTraining', 'selectedUsers'));
    }

    /**
     * @param GroupRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GroupRequest $request)
    {
        $group = Group::create($request->all());
        $group->trainings()->attach($request->input('trainings'));
        $group->users()->attach($request->input('users'));

        $this->assignTraining($group, $request->input('users'));

        return redirect()->action('GroupController@index');
    }

    public function edit($groupId)
    {
        $this->authorize('edit');

        $group = Group::findOrFail($groupId);
        $training = Training::orderBy('name')->get()->pluck('name', 'id')->toArray();
        $users = User::skipSystem()->active()->orderBy('last_name')->get()->pluck('UserFullName', 'id')->toArray();
        $selectedTraining = $group->trainings()->pluck('id')->toArray();
        $selectedUsers = $group->users()->pluck('id')->toArray();

        return view('group.edit', compact('group', 'users', 'training', 'selectedTraining', 'selectedUsers'));
    }

    /**
     * @param GroupRequest $request
     * @param $groupId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GroupRequest $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        $group->update($request->all());

        //If we don't get anything back, sync as an empty array.
        $group->users()->sync($request->input('users') ?: []);
        $group->trainings()->sync($request->input('trainings') ?: []);

        $this->assignTraining($group, $request->input('users'));

        return redirect()->action('GroupController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $groupId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($groupId)
    {
        $this->authorize('edit');
        Group::find($groupId)->delete();
    }

    /**
     * Get the users associated with a group.
     * Used for ajax/js calls so we can populate a user select field based on group select field.
     *
     * @return mixed
     */
    public function getUserIDs()
    {
        $request = Input::all();
        if (empty($request['groups'])) {
            return 'nothing sent.';
        }

        return User::whereHas('groups', function ($query) use ($request) {
            $query->whereIn('id', $request['groups']);
        })->pluck('id');
    }

    /**
     * Cycle through each user & determine what training is missing.
     * Then assign said training to be due in 1 month.
     *
     * Called via store & update methods above.
     *
     * @param $group - Collection
     * @param $users - array
     */
    public function assignTraining($group, $users)
    {
        //If users is empty, then we don't need to do any more.
        if ($users === null) {
            return;
        }

        //We need to take the group and get all the training records so we can assign them to users.
        $groupList = $group->trainings()->get();
        $data = [
            'author_id' => Auth::user()->id,
            'type'      => 'training',
            'due_date'  => Carbon::now()->AddWeeks(2)->format('Y-m-d'),
        ];

        // Cycle through each user & figure what training they are missing.
        foreach ($users as $userID) {
            $data['user_id'] = $userID;
            $userList = User::find($userID)->trainings()->get();
            $trainingList = $groupList->diff($userList)->pluck('id');

            // Assign the missing training to the user & send them an email.
            foreach ($trainingList as $trainingId) {
                $data['training_id'] = $trainingId;
                $trainingUser = TrainingUser::create($data);

                Event::dispatch(new TrainingAssigned($trainingUser));
            }
        }
    }
}
