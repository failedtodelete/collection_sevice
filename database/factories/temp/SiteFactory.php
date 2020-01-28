<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(\App\Models\Temp\Site::class, function (Faker $faker) {

    return [
        'link_id'       => 1,
        'rating'        => mt_rand( 0, 100 ) / 10,
        'thumbnail'     => null,
        'hash'          => substr($faker->sha256,0,12),
        'creator_id'    => 1,
        'status'        => 1
    ];
});
