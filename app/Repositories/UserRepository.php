<?php


namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{
    /**
     * Return the model backing this repository.
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }
}
