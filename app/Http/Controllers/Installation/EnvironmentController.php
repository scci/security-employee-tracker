<?php

namespace SET\Http\Controllers\Installation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use RachidLaasri\LaravelInstaller\Helpers\EnvironmentManager;
use SET\Http\Controllers\Controller;

class EnvironmentController extends Controller
{

    /**
     * @var EnvironmentManager
     */
    protected $EnvironmentManager;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();
        $fields = $this->breakApartEnv($envConfig);

        return view('vendor.installer.environment', compact('fields'));
    }


    /**
     * Processes the newly saved environment configuration and redirects back.
     *
     * @param Request $input
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $input)
    {
        $message = $this->EnvironmentManager->saveFile($input);

        Artisan::call('generate:key');

        return $this->route('LaravelInstaller::requirements')
            ->with(['message' => $message]);
    }

    /**
     * Key should be before the first = symbol and value should be after.
     * Each new line (\r\n) should be a new array entry.
     * @param $string
     * @return array
     */
    private function breakApartEnv($string)
    {
        preg_match_all('/([^=]*?)=([^\r\n]*?)[\r\n]+/', $string, $matches);

        return array_combine($matches[1], $matches[2]);
    }

}
