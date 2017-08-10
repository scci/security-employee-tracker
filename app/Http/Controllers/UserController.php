<?php

namespace SET\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Krucas\Notification\Facades\Notification;
use SET\Duty;
use SET\Group;
use SET\Handlers\Excel\JpasImport;
use SET\Http\Requests\StoreUserRequest;
use SET\Training;
use SET\User;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->authorize('view');

        $users = User::with([
            'assignedTrainings' => function ($q) {
                $q->whereNull('completed_date')
                    ->whereBetween('due_date', [Carbon::now()->subYear(), Carbon::now()->addWeeks(4)]);
            },
            'trainings',
        ])
            ->skipSystem()
            ->orderBy('last_name')->get();

        return view('user.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('edit');

        $supervisors = User::skipSystem()->active()->orderBy('last_name')->get()->pluck('userFullName', 'id')->toArray();
        $groups = Group::all();

        return view('user.create', compact('supervisors', 'groups'));
    }

    /**
     * @param StoreUserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->all();
        $data['status'] = 'active';
        $user = User::create($data);

        if (array_key_exists('groups', $data)) {
            settype($data['groups'], 'array');
            $user->groups()->sync($data['groups']);
        }

        return redirect()->action('UserController@index');
    }

    /**
     * @param $userId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($userId, $sectionId = null)
    {
        $user = User::with(['subordinates' => function ($query) {
            $query->active();
        },
                            'groups', 'duties', 'attachments',
                            'visits', 'notes.author', 'notes.attachments',
                            'travels.author', 'travels.attachments', ])
                    ->findOrFail($userId);

        //Make sure the user can't access other people's pages.
        $this->authorize('show_user', $user);

        $user['clearance'] = $this->spellOutClearance($user['clearance']);
        $user['access_level'] = $this->spellOutClearance($user['access_level']);

        $trainings = $this->getUserTrainings($user, $sectionId);
        $user_training_types = $this->getUserTrainingTypes($trainings);
        $training_user_types = $user_training_types[0]; // List of the user's training types
        $training_blocks = $user_training_types[1]; // List of training block titles for user

        $activityLog = [];
        if (Gate::allows('view')) {
            $activityLog = $user->getUserLog($user);
        }

        $this->previousAndNextUsers($user, $previous, $next);

        //This mess is just so that we can output the Security Check list or show none. Mainly just to show none.
        $duties = Duty::whereHas('users', function ($q) use ($userId) {
            $q->where('id', $userId);
        })->orWhereHas('groups.users', function ($q) use ($userId) {
            $q->where('id', $userId);
        })->get();

        return view('user.show', compact('user', 'duties', 'previous', 'next',
            'trainings', 'activityLog', 'training_blocks', 'training_user_types'));
    }

    public function edit(User $user)
    {
        $this->authorize('edit');

        $supervisors = User::skipSystem()->active()->orderBy('last_name')->get()->pluck('userFullName', 'id')->toArray();
        $groups = Group::all();

        return view('user.edit', compact('user', 'supervisors', 'groups'));
    }

    public function update(User $user)
    {
        $this->authorize('edit');

        $data = Input::all();

        $data['destroyed_date'] = $user->getDestroyDate($data['status']);

        $user->update($data);

        //Handle user groups
        if (!array_key_exists('groups', $data)) {
            $data['groups'] = [];
        }
        $user->groups()->sync($data['groups']);

        //Handled closed area access (MUST come AFTER syncing groups).
        if (array_key_exists('access', $data)) {
            foreach ($data['access'] as $group_id => $accessLevel) {
                $user->groups()->updateExistingPivot($group_id, ['access' => $accessLevel]);
            }
        }

        return redirect()->action('UserController@show', $user->id);
    }

    /**
     * @param $userId
     *
     * @return string
     */
    public function destroy($userId)
    {
        $this->authorize('edit');

        Storage::deleteDirectory('user_'.$userId);
        User::findOrFail($userId)->delete();

        return 'success';
    }

    /**
     * Get all the scheduled trainings for the user and either the most recent completed
     * trainings or all completed trainings for the desired training type based on data values.
     *
     * @param $user
     * @param $data
     *
     * @return array trainings
     */
    private function getUserTrainings($user, $sectionId)
    {
        // Get the scheduled trainings and the completed trainings separately, so it will be easier to get either
        // the full list of completed trainings or the most recently completed training for each training type.
        $scheduledTrainings = $user->assignedTrainings()->with('author', 'training.attachments', 'attachments')
                                ->whereNull('completed_date');
                
        // Now fetch the recently completed training records for the specified  user
        $recentCompletedTrainings = DB::table('training_user as t1')
                                    ->where('id', function($query) use ($user) {
                                        $query->from('training_user as t2')
                                              ->selectRaw('t2.id')
                                              ->where('t2.user_id', '=', $user->id)
                                              ->whereRaw('t1.training_id = t2.training_id')
                                              ->orderBy('completed_date', 'DESC')->limit(1);
                                    });

        // If the user clicks the showAll button for a particular type of training,
        // show all the trainings completed by that user for that training type.
        if ($sectionId != null) {
            $selectedTraining = Training::trainingByType($sectionId)->get()->pluck('id')->toArray();

            $completedTrainings = $user->assignedTrainings()->with('author', 'training.attachments', 'attachments')
                                    ->whereIn('training_id', $selectedTraining);

            $trainings = $scheduledTrainings->union($completedTrainings)->union($recentCompletedTrainings)
                                        ->orderBy('completed_date', 'DESC')->get();
        } else {
            $trainings = $scheduledTrainings->union($recentCompletedTrainings)->orderBy('completed_date', 'DESC')->get();
        }

        return $trainings;
    }

    /**
     * @param $trainings[]
     * From the User's trainings, a list of the training types is determined and
     * a list of the training block titles is determined.
     *
     * @return user_training_types[], training_block_titles[]
     */
    public function getUserTrainingTypes($trainings = [])
    {
        $training_block_titles = $user_training_types = [];
        foreach ($trainings as $trainingUser) {
            if (is_null($trainingUser->completed_date)) {
                $training_block_titles['AAA'] = 'Scheduled';
                $user_training_types[$trainingUser->id] = 'Scheduled';
            } elseif ($trainingUser->Training->trainingType) {
                $typeName = $trainingUser->Training->trainingType->name;
                $training_block_titles[$typeName] = $typeName;
                $user_training_types[$trainingUser->id] = $typeName;
            } else { // No training type
                $training_block_titles['999'] = 'Miscellaneous';
                $user_training_types[$trainingUser->id] = 'Miscellaneous';
            }
        }
        ksort($training_block_titles);  // Order by key
        return [$user_training_types, $training_block_titles];
    }

    /**
     * Process our JPAS import. Once that has been handled, we pass the file, changes,
     * unique/unmapped users & a user list to the user.import view.
     * That way we keep all this data for the resolve phase.
     *
     * @param JpasImport $import
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import(JpasImport $import)
    {
        $results = $import->handleImport();
        $uploadedFile = $import->getFile('file');

        $changes = $results['changes'];
        $unique = $results['unique'];

        $userList = User::orderBy('last_name')->get()->pluck('UserFullName', 'id')->toArray();

        return view('user.import', compact('unique', 'changes', 'userList', 'uploadedFile'));
    }

    /**
     * @param JpasImport $import
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolveImport(JpasImport $import)
    {
        $import->handleImport();

        Notification::container()->success('Import Complete');

        File::delete($import->getFile('file'));

        return redirect()->action('HomeController@index');
    }

    /**
     * Generate the grab the previous and next user if our users are sorted alphabetically.
     *
     * @param $user
     * @param $previous
     * @param $next
     */
    private function previousAndNextUsers($user, &$previous, &$next)
    {
        //Build the previous/next user that are in alphabetical order.
        $users = User::skipSystem()->orderBy('last_name')->orderBy('first_name')->get();
        $previous = null; // set to null by default in case we are at the start of the list.
        while ($users->first()->id != $user->id) {
            $previous = $users->shift()->id;
        }
        //check if we have a record aft the current user. If not, then we are at the end.
        if ($users->count() > 1) {
            $users->shift();
            $next = $users->shift()->id;
        } else {
            $next = null;
        }
    }

    /**
     * @param $clearance
     *
     * @return mixed
     */
    private function spellOutClearance($clearance)
    {
        //fully spell out user's clearance.
        switch ($clearance) {
            case 'S':
                $clearance = 'Secret';
                break;
            case 'TS':
                $clearance = 'Top Secret';
                break;
            case 'Int S':
                $clearance = 'Interim Secret';
                break;
        }

        return $clearance;
    }
}
