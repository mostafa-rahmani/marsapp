<?php

use Faker\Generator as Faker;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
$factory->define(\App\Design::class, function (Faker $faker) {

    $image = Image::make($faker->imageUrl($width = 640, $height = 480))->encode();
    $filename = date('Y-m-d_h-m-s') . '_' . str_random('4') . '.jpeg';
    $image->save( storage_path('app/full_size/') . 'full_size_' . $filename);
    $image->widen(500, function ($constraint) {
        $constraint->upsize();
    });
    $image->save(storage_path('app/public/' . 'small_size' . $filename));
    return [
        'description' => $faker->sentence,
        'image' => $filename,
        'small_image' => Storage::url( 'small_size_' . $filename),
        'is_download_allowed' => true,
        'original_width' => '1980',
        'original_height' => '1200',
        'user_id' => factory(\App\User::class)->create()->id,
        'blocked'   => "0"
    ];
});
