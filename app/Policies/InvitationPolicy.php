<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Support\RoleAbility;

class InvitationPolicy
{
    public function create(User $user): bool
    {
        return RoleAbility::canInvite($user->role);
    }

    public function inviteRole(User $user, Role $role, bool $isNewCompany): bool
    {
        if ($user->role->isSuperAdmin() && $isNewCompany) {
            return RoleAbility::invitableBySuperAdminForNewCompany($role);
        }

        if ($user->role->isAdmin() && ! $isNewCompany) {
            return RoleAbility::invitableByAdminInOwnCompany($role);
        }

        if ($user->role->isSuperAdmin() && ! $isNewCompany) {
            return true;
        }

        return false;
    }
}
