<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutation extends Model
{
    protected $fillable = [
        'user_id',
        'period_id',
        'from_account_id',
        'to_account_id',
        'from_account',
        'to_account',
        'amount',
        'note',
        'date',
        'total_income',
        'total_expense',
        'total_adjustment',
        'net_balance',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'total_adjustment' => 'decimal:2',
        'net_balance' => 'decimal:2',
        'date' => 'date',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
}
