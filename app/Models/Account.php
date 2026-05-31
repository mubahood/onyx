<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'name', 'type', 'bank_name', 'account_number', 'branch',
        'opening_balance', 'description', 'is_active', 'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getCurrentBalanceAttribute(): float
    {
        $income  = (float) $this->transactions()->where('type', 'income')->sum('amount');
        $expense = (float) $this->transactions()->where('type', 'expense')->sum('amount');
        return (float) $this->opening_balance + $income - $expense;
    }

    public static function typeLabel(string $key): string
    {
        return match($key) {
            'bank'         => 'Bank Account',
            'mobile_money' => 'Mobile Money',
            default        => 'Cash',
        };
    }
}
