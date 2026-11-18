<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'type', 'budget_limit', 'user_id', 'family_id'];

    protected $casts = [
        'budget_limit' => 'decimal:2',
    ];

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user): void {
            $query->whereNull('user_id')
                ->whereNull('family_id')
                ->orWhere($user->belongsToFamily() ? 'family_id' : 'user_id', $user->scopeKey());
        });
    }

    public function inSameScope(User $user): bool
    {
        return $user->belongsToFamily()
            ? $this->family_id === $user->family_id
            : $this->user_id === $user->id;
    }

    public function isGlobal(): bool
    {
        return $this->user_id === null && $this->family_id === null;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
