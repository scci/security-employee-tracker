<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use SET\User;

class TestCase extends BaseTestCase
{
    use DatabaseTransactions;
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost/';

    protected $user;

    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();
        config(['app.url' => 'http://localhost/']);
    }

    public function signIn($user = null)
    {
        if (!$user) {
            $user = factory(User::class)->create(['role' => 'edit']);
        }

        $this->user = $user;

        $this->actingAs($user);

        return $this;
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
