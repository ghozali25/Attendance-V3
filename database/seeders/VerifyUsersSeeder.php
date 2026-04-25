<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VerifyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = [
            'ali@alitech.com',
            'idham.maulana@alitech.com',
            'maulana.hasan@alitech.com',
            'marsha@alitech.com',
            'novelda@alitech.com',
            'wisnu@alitech.com',
        ];

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();
            if ($user && !$user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }
        }
    }
}
