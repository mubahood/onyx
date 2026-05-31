<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_number', 'receipt_number', 'type', 'amount', 'description', 'details',
        'account_id', 'case_id', 'client_id', 'financial_period_id',
        'payment_method', 'reference_number', 'transaction_date',
        'approved_by', 'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class, 'financial_period_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Helpers ────────────────────────────────────────────────

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'TXN-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }

    public static function generateReceiptNumber(): string
    {
        $last = static::where('type', 'income')->max('id') ?? 0;
        return 'RCP-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }

    public static function methodLabel(string $key): string
    {
        return match($key) {
            'bank_transfer' => 'Bank Transfer',
            'mobile_money'  => 'Mobile Money',
            'cheque'        => 'Cheque',
            default         => 'Cash',
        };
    }
}
