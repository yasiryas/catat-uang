<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutation extends Model
{
    protected $fillable = [
        'period_id',
        'total_income',
        'total_expense',
        'total_adjustment',
        'net_balance',
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'total_adjustment' => 'decimal:2',
        'net_balance' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
