<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\{User, Task};
use Tests\Browser\Pages\Auth\Login;
use Tests\Browser\Pages\Tasks;
use Carbon\Carbon;

class TasksTest extends DuskTestCase
{
    private Tasks $tasksPage;
    private Login $loginPage;
    private array $users;
    private array $taskStatuses;
    private array $tasks;
    private Carbon $createdAt;
    private array $task;
    private array $status;


    public function setUp(): void
    {
        parent::setUp();
        $this->tasksPage = new Tasks();
        $this->loginPage = new Login();

        $password = 'awesomeness';

        $this->users = [
            'user1' => [
                'name' => 'Toto',
                'email' => 'toto@hexlet.io',
                'password' => $password,
                'password_confirmation' => $password
            ],
            'user2' => [
                'name' => 'Pair',
                'email' => 'pair@hexlet.io',
                'password' => $password,
                'password_confirmation' => $password
            ],
            'user3' => [
                'name' => 'Smith',
                'email' => 'smith@hexlet.io',
                'password' => $password,
                'password_confirmation' => $password
            ]
        ];

        $this->post('/register', $this->users['user1']);
        $this->post('/logout');
        $this->post('/register', $this->users['user2']);
        $this->post('/logout');
        $this->post('/register', $this->users['user3']);
        $this->post('/logout');

        $this->post(
            '/login',
            collect($this->users['user1'])
                ->except(['name', 'password_confirmation'])
                ->toArray()
        );


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

        $this->tasks = [
            'task1' => [
                'name' => 'Task 1',
                'description' => 'Description of task 1',
                'status_id' => 1,
                'assigned_to_id' => 1
            ],
            'task2' => [
                'name' => 'Task 2',
                'description' => 'Description of task 2',
                'status_id' => 2,
                'assigned_to_id' => 1
            ],
            'task3' => [
                'name' => 'Task 3',
                'description' => 'Description of task 3',
                'status_id' => 3,
                'assigned_to_id' => 1
            ]
        ];

        foreach ($this->tasks as $task) {
            $this->post('/tasks', $task);
        }

        $this->createdAt = Carbon::now();

        $this->task = [
            'name' => 'Task 4',
            'description' => 'Description of task 4'
        ];

        $this->status = ['name' => 'testing'];
    }

