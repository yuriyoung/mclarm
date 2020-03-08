<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    protected $wantJson = false;

    /**
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * @var SocialAccountService
     */
    protected $service;

    /**
     * @var array
     */
    protected $providers = [
        'github',
//        'qq',
//        'weixin',
//        'weibo'
    ];

    public function __construct(Request $request, UserRepositoryInterface $repository, SocialAccountService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->wantJson = $request->wantsJson();
    }

    /**
     * @param string $provider
     * @return mixed
     * @throws AuthenticationException
     */
    public function oauthRedirect(string $provider)
    {
        if(! $this->isDriverAllowed($provider)) {
            return $this->sendFailedResponse("{$provider} is not currently supported");
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return $this->sendFailedResponse($e->getMessage() . "aaaaaaaaaaa" ?: "Unable to login with {$provider}, try with another provider to login.");
        }
    }

    /**
     * @param string $provider
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws AuthenticationException
     */
    public function oauthCallback(string $provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return $this->sendFailedResponse($e->getMessage() ?: "Unable to login with {$provider}, try with another provider to login.");
        }

        // 1.check for provider_name and provider_id in returned user
        // 2.check for already has account
        // 3.if already found: update the user details
        // 4.else create a new user and social record
        // 5.login the user and redirect some page or returns a token if request want json
        $authUser = $this->service->handle($user, $provider);

        // wantJson for api
        if ($this->wantJson) {
            // generate a JWT token
            $token = Auth::guard('api')->fromUser($authUser);
            return $this->respondWithToken($token);
        }

        // for web page
        auth()->login($authUser, true);
        return redirect()->intended('/');
    }

    /**
     * @param null $message
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    protected function sendFailedResponse($message = null)
    {
        if ($this->wantJson) {
            return response()->json($message);
//            throw new AuthenticationException($message);
        }

        return redirect()->to('/')
            ->withErrors(['message' => $message ?: 'Unable to login, try with another provider to login.']);
    }

    protected function isDriverAllowed($driver)
    {
        return in_array($driver, $this->providers) && config()->has("services.{$driver}");
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
