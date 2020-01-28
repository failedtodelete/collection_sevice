<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\UserStatus::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'display_name' => $faker->name
    ];
});
