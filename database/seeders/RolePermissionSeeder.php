<?php

namespace Database\Seeders;

use App\Support\SchoolRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed roles and permissions for the school management system.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';
        $permissions = [
            'view-dashboard',
            'manage-classes',
            'manage-subjects',
            'manage-teachers',
            'manage-students',
            'manage-guardians',
            'view-teacher-dashboard',
            'view-student-dashboard',
            'view-parent-dashboard',
            'view-accountant-dashboard',
        ];

        $permissionModels = collect();
        foreach ($permissions as $permission) {
            $permissionModels->put($permission, Permission::findOrCreate($permission, $guard));
        }

        foreach (SchoolRoles::all() as $roleName) {
            Role::findOrCreate($roleName, $guard);
        }

        Role::findByName(SchoolRoles::SUPER_ADMIN, $guard)->syncPermissions($permissionModels->values());
        Role::findByName(SchoolRoles::ADMIN, $guard)->syncPermissions($permissionModels->only([
            'view-dashboard',
            'manage-classes',
            'manage-subjects',
            'manage-teachers',
            'manage-students',
            'manage-guardians',
        ])->values());
        Role::findByName(SchoolRoles::TEACHER, $guard)->syncPermissions($permissionModels->only([
            'view-dashboard',
            'view-teacher-dashboard',
        ])->values());
        Role::findByName(SchoolRoles::STUDENT, $guard)->syncPermissions($permissionModels->only([
            'view-dashboard',
            'view-student-dashboard',
        ])->values());
        Role::findByName(SchoolRoles::PARENT, $guard)->syncPermissions($permissionModels->only([
            'view-dashboard',
            'view-parent-dashboard',
        ])->values());
        Role::findByName(SchoolRoles::ACCOUNTANT, $guard)->syncPermissions($permissionModels->only([
            'view-dashboard',
            'view-accountant-dashboard',
        ])->values());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
