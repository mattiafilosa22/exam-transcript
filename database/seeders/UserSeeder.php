<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => 17,
        ]);
        User::create([
            'name' => 'supervisor User',
            'email' => 'supervisor@example.com',
            'password' => bcrypt('password'),
            'role_id' => 18,
        ]);
        User::create([
            'name' => 'Default User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);
        User::create([
            'name' => 'Default 0 User',
            'email' => 'user_0@example.com',
            'password' => bcrypt('password'),
        ]);
        User::create([
            'name' => 'Default 1 User',
            'email' => 'user_1@example.com',
            'password' => bcrypt('password'),
        ]);
        User::create([
            'name' => 'Default 2 User',
            'email' => 'user_2@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
