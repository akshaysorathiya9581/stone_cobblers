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
            'first_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'modules' => null,
        ]);

        // Customer
        User::create([
            'name' => 'Customer User',
            'first_name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'modules' => json_encode(['dashboard', 'projects','files','quotes']),
        ]);
        
        // Designer
        User::create([
            'name' => 'Designer User',
            'first_name' => 'Designer',
            'email' => 'designer@example.com',
            'password' => Hash::make('password'),
            'role' => 'designer',
            'modules' => json_encode(['dashboard', 'projects']),
        ]);
    }
}

