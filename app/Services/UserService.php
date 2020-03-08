<?php


namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Contracts\UserRepositoryInterface;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ProviderUser|\App\Models\User $user
     */
    public function handle($user)
    {

    }
}
