<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'doc_number', 'title', 'category', 'case_id', 'client_id',
        'file_path', 'file_name', 'file_size', 'mime_type',
        'description', 'is_confidential', 'uploaded_by',
    ];

    protected $casts = ['is_confidential' => 'boolean'];

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public static function generateNumber(): string
    {
        $last = static::max('id') ?? 0;
        return 'DOC-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public static function categoryLabel(string $key): string
    {
        return match($key) {
            'notice_to_sue'     => 'Notice to Sue',
            'court_order'       => 'Court Order / Ruling',
            'affidavit'         => 'Affidavit',
            'power_of_attorney' => 'Power of Attorney',
            'contract_agreement'=> 'Contract / Agreement',
            'evidence'          => 'Evidence / Exhibit',
            'police_report'     => 'Police Report (OB)',
            'correspondence'    => 'Correspondence',
            'legal_opinion'     => 'Legal Opinion',
            'judgment'          => 'Judgment / Decree',
            'land_title'        => 'Land Title',
            'company_docs'      => 'Company Documents',
            'id_documents'      => 'ID Documents',
            'summons'           => 'Summons',
            'pleadings'         => 'Pleadings',
            default             => 'Other',
        };
    }
}
