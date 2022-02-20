<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'ref' => $this->ref,
            'name' => $this->name,
            'image_url' => $image,
            'sku' => $this->sku,
            'mainRef' => $this->mainRef,
            'cost' => $this->cost,
            'is_paid' => $this->is_paid,
            'supplier' => $this->supplier,
            'location' => $this->location,
            'category' => $this->category,
        ];
    }
}
