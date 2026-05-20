<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'label'])]
class Role extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function hasSlug(string $slug): bool
    {
        return $this->name === $slug;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasSlug('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasSlug('admin');
    }
}
