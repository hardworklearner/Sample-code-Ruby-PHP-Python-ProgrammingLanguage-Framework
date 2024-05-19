<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Arr;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cate_ids = Category::get()->pluck('id')->toArray();
        return [
            'achievement_name' => fake()->name(),
            'level' => fake()->randomElement([1, 2, 3]),
            'category_id' => Arr::random($cate_ids),
            'description' => fake()->text()
        ];
    }
}
