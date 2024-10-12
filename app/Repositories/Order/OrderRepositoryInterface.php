<?php

namespace App\Repositories\Order;

interface OrderRepositoryInterface
{
    public function updateOrderByTransaction($transaction_id);

    public function getHistoryByUser($user_id, $params);
}
