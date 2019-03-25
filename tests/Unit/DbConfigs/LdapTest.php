<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Handlers\DBConfigs\Ldap;
use SET\Setting;
use Tests\Testcase;

class LdapTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sets_the_eloquent_driver_if_in_db()
    {
        Setting::set('auth.providers.users.driver', 'eloquent');
        (new Ldap())->setup();

        $this->assertEquals(Setting::get('auth.providers.users.driver'), 'eloquent');
    }

    /** @test */
    public function it_sets_adldap_settings_from_array()
    {
        $array = [
            'auth.providers.users.driver'                                       => 'ldap',
            'adldap.connections.default.connection_settings.domain_controllers' => ['123.123.123', '789.789.789'],
            'adldap.connections.default.connection_settings.admin_username'     => 'someaddress@email.com',
            'adldap.connections.default.connection_settings.admin_password'     => 'SomePassword',
            'adldap.connections.default.connection_settings.account_suffix'     => '@company.com',
        ];

        (new Ldap($array))->setup();

        foreach ($array as $key => $value) {
            $this->assertEquals($array[$key], config($key));
        }
    }

    /** @test */
    public function it_gets_the_env_or_config_value_when_no_value_is_in_the_db()
    {
        (new Ldap(['auth.providers.users.driver' => null]))->setup();
        $this->assertNotNull(config('auth.providers.users.driver'));
    }
}
