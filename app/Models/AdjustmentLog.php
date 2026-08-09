<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustmentLog extends Model
{
    protected $fillable = [
        'period_id',
        'type',
        'amount',
        'note',
        'date',
        'transaction_id',
        'user_id',
        'family_id',
        'old_amount',
        'new_amount',
        'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, User $user)
    {
        if ($user->belongsToFamily()) {
            return $query->where('family_id', $user->family_id);
        }

        return $query->where('user_id', $user->id);
    }

    public function inSameScope(User $user)
    {
        return $user->belongsToFamily()
            ? $this->family_id === $user->family_id
            : $this->user_id === $user->id;
    }
}
