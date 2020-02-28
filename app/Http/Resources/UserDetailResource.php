<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'avatar'  => $this->avatar,
            'qrcode' => $this->qrcode,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'career' => $this->career,
            'website' => $this->website,
            'github' => $this->github,
            'address_home' => $this->address_home,
            'address_work' => $this->address_work,
            'signature' => $this->signature,
            'about' => $this->about,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
