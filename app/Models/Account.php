<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id', 'account_number', 'balance',
        'minimum_balance', 'low_balance_notified_at',
    ];

    protected $casts = ['low_balance_notified_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}