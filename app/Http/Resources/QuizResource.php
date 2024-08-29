<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id,
            'note' => $this->note,
            'start_time' => $this->pivot->start_time,
            'end_time' => $this->pivot->end_time,
            'teacher_name' => $this->user->name,
        ];
    }
}