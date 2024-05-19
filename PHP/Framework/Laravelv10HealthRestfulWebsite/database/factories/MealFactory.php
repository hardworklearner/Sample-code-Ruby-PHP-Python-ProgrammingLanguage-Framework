<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cate_ids = Category::get()->pluck('id')->toArray();
        $user_ids = User::get()->pluck('id')->toArray();
        $path = 'storage/images/meals/';
        $id = fake()->numberBetween(1, 10);
        $path .= $id . '.jpg';
        return [
            'meal_time' => now(),
            'picture' => $path,
            'description' => fake()->text(),
            'food_time' => Arr::random(['Morning', 'Breakfast', 'Lunch', 'Dinner', 'Snack']),
            'meal_name' => fake()->name(),
            'calories_provide' => fake()->randomFloat(2,1,500),
            'category_id' => Arr::random($cate_ids),
            'user_id' => Arr::random($user_ids),
        ];
    }
}
