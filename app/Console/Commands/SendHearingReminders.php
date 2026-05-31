<?php

namespace App\Console\Commands;

use App\Mail\UpcomingHearingReminder;
use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendHearingReminders extends Command
{
    protected $signature   = 'onyx:hearing-reminders';
    protected $description = 'Send email reminders for court hearings scheduled in 7, 3, 1 days and today';

    // Days before the hearing on which reminders are sent
    private const REMIND_DAYS = [7, 3, 1, 0];

    public function handle(): int
    {
        $sent  = 0;
        $today = now()->startOfDay();

        foreach (self::REMIND_DAYS as $daysAhead) {
            $targetDate = $today->copy()->addDays($daysAhead)->toDateString();

            $cases = LegalCase::with(['mainOfficer', 'officers', 'client'])
                ->where('is_in_court', true)
                ->whereNotIn('status', ['closed', 'archived'])
                ->whereDate('next_hearing_date', $targetDate)
                ->get();

            foreach ($cases as $case) {
                $notified = collect();

                // Always notify the main officer
                if ($case->mainOfficer && $case->mainOfficer->email) {
                    $this->sendReminder($case->mainOfficer, $case, $daysAhead);
                    $notified->push($case->mainOfficer->id);
                    $sent++;
                }

                // Also notify team officers
                foreach ($case->officers as $officer) {
                    if (!$notified->contains($officer->id) && $officer->email) {
                        $this->sendReminder($officer, $case, $daysAhead);
                        $notified->push($officer->id);
                        $sent++;
                    }
                }

                $label = $daysAhead === 0 ? 'TODAY' : "in {$daysAhead}d";
                $this->line("  ✓ {$case->case_number} — hearing {$label} → {$notified->count()} officer(s) notified");
            }
        }

        $this->info("Hearing reminders sent: {$sent}");
        return Command::SUCCESS;
    }

    private function sendReminder(User $officer, LegalCase $case, int $daysUntil): void
    {
        try {
            Mail::to($officer->email)->send(new UpcomingHearingReminder($officer, $case, $daysUntil));
        } catch (\Exception $e) {
            \Log::error("HearingReminder mail failed [{$case->case_number}] → {$officer->email}: " . $e->getMessage());
            $this->warn("  ✗ Failed to email {$officer->email}: " . $e->getMessage());
        }
    }
}
