<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class DelegateOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $product_image_url = null;
        $supplier = null;

        if ($this->product->image) {
            $product_image_url = $this->product->image->storage_type == 'local' ? asset($this->product->image->resource) : $this->product->image->resource;
        }

        if($this->product->supplier)  {
            $supplier = [
                'id' => $this->product->supplier->id,
                'name' => $this->product->supplier->name
            ];
        }
        
        $quantity = $this->products->sum('quantity');
        $rest_quantity = $quantity - $this->purchases->sum('quantity');

        // total cost
        $total_cost = $this->is_paid ? 0 : $rest_quantity * $this->product_cost;

        return [
            'id' => $this->id,
            'status' => $this->status,
            'quantity' => $quantity,
            'rest_quantity' => $rest_quantity,
            'total_cost' => $total_cost,
            'formattedCost' => isset($this->product_cost) ? $this->product_cost . 'SAR' : null,
            'product' => [
                // We clone these value from the product so if someone edited will not affect the order
                'id' => $this->product_id,
                'cost' => $this->product_cost,
                //
                'name' => $this->product->name,
                'ref' => $this->product->ref,
                'mainRef' => $this->product->mainRef,
                'image_url' => $product_image_url,    
                'supplier' => $supplier
            ]
        ];
    }
}
