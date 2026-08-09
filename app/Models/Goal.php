<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'family_id',
        'name',
        'type',
        'target_amount',
        'period_id',
        'category_id',
        'account_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->belongsToFamily()) {
            return $query->where('family_id', $user->family_id);
        }

        return $query->where('user_id', $user->id);
    }

    public function inSameScope(User $user): bool
    {
        return $user->belongsToFamily()
            ? $this->family_id === $user->family_id
            : $this->user_id === $user->id;
    }
}
