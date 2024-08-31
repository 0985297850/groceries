<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\Update;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $user_service) {}

    public function index()
    {
        $id_user = Auth()->id();
        $user = $this->user_service->getUserLogin($id_user);
        return $this->responseSuccess($user, "SUCCESS");
    }

    public function update(Update $request)
    {
        try {
            $id_user = Auth()->id();
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
}
