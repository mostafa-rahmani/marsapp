<?php

use Faker\Generator as Faker;

$factory->define(\App\Design::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'design_url' => $faker->url,
        'download_url' => $faker->url,
        'is_download_allowed' => false,
        'download_resolution' => '1960x1200',
        'user_id' => rand(1, 5)
    ];
});
