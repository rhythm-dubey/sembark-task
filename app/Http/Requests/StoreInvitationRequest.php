<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Invitation::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {

        $isNewCompany = $this->user()?->isSuperAdmin() && $this->boolean('new_company');

        return [
            'new_company' => ['sometimes', 'boolean'],
            'company_name' => [Rule::requiredIf($isNewCompany), 'string', 'max:255'],
            'company_id' => [
                Rule::requiredIf($this->user()?->isSuperAdmin() && ! $isNewCompany),
                'nullable',
                'exists:companies,id',
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function isNewCompany(): bool
    {
        return $this->user()?->isSuperAdmin() && $this->boolean('new_company');
    }

    public function invitedRole(): Role
    {
        return Role::query()->findOrFail($this->role_id);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (! $this->user()->can('inviteRole', [
                Invitation::class,
                $this->invitedRole(),
                $this->isNewCompany(),
            ])) {
                $validator->errors()->add('role_id', 'You are not allowed to invite users with this role.');
            }
        });
    }
}
