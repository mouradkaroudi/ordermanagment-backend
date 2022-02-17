<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'orders.*' => ['required', 'exists:App\Models\Order,id'],
            'delegate_id' => ['required', 'exists:App\Models\User,id']
        ];
    }
}
