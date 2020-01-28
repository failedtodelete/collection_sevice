<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Temp\Link::class, function (Faker $faker) {
    return [
        'url'           => $faker->url,
        'creator_id'    => 1,
        'moderator_id'  => 1,
        'type_id'       => 1,
        'status'        => 0
    ];
});
