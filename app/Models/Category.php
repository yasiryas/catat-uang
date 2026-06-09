<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'type', 'budget_limit'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
