<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(\App\Models\Main\Type::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName
    ];
});
