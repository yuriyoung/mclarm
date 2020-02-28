<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateUser()
    {
//        $this->assertDatabaseHas('users', [
//            'email' => 'admin@xueyuanjun.com'
//        ]);

        $users = factory(\App\Models\User::class, 10)->make();
//        $this->assertNotNull($user->email);
        $this->assertCount(10,  $users);
    }

}
