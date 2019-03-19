<?php

use App\Story;
use App\Sprint;
use Faker\Generator as Faker;

$factory->define(Story::class, function (Faker $faker) {
    return [
        'caption' => $faker->sentence(3),
        'description' => $faker->paragraph(10),
        'points' => $faker->numberBetween(1, 21),
        'sprint_id' => function () {
            return factory(Sprint::class)->create()->id;
        }
    ];
});
