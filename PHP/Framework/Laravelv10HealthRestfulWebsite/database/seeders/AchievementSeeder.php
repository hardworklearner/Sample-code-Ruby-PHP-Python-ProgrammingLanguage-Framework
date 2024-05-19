<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Truncate tables to reset auto-increment IDs
        DB::table('achievements')->truncate();
        Achievement::factory()
            ->count(50)
            ->create();
        Schema::enableForeignKeyConstraints();
    }
}
