<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\User;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    use DatabaseTransactions;
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    protected $user;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
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
}
