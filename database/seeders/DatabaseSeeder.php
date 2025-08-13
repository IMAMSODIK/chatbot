<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('123'),
            'file_identitas' => null,
        ]);

        \App\Models\User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'role' => 'user',
            'password' => bcrypt('123'),
            'file_identitas' => null,
        ]);

        \App\Models\User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'role' => 'user',
            'password' => bcrypt('123'),
            'file_identitas' => null,
        ]);
    }
}
