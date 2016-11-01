<?php

namespace SET\Listeners;

use Adldap\Laravel\Facades\Adldap;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use SET\User;

class ResolveLdap implements ShouldQueue
{
    private $ldapUsers;

    /**
     * Build the event handler.
     */
    public function __construct()
    {
        //
    }

    /**
     * Call to add missing users.
     */
    public function handle()
    {
        if (config('auth.providers.users.driver') == 'adldap') {
            $this->addMissingUsers();
        }
    }

    /**
     *    Compare the current database against ldap and add any missing/new users.
     */
    private function addMissingUsers()
    {
        $dbUsers = User::all()->pluck('username')->toArray();

        $this->ldapList();

        foreach ($this->ldapUsers as $ldapUser) {
            if (!in_array($ldapUser['username'], $dbUsers)) {
                $ldapUser['status'] = 'active';
                User::create($ldapUser);
            } else {
                User::where('username', $ldapUser['username'])->update($ldapUser);
            }
        }
    }

    /**
     * Gets an array of current ldap users in Austin.
     */
    private function ldapList()
    {
        $this->ldapUserSearch();
        $this->actualUsers();
        $this->simplifyList();
    }

    private function ldapUserSearch()
    {
        $division = Config::get('adldap.divisionKeyword');

        if ($division != '') {
            $this->ldapUsers = Adldap::search()
                ->where('mail', '*')
                ->orWhere('division', '=', $division)
                ->get();
        } else {
            $this->ldapUsers = Adldap::search()->where('mail', '*')->get();
        }
    }

    /**
     * Takes our ldap list and kicks anyone in our rejected list or records that are not a person.
     */
    private function actualUsers()
    {
        for ($i = count($this->ldapUsers) - 1; $i >= 0; $i--) {
            if (count($this->ldapUsers[$i]['samaccountname']) == 0 ||
                count($this->ldapUsers[$i]['sn']) == 0 ||
                count($this->ldapUsers[$i]['givenname']) == 0 ||
                count($this->ldapUsers[$i]['mail']) == 0
            ) {
                unset($this->ldapUsers[$i]);
            }
        }
    }

    /**
     * Takes our ldap list and simplify with with just the username, first/last name, phone and email.
     */
    private function simplifyList()
    {
        $array = [];
        $counter = 0;

        foreach ($this->ldapUsers as $user) {
            $array[$counter]['username'] = $user->samaccountname[0];
            $array[$counter]['first_name'] = $user->givenname[0];
            $array[$counter]['last_name'] = $user->sn[0];
            $array[$counter]['email'] = $user->mail[0];
            $array[$counter]['phone'] = $user->telephonenumber[0];
            $counter++;
        }
        $this->ldapUsers = $array;
    }
}
