<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        TaskStatus::factory()->create([
            'name' => 'Запланирована',
        ]);

        TaskStatus::factory()->create([
            'name' => 'Принята к исполнению',
        ]);

        TaskStatus::factory()->create([
            'name' => 'Выполнена',
        ]);
    
        TaskStatus::factory()->create([
            'name' => 'Отменена',
        ]);

        TaskStatus::factory()->create([
            'name' => 'На контроле',
        ]);
    
        TaskStatus::factory()->create([
            'name' => 'Возвращена',
        ]);
    
        TaskStatus::factory()->create([
            'name' => 'Проконтролирована',
        ]);
    }
}
