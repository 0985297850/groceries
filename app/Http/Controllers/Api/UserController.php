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

    public function update(Update $request)
    {
        try {
            $id_user = Auth()->id();

            if (!$id_user) {
                return $this->responseFail([], "FAILED", null, 404);
            }

            $params =  $request->validated();
            $file = $request->file('avatar');
            $user = $this->user_service->find($id_user);

            if ($file) {
                if ($user->profile?->avatar) {
                    $oldFilePath = $user->profile?->avatar;
                    if ($oldFilePath && file_exists(public_path($oldFilePath))) {
                        unlink(public_path($oldFilePath));
                    }
                }

                $name_file = "user";
                $dateFolder = now()->format('Y-m-d');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = "uploads/{$name_file}/{$dateFolder}/";
                $file->move(public_path($path), $filename);
                $params["avatar"] = $path . $filename;
            }

            $user->profile()->update($params);
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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:3,100',
            'last_name' => 'required|string|between:3,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|numeric|digits:10',
            'address' => 'required|string|min:5|max:50',
            'gender' => 'required|string|min:2|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $param_users = array_merge(
            $request->only(["email"]),
            ['password' => bcrypt($request->password)]
        );

        $user = $this->user_service->createUser($param_users);
        $user->profile()->create($request->only(['first_name', 'last_name', 'phone', 'address', 'gender']));

        $userWithProfile = User::with('profile')->find($user->id);

        return $this->responseSuccess($userWithProfile, 'User created successfully');
    }

    public function delete($id)
    {
        if ($id) {
            $this->user_service->deleteUser($id);

            return $this->responseSuccess([], "DELETED SUCCESSFULLY");
        }

        return $this->responseFail([], "DELETED FAILED");
    }
}
