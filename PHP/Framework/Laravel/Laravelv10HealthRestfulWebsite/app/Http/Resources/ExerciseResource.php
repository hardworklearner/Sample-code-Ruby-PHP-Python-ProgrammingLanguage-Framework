<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
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
            'exercise_name' => $this->exercise_name,
            'calories_burned' => $this->calories_burned,
            'duration' => $this->duration,
            'category_id' => $this->category_id,
            'exercise_type' => $this->exercise_type,
            'exercise_level' => $this->exercise_level,
            'exercise_description' => $this->exercise_description,
            'exercise_image' => asset($this->exercise_image),
        ];
    }
}
