<?php

use App\Story;
use App\Task;
use App\TaskState;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'caption' => $faker->sentence(3),
        'description' => $faker->paragraph(10),
        'story_id' => function () {
            return factory(Story::class)->create()->id;
        },
        'state_id' => function () use ($faker) {
            return $faker->randomElement(TaskState::pluck('id')->all());
        },
    ];
});
