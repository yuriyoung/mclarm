<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserRepositoryInterface;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserDetailResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    /**
     * @var \App\Contracts\UserRepositoryInterface;
     */
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $users = $this->repository->orderBy('created_at', 'DESC')->paginate();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return UserResource
     * @throws \Exception
     */
    public function store(UserRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $this->repository->create($request->all());
        $user->detail()->create();

        // TODO: send notification for current account created
        // $user->notify();

        return UserResource::make($user);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return UserResource
     * @throws \Exception
     */
    public function show($id)
    {
        $user = $this->repository->with(['detail'])->find($id);

        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param int $id
     * @return UserResource
     * @throws \Exception
     */
    public function update(UserRequest $request, $id)
    {
        $data = collect($request->all())->forget(['password', 'email'])->toArray();
        $user = $this->repository->with(['detail'])->update($id, $data);
//        $user = $this->repository->updateWithRelations($id, ['detail'], $data);
        return UserResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->repository->destroy($id);
        return response('', 204);
    }

    /**
     * @param $id
     * @return UserResource
     * @throws \Exception
     */
    public function restore($id)
    {
        $user = $this->repository->findTrashed($id);
        $user->restore();
        return UserResource::make($user);
    }

    /**
     * @param $id
     * @return UserDetailResource
     * @throws \Exception
     */
    public function detail($id)
    {
        return UserDetailResource::make($this->repository->find($id)->detail);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function loginHistory($id)
    {
        $devices = $this->repository->getLoginHistory($id);
        return response()->json($devices);
    }
}
