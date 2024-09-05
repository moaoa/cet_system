<?php

namespace App\Http\Resources;

use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ref_number' => $this->ref_number,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'image' => $this->type == UserType::Student ? 'https://st2.depositphotos.com/3369547/11438/v/380/depositphotos_114380960-stock-illustration-graduation-cap-and-boy-icon.jpg' : 'https://www.clipartmax.com/png/small/284-2845924_graduate-icon-student-avatar-icon.png'
        ];
    }
}
