<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah admin sudah ada
        if (!User::where('email', 'admin@kasrt.com')->exists()) {
            User::create([
                'name' => 'Admin Kas RT',
                'email' => 'admin@kasrt.com',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]);
            
            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists!');
        }
    }
}