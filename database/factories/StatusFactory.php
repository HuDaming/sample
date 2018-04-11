<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(\App\Models\Status::class, function (Faker $faker) {
    $dateTime = Carbon::now()->toDateTimeString();

    return [
        'content' => $faker->text(),
        'created_at' => $dateTime,
        'updated_at' => $dateTime,
    ];
});
