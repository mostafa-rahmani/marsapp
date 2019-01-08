<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AdminLoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testExample()
    {

            $this->browse(function(Browser $browser) {

                $browser->visit('/auth/login')
                    ->assertSee('اپلیکیشن پرتقال')
                    ->value('#login_email', 'mostafaaa_rahmani@outlook.com')
                    ->value('#login_password', 'password')
                    ->click('button[type="submit')
                    ->assertSee('تنظیمات');

                });

    }
}
