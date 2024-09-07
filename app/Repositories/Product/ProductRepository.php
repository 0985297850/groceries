<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getProductByCategory($params, $id)
    {
        $result = $this->model->select('*')->where('category_id', '=', $id);

        $per_page = $params->per_page ?? 10;

        if (isset($params->keyword)) {
            $result = $result->where('name', 'like', '%' . $params->keyword . '%');
        }

        return $result->paginate($per_page);
    }

    public function getProduct($params)
    {
        $result = $this->model;

        $per_page = $params->per_page ?? 10;

        if (isset($params->keyword)) {
            $result = $result->where('name', 'like', '%' . $params->keyword . '%');
        }

        return $result->paginate($per_page);
    }
}
