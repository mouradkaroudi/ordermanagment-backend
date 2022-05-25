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

        $reviewer = $this->reviewer ? [
            'id' => $this->reviewer->id,
            'name' => $this->reviewer->name
        ] : null;

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order' => new OrderResource( $this->order ),
            'delegate' => [
                'id' => $this->delegate->id,
                'name' => $this->delegate->name
            ],
            'quantity' => $this->quantity,
            'status' => $this->status,
            'missing_quantity' => $this->missing_quantity,
            'reviewer' => $reviewer
        ];
    }
}
