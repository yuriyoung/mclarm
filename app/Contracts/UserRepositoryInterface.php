<?php


namespace App\Contracts;


interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     *
     * @param int $id
     * @return mixed
     */
    public function getLoginHistory(int $id);
}
