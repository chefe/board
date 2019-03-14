<?php

use App\User;
use App\Team;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {
    return [
        'caption' => $faker->sentence(3),
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
