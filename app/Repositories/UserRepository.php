<?php


namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Models\SocialAccount;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository extends Repository implements UserRepositoryInterface
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

    /**
     * TODO: create a user login Event to insert login log
     *
     * @param int $id
     * @return Collection
     * @throws \App\Exceptions\RepositoryException
     */
    public function getLoginHistory(int $id)
    {
        $user_id = $this->find($id)->getKey();
        return DB::table('user_signed_logs')
            ->where('user_id', $user_id)
            ->orderByDesc('signed_at')
            ->get();
    }

    /**
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function socialAccount(array $where, $columns = ['*'])
    {
        /**
         * @var SocialAccount $account
         */
        $account = $this->app->make(SocialAccount::class);
        foreach ($where as $field => $value) {
            if(is_array($value)) {
                list($field, $condition, $val) = $value;
                $account = $account->where($field, $condition, $val);
            } else {
                $account = $account->where($field, '=', $value);
            }
        }

        return $account->newQuery()->first($columns);
    }
}
