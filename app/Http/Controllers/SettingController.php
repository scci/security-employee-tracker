<?php

namespace SET\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use SET\Setting;
use SET\User;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('edit');

        $settings = Setting::getAll();
        $ldapControllers = $settings['adldap.connections.default.connection_settings.domain_controllers'] ?? config('adldap.connections.default.connection_settings.domain_controllers');
        $settings['ldap_controller1'] = $ldapControllers[0];
        $settings['ldap_controller2'] = $ldapControllers[1];
        //$settings['summary_recipient'] = explode(',', $settings['summary_recipient']);
        $settings['summary_recipient'] = User::whereIn('id', $settings['summary_recipient'])->pluck('id')->all();
        
        $users = User::skipSystem()->active()->get()->sortBy('UserFullName');
        $userList = $users->pluck('UserFullName', 'id');
        $admins = $users->where('role', 'edit')->pluck('id')->all();
        $viewers = $users->where('role', 'view')->pluck('id')->all();
        $configAdmins = User::whereIn('username', Config::get('auth.admin'))
            ->get()->pluck('userFullName')->implode('; ');
        
        return view('setting.index', compact('settings', 'userList', 'admins', 'configAdmins', 'viewers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $this->authorize('edit');

        $data = $request->all();
        $data = $this->updateUserRoles($data);
        $data = $this->saveAdldapKeyFormat($data);

        unset($data['_method']);
        unset($data['_token']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->action('SettingController@index');
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    private function updateUserRoles($data)
    {
        User::where('role', '!=', '')->update(['role' => '']);
        if (isset($data['viewer'])) {
            User::whereIn('id', $data['viewer'])->update(['role' => 'view']);
            unset($data['viewer']);
        }
        if (isset($data['admin'])) {
            User::whereIn('id', $data['admin'])->update(['role' => 'edit']);
            unset($data['admin']);
        }

        return $data;
    }

    /**
     * PHP converts our periods to underscores.
     * This causes issues where adldap can't figure out the ADLDAP settings.
     * As such, we need to convert the correct underscores back to their dot notation.
     * Note: some underscores must be retained.
     *
     * @param $data
     */
    private function saveAdldapKeyFormat($data)
    {
        $adldapData = [
            'auth.providers.users.driver'                                   => 'auth_driver',
            'adldap.connections.default.connection_settings.base_dn'        => 'adldap_connections_default_connection_settings_base_dn',
            'adldap.connections.default.connection_settings.admin_username' => 'adldap_connections_default_connection_settings_admin_username',
            'adldap.connections.default.connection_settings.admin_password' => 'adldap_connections_default_connection_settings_admin_password',
            'adldap.connections.default.connection_settings.account_prefix' => 'adldap_connections_default_connection_settings_account_prefix',
            'adldap.connections.default.connection_settings.account_suffix' => 'adldap_connections_default_connection_settings_account_suffix',
            'adldap_auth.limitation_filter'                                 => 'adldap_auth_limitation_filter',
            'mail.driver'                                                   => 'mail_driver',
            'mail.username'                                                 => 'mail_username',
            'mail.password'                                                 => 'mail_password',
            'mail.port'                                                     => 'mail_port',
            'mail.encryption'                                               => 'mail_encryption',
            'mail.host'                                                     => 'mail_host',
        ];

        foreach ($adldapData as $correctKey => $formKey) {
            $data[$correctKey] = $data[$formKey];
            unset($data[$formKey]);
        }

        $data['adldap.connections.default.connection_settings.domain_controllers'] = [$data['ldap_controller1'], $data['ldap_controller2']];
        unset($data['ldap_controller1']);
        unset($data['ldap_controller2']);

        if ($data['adldap.connections.default.connection_settings.admin_password'] == '') {
            $data['adldap.connections.default.connection_settings.admin_password'] = Setting::get('adldap.connections.default.connection_settings.admin_password');
        }

        return $data;
    }
}
