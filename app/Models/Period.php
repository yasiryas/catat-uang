<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = ['user_id', 'name', 'start_date', 'end_date', 'year', 'month', 'opening_balance', 'closing_balance', 'is_closed'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mutation()
    {
        return $this->hasOne(Mutation::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('year', date('Y'))->where('month', date('m'));
    }
}
