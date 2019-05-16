<?php

namespace SET\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use SET\Duty;
use SET\Handlers\Calendar\Calendar;
use SET\Handlers\Duty\DutyGroups;
use SET\Handlers\Duty\DutyUsers;
use SET\Training;
use SET\TrainingUser;
use SET\User;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     * Return notes with due dates 4 weeks from now.
     * Return last 20 recent notes.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        //if you don't have view ability, redirect to your own page.
        if (Gate::denies('view')) {
            return redirect()->action('UserController@show', Auth::user()->id);
        }

        $trainingUser = TrainingUser::with('user', 'training')
            ->where('completed_date', '>=', Carbon::today()->subWeek(1))
            ->orderBy('updated_at', 'DESC')
            ->get();

        $activityLog = (new User())->getUserLog();

        $calendar = (new Calendar())->getCalendar();

        $duties = $this->getDuties();

        return view('home.index', compact('trainingUser', 'activityLog', 'calendar', 'duties'));
    }

    /**
     * Return a list of users & trainings that will be used for our ajax search bar in the headers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        $this->authorize('view');
        $qInput = Request::input('q');
        $status = true;
        $dbUsers = User::skipSystem()->searchUsers($qInput)
            ->get(['id', 'first_name', 'last_name', 'status', 'emp_num']);
        $dbTraining = Training::searchTraining($qInput)->get(['id', 'name']);

        // Means no result were found
        if (count($dbUsers) <= 0 && count($dbTraining) <= 0) {
            $status = false;
        }

        return response()->json([
            'status' => $status,
            'error'  => null,
            'data'   => [
                'user'     => $dbUsers,
                'training' => $dbTraining,
            ],
        ]);
    }

    private function getDuties()
    {
        $newCollection = new Collection();
        $allDuties = Duty::all();

        foreach ($allDuties as $duty) {
            if ($duty->has_groups) {
                $userList = (new DutyGroups($duty))->getList()->first()['group'];
                $groupUsers = $this->getHtmlUserOutput($userList);
                $newCollection->push([
                    'duty'  => $duty->name,
                    'user'  => implode('; ', $groupUsers),
               ]);
            } else {
                $user = (new DutyUsers($duty))->getList()->first()['user'];
                $newCollection->push([
                    'duty'  => $duty->name,
                    'user'  => "<a href='".url('user', $user->id)."'>".$user->userFullName.'</a>',
                ]);
            }
        }

        return $newCollection;
    }

    private function getHtmlUserOutput($userList)
    {
        foreach ($userList as $user) {
            $groupUsers[] = "<a href='".url('user', $user->id)."'>".$user->userFullName.'</a>';
        }

        return $groupUsers;
    }
}
