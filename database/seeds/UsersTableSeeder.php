<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(Faker::class);
        factory(App\Models\User::class, 10)->create()->each(function (App\Models\User $user) use($faker) {
            $user->detail()->save(factory(App\Models\UserDetail::class)->make());
            for ($i = 0; $i < random_int(1, 5); $i++) {
                DB::table('user_signed_logs')->insert([
                    'user_id' => $user->id,
                    'ip' => $faker->ipv4,
                    'device' => $faker->randomElement(['Mobile', 'Desktop', 'Tablet', 'Robot', 'Other']),
                    'platform' => $faker->randomElement(['Windows', 'Linux', 'Mac', 'Android', 'iOS', 'Other']),
                    'client' => $faker->randomElement(['MyApp', 'Chrome', 'FireFox', 'IE', 'Edge', 'Safari', 'Opera', 'Sogou', 'QQ', 'UC']),
                    'signed_at' => $faker->dateTime
                ]);
            }
        });
    }
}
