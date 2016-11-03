<?php

namespace SET\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use SET\Duty;
use SET\Handlers\Calendar\Calendar;
use SET\Log;
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

        $log = Log::with('user')
            ->where('updated_at', '>=', Carbon::today()->subWeek(1))
            ->orderBy('updated_at', 'DESC')
            ->get();

        $calendar = (new Calendar())->getCalendar();

        $duties = Duty::with([
            'users' => function ($query) {
                $query->orderBy('duty_user.last_worked', 'desc');
            },
            'groups' => function ($query) {
                $query->orderBy('duty_group.last_worked', 'desc');
            }, ])->get();
        
        return view('home.index', compact('trainingUser', 'log', 'calendar', 'duties'));
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
}
