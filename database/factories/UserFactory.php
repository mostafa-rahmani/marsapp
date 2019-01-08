<?php

use Faker\Generator as Faker;
use App\User;
$factory->define(User::class, function (Faker $faker) {
    return [

        'username' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'profile_image' => null,
        'bio' => $faker->sentence,
        'instagram' => null,
        'profile_background' => null,
        'blocked' => "0"
    ];
});
