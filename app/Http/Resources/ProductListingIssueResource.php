<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListingIssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'issue' => $this->issue,
            'product' => $this->product,
            'created_at' => $this->created_at ? date('Y-m-d H:i', strtotime($this->created_at)) : null,
            'resolved_at' => $this->resolved_at ? date('Y-m-d H:i', strtotime($this->resolved_at)) : null,
            'resolved_by' => $this->resolved_user,
            'created_by' => $this->created_user,
        ];
    }
}
