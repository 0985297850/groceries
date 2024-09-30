<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function updateOrderByTransaction($transaction_id)
    {
        return $this->model->where("transaction_id", "=", $transaction_id)->update(['payment_status' => 'completed']);
    }
}
