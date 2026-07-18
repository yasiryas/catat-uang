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
        'old_amount',
        'new_amount',
        'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
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
}
