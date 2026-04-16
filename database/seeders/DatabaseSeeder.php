<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Owner
        User::updateOrCreate(
            ['email' => 'owner@warehouse.com'],
            [
                'name' => 'Owner',
                'password' => bcrypt('password123'),
                'role' => 'owner',
            ]
        );

        // 2. Finance
        User::updateOrCreate(
            ['email' => 'finance@warehouse.com'],
            [
                'name' => 'Finance Staff',
                'password' => bcrypt('password123'),
                'role' => 'finance',
            ]
        );

        // 3. 5 Admins
        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "admin{$i}@warehouse.com"],
                [
                    'name' => "Admin Gudang {$i}",
                    'password' => bcrypt('password123'),
                    'role' => 'admin',
                ]
            );
        }
    }
}
