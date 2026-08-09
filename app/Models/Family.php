<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'family_id');
    }

    public function admins(): HasMany
    {
        return $this->hasMany(User::class, 'family_id')->where('family_role', 'admin');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeManagedBy(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    public function scopeForUser(Builder $query, int $familyId): Builder
    {
        return $query->where('id', $familyId);
    }
}