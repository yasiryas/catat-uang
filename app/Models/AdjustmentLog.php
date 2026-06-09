<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustmentLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'old_amount',
        'new_amount',
        'reson',
    ];


    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
