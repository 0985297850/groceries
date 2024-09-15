<?php

namespace App\Services;

use App\Strategy\Payments\PaymentGatewayInterface;

class VnPaymentService implements PaymentGatewayInterface
{
    public function createPayment($amount, $orderInfo, $transition_id)
    {
        $vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_ReturnUrl = config('vnpay.vnp_ReturnUrl');

        $vnp_TxnRef = $transition_id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount; // Số tiền
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = request()->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = http_build_query($inputData);
        $vnpSecureHash = hash_hmac('sha512', urldecode($query), $vnp_HashSecret);
        $vnp_Url .= "?" . $query . '&vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    public function validateResponse($inputData)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = urldecode(http_build_query($inputData));

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        if ($secureHash === $vnp_SecureHash) {
            return $inputData['vnp_ResponseCode'] == '00'; // 00 là thanh toán thành công
        }

        return false;
    }
}
