<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'total_amount' => 'required|numeric|min:0',
            'list_order_item' => 'required|array|min:1',
            'list_order_item.*.product_id' => 'required|integer|exists:products,id',
            'list_order_item.*.quantity' => 'required|integer|min:1',
        ];
    }


    public function messages()
    {
        return [
            'total_amount.required' => 'Tổng số tiền là bắt buộc.',
            'total_amount.numeric' => 'Tổng số tiền phải là một số.',
            'list_order_item.required' => 'Danh sách đơn hàng là bắt buộc.',
            'list_order_item.*.product_id.required' => 'ID sản phẩm là bắt buộc.',
            'list_order_item.*.product_id.exists' => 'ID sản phẩm không hợp lệ.',
            'list_order_item.*.quantity.required' => 'Số lượng là bắt buộc.',
            'list_order_item.*.quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'status'     => 'fail',
                'message_key' => 'VALIDATE_FAILED',
                'errors' => $errors,
                'code' => 400,
                'data' => null
            ],
            JsonResponse::HTTP_BAD_REQUEST
        ));
    }
}
