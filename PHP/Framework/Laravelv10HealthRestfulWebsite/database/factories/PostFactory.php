<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
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
        return [
            'title' => fake()->name(),
            'category_id' => Arr::random($cate_ids),
            'user_id' => Arr::random($user_ids),
            'description' => fake()->text(),
            'post_content' => fake()->text(),
        ];
    }
}
