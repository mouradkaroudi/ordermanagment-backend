<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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

        $refRules = ['required'];
        $skuRules = ['required'];
        
        if(request()->method() == 'PUT') {
            $refRule[] = Rule::unique('products')->ignore($this->product->id);
            $skuRules[] = Rule::unique('products')->ignore($this->product->id);
        }else{
            $refRules[] = 'unique:products';
            $skuRules[] = 'unique:products';
        }

        return [
            'ref' => $refRules,
            'name' => ['required'],
            'sku' => $skuRules,
            'image_id' => ['sometimes', 'exists:App\Models\File,id'],
            //'mainRef' => ['required'],
            'supplier_id' => ['required', 'exists:App\Models\Supplier,id'],
            'location_id' => ['required', 'exists:App\Models\Location,id'],
            'category_id' => ['required', 'exists:App\Models\Category,id'],
            'cost' => ['required', 'numeric', 'gt:0'],
            'is_paid' => ['sometimes', 'bool']
        ];
    }
}
