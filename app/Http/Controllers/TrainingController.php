<?php

namespace SET\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Krucas\Notification\Facades\Notification;
use SET\Attachment;
use SET\Events\TrainingAssigned;
use SET\Group;
use SET\Handlers\Excel\CompletedTrainingExportHandler;
use Maatwebsite\Excel\Facades\Excel;
use SET\Http\Requests\AssignTrainingRequest;
use SET\Http\Requests\StoreTrainingRequest;
use SET\Training;
use SET\TrainingType;
use SET\TrainingUser;
use SET\User;

/**
 * Class TrainingController.
 */
class TrainingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $trainingTypeID = null)
    {
        $this->authorize('view');

        $hasTrainingType = TrainingType::count() > 0;
        $isTrainingType = (strpos($request->path(), '/trainingtype/'));

        if ($trainingTypeID) {
            $trainings = Training::with(['users' => function ($q) {
                $q->active();
            }])
                          ->where('training_type_id', $trainingTypeID)
                          ->get()
                          ->sortBy('name');
        } else {
            $trainings = Training::with(['users' => function ($q) {
                $q->active();
            }])->get()->sortBy('name');
        }

        return view('training.index', compact('trainings', 'isTrainingType', 'hasTrainingType'));
    }

    public function create()
    {
        $this->authorize('edit');

        $users = User::skipSystem()->active()->orderBy('last_name')->get()->pluck('UserFullName', 'id');
        $groups = Group::orderBy('name')->get()->pluck('name', 'id');
        $training_types = TrainingType::whereStatus(true)->orderBy('name')->get()->pluck('name', 'id');

        return view('training.create', compact('users', 'groups', 'training_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTrainingRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTrainingRequest $request)
    {
        $data = $request->all();
        $training = Training::create($data);

        if ($request->hasFile('files')) {
            Attachment::upload($training, $request->file('files'));
        }

        $data['training_id'] = $training->id;

        $this->createTrainingNotes($data);

        Notification::container()->success('Training Created');

        return redirect()->action('TrainingController@index');
    }

    /**
     * Show the individual training record.
     *
     * @param $trainingId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($trainingId)
    {
        $this->authorize('view');
        $showAll = Input::get('showAll');

        $training = Training::with('attachments')->find($trainingId);
        $notes = $training->assignedUsers()
            ->with('user')
            ->orderBy(DB::raw('CASE WHEN completed_date IS NULL THEN 0 ELSE 1 END'))
            ->orderBy('completed_date', 'desc')
            ->get();
        if (!$showAll) {
            $notes = $notes->unique('user_id')->where('user.status', 'active');
        }

        return view('training.show', compact('notes', 'training', 'showAll'));
    }

    public function edit(Training $training)
    {
        $this->authorize('edit');

        $users = User::skipSystem()->active()->orderBy('last_name')->get()->pluck('UserFullName', 'id');
        $groups = Group::orderBy('name')->get()->pluck('name', 'id');
        $training_types = TrainingType::whereStatus(true)->orderBy('name')->get()->pluck('name', 'id');

        return view('training.edit', compact('training', 'users', 'groups', 'training_types'));
    }

    public function update(Request $request, Training $training)
    {
        $this->authorize('edit');

        $data = $request->all();
        $training->update($data);
        if ($request->hasFile('files')) {
            Attachment::upload($training, $request->file('files'));
        }

        $data['training_id'] = $training->id;
        $this->createTrainingNotes($data);

        Notification::container()->success('Training Updated');

        return redirect()->action('TrainingController@show', $training->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $trainingId
     */
    public function destroy($trainingId)
    {
        $this->authorize('edit');
        Training::findOrFail($trainingId)->delete();
    }

    public function assignForm($trainingID)
    {
        $this->authorize('edit');

        $training = Training::findOrFail($trainingID);
        $users = User::skipSystem()->active()->orderBy('last_name')->get()->pluck('UserFullName', 'id');
        $groups = Group::orderBy('name')->get()->pluck('name', 'id');

        return view('training.assign_user', compact('users', 'groups', 'training'));
    }

    /**
     * Assign our users to training.
     *
     * @param AssignTrainingRequest $request
     * @param int                   $trainingID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assign(AssignTrainingRequest $request, $trainingID)
    {
        $this->authorize('edit');

        $data = $request->all();
        $data['training_id'] = $trainingID;

        $this->createTrainingNotes($data);

        Notification::container()->success('Training was assigned to the user(s).');

        return redirect()->action('TrainingController@show', $trainingID);
    }

    /**
     * Generate Excel file with user/training table with date of completion.
     *
     * Calls to \SET\app\Handlers\Excel\
     *
     * @param CompletedTrainingExport $export
     *
     * @return mixed
     */
    public function showCompleted()
    {
        $this->authorize('view');
        return Excel::download(new CompletedTrainingExportHandler, 'CompletedTraining.xlsx');
    }

    /**
     * Send out a reminder that the training is due.
     *
     * @param $trainingUserId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendReminder($trainingUserId)
    {
        $trainingUser = TrainingUser::with('user')->find($trainingUserId);
        Event::fire(new TrainingAssigned($trainingUser));
        Notification::container()->success('Reminder sent to '.$trainingUser->user->userFullName);

        return redirect()->back();
    }

    /***************************/
    /* BEGIN PRIVATE FUNCTIONS */
    /***************************/

    /**
     * Get a list of users and create training notes from that list.
     *
     * @param $data
     */
    private function createTrainingNotes($data)
    {
        $data['author_id'] = Auth::user()->id;

        $users = [];

        //If we have groups, let's get the user ids.
        if (isset($data['groups'])) {
            $groupUsers = User::whereHas('groups', function ($q) use ($data) {
                $q->where('id', $data['groups']);
            })->get();

            //get the user ids from our groups
            foreach ($groupUsers as $user) {
                array_push($users, $user->id);
            }
        }

        //pull out our user ids.
        if (isset($data['users'])) {
            $users = array_merge($users, $data['users']);
        }

        //create records for each unique
        foreach (array_unique($users) as $user) {
            $data['user_id'] = $user;
            $note = TrainingUser::create($data);
            Event::fire(new TrainingAssigned($note));
        }
    }
}
