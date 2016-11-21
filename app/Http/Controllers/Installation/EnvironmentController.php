<?php

namespace SET\Http\Controllers\Installation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use RachidLaasri\LaravelInstaller\Helpers\EnvironmentManager;
use SET\Http\Controllers\Controller;
use Exception;

class EnvironmentController extends Controller
{
    /**
     * @var EnvironmentManager
     */
    protected $EnvironmentManager;
    protected $envPath;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
        $this->envPath = base_path('.env');
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
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $input)
    {
        $env = $this->flattenRequest($input);
        $message = $this->saveFile($env);

        Artisan::call('key:generate');

        return redirect()->route('LaravelInstaller::requirements')
            ->with(['message' => $message]);
    }

    /**
     * Key should be before the first = symbol and value should be after.
     * Each new line (\r\n) should be a new array entry.
     *
     * @param $string
     *
     * @return array
     */
    private function breakApartEnv($string)
    {
        preg_match_all('/([^=]*?)=([^\r\n]*?)[\r\n]+/', $string, $matches);

        return array_combine($matches[1], $matches[2]);
    }

    private function flattenRequest(Request $input)
    {
        $fields = $this->breakApartEnv($this->EnvironmentManager->getEnvContent());
        $results = array_merge($fields, $input->toArray());
        unset($results['_token']);

        $env = '';
        foreach($results as $key => $value) {
            $env .= $key ."=". $value ."\r\n";
        }
        return $env;
    }

    /**
     * Save the edited content to the file.
     *
     * @param $string
     * @return string
     */
    public function saveFile($string)
    {
        $message = trans('messages.environment.success');

        try {
            file_put_contents($this->envPath, $string);
        }
        catch(Exception $e) {
            $message = trans('messages.environment.errors');
        }

        return $message;
    }

}
