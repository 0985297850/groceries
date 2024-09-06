<?php

namespace App\Repositories\Product;

interface ProductRepositoryInterface
{
    public function getProductByCategory($params, $id);
}
