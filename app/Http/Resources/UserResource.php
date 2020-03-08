<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'verified_at' =>  $this->when($this->verified_at, (string)$this->verified_at),
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'deleted_at' => $this->when($this->deleted_at, (string)$this->deleted_at),
            'detail' => UserDetailResource::make($this->whenLoaded('detail')),
            'socials' => $this->when($this->socials, $this->socials),
        ];
    }
}
