<?php

namespace App\Mail;

use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CaseClosed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $officer,
        public LegalCase $case
    ) {}

    public function envelope(): Envelope
    {
        $outcome = match($this->case->score) {
            1  => 'WON ✓',
            -1 => 'LOST',
            default => 'NEUTRAL',
        };
        return new Envelope(
            subject: "Case Closed [{$outcome}] — {$this->case->case_number}: {$this->case->title}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.case-closed');
    }
}
