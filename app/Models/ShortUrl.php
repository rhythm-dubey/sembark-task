<?php

namespace App\Models;

use App\Policies\ShortUrlPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'user_id', 'code', 'original_url', 'hits'])]
#[UsePolicy(ShortUrlPolicy::class)]

class ShortUrl extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hits' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isVisibleTo(User $viewer): bool
    {
        return match ($viewer->role?->name) {
            'super_admin' => true,
            'admin' => $this->company_id === $viewer->company_id,
            'member' => $this->user_id === $viewer->id,
            default => false,
        };
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeVisibleTo(Builder $query, User $viewer): Builder
    {
        return match ($viewer->role?->name) {
            'super_admin' => $query,
            'admin' => $query->where('company_id', $viewer->company_id),
            'member' => $query->where('user_id', $viewer->id),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
