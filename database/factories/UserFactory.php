<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'email_verified_at' => now(),
        'remember_token' => Str::random(10),
    ];
});

$factory->define(App\Models\UserDetail::class, function (Faker $faker) {
    $gender = $faker->randomElement(['男', '女']);
    return [
        'first_name' => $faker->firstName($gender),
        'last_name' => $faker->lastName,
        'avatar' => $faker->imageUrl(640, 480, 'cats'), // http://lorempixel.com/
//        'qrcode' => $faker->url,
        'gender' => $gender,
        'birthday' => $faker->dateTimeBetween(),
        'career' => $faker->jobTitle,
//        'website' => $faker->url,
        'address_home' => $faker->streetAddress,
        'address_work' => $faker->address,
        'signature' => $faker->text(120),
        'about' => $faker->paragraph(5),
    ];
});

$factory->define(App\Models\UserDevice::class, function (Faker $faker) {
    return [
        'ip' => $faker->ipv4,
        'device' => $faker->randomElement(['手机', '台式机', '网页', '其他']),
        'created_at' => $faker->dateTime
    ];
});

