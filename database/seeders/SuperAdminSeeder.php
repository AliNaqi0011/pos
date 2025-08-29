<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@example.com'
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'phone_number' => '+1234567890',
            'is_verified' => true,
            'email_verified_at' => now(),
            'status' => 'approved',
            'payment_verified' => true,
        ]);

        $superAdmin->assignRole('super_admin');

        $admin = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'phone_number' => '+1234567891',
            'is_verified' => true,
            'email_verified_at' => now(),
            'status' => 'approved',
            'payment_verified' => true,
        ]);

        $admin->assignRole('admin');

        $seller = User::firstOrCreate([
            'email' => 'seller@example.com'
        ], [
            'name' => 'Seller User',
            'password' => Hash::make('password'),
            'phone_number' => '+1234567892',
            'is_verified' => true,
            'email_verified_at' => now(),
            'status' => 'approved',
            'payment_verified' => true,
        ]);

        $seller->assignRole('seller');
    }
}