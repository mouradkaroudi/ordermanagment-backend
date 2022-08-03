<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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

        if($this->product->image) {
            $product_image_url = $this->product->image->storage_type == 'local' ? asset($this->product->image->resource) : $this->product->image->resource;
        }

        $quantity = $this->products->sum('quantity');

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_cost' => $this->product->cost,
            'product_name' => $this->product->name,
            'product_ref' => $this->product->ref,
            'product_mainRef' => $this->product->mainRef,
            'product_image' => $product_image_url,
            'is_paid' => $this->is_paid,
            'status' => $this->status,
            'suppliers' => ProductSuppliersResource::collection($this->product?->suppliers),
            'delegate' => $this->delegate,
            'quantity' =>  $quantity,
            'rest_quantity' => $quantity - $this->purchases->where('status','!=', 'under_review')->sum('quantity'),
            'total_cost' => $this->products->sum('total_amount'),
            'created_at' => date('Y-m-d H:i', strtotime($this->created_at)),
            'updated_at' => date('Y-m-d H:i', strtotime($this->updated_at)),
        ];
    }
}
