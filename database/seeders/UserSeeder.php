<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'modules' => json_encode(['dashboard', 'customers', 'projects', 'reports', 'settings']),
        ]);

        // Customer
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'modules' => json_encode(['dashboard', 'projects']),
        ]);

        // Designer
        User::create([
            'name' => 'Designer User',
            'email' => 'designer@example.com',
            'password' => Hash::make('password'),
            'role' => 'designer',
            'modules' => json_encode(['dashboard', 'projects', 'files']),
        ]);
    }
}

