<?php

/**
 * Class AppServiceProvider Test.
 *
 * Note: The TestCase.php includes the refreshApplication() which sets APP_ENV to 'testing'
 *   and calls createApplication() which calls the AppServiceProvider object.
 *   The refreshApplication() gets called everay testcase.
 */
class AppServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_apply_ssl_to_installation()
    {
        // Set up
        putenv('APP_ENV=staging');                                        // Set non-local environment
        // Load install page
        if (File::exists('storage/installed')) {
            File::move('storage/installed', 'storage/installed.tmp');  }
        $response = $this->call('GET', '/install');                       // Load install page
        $this->assertEquals(200, $response->getStatusCode());             // Status OK
        if (File::exists('storage/installed.tmp')) {
            File::move('storage/installed.tmp', 'storage/installed');  }

        // Test Install, non-local, non-Secure
        $this->assertFalse(!strpos(url()->current(), '/install'), 'Page Is installation');
        $this->assertNotEquals('local', getenv('APP_ENV'), 'Environment is Not local: '.getenv('APP_ENV') );
        $this->assertFalse(Request::secure(), 'Not Secured Route');         // https://laravel.com/api/5.4/Illuminate/Http/Request.html

        $this->app = $this->createApplication();                            // Call App Service Provider Boot()

        // Test non-Secure
        $this->assertFalse(Request::secure(), 'Still Not Secured Route');
    }

    /**
     * @test
     */
    public function it_does_not_apply_ssl_to_local_env()
    {
        putenv('APP_ENV=local');                      // Set local environment

        // Test non-Install, local, non-Secure
        $this->assertTrue(!strpos(url()->current(), '/install'), 'Not installation');
        $this->assertEquals('local', getenv('APP_ENV'), 'Environment is local: '.getenv('APP_ENV') );
        $this->assertFalse(Request::secure(), 'Not Secured Route');

        $this->app = $this->createApplication();                      // Call App Service Provider Boot()

        $response = $this->action('GET', 'HomeController@index');     // Home page
        $this->assertEquals(302, $response->getStatusCode());         // Status Redirects

        // Test non-Secure
        $this->assertFalse(Request::secure(), 'Still not Secured Route');
    }

    /**
     * @test
     */
    public function it_has_ssl_secured_route()
    {
        // Ensure scheme is not SSL
        if (app()::VERSION >= 5.4) {
           \URL::forceScheme('http'); 	## Method changed in Laravel 5.4
        } else {
           \URL::forceSchema('http');
        }

        $response =$this->action('GET', 'HomeController@index');      // Load Home page
        $this->assertEquals(302, $response->getStatusCode());         // Status Redirect

        // Test non-Install, non-Local, non-Secure
        $this->assertTrue(!strpos(url()->current(), '/install'), 'Page Not installation');
        $this->assertNotEquals('local', getenv('APP_ENV'), 'Environment Not local '.getenv('APP_ENV') );
        $this->assertFalse(Request::secure(), 'Un-Secured Route');

        $this->app = $this->createApplication();                      // Call App Service Provider Boot()
        $response = $this->action('GET', 'HomeController@index');     // Home page
        $this->assertEquals(302, $response->getStatusCode());         // Status Redirect

        // Test Secure
        $this->assertTrue(Request::secure(), 'SSL Secured Route');
    }
}
