<?php

namespace App\Mail;

use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpcomingHearingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $officer,
        public LegalCase $case,
        public int $daysUntil
    ) {}

    public function envelope(): Envelope
    {
        $when = match(true) {
            $this->daysUntil === 0 => 'TODAY',
            $this->daysUntil === 1 => 'TOMORROW',
            default                => "IN {$this->daysUntil} DAYS",
        };
        return new Envelope(
            subject: "⚖️ Hearing {$when} — {$this->case->case_number}: {$this->case->title}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.upcoming-hearing-reminder');
    }
}
