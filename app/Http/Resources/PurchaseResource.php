<?php

namespace App\Http\Resources;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $order_delegate = PurchaseOrder::with('delegate')->where('purchase_id', $this->id)->first();

        return [
            'id' => $this->id,
            'total_cost' => $this->total_cost,
            //'orders_count' => count($this->orders),
            'orders' => $this->orders,
            'delegate' => $order_delegate->delegate
        ];
    }
}
