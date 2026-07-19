<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'period_id',
        'category_id',
        'account_id',
        'type',
        'amount',
        'note',
        'receipt_image',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected $appends = ['receipt_image_url'];

    public function getReceiptImageUrlAttribute(): ?string
    {
        if (!$this->receipt_image) {
            return null;
        }
        return asset('storage/' . $this->receipt_image);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeMutation($query)
    {
        return $query->where('type', 'mutation');
    }
}
