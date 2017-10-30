<?php

namespace Tests\Integration;

use SET\User;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_loads_the_login_page()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                    ->assertSee('You are accessing a U.S. Government');
        });
    }

    /** @test */
    public function it_gives_an_error_when_credentials_are_missing()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                    ->type('username', ' ')
                    ->type('password', ' ')
                    ->press('SIGN IN')
                    ->assertSee('The username field is required.');
        });
    }

    /** @test */
    public function it_gives_an_error_when_login_credentials_are_invalid()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                    ->type('username', 'asdf')
                    ->type('password', 'gibberish')
                    ->press('SIGN IN')
                    ->assertSee('These credentials do not match our records.');
        });
    }

    /* @test */
    /*public function it_loads_the_user_page_when_logged_in()
    {
        $newUser = factory(User::class)->create();

        $this->browse(function ($browser) use ($newUser) {
                $browser//->loginAs($newUser)
                    ->visit('/login')
                    ->type('username', '$newUser->username')
                    ->type('password', '$newUser->password')
                    ->press('SIGN IN')
                    //->assertPathIs('/login');
                  ->assertSee('Training');
        });
    }*/
}
