<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\SocialAccountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * @var SocialAccountService
     */
    protected $service;

    public function __construct(UserRepositoryInterface $repository, SocialAccountService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function register()
    {

    }

    /**
     * Handle a login request to the application.
     *
     * @param \App\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws AuthenticationException
     */
    public function login(UserRequest $request)
    {
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        $credentials = $request->only([ $this->username(), 'password']);
        if ($token = auth('api')->attempt($credentials)) {
            $this->clearLoginAttempts($request);
            return $this->respondWithToken($token);
        }

        $this->incrementLoginAttempts($request);
        $this->sendFailedLoginResponse($request);
    }

    public function me()
    {
        return UserResource::make(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        try {
            $token = auth('api')->refresh();
            return $this->respondWithToken($token);
        } catch (TokenExpiredException $e) {
            //Do something
            return response()->json(['message' => $e->getMessage()]);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    protected function username()
    {
        return 'email';
    }

    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws AuthenticationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
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
