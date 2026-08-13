<?php

namespace App\Support;

use App\Models\User;

class SchoolRoles
{
    public const SUPER_ADMIN = 'super-admin';

    public const ADMIN = 'admin';

    public const TEACHER = 'teacher';

    public const STAFF = 'staff';

    public const STUDENT = 'student';

    public const PARENT = 'parent';

    public const ACCOUNTANT = 'accountant';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::TEACHER,
            self::STAFF,
            self::STUDENT,
            self::PARENT,
            self::ACCOUNTANT,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function dashboardRoutes(): array
    {
        return [
            self::SUPER_ADMIN => 'dashboard.admin',
            self::ADMIN => 'dashboard.admin',
            self::TEACHER => 'dashboard.teacher',
            self::STAFF => 'dashboard.teacher',
            self::STUDENT => 'dashboard.student',
            self::PARENT => 'dashboard.parent',
            self::ACCOUNTANT => 'dashboard.accountant',
        ];
    }

    public static function dashboardRouteNameFor(?User $user): string
    {
        if (! $user) {
            return 'login';
        }

        foreach (self::dashboardRoutes() as $role => $route) {
            if ($user->hasRole($role)) {
                return $route;
            }
        }

        return 'dashboard.admin';
    }
}
