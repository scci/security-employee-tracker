<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Handlers\DBConfigs\Mail;
use SET\Setting;
use Tests\Testcase;

class MailTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sets_the_php_mail_driver_if_in_db()
    {
        Setting::set('mail.driver', 'mail');
        (new Mail())->setup();

        $this->assertEquals(Setting::get('mail.driver'), 'mail');
    }

    /** @test */
    public function it_sets_smtp_settings_from_array()
    {
        $array = [
            'mail.driver'       => 'smtp',
            'mail.host'         => '123.456.789',
            'mail.port'         => 5555,
            'mail.from.address' => 'someaddress@email.com',
            'mail.from.name'    => 'My Name',
            'mail.encryption'   => null,
            'mail.username'     => 'username',
            'mail.password'     => 'password',
        ];

        (new Mail($array))->setup();

        $this->assertEquals($array['mail.driver'], config('mail.driver'));
        $this->assertEquals($array['mail.host'], config('mail.host'));
        $this->assertEquals($array['mail.port'], config('mail.port'));
        $this->assertEquals($array['mail.from.address'], config('mail.from.address'));
        $this->assertEquals($array['mail.from.name'], config('mail.from.name'));
        $this->assertEquals($array['mail.encryption'], config('mail.encryption'));
        $this->assertEquals($array['mail.username'], config('mail.username'));
        $this->assertEquals($array['mail.password'], config('mail.password'));
    }

    /** @test */
    public function it_gets_the_env_or_config_value_when_no_value_is_in_the_db()
    {
        (new Mail(['mail.driver' => null]))->setup();
        $this->assertNotNull(config('mail.driver'));
    }
}
