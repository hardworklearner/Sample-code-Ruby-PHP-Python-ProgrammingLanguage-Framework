<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Truncate tables to reset auto-increment IDs
        DB::table('users')->truncate();
        $admin_data = [
            [
                'name' => 'Admin',
                'email' => 'admin1@example.com',
                'password' => bcrypt('12345678'),
                'role'  => User::getRole('admin'),
            ],
            [
                'name' => 'Admin 1',
                'email' => 'admin2@example.com',
                'password' => bcrypt('12345678'),
                'role'  => User::getRole('admin'),
            ]
        ];
        DB::table('users')->insert($admin_data);
        Schema::enableForeignKeyConstraints();
    }
}
