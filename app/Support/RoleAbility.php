<?php

namespace App\Support;

use App\Models\Role;

class RoleAbility
{
    public static function canCreateShortUrls(Role $role): bool
    {
        return in_array($role->name, ['admin', 'member'], true);
    }

    public static function canViewShortUrls(Role $role): bool
    {
        return in_array($role->name, ['super_admin', 'admin', 'member'], true);
    }

    public static function canInvite(Role $role): bool
    {
        return in_array($role->name, ['super_admin', 'admin'], true);
    }

    public static function invitableBySuperAdminForNewCompany(Role $role): bool
    {
        return $role->name !== 'super_admin';
    }

    public static function invitableByAdminInOwnCompany(Role $role): bool
    {
        return in_array($role->name, ['admin', 'member'], true);
    }
}
