<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class DelegatePurchaseOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $product_id = $this->order->product_id;

        $product = ProductResource::make(Product::where('id', $product_id)->first());
        
        return [
            'id' => $this->id,
            'order' => [
                'id' => $this->order->id,
                'status' => $this->order->status,
                'quantity' => $this->order->quantity
            ],
            'formattedCost' => $product->cost . 'SAR',
            'product' => $product
        ];
    }
}
