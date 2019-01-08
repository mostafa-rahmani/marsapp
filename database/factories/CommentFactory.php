<?php

use Faker\Generator as Faker;

$factory->define(\App\Comment::class, function (Faker $faker) {
    return [
        'content' => $faker->paragraph(4),
        'user_id' => factory(\App\User::class)->create(),
        'design_id' => factory(\App\Design::class)->create()
    ];
});
