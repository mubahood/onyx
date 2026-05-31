<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use App\Models\LegalCase;
use App\Models\CaseNote;
use App\Models\Transaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'phone', 'bio', 'avatar', 'is_active', 'is_admin',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_admin'          => 'boolean',
        ];
    }

    // ── Avatar helper ──────────────────────────────────────────

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name ?? 'U'));
        $initials = strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) {
            $initials .= strtoupper(substr(end($parts), 0, 1));
        }
        return $initials;
    }

    // ── Password reset ─────────────────────────────────────────

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // ── Role helpers ───────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->is_admin === true;
    }

    public function isOfficer(): bool
    {
        return $this->role === 'officer';
    }

    public function isFrontdesk(): bool
    {
        return $this->role === 'frontdesk';
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this->role, ['admin', 'officer', 'frontdesk']) || $this->is_admin;
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'     => 'Administrator',
            'officer'   => 'Legal Officer',
            'frontdesk' => 'Front Desk',
            default     => ucfirst($this->role ?? 'Staff'),
        };
    }

    // ── Relationships ──────────────────────────────────────────

    public function assignedCases(): HasMany
    {
        return $this->hasMany(LegalCase::class, 'main_officer_id');
    }

    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(LegalCase::class, 'case_officers', 'user_id', 'case_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function caseNotes(): HasMany
    {
        return $this->hasMany(CaseNote::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }
}
