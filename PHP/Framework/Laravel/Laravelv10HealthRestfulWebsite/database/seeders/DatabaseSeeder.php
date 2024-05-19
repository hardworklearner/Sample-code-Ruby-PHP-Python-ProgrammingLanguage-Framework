<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Category;
use App\Models\Achievement;
use App\Models\Exercise;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            AchievementSeeder::class,
            WeightSeeder::class,
            BodySeeder::class,
            DiarySeeder::class,
            MealSeeder::class,
            ExerciseSeeder::class,
            PostSeeder::class,
        ]);
        $user_ids = User::get()->pluck('id')->toArray();
        $category_ids = Category::get()->pluck('id')->toArray();
        $achievement_ids = Achievement::get()->pluck('id')->toArray();
        $exercise_ids = Exercise::get()->pluck('id')->toArray();

        DB::table('achievement_user')->truncate();
        DB::table('category_user')->truncate();
        DB::table('exercise_user')->truncate();
        foreach ($user_ids as $user_id) {
            $assigned_achievements = array_rand($achievement_ids, 3);
            $achievement_data = array_map(function ($achievement_id) use ($user_id, $achievement_ids) {
                return [
                    'user_id' => $user_id,
                    'achievement_id' => $achievement_ids[$achievement_id],
                    'complete_time' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $assigned_achievements);
            DB::table('achievement_user')->insert($achievement_data);
        }

        foreach ($user_ids as $user_id) {
            $assigned_categories = array_rand($category_ids, 3);
            $categories_data = array_map(function ($category_id) use ($user_id, $category_ids) {
                return [
                    'user_id' => $user_id,
                    'category_id' => $category_ids[$category_id],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $assigned_categories);
            DB::table('category_user')->insert($categories_data);
        }

        foreach ($user_ids as $user_id) {
            $assigned_exercises = array_rand($exercise_ids, 3);
            $exercise_data = array_map(function ($exercise_id) use ($user_id, $exercise_ids) {
                return [
                    'user_id' => $user_id,
                    'exercise_id' => $exercise_ids[$exercise_id],
                    'exercise_time' => now(),
                    'duration' => random_int(1, 60),
                    'calories_burned' => random_int(1, 500),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $assigned_exercises);
            DB::table('exercise_user')->insert($exercise_data);
        }

        $admin_data = [
            [
                'name' => 'Admin',
                'email' => 'admin1@example.com',
                'password' => bcrypt('12345678'),
                'role'  => User::getRole('admin'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Admin 1',
                'email' => 'admin2@example.com',
                'password' => bcrypt('12345678'),
                'role'  => User::getRole('admin'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        DB::table('users')->insert($admin_data);
    }
}
