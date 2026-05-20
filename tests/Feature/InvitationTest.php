<?php

use App\Models\Company;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;

function invitationPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Invited User',
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides);
}

test('super admin can invite admin for a new company', function () {
    $superAdmin = createUserWithRole('super_admin');
    $adminRoleId = Role::query()->where('name', 'admin')->value('id');
    $email = 'admin-new@example.com';

    $this->actingAs($superAdmin)
        ->post(route('invitations.store'), invitationPayload([
            'new_company' => true,
            'company_name' => 'New Client Co',
            'role_id' => $adminRoleId,
            'email' => $email,
        ]))
        ->assertRedirect(route('companies.index'))
        ->assertSessionHas('status', 'User invited successfully.');

    $invited = User::query()->where('email', $email)->first();

    expect($invited)->not->toBeNull();
    expect($invited->role_id)->toBe($adminRoleId);
    expect(Invitation::query()->where('email', $email)->exists())->toBeTrue();
});

test('super admin can invite admin for an existing company', function () {
    $superAdmin = createUserWithRole('super_admin');
    $company = Company::query()->create(['name' => 'Existing Client']);
    $adminRoleId = Role::query()->where('name', 'admin')->value('id');
    $email = 'admin-existing@example.com';

    $this->actingAs($superAdmin)
        ->post(route('invitations.store'), invitationPayload([
            'company_id' => $company->id,
            'role_id' => $adminRoleId,
            'email' => $email,
        ]))
        ->assertRedirect(route('companies.index'));

    $invited = User::query()->where('email', $email)->first();

    expect($invited)->not->toBeNull();
    expect($invited->company_id)->toBe($company->id);
    expect($invited->role_id)->toBe($adminRoleId);
});

test('super admin cannot invite super admin for a new company', function () {
    $superAdmin = createUserWithRole('super_admin');
    $superAdminRoleId = Role::query()->where('name', 'super_admin')->value('id');

    $this->actingAs($superAdmin)
        ->post(route('invitations.store'), invitationPayload([
            'new_company' => true,
            'company_name' => 'Another Client',
            'role_id' => $superAdminRoleId,
        ]))
        ->assertSessionHasErrors('role_id');
});

test('admin can invite member in own company', function () {
    $company = Company::query()->create(['name' => 'Acme Corp']);
    $admin = createUserWithRole('admin', $company->id);
    $memberRoleId = Role::query()->where('name', 'member')->value('id');
    $email = 'member@acme.test';

    $this->actingAs($admin)
        ->post(route('invitations.store'), invitationPayload([
            'role_id' => $memberRoleId,
            'email' => $email,
        ]))
        ->assertRedirect(route('dashboard'));

    $invited = User::query()->where('email', $email)->first();

    expect($invited)->not->toBeNull();
    expect($invited->company_id)->toBe($company->id);
    expect($invited->role_id)->toBe($memberRoleId);
});
