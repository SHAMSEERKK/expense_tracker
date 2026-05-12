<?php

namespace Database\Seeders;

use App\Models\Category;
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
        foreach (['Food', 'Transportation', 'Entertainment', 'Utilities', 'Healthcare', 'Shopping'] as $name) {
            Category::firstOrCreate(['name' => $name], ['status' => true]);
        }

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Demo User', 'password' => 'password']
        );
    }
}
