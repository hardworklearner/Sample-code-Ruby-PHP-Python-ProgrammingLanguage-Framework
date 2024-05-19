<?php

namespace Database\Factories;

use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Body>
 */
class BodyFactory extends Factory
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
            'body_size' => fake()->randomFloat(2,1,100),
            'user_id' => Arr::random($user_ids),
            'calculate_time' => now()
        ];
    }
}
