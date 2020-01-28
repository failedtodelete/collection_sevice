<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(\App\Models\Main\Site::class, function (Faker $faker) {

    $thumbnail = substr($faker->sha256, 0, 7) . '.';
    $thumbnail .= rand(0, 1) ? 'png' : 'jpg';

    return [
        'url'       => $faker->unique()->url,
        'type_id'   => 1,
        'rating'    => mt_rand( 0, 100 ) / 10,
        'thumbnail' => $thumbnail,
        'hash'      => substr($faker->sha256,0,12),
    ];
});
