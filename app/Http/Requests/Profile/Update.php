<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class Update extends FormRequest
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
            'first_name' => 'nullable|string|between:3,100',
            'last_name' => 'nullable|string|between:3,100',
            'phone' => 'nullable|numeric|digits:10',
            'address' => 'nullable|string|min:5|max:50',
            'gender' => 'nullable|string|min:2|max:3',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }


    public function messages()
    {
        return [
            'first_name.string' => 'First name must be a string.',
            'first_name.between' => 'First name must be between 3 and 100 characters.',
            'last_name.string' => 'Last name must be a string.',
            'last_name.between' => 'Last name must be between 3 and 100 characters.',
            'phone.numeric' => 'Phone number must be numeric.',
            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'address.string' => 'Address must be a string.',
            'address.min' => 'Address must be at least 5 characters.',
            'address.max' => 'Address may not be greater than 50 characters.',
            'gender.string' => 'Gender must be a string.',
            'gender.min' => 'Gender must be at least 2 characters.',
            'gender.max' => 'Gender may not be greater than 3 characters.',
            'avatar.required' => 'The avatar field is required.',
            'avatar.image' => 'The file must be an avatar.',
            'avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg',
            'avatar.max' => 'The image may not be greater than 2048 kilobytes.',
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
