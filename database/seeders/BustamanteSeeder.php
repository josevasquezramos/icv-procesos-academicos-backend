<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\EmploymentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BustamanteSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create(
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'full_name' => 'Administrador',
                'email' => 'adminstrategic@example.com',
                'password' => Hash::make('secret123'),
                'role' => ['admin', 'planner', 'moderator'],
            ]
        );

        // Usuario de prueba
        User::create(
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'full_name' => 'Test User',
                'email' => 'teststrategic@example.com',
                'password' => Hash::make('secret123'),
                'role' => ['user'],
            ]
        );
    }
}