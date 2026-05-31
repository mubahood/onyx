<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    protected $fillable = [
        'client_number', 'first_name', 'last_name', 'email', 'phone', 'phone_alt',
        'gender', 'dob', 'id_type', 'id_number', 'address', 'district',
        'occupation', 'company', 'notes', 'photo', 'created_by',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'CL-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
