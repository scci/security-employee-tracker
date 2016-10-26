<?php

namespace SET\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Config;
use SET\Http\Requests;
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

        $settings = Setting::all();
        $users = User::skipSystem()->active()->get()->sortBy('UserFullName');

        $userList = $users->pluck('UserFullName', 'id');
        $admins = $users->where('role', 'edit')->pluck('id')->all();
        $viewers = $users->where('role', 'view')->pluck('id')->all();
        $report = $settings->where('name', 'report_address')->first();
        $configAdmins = User::whereIn('username', Config::get('auth.admin'))
            ->get()->pluck('userFullName')->implode('; ');

        return view('setting.index', compact('report', 'userList', 'admins', 'configAdmins', 'viewers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->authorize('edit');

        $data = $request->all();

        User::where('role', '!=', '')->update(['role' => '']);
        if (isset($data['admin'])) {
            User::whereIn('id', $data['admin'])->update(['role' => 'edit']);
        }
        if (isset($data['viewer'])) {
            User::whereIn('id', $data['viewer'])->update(['role' => 'view']);
        }

        Setting::where('name', 'report_address')->update([
            'primary' => $data['report_address-primary'],
            'secondary' => $data['report_address-secondary']
        ]);

        return redirect()->action('SettingController@index');
    }
}
