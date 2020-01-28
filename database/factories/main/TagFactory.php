s<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(\App\Models\Main\Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});
