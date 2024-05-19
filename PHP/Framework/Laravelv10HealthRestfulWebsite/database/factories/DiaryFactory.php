<?php

namespace Database\Factories;

use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diary>
 */
class DiaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user_ids = User::get()->pluck('id')->toArray();
        return [
            'diary_content' => fake()->text(),
            'user_id' => Arr::random($user_ids),
            'diary_time' => now()
        ];
    }
}
