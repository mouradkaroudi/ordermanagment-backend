<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SuggestedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $image = null;

        if($this->image) {
            $image = $this->image->storage_type == 'local' ? asset($this->image->resource) : $this->image->resource;
        }

        return [
            'id' => $this->id,
            'store' => $this->store ?? '',
            'image_id' => $this->image_id,
            'image_url' => $image,
            'delivery_method_id' => $this->delivery_method_id,
            'category_id' => $this->category_id,
            'user' => $this->user,
            'sell_price' => $this->sell_price ?? '',
            'sku' => $this->sku ?? '',
            'cost' => $this->cost ?? 0,
            'is_new' => $this->is_new ?? false
        ];
    }
}
