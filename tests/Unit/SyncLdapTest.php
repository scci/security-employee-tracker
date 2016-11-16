<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Console\Commands\SyncLdap;
use SET\User;

class SyncLdapTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * WARNING: THIS IS A VERY SLOW TEST
     *
     * @test
     */
    public function it_adds_missing_users_if_ldap_is_setup()
    {
//        if (config('auth.providers.users.driver') != 'adldap') {
//            return true;
//        }
//
//        (new SyncLdap())->handle();
//
//        $userCreated = User::where('created_at', Carbon::now())->get();
//
//        $this->assertNotNull($userCreated);
//        $this->assertEquals('active', $userCreated->first()->status);
    }
}
