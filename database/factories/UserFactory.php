<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\User::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'email' => $faker->unique()->email,
        'password' => \Illuminate\Support\Facades\Hash::make($faker->password),
        'role_id' => 1,
        'status_id' => 1
    ];
});
