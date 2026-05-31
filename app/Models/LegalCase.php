<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalCase extends Model
{
    protected $table = 'legal_cases';

    protected $fillable = [
        'case_number', 'title', 'description', 'category', 'status', 'stage', 'priority',
        'client_id', 'main_officer_id', 'filing_date', 'closed_date',
        'is_in_court', 'court_name', 'court_division', 'court_case_number', 'judge_name', 'next_hearing_date',
        'is_at_police', 'police_station', 'police_ref_number', 'investigating_officer',
        'score', 'closing_remarks', 'created_by',
    ];

    protected $casts = [
        'filing_date'       => 'date',
        'closed_date'       => 'date',
        'next_hearing_date' => 'date',
        'is_in_court'       => 'boolean',
        'is_at_police'      => 'boolean',
        'score'             => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function mainOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_officer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function officers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'case_officers', 'case_id', 'user_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CaseNote::class, 'case_id')->latest();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'case_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'case_id');
    }

    // ── Helpers ────────────────────────────────────────────────

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'LC-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getScoreLabelAttribute(): string
    {
        return match($this->score) {
            1  => 'Win',
            0  => 'Neutral',
            -1 => 'Lost',
            default => 'N/A',
        };
    }

    public static function categoryLabel(string $key): string
    {
        return match($key) {
            'civil_litigation'    => 'Civil Litigation',
            'criminal_defense'    => 'Criminal Defence',
            'family_law'          => 'Family & Matrimonial',
            'land_property'       => 'Land & Property',
            'commercial_corporate'=> 'Commercial & Corporate',
            'employment_labour'   => 'Employment & Labour',
            'human_rights'        => 'Human Rights',
            'constitutional'      => 'Constitutional Law',
            'succession_probate'  => 'Succession & Probate',
            'debt_recovery'       => 'Debt Recovery',
            'immigration'         => 'Immigration & Citizenship',
            default               => 'Other',
        };
    }

    public static function stageLabel(string $key): string
    {
        return match($key) {
            'intake'        => 'Initial Intake',
            'investigation' => 'Investigation',
            'pre_trial'     => 'Pre-Trial / Filing',
            'mediation'     => 'Mediation',
            'trial'         => 'Active Trial',
            'appeal'        => 'Appeal',
            'settlement'    => 'Settlement',
            'enforcement'   => 'Enforcement',
            'closed'        => 'Closed',
            default         => ucfirst($key),
        };
    }
}
