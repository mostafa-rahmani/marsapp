<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    /** @test */
    public function home_is_loading(){
        $this->browse(function (Browser $browser){
           $browser->visit('/')->assertSee('مارس');
        });
    }
}
