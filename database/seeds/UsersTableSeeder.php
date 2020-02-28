<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\User::class, 10)->create()->each(function (App\Models\User $user) {
            $user->detail()->save(factory(App\Models\UserDetail::class)->make());
            $user->loginDevices()->saveMany(factory(App\Models\UserDevice::class, random_int(1,5))->make());
        });
    }
}
