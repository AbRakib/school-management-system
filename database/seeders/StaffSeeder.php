<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Seed non-teaching staff login accounts.
     */
    public function run(): void
    {
        $staff = [
            [
                'name' => 'Accounts Officer',
                'email' => 'accountant@school.test',
                'role' => SchoolRoles::ACCOUNTANT,
            ],
            [
                'name' => 'Office Admin',
                'email' => 'officeadmin@school.test',
                'role' => SchoolRoles::ADMIN,
            ],
        ];

        foreach ($staff as $member) {
            $user = User::query()->updateOrCreate([
                'email' => $member['email'],
            ], [
                'name' => $member['name'],
                'password' => Hash::make('password'),
            ]);

            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();

            $user->syncRoles([$member['role']]);
        }
    }
}
