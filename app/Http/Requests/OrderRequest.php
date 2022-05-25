<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'orders' => ['required', 'array'],
            'orders.*.ref' => ['required', 'exists:App\Models\Product,ref'],
            'orders.*.store_id' => ['required', 'exists:App\Models\Store,id'],
            'orders.*.quantity' => ['required', 'numeric', 'gt:0']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'orders.*.ref.required' => 'The product ref is not valid',
            //'body.required' => 'A message is required',
        ];
    }
}
