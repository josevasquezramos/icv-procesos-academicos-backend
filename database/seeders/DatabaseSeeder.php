<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'full_name' => 'Test User',
            'dni' => '12345678',
            'document' => 'DOC123456',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '+1234567890',
            'address' => 'Test Address',
            'birth_date' => '1990-01-01',
            'role' => '"student"',
            'gender' => 'male',
            'country' => 'Peru',
            'country_location' => 'Lima',
            'timezone' => 'America/Lima',
            'status' => 'active',
            'email_verified_at' => now(),
            'synchronized' => true,
        ]);
    }
}
