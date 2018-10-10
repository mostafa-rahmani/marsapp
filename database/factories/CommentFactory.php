<?php

use Faker\Generator as Faker;

$factory->define(\App\Comment::class, function (Faker $faker) {
    return [
        'content' => $faker->paragraph(4),
        'user_id' => rand(1, 5),
        'design_id' => rand(1, 5)
    ];
});