    public function testAsGuest(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->tasksPage)
                    ->assertDontSee('Создать задачу')
                    ->assertDontSee('Удалить')
                    ->assertDontSee('Изменить');
        });
    }

    public function testTaskFormNameRequiredValidation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->clickLink('Выход')
                    ->visit($this->loginPage)
                    ->type('email', $this->users['user1']['email'])
                    ->type('password', $this->users['user1']['password'])
                    ->press('Войти');
            $browser->visit($this->tasksPage)
                    ->clickLink('Создать задачу')
                    ->assertPathIs('/tasks/create')
                    ->press('Создать')
                    ->assertPathIs('/tasks/create')
                    ->assertSee('Это обязательное поле');
        });
    }

    public function testAdd(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->users['user1']['email'])
                    ->type('password', $this->users['user1']['password'])
                    ->press('Войти');

            $browser->visit($this->tasksPage)
                    ->clickLink('Создать задачу')
                    ->assertPathIs('/tasks/create')
                    ->type('name', $this->task['name'])
                    ->type('description', $this->task['description'])
                    ->select('status_id', '1')
                    ->select('assigned_to_id', '2')
                    ->press('Создать')
                    ->assertPathIs('/tasks')
                    ->assertSee('Задача успешно создана');

            $browser->with('table', function ($table): void {
                $table->assertSee($this->task['name'])
                      ->assertSee($this->users['user2']['name']);
            });
        });
    }

    public function testTasksHasBeenAdded(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->tasksPage);

            $browser->with('table', function ($table): void {
                foreach ($this->tasks as $task) {
                    $table->assertSee($task['name']);
                }

                foreach ($this->taskStatuses as $taskStatus) {
                    $table->assertSee($taskStatus['name']);
                }

                $table->assertSee($this->createdAt->format('d.m.Y'));
            });
        });
    }

    public function testShow(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->users['user1']['email'])
                    ->type('password', $this->users['user1']['password'])
                    ->press('Войти');
            $id = $browser->visit($this->tasksPage)
                          ->text('tbody > tr > td');

            $browser->clickLink($this->tasks['task1']['name'])
                    ->assertPathIs("/tasks/{$id}")
                    ->assertSee($this->tasks['task1']['name'])
                    ->assertSee($this->taskStatuses['status1']['name'])
                    ->assertSee($this->tasks['task1']['description']);
        });
    }

    public function testEdit(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->users['user1']['email'])
                    ->type('password', $this->users['user1']['password'])
                    ->press('Войти');
            $id = $browser->visit($this->tasksPage)
                          ->text('tbody > tr > td');

            $browser->clickLink('Изменить')
                    ->assertPathIs("/tasks/{$id}/edit")
                    ->type('name', $this->task['name'])
                    ->type('description', $this->task['description'])
                    ->select('status_id', '2')
                    ->select('assigned_to_id', '2')
                    ->press('Обновить')
                    ->assertSee('Задача успешно изменена')
                    ->assertPathIs('/tasks');

            $browser->with('table', function ($table): void {
                $table->assertSee($this->task['name'])
                      ->assertSee($this->users['user2']['name'])
                      ->assertDontSee($this->taskStatuses['status1']['name'])
                      ->assertDontSee($this->tasks['task1']['name']);
            });

            $browser->clickLink($this->task['name'])
                    ->assertSee($this->task['name'])
                    ->assertSee($this->taskStatuses['status2']['name'])
                    ->assertSee($this->task['description']);
        });
    }

    public function testFilter(): void
    {
        $this->post('/task_statuses', $this->status);

        $this->browse(function (Browser $browser): void {
            $browser->clickLink('Выход')
                    ->visit($this->loginPage)
                    ->type('email', $this->users['user2']['email'])
                    ->type('password', $this->users['user2']['password'])
                    ->press('Войти');

            $browser->visit($this->tasksPage)
                    ->clickLink('Создать задачу')
                    ->type('name', $this->task['name'])
                    ->type('description', $this->task['description'])
                    ->select('status_id', '4')
                    ->select('assigned_to_id', '3')
                    ->press('Создать');

            $browser->select('filter[status_id]', '4')
                    ->select('filter[created_by_id]', '2')
                    ->select('filter[assigned_to_id]', '3')
                    ->press('Применить');

            $browser->with('table', function ($table): void {
                $table->assertSee($this->status['name'])
                      ->assertSee($this->task['name']);

                foreach ($this->taskStatuses as $status) {
                    $table->assertDontSee($status['name']);
                }

                foreach ($this->tasks as $task) {
                    $table->assertDontSee($task['name']);
                }

                $table->assertDontSee($this->users['user1']['name']);
            });
        });
    }

    public function testRemoveNotByCreator(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->clickLink('Выход')
                    ->visit($this->loginPage)
                    ->type('email', $this->users['user2']['email'])
                    ->type('password', $this->users['user2']['password'])
                    ->press('Войти');
            $browser->visit($this->tasksPage)
                    ->assertDontSee('Удалить');
        });
    }

    public function testTaskFormTaskStatusRequiredValidation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->clickLink('Выход')
                    ->visit($this->loginPage)
                    ->type('email', $this->users['user1']['email'])
                    ->type('password', $this->users['user1']['password'])
                    ->press('Войти');
            $browser->visit($this->tasksPage)
                    ->clickLink('Создать задачу')
                    ->assertPathIs('/tasks/create')
                    ->type('name', 'Hexlet Task')
                    ->press('Создать')
                    ->assertPathIs('/tasks/create');
        });
    }

    public function testRemove(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit($this->loginPage)
                    ->type('email', $this->users['user1']['email'])
                    ->type('password', $this->users['user1']['password'])
                    ->press('Войти');
            $browser->visit($this->tasksPage)
                    ->clickLink('Удалить')
                    ->acceptDialog()
                    ->assertPathIs('/tasks')
                    ->assertDontSee($this->tasks['task1']['name'])
                    ->assertSee('Задача успешно удалена');
        });
    }
}
