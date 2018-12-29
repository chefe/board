<?php

use App\TaskState;
use Illuminate\Database\Seeder;

class TaskStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaskState::create(['id' => 1, 'caption' => 'Todo']);
        TaskState::create(['id' => 2, 'caption' => 'In Progress']);
        TaskState::create(['id' => 3, 'caption' => 'Verify']);
        TaskState::create(['id' => 4, 'caption' => 'Done']);
    }
}
