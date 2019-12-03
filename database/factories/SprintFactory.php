<?php

use App\Sprint;
use App\Team;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(Sprint::class, function (Faker $faker) {
    $startDate = $faker->date;

    return [
        'caption' => $faker->sentence(3),
        'start' => $startDate,
        'end' => Carbon::parse($startDate)->addDays(14),
        'team_id' => function () {
            return factory(Team::class)->create()->id;
        },
    ];
});
