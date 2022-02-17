<?php

namespace App\Http\Resources;

use App\Models\Location;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\User;
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

        $delegate = null;

        if($this->purchase_order) {
            $delegate = User::where('id', $this->purchase_order->delegate_id)->first(['id', 'name']);
        }
        

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'purchase_id' => $this->purchase_order ? $this->purchase_order->purchase_id : null,
            'product' => [
                'id' => $this->product->name,
                'name' => $this->product->name,
                'ref' => $this->product->ref,
                'location' => $this->product->location_id ? Location::where('id', $this->product->location_id)->first(['id', 'name']) : null
                //'image_url' => $this->product->image ? asset($this->image->path) : null,
            ],
            'delegate' => $delegate,
        ];
    }
}
