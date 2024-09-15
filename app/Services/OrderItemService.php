<?php

namespace App\Services;

use App\Repositories\OrderItem\OrderItemRepository;
use Illuminate\Support\Facades\DB;

class OrderItemService
{
    public function __construct(
        protected OrderItemRepository $order_item_repo
    ) {}

    public function createOrderItem($params, $order_id)
    {
        $data = [];
        DB::beginTransaction();
        try {
            foreach ($params as $item) {
                $product = $this->order_item_repo->find($item['product_id']);
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Trong kho đã hết sản phẩm!");
                }

                $product->decrement('quantity', $item['quantity']);

                $data[] = [
                    'order_id' => $order_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $this->order_item_repo->insert($data);

            return response()->json(['message' => 'Items created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
