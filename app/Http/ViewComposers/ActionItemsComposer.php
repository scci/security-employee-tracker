<?php

namespace SET\Http\ViewComposers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use SET\Training;
use SET\User;

class ActionItemsComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $dueTraining = $this->getDueTraining();
        $expiringVisits = $this->getExpiringVisits();
        $eligibilityRenewal = $this->getEligibilityRenewal();

        $view->with('dueTraining', $dueTraining)
            ->with('expiringVisits', $expiringVisits)
            ->with('eligibilityRenewal', $eligibilityRenewal);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getDueTraining()
    {
        return Training::with(['users', 'assignedUsers.user', 'assignedUsers' => function ($query) {
            //filter the assignedusers we get back
            $query->ActiveUsers()
                ->whereNull('completed_date')
                ->where('due_date', '<=', Carbon::now());
        }])
            ->whereHas('assignedUsers', function ($q) {
                //filter the training we get.
                $q->ActiveUsers()
                    ->whereNull('completed_date')
                    ->where('stop_renewal', '!=', 1)
                    ->where('due_date', '<=', Carbon::now());
            })
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    private function getExpiringVisits()
    {
        return User::with(['visits' => function ($query) {
            $query->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addWeek()]);
        }])->whereHas('visits', function ($q) {
            $q->whereBetween('expiration_date', [Carbon::now(), Carbon::now()->addWeek()]);
        })->Active()->get();
    }

    /**
     * @return Collection
     */
    private function getEligibilityRenewal()
    {
        $builtUser = new Collection();
        $users = User::where('inv_close', '<=', Carbon::now())->active()->get();

        foreach ($users as $user) {
            $calculatedDays = $this->calculateDaysToRenewClearance($user);

            $this->buildUserArray($calculatedDays, $builtUser, $user);
        }

        return $this->sortUserCollection($builtUser);
    }

    /**
     * @param $user
     *
     * @return int
     */
    private function calculateDaysToRenewClearance($user)
    {
        $years = 100;
        if ($user->clearance == 'TS' || $user->clearance == 'Int TS' || $user->clearance == 'SCI') {
            $years = 6;
        } elseif ($user->clearance == 'S' || $user->clearance == 'Int S') {
            $years = 10;
        }

        if ($user->cont_eval)
            $calculatedDays = Carbon::now()->diffInDays(
                    Carbon::createFromFormat('Y-m-d', $user->cont_eval_date)->addYears($years), false);
        else
            $calculatedDays = Carbon::now()->diffInDays(
                Carbon::createFromFormat('Y-m-d', $user->inv_close)->addYears($years), false);

        return $calculatedDays;
    }

    /**
     * @param int $calculatedDays
     * @param $user
     * @param $builtUser
     */
    private function buildUserArray($calculatedDays, Collection $builtUser, User $user)
    {
        if ($calculatedDays <= 90) {
            $builtUser->push([
                'id'           => $user->id,
                'userFullName' => $user->userFullName,
                'days'         => $calculatedDays,
            ]);
        }
    }

    /**
     * @param Collection $userArray
     */
    private function sortUserCollection($userArray)
    {
        return $userArray->sortByDesc(function ($array) {
            return $array['days'];
        });
    }
}
