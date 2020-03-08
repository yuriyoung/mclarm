<?php


namespace App\Services;

use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Contracts\UserRepositoryInterface;

class SocialAccountService
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
     * @param ProviderUser $providerUser
     * @param string $provider
     * @return User|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function handle(ProviderUser $providerUser, string $provider)
    {
        /**
         * @var SocialAccount $account
         */
        $account = $this->repository->socialAccount([
            'provider_name' => $provider,
            'provider_id' => $providerUser->getId()
        ]);

        if(! $account) {
            /** @var User $user */
            $user = $this->repository->firstOrCreate([
                'email' => $providerUser->getEmail(),
                'name' => $providerUser->getName()
            ]);
            // create default record for user detail ?
            if(!$user->detail())
                $user->detail()->create();

            $user->socials()->create([
                'provider_name' => $provider,
                'provider_id' => $providerUser->getId(),
                'nickname' => $providerUser->getNickname(),
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'avatar' => $providerUser->getAvatar(),
                'access_token' =>  $providerUser->token,
                'refresh_token' => $providerUser->refreshToken,
                'expires_in' => $providerUser->expiresIn,
            ]);

            return $user;
        }

        return $account->user;
    }
}
