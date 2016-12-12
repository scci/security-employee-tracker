<?php

namespace SET\Handlers\DBConfigs;

class Mail extends ConfigAbstract
{
    public $allowable = [
        'mail.host',
        'mail.port',
        'mail.encryption',
        'mail.username',
        'mail.password',
    ];

    public function setup()
    {
        $this->setConfigValues('mail.driver');
        $this->setConfigValues('mail.from.address');
        $this->setConfigValues('mail.from.name');

        if (config('mail.driver') == 'smtp') {
            $this->implementAllowable();
        }
    }
}
