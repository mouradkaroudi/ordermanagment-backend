<?php

namespace App\Http\Resources;

use App\Models\Usermeta;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $abilities = Usermeta::where(['user_id' => $this->id, 'key' => 'abilities'])->first('value');

        if(!empty($abilities)) {
            $abilities = unserialize($abilities['value']);
        }else{
            $abilities = [];
        }


        return [
            'id' => $this->id,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->role,
            'abilities' => $abilities,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
