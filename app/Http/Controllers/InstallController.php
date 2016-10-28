<?php

namespace SET\Http\Controllers;

use Illuminate\Http\Request;
use RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;
use SET\User;

class InstallController extends Controller
{
    public function buildDatabase(DatabaseManager $database)
    {
        $response = $database->migrateAndSeed();
        $this->createUser($response);
    }

    public function createUser($response)
    {
        return view('vendor.installer.user')
            ->with(['message' => $response]);
    }

    /**
     * Replaces RachidLaasri\LaravelInstaller\Controllers\FinalController
     * so that we can also create the admin user.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */

    public function storeUser(Request $request)
    {
        $user = User::create($request->all());
        $user->role = 'edit';
        $user->save();

        (new InstalledFileManager)->update();

        return view('vendor.installer.finished');
    }
}
