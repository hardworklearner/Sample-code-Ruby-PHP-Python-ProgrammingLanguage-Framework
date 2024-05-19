<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
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
            'meal_name' => $this->meal_name,
            'calories_provide' => $this->calories_provide,
            'meal_time' => $this->meal_time,
            'category_id' => (int)$this->category_id,
            'food_time' => $this->food_time,
            'user_id' => (int)$this->user_id,
            'description' => $this->description,
            'picture' => asset($this->picture),
        ];
    }
}
