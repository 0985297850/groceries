<?php

namespace App\Services;

use App\Repositories\User\UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function getUserLogin($id)
    {
        return $this->userRepository->getUserLogin($id);
    }

    public function find($id)
    {
        return $this->userRepository->find($id);
    }
}
