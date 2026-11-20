<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@aurie.test'],
            [
                'name' => 'Aurie Admin',
                'password' => 'ChangeMe#2026',
                'email_verified_at' => now(),
            ]
        );

        $admin->forceFill([
            'role' => 'admin',
            'status' => 'active',
        ])->save();
    }
}
