<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'family_id',
        'name',
        'type',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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
