<?php

namespace SET\Http\Controllers\Installation;

use Illuminate\Support\Facades\Hash;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;
use RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager;
use SET\Http\Controllers\Controller;
use SET\Http\Requests\InstallationRequest;
use SET\User;

class AdminController extends Controller
{

    public function index()
    {
        $response = (new DatabaseManager())->migrateAndSeed();

        return view('vendor.installer.user')
            ->with(['message' => $response]);
    }

    /**
     * Replaces RachidLaasri\LaravelInstaller\Controllers\FinalController
     * so that we can also create the admin user.
     *
     * @param InstallationRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(InstallationRequest $request)
    {
        $user = User::create($request->all());
        $user->password = Hash::make($request->password);
        $user->role = 'edit';
        $user->save();

        (new InstalledFileManager())->update();

        return view('vendor.installer.finished');
    }
}
