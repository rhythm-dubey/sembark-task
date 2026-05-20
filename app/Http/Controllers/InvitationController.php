<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use App\Support\RoleAbility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function create(Request $request): Response
    {
        $this->authorize('create', Invitation::class);

        $user = $request->user();

        return Inertia::render('invitations/Create', [
            'companies' => $user->isSuperAdmin()
                ? Company::query()->orderBy('name')->get(['id', 'name'])
                : [],
            'roles' => $this->availableRoles($user, true),
            'rolesForExistingClient' => $user->isSuperAdmin()
                ? $this->availableRoles($user, false)
                : [],
            'isSuperAdmin' => $user->isSuperAdmin(),
            'defaultNewCompany' => $user->isSuperAdmin(),
        ]);
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $inviter = $request->user();
        $role = $request->invitedRole();

        $company = $request->isNewCompany()
            ? Company::query()->create(['name' => $request->string('company_name')->toString()])
            : Company::query()->findOrFail(
                $inviter->isSuperAdmin()
                    ? $request->integer('company_id')
                    : $inviter->company_id
            );

        $invitedUser = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'company_id' => $company->id,
            'role_id' => $role->id,
            'email_verified_at' => now(),
        ]);

        Invitation::query()->create([
            'company_id' => $company->id,
            'company_name' => $request->isNewCompany() ? $company->name : null,
            'name' => $invitedUser->name,
            'email' => $invitedUser->email,
            'role_id' => $role->id,
            'invited_by' => $inviter->id,
            'accepted_at' => now(),
        ]);

        return redirect()
            ->route($inviter->isSuperAdmin() ? 'companies.index' : 'dashboard')
            ->with('status', 'User invited successfully.');
    }

    /**
     * @return Collection<int, Role>
     */
    private function availableRoles(User $user, bool $forNewCompany): Collection
    {
        $roles = Role::query()->orderBy('label')->get();

        if ($user->isSuperAdmin() && $forNewCompany) {
            return $roles->filter(
                fn (Role $role) => RoleAbility::invitableBySuperAdminForNewCompany($role)
            )->values();
        }

        if ($user->role->isAdmin()) {
            return $roles->filter(
                fn (Role $role) => RoleAbility::invitableByAdminInOwnCompany($role)
            )->values();
        }

        return $roles;
    }
}
