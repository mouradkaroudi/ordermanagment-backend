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

        $file_id = request()->input('file_id');

        if($file_id) {
            return [
                'file_id' => ['required', 'exists:App\Models\File,id'],
            ];
        }

        $refRules = ['required'];
        $skuRules = ['required'];
        
        if(request()->method() == 'PUT') {
            $refRules[] = Rule::unique('products')->ignore($this->product->id);
            $skuRules[] = Rule::unique('products')->ignore($this->product->id);
        }else{
            $refRules[] = 'unique:products,ref';
            $skuRules[] = 'unique:products,sku';
        }

        return [
            'ref' => $refRules,
            'name' => ['required'],
            'sku' => $skuRules,
            'image_id' => ['sometimes', 'exists:App\Models\File,id'],
            'mainRef' => ['nullable', 'string'],
            'suppliers.*.supplier_id' => ['sometimes', 'exists:App\Models\Supplier,id'],
            'location_id' => ['sometimes', 'exists:App\Models\Location,id'],
            'category_id' => ['sometimes', 'exists:App\Models\Category,id'],
            'cost' => ['required', 'numeric', 'gt:0'],
            'is_paid' => ['sometimes', 'bool']
        ];
    }
}
