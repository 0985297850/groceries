<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $user_service)
    {
    }

    public function index()
    {
        $id_user = Auth()->id();
        $user = $this->user_service->getUserLogin($id_user);
        return $this->responseSuccess($user, "SUCCESS");
    }
}
