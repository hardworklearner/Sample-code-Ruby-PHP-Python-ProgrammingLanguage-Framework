<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = 'storage/images/exercises/';
        $id = fake()->numberBetween(1, 5);
        $path .= $id . '.jpg';
        $cate_ids = Category::get()->pluck('id')->toArray();
        return [
            'exercise_name' => fake()->name(),
            'calories_burned' => fake()->randomFloat(2, 1, 500),
            'duration' => fake()->numberBetween(1, 60),
            'exercise_type' => fake()->numberBetween(1, 5),
            'exercise_level' => fake()->numberBetween(1, 4),
            'exercise_description' =>fake()->text(), 
            'exercise_image' => $path,
            'category_id' => Arr::random($cate_ids),
        ];
    }
}
