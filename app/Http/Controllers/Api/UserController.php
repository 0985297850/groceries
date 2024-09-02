<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\Update;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct(protected UserService $user_service) {}

    public function profile()
    {
        $id_user = Auth()->id();
        $user = $this->user_service->getUserLogin($id_user);
        return $this->responseSuccess($user, "SUCCESS");
    }

    public function update(Update $request, $id)
    {
        try {
            if (!$id) {
                return $this->responseFail([], "FAILED", null, 404);
            }

            $params =  $request->validated();
            $user = $this->user_service->find($id);
            $user->update($params);
            return $this->responseSuccess($user, "SUCCESS");
        } catch (\Exception $e) {
            return $this->responseFail([], $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $users = $this->user_service->getUserAll($params);

        $response = [
            'data' => $users->items(),
            'current_page' => $users->currentPage(),
            'total_pages' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'total_items' => $users->total(),
        ];

        return $this->responseSuccess($response, "SUCCESSFULLY");
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|min:6',
                'phone' => 'required|numeric|digits:10',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $param_users = array_merge(
                $request->only(["email", "phone", 'user_name']),
                ['password' => bcrypt($request->password)]
            );

            $user = $this->user_service->createUser($param_users);

            return $this->responseSuccess($user, 'User created successfully');
        } catch (\Exception $e) {
            return $this->responseFail([], $e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($id) {
            $this->user_service->deleteUser($id);

            return $this->responseSuccess([], "DELETED SUCCESSFULLY");
        }

        return $this->responseFail([], "DELETED FAILED");
    }

    public function edit($id)
    {
        $user = $this->user_service->find($id);
        if ($user)
            return $this->responseSuccess($user);

        return $this->responseFail([]);
    }
}
