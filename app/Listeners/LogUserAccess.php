<?php

namespace SET\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use SET\User;

class LogUserAccess
{
    /**
     * Build the event handler.
     */
    public function __construct()
    {
        //
    }

    /**
     * Call to add missing users, flash last login and then update those records.
     *
     * @param User $login
     */
    public function handle($login)
    {
        $user = $login->user;
        Session::flash('last_logon', $user->last_logon);
        Session::flash('ip', $user->ip);
        Auth::user()->last_logon = Carbon::now();
        Auth::user()->ip = $this->userIP();

        Auth::user()->save();
    }

    private function userIP()
    {
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            return array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        }

        return $_SERVER && isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'No IP Found';
    }
}
