<?php

use App\Team;
use App\Sprint;
use Illuminate\Support\Carbon;
use Faker\Generator as Faker;

$factory->define(Sprint::class, function (Faker $faker) {
    $startDate = $faker->date;

    return [
        'caption' => $faker->sentence(3),
        'start' => $startDate,
        'end' => Carbon::parse($startDate)->addDays(14),
        'team_id' => function () {
            return factory(Team::class)->create()->id;
        }
    ];
});
