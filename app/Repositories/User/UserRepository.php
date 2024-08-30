<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\User\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAllUser()
    {
        return $this->model->getAll();
    }

    public function getUserLogin($id)
    {
        return $this->model->find($id);
    }
}
