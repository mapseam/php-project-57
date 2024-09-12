<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        $taskStatuses =  Yaml::parseFile(database_path('seeds/taskStatuses.yaml'));
        foreach ($taskStatuses as $taskStatus) {
            TaskStatus::firstOrCreate([
                'name' => $taskStatus['name']
            ]);
        }

        $tasks = Yaml::parseFile(database_path('seeds/tasks.yaml'));
        foreach ($tasks as $task) {
            $existingTask = Task::where('name', $task['name'])->first();

            if (!$existingTask) {
                Task::create([
                    'name' => $task['name'],
                    'description' => $task['description'],
                    'status_id' => TaskStatus::inRandomOrder()->first()->id,
                    'created_by_id' => User::inRandomOrder()->first()->id,
                    'assigned_to_id' => User::inRandomOrder()->first()->id,
                ]);
            }
        }

        $labels =  Yaml::parseFile(database_path('seeds/labels.yaml'));
        foreach ($labels as $label) {
            Label::firstOrCreate([
                'name' => $label['name'],
                'description' => $label['description']
            ]);
        }

        $tasks = Task::all();
        foreach ($tasks as $task) {
            $labels = Label::all()->random(random_int(0, 3))->unique();
            $task->labels()->attach($labels);
        }
    }
}
