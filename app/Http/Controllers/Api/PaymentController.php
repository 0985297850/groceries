<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderRequest;
use App\Services\OrderItemService;
use App\Services\OrderService;
use App\Services\VnPaymentService;
use Illuminate\Support\Facades\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected VnPaymentService $vn_payment_service,
        protected OrderService $order_service,
        protected OrderItemService $order_item_service,
    ) {}

    public function createPayment(OrderRequest $request)
    {
        $params = $request->validated();
        $create_order = $this->order_service->createOrder($params);
        if (isset($create_order)) {
            $this->order_item_service->createOrderItem($params, $create_order['id']);
        }

        $orderInfo = "Thanh toán sản phẩm!";
        $paymentUrl = $this->vn_payment_service->createPayment($params['amount'], $orderInfo, $create_order['transaction_id']);

        return redirect($paymentUrl);
    }

    public function callBack(Request $request)
    {
        if ($this->vn_payment_service->validateResponse($request->all())) {
            return response()->json(['status' => 'success', 'message' => 'Thanh toán thành công']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Thanh toán thất bại']);
        }
    }
}
