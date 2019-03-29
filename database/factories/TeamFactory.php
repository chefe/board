<?php

use App\Team;
use App\User;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {
    return [
        'caption' => $faker->sentence(3),
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
