<?php

namespace App\Http\Resources;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends Controller
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
            'first_name' => $this->first_name,
            'other_names' => $this->other_names,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}