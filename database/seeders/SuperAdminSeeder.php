<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the primary super admin login.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate([
            'email' => 'admin@school.test',
        ], [
            'name' => 'School Administrator',
            'password' => Hash::make('password'),
        ]);

        $admin->forceFill([
            'email_verified_at' => now(),
        ])->save();

        $admin->syncRoles([SchoolRoles::SUPER_ADMIN]);
    }
}
