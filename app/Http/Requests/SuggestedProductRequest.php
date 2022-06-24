<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SuggestedProductRequest extends FormRequest
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
            'store_id' => ['required_without:is_local', 'required_if:is_local,false', 'exists:App\Models\Store,id'],
            'image_id' => ['required', 'exists:App\Models\File,id'],
            'category_id' => ['required', 'exists:App\Models\Category,id'],
            'delivery_method_id' => ['required', 'exists:App\Models\DeliveryMethod,id'],
            // if product is from a store
            'sell_price' => 'required_without:is_local|nullable|numeric',
            'sku' => 'required_without:is_local|nullable|string',
            // if product is from a local market
            'cost' => ['required_if:is_local,true', 'nullable', 'numeric']
        ];
    }
}
