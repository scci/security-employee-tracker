{!! Form::hidden('auth_driver', 'eloquent') !!}
{!! Form::checkbox('auth_driver', 'adldap', null, ['id' => 'auth_driver', 'class' => 'filled-in', (isset($settings['auth.providers.users.driver']) && $settings['auth.providers.users.driver'] == 'adldap') ? 'checked' : '' ]) !!}
{!! Form::label('auth_driver', 'Enable AD/LDAP:') !!}
<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('adldap.connections.default.connection_settings.base_dn', 'Base DN:') !!}
        {!! Form::text('adldap.connections.default.connection_settings.base_dn', $settings['adldap.connections.default.connection_settings.base_dn'] ?? config('adldap.connections.default.connection_settings.base_dn')) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('ldap_controller1', 'Primary Domain Controller:') !!}
        {!! Form::text('ldap_controller1', $settings['ldap_controller1']) !!}
    </div>
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('ldap_controller2', 'Secondary Domain Controller:') !!}
        {!! Form::text('ldap_controller2', $settings['ldap_controller2']) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('adldap.connections.default.connection_settings.admin_username', 'Username:') !!}
        {!! Form::text('adldap.connections.default.connection_settings.admin_username', $settings['adldap.connections.default.connection_settings.admin_username'] ?? config('adldap.connections.default.connection_settings.admin_username')) !!}
    </div>
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('adldap.connections.default.connection_settings.admin_password', 'Password:') !!}
        <input type="password" name="adldap.connections.default.connection_settings.admin_password" />
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('adldap.connections.default.connection_settings.account_prefix', 'Account Prefix:') !!}
        {!! Form::text('adldap.connections.default.connection_settings.account_prefix', $settings['adldap.connections.default.connection_settings.account_prefix'] ?? config('adldap.connections.default.connection_settings.account_prefix')) !!}
    </div>
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('adldap.connections.default.connection_settings.account_suffix', 'Account Suffix:') !!}
        {!! Form::text('adldap.connections.default.connection_settings.account_suffix', $settings['adldap.connections.default.connection_settings.account_suffix'] ?? config('adldap.connections.default.connection_settings.account_suffix')) !!}
    </div>
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('adldap_auth.limitation_filter', 'LDAP Raw Filter') !!}
        {!! Form::text('adldap_auth.limitation_filter', $settings['adldap_auth.limitation_filter'] ?? config('adldap_auth.limitation_filter')) !!}
    </div>
</div>