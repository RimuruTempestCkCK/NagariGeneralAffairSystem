<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Admin
        User::updateOrCreate(
            ['email' => 'admin@banknagari.co.id'],
            [
                'name' => 'Administrator GAS',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // User Staff
        User::updateOrCreate(
            ['email' => 'staff@banknagari.co.id'],
            [
                'name' => 'Staff Umum',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
            ]
        );
    }
}
