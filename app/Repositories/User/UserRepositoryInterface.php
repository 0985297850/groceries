<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function getAllUser();

    public function getUserLogin($id);
}
