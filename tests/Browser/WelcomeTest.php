<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WelcomeTest extends DuskTestCase
{
    public function testRootPageWorks()
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/')
                    ->assertSee('Привет от Хекслета!');
            $browser->visit('/')
                    ->assertDontSee('Какой-то текст');
        });
    }
}
