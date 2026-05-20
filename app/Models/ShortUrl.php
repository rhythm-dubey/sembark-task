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

    /**
     * @param  Builder<self>  $query
     */
    public function scopeVisibleTo(Builder $query, User $viewer): Builder
    {
        $roleName = $viewer->role?->name;

        return match ($roleName) {
            'super_admin' => $query->whereRaw('1 = 0'),
            'admin' => $query->where('company_id', '!=', $viewer->company_id),
            'member' => $query->where('user_id', '!=', $viewer->id),
            default => $query->where('company_id', $viewer->company_id),
        };
    }
}
