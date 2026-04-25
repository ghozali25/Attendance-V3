<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ali',
                'email' => 'ali@alitech.com',
                'phone' => '081234567890',
                'address' => 'Jakarta',
                'gender' => 'male',
                'group' => 'employee',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Idham Maulana',
                'email' => 'idham.maulana@alitech.com',
                'phone' => '081234567891',
                'address' => 'Jakarta',
                'gender' => 'male',
                'group' => 'employee',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Maulana Hasan',
                'email' => 'maulana.hasan@alitech.com',
                'phone' => '081234567892',
                'address' => 'Jakarta',
                'gender' => 'male',
                'group' => 'employee',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Marsha',
                'email' => 'marsha@alitech.com',
                'phone' => '081234567893',
                'address' => 'Jakarta',
                'gender' => 'female',
                'group' => 'employee',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Novelda',
                'email' => 'novelda@alitech.com',
                'phone' => '081234567894',
                'address' => 'Jakarta',
                'gender' => 'female',
                'group' => 'employee',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Wisnu',
                'email' => 'wisnu@alitech.com',
                'phone' => '081234567895',
                'address' => 'Jakarta',
                'gender' => 'male',
                'group' => 'employee',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'phone' => $user['phone'],
                    'address' => $user['address'],
                    'gender' => $user['gender'],
                    'group' => $user['group'],
                    'password' => $user['password'],
                ]
            );
        }
    }
}
