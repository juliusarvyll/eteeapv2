<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'roles' => 'admin'
        ]);

        // Create Assessment User
        User::create([
            'name' => 'Assessment Officer',
            'email' => 'assessment@example.com',
            'password' => Hash::make('password'),
            'roles' => 'assessor'
        ]);


        // Create Faculty User
        User::create([
            'name' => 'Bao',
            'email' => 'bao@example.com',
            'password' => Hash::make('password'),
            'roles' => 'bao'
        ]);
    }
}
