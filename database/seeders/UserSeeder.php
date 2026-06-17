<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@jjhouse.com'],
            [
                'name' => 'Admin Jjhouse',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1, Jakarta',
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Karyawan (Employee)
        $karyawan = User::firstOrCreate(
            ['email' => 'anita@jjhouse.com'],
            [
                'name' => 'Anita Wulandari',
                'password' => Hash::make('password'),
                'phone' => '081234567893',
                'address' => 'Jl. Karyawan No. 4, Jakarta',
            ]
        );
        if (!$karyawan->hasRole('karyawan')) {
            $karyawan->assignRole('karyawan');
        }

        // Kasir (Cashier)
        $kasir = User::firstOrCreate(
            ['email' => 'doni@jjhouse.com'],
            [
                'name' => 'Doni Hermawan',
                'password' => Hash::make('password'),
                'phone' => '081234567894',
                'address' => 'Jl. Kasir No. 5, Jakarta',
            ]
        );
        if (!$kasir->hasRole('kasir')) {
            $kasir->assignRole('kasir');
        }

        // Customers
        $customer1 = User::firstOrCreate(
            ['email' => 'budi@jjhouse.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'address' => 'Jl. Customer No. 2, Bandung',
            ]
        );
        if (!$customer1->hasRole('user')) {
            $customer1->assignRole('user');
        }

        $customer2 = User::firstOrCreate(
            ['email' => 'siti@jjhouse.com'],
            [
                'name' => 'Siti Aminah',
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'address' => 'Jl. Customer No. 3, Surabaya',
            ]
        );
        if (!$customer2->hasRole('user')) {
            $customer2->assignRole('user');
        }
    }
}
