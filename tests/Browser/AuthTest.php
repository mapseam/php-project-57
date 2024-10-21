<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use Tests\Browser\Pages\Auth\{Register, Login};

class AuthTest extends DuskTestCase
{
    private array $userData;

    public function setUp(): void
    {
        parent::setUp();

        $this->userData = [
            'name' => 'Toto',
            'email' => 'toto@hexlet.io',
            'password' => 'awesomeness'
        ];
    }

    public function testRegisterFormValidation(): void
    {
        $invalidPassword = 'awesome';
        $invalidPasswordConfirmation = 'hexlet';

        $this->browse(function (Browser $browser) use ($invalidPassword, $invalidPasswordConfirmation): void {
            $browser->visit(new Register())
                    ->type('name', $this->userData['name'])
                    ->type('email', $this->userData['email'])
                    ->type('password', $invalidPassword)
                    ->type('password_confirmation', $invalidPassword)
                    ->press('Зарегистрировать')
                    ->assertPathIs('/register')
                    ->assertSee('Пароль должен иметь длину не менее 8 символов');

            $browser->visit(new Register())
                    ->type('name', $this->userData['name'])
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->type('password_confirmation', $invalidPasswordConfirmation)
                    ->press('Зарегистрировать')
                    ->assertPathIs('/register')
                    ->assertSee('Пароль и подтверждение не совпадают');
        });
    }

    public function testLoginFormValidation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit(new Login())
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти')
                    ->assertPathIs('/login')
                    ->assertSee('Введите правильные имя пользователя и пароль');
        });
    }

    public function testAuthWorks(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit(new Register())
                    ->type('name', $this->userData['name'])
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->type('password_confirmation', $this->userData['password'])
                    ->press('Зарегистрировать')
                    ->assertPathIs('/')
                    ->clickLink('Выход')
                    ->assertPathIs('/')
                    ->visit(new Login())
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти')
                    ->assertPathIs('/');
        });
    }
}
