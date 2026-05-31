<?php

namespace App\Mail;

use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CaseAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $officer,
        public LegalCase $case,
        public string $role = 'main'   // 'main' | 'team'
    ) {}

    public function envelope(): Envelope
    {
        $roleLabel = $this->role === 'main' ? 'Lead Officer' : 'Team Member';
        return new Envelope(
            subject: "Case Assignment [{$roleLabel}] — {$this->case->case_number}: {$this->case->title}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.case-assigned');
    }
}
