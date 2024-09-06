<?php

namespace App\Services;

use App\Repositories\Product\ProductRepository;

class ProductService
{
    public function __construct(
        protected ProductRepository $product_repo
    ) {}

    public function createProduct($params)
    {
        return $this->product_repo->create($params);
    }

    public function getProductByCategory($params, $id)
    {
        return $this->product_repo->getProductByCategory($params, $id);
    }
}
