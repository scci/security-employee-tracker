<?php

namespace SET\Http\Controllers\Installation;

use Exception;
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
    protected $envPath;
    protected $envSetup = [
        'APP_ENV' => FILTER_SANITIZE_ENCODED,
        'APP_DEBUG' => FILTER_SANITIZE_ENCODED,
        'APP_KEY' => FILTER_DEFAULT,
        'APP_URL' => FILTER_VALIDATE_URL,
        'DB_CONNECTION' => FILTER_SANITIZE_ENCODED,
        'DB_HOST' => FILTER_SANITIZE_ENCODED,
        'DB_DATABASE' => FILTER_SANITIZE_ENCODED,
        'DB_USERNAME' => FILTER_SANITIZE_ENCODED,
        'DB_PASSWORD' => FILTER_DEFAULT,
        'MAIL_DRIVER' => FILTER_SANITIZE_ENCODED,
        'MAIL_HOST' => FILTER_SANITIZE_ENCODED,
        'MAIL_PORT' => FILTER_VALIDATE_INT,
        'MAIL_USERNAME' => FILTER_SANITIZE_ENCODED,
        'MAIL_PASSWORD' => FILTER_DEFAULT,
        'MAIL_ENCRYPTION' => FILTER_SANITIZE_ENCODED,
        'MAIL_FROM_ADDRESS' => FILTER_VALIDATE_EMAIL,
        'MAIL_FROM_NAME' => FILTER_DEFAULT,
    ];

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

        $array = array_combine($matches[1], $matches[2]);
        $array['MAIL_FROM_NAME'] = $this->removeQuotes($array['MAIL_FROM_NAME']);
        return $array;
    }

    private function flattenRequest(Request $input)
    {
        $fields = $this->breakApartEnv($this->EnvironmentManager->getEnvContent());
        $results = $this->scrubInput(array_merge($fields, $input->toArray()));
        $results['MAIL_FROM_NAME'] = '"'.$this->removeQuotes($results['MAIL_FROM_NAME']).'"';

        $env = '';
        foreach ($results as $key => $value) {
            $env .= $key.'='.$value."\r\n";
        }

        return $env;
    }

    /**
     * Save the edited content to the file.
     *
     * @param $string
     *
     * @return string
     */
    public function saveFile($string)
    {
        $message = trans('messages.environment.success');

        try {
            file_put_contents($this->envPath, $string);
        } catch (Exception $e) {
            $message = trans('messages.environment.errors');
        }

        return $message;
    }

    private function removeQuotes($string)
    {
        $string = str_replace('"', "", $string);
        return str_replace("'", "", $string);
    }

    private function scrubInput($array)
    {
        $array = filter_var_array($array, $this->envSetup);
        foreach ($array as $key => $value) {
            if( !in_array($key, array_keys($this->envSetup))) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}
