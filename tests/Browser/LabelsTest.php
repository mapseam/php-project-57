<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Pages\Auth\Login;
use Tests\Browser\Pages\Labels;

class LabelsTest extends DuskTestCase
{
    private Labels $labelsPage;
    private Login $loginPage;
    private array $userData;
    private array $labels;
    private string $labelName;
    private string $labelDescription;


    public function setUp(): void
    {
        parent::setUp();
        $this->labelsPage = new Labels();
        $this->loginPage = new Login();

        $this->userData = [
            'name' => 'Toto',
            'email' => 'toto@hexlet.io',
            'password' => 'awesomeness',
            'password_confirmation' => 'awesomeness'
        ];

        $this->post('/register', $this->userData);

        $this->labels = [
            'label1' => [
                'name' => 'bug',
                'description' => 'Indicates an unexpected problem or unintended behavior'
            ],
            'label2' => [
                'name' => 'enhancement',
                'description' => 'Indicates new feature requests'
            ],
            'label3' => [
                'name' => 'help wanted',
                'description' => 'Indicates that a maintainer wants help'
            ]
        ];

        foreach ($this->labels as $label) {
            $this->post('/labels', $label);
        }

        $this->labelName = 'question';
        $this->labelDescription = 'Indicates that an issue or pull request needs more information';
    }

    public function testAsGuest(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->labelsPage)
                    ->assertDontSee('Создать метку')
                    ->assertDontSee('Удалить')
                    ->assertDontSee('Изменить');
        });
    }

    public function testLabelFormNameRequiredValidation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');

            $browser->visit($this->labelsPage)
                    ->clickLink('Создать метку')
                    ->assertPathIs('/labels/create')
                    ->press('Создать')
                    ->assertPathIs('/labels/create')
                    ->assertSee('Это обязательное поле');
        });
    }

    public function testAdd(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');

            $browser->visit($this->labelsPage)
                    ->clickLink('Создать метку')
                    ->assertPathIs('/labels/create')
                    ->type('name', $this->labelName)
                    ->type('description', $this->labelDescription)
                    ->press('Создать')
                    ->assertPathIs('/labels')
                    ->assertSee($this->labelName)
                    ->assertSee($this->labelDescription)
                    ->assertSee('Метка успешно создана');
        });
    }

    public function testLabelsHasBeenAdded(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->labelsPage);

            foreach ($this->labels as $label) {
                $browser->assertSee($label['name']);
                $browser->assertSee($label['description']);
            }
        });
    }

    public function testLabelFormNameUniqueValidation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');

            $browser->visit($this->labelsPage)
                    ->clickLink('Создать метку')
                    ->assertPathIs('/labels/create')
                    ->type('name', $this->labels['label2']['name'])
                    ->press('Создать')
                    ->assertPathIs('/labels/create')
                    ->assertSee('Метка с таким именем уже существует');
        });
    }

    public function testEdit(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');

            $id = $browser->visit($this->labelsPage)
                          ->text('tbody > tr > td');

            $browser->clickLink('Изменить')
                    ->assertPathIs("/labels/{$id}/edit")
                    ->type('name', $this->labelName)
                    ->type('description', $this->labelDescription)
                    ->press('Обновить')
                    ->assertSee('Метка успешно изменена')
                    ->assertPathIs('/labels')
                    ->assertSee($this->labelName)
                    ->assertSee($this->labelDescription)
                    ->assertDontSee($this->labels['label1']['name'])
                    ->assertDontSee($this->labels['label1']['description']);
        });
    }

    public function testRemove(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');

            $browser->visit($this->labelsPage)
                    ->clickLink('Удалить')
                    ->acceptDialog()
                    ->assertPathIs('/labels')
                    ->assertDontSee($this->labels['label1']['name'])
                    ->assertDontSee($this->labels['label1']['description'])
                    ->assertSee('Метка успешно удалена');
        });
    }
}
