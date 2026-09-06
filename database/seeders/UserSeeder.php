<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@smkn1airnaningan.sch.id'],
            [
                'name'     => 'Administrator Sistem',
                'username' => 'admin',
                'role'     => 'admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
