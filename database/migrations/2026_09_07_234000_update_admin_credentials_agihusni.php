<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update user admin (ID 180 atau role admin)
        $admin = User::where('role', 'admin')->orWhere('id', 180)->first();
        if ($admin) {
            $admin->update([
                'username' => 'agihusni',
                'password' => Hash::make('J@ng@NDikaSih(0'),
            ]);
        }

        // Update juga user Agi Husni (ID 179) jika ada
        $userAgi = User::find(179);
        if ($userAgi) {
            $userAgi->update([
                'password' => Hash::make('J@ng@NDikaSih(0'),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert username back to admin
        $admin = User::where('role', 'admin')->orWhere('id', 180)->first();
        if ($admin) {
            $admin->update([
                'username' => 'admin',
            ]);
        }
    }
};
