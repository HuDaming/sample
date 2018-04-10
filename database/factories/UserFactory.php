<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    $dataTime = Carbon::now()->toDateTimeString();
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'is_admin' => false,
        'activated' => true,
        'password' => $password ?: $password = bcrypt('password'),
        'remember_token' => str_random(10),
        'created_at' => $dataTime,
        'updated_at' => $dataTime,
    ];
});
