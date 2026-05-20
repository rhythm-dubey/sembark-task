<?php

namespace App\Policies;

use App\Models\ShortUrl;
use App\Models\User;
use App\Support\RoleAbility;

class ShortUrlPolicy
{
    public function viewAny(User $user, ShortUrl $shortUrl): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if($user->role->isAdmin() && $shortUrl->company_id === $user->company_id) {
            return true;
        }

        if($shortUrl->company_id === $user->company_id
            && $shortUrl->user_id === $user->id
        ) {
            return true;
        }

        return false;
    }

    public function view(User $user, ShortUrl $shortUrl): bool
    {
        if (! RoleAbility::canViewShortUrls($user->role)) {
            return false;
        }

        return match ($user->role->name) {
            'admin' => $shortUrl->company_id !== $user->company_id,
            'member' => $shortUrl->user_id !== $user->id,
            default => $shortUrl->company_id === $user->company_id,
        };
    }

    public function create(User $user): bool
    {
        return RoleAbility::canCreateShortUrls($user->role);
    }

    public function resolve(?User $user): bool
    {
        return $user !== null;
    }
}
