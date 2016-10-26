<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;

class LoginTest extends TestCase
{
	use DatabaseTransactions;

    /** @test */
	public function it_loads_the_login_page_when_not_logged_in()
	{
		$this->call('GET','/');
		$this->assertRedirectedTo('login');
		
    }

    /** @test */
	public function it_loads_the_login_page()
	{
		$this->call('GET', '/login');
		$this->see('You are accessing a U.S. Government');

	}

    /** @test */
	public function it_gives_an_error_when_credentials_are_missing()
    {
        $this->visit('/login')
			->type('', 'username')
			->type('', 'password')
			->press('Sign in')
			->seePageIs('/login')
			->see('the username field is required');
	}

    /** @test */
	public function it_gives_an_error_when_login_credentials_are_invalid()
	{
		$this->visit('/login')
			->type('asdf', 'username')
			->type('gibberish', 'password')
			->press('Sign in')
			->see('These credentials do not match our records.');
	}

    /** @test */
	public function it_loads_the_user_page_when_logged_in()
	{
		$user = factory(SET\User::class)->create();

        $this->actingAs($user)
            ->visit('/')
            ->seePageIs('/user/' . $user->id);
	}
}
