<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use Closure;

trait HasScopedValidation
{
    protected function scopedClause(): Closure
    {
        return function ($query): void {
            $user = $this->user();
            $query->where($user->belongsToFamily() ? 'family_id' : 'user_id', $user->scopeKey());
        };
    }

    protected function categoryClause(): Closure
    {
        return function ($query): void {
            $user = $this->user();
            $query->where(function ($query) use ($user): void {
                $query->whereNull('user_id')
                    ->whereNull('family_id')
                    ->orWhere($user->belongsToFamily() ? 'family_id' : 'user_id', $user->scopeKey());
            });
        };
    }
}
