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
    $gender = $faker->randomElement(['male', 'female', 'neuter']);
    return [
        'first_name' => $faker->firstName($gender),
        'last_name' => $faker->lastName,
        'avatar' => $faker->imageUrl(640, 480, 'cats'), // http://lorempixel.com/
//        'qrcode' => $faker->url,
        'gender' => $gender,
        'birthday' => $faker->date('Y-m-d', 'now'),
        'career' => $faker->jobTitle,
        'location' => $faker->locale,
        'company' => $faker->company,
        // $faker->url Exception  a error if faker_locale = 'zh_CN'
        'website' => 'www.' . $faker->word() . $faker->randomElement(['.com', '.org', '.io', '.cn', '.me']),
        'address_home' => $faker->streetAddress,
        'address_work' => $faker->address,
        'bio' => $faker->text(120),
        'about' => $faker->paragraph(5),
    ];
});
