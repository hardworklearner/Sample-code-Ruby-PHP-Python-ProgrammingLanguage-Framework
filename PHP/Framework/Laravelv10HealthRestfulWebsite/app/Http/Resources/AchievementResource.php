<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
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
            'achievement_name' => $this->achievement_name,
            'level' => $this->level,
            'category_id' => $this->category_id,
            'description' => $this->description
        ];
    }
}
