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
            'id'=> $this->id,
            'name'=> $this->username,
            'email'=> $this->email,
            'created_at'=> $this->created_at->diffForHumans(),
            'updated_at'=> $this->updated_at->diffForHumans(),
            'formatted_address'=> $this->formatted_address,
            'tagline'=> $this->tagline,
            'about'=> $this->about,
            'location'=> $this->location,
            
        ];
    }
}
