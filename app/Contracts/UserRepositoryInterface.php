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

    /**
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function socialAccount(array $where, $columns = ['*']);

}
