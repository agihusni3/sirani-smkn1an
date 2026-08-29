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
                'name' => 'Admin TU / Super Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
