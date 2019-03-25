<?php

namespace SET\Handlers\DBConfigs;

class Ldap extends ConfigAbstract
{
    public $allowable = [
        'auth.providers.users.driver',
        'adldap.connections.default.connection_settings.base_dn',
        'adldap.connections.default.connection_settings.domain_controllers',
        'adldap.connections.default.connection_settings.admin_username',
        'adldap.connections.default.connection_settings.admin_password',
        'adldap.connections.default.connection_settings.account_prefix',
        'adldap.connections.default.connection_settings.account_suffix',
        'adldap_auth.limitation_filter',
    ];

    public function setup()
    {
        $this->setConfigValues('auth.providers.users.driver');
        if (config('auth.providers.users.driver') == 'ldap') {
            $this->implementAllowable();
        }
    }
}
