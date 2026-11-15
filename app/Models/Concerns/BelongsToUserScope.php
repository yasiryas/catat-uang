<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Untuk model milik user atau keluarga: scoping query & otorisasi baris.
 */
trait BelongsToUserScope
{
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $user->belongsToFamily()
            ? $query->where('family_id', $user->family_id)
            : $query->where('user_id', $user->id);
    }

    public function inSameScope(User $user): bool
    {
        return $user->belongsToFamily()
            ? $this->family_id === $user->family_id
            : $this->user_id === $user->id;
    }
}
