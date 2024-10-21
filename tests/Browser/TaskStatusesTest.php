<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\{User, TaskStatus};
use Tests\Browser\Pages\Auth\Login;
use Tests\Browser\Pages\TaskStatuses;

class TaskStatusesTest extends DuskTestCase
{
    private array $userData;
    private array $taskStatuses;
    private string $taskStatusName;
    private TaskStatuses $taskStatusesPage;
    private Login $loginPage;

    public function setUp(): void
    {
        parent::setUp();
        $this->taskStatusesPage = new TaskStatuses();
        $this->loginPage = new Login();

        $this->userData = [
            'name' => 'Toto',
            'email' => 'toto@hexlet.io',
            'password' => 'awesomeness',
            'password_confirmation' => 'awesomeness'
        ];

        $this->post('/register', $this->userData);

        $this->taskStatuses = [
            'status1' => [
                'name' => 'new',
            ],
            'status2' => [
                'name' => 'finished',
            ],
            'status3' => [
                'name' => 'processing',
            ]
        ];

        foreach ($this->taskStatuses as $status) {
            $this->post('/task_statuses', $status);
        }

        $this->taskStatusName = 'testing';
    }

    public function testAsGuest(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->taskStatusesPage)
                    ->assertDontSee('Создать статус')
                    ->assertDontSee('Удалить')
                    ->assertDontSee('Изменить');
        });
    }

    public function testTaskStatusFormNameRequiredValidation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');
            $browser->visit($this->taskStatusesPage)
                    ->clickLink('Создать статус')
                    ->assertPathIs('/task_statuses/create')
                    ->press('Создать')
                    ->assertPathIs('/task_statuses/create')
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

            $browser->visit($this->taskStatusesPage)
                    ->clickLink('Создать статус')
                    ->assertPathIs('/task_statuses/create')
                    ->type('name', $this->taskStatusName)
                    ->press('Создать')
                    ->assertPathIs('/task_statuses')
                    ->assertSee($this->taskStatusName)
                    ->assertSee('Статус успешно создан');
        });
    }

    public function testTaskStatusesHasBeenAdded(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->taskStatusesPage);

            foreach ($this->taskStatuses as $status) {
                $browser->assertSee($status['name']);
            }
        });
    }

    public function testTaskStatusFormNameUniqueValidation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');
            $browser->visit($this->taskStatusesPage)
                    ->clickLink('Создать статус')
                    ->assertPathIs('/task_statuses/create')
                    ->type('name', $this->taskStatuses['status2']['name'])
                    ->press('Создать')
                    ->assertPathIs('/task_statuses/create')
                    ->assertSee('Статус с таким именем уже существует');
        });
    }

    public function testEdit(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');
            $id = $browser->visit($this->taskStatusesPage)
                          ->text('tbody > tr > td');

            $browser->clickLink('Изменить')
                    ->assertPathIs("/task_statuses/{$id}/edit")
                    ->type('name', $this->taskStatusName)
                    ->press('Обновить')
                    ->assertSee('Статус успешно изменён')
                    ->assertPathIs('/task_statuses')
                    ->assertSee($this->taskStatusName)
                    ->assertDontSee($this->taskStatuses['status1']['name']);
        });
    }

    public function testRemove(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->userData['email'])
                    ->type('password', $this->userData['password'])
                    ->press('Войти');
            $browser->visit($this->taskStatusesPage)
                    ->clickLink('Удалить')
                    ->acceptDialog()
                    ->assertPathIs('/task_statuses')
                    ->assertDontSee($this->taskStatuses['status1']['name'])
                    ->assertSee('Статус успешно удалён');
        });
    }
}
