<?php

namespace App\Policies;

use App\Models\ShortUrl;
use App\Models\User;
use App\Support\RoleAbility;

class ShortUrlPolicy
{
    public function viewAny(User $user): bool
    {
        return RoleAbility::canViewShortUrls($user->role);
    }

    public function view(User $user, ShortUrl $shortUrl): bool
    {
        return $shortUrl->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return RoleAbility::canCreateShortUrls($user->role);
    }
}
