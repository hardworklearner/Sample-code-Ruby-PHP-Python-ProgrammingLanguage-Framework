<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Truncate tables to reset auto-increment IDs
        // Replace 'table1' and 'table2' with the names of your tables
        DB::table('categories')->truncate();
        // Add more tables if needed

        // Enable foreign key checks
        \App\Models\Category::factory()->create([
            'category_name' => 'Morning',
            'description' => 'Morning',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Lunch',
            'description' => 'Lunch',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Dinner',
            'description' => 'Dinner',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Snack',
            'description' => 'Snack',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Body',
            'description' => 'Body',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Exercise',
            'description' => 'Exercise',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Diary',
            'description' => 'Diary',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Weight',
            'description' => 'Weight',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Food Time',
            'description' => 'Food Time',
        ]);
        \App\Models\Category::factory()->create([
            'category_name' => 'Breakfast',
            'description' => 'Breakfast',
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
