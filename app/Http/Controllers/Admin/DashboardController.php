<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use App\Models\Client;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $casesQuery = LegalCase::query();

        if ($user->isOfficer()) {
            $casesQuery->where(function ($q) use ($user) {
                $q->where('main_officer_id', $user->id)
                  ->orWhereHas('officers', fn($q2) => $q2->where('user_id', $user->id));
            });
        }

        $stats = [
            'total_cases'   => (clone $casesQuery)->count(),
            'active_cases'  => (clone $casesQuery)->whereIn('status', ['active', 'ongoing'])->count(),
            'pending_cases' => (clone $casesQuery)->where('status', 'pending')->count(),
            'closed_cases'  => (clone $casesQuery)->where('status', 'closed')->count(),
            'in_court'      => (clone $casesQuery)->where('is_in_court', true)->count(),
            'at_police'     => (clone $casesQuery)->where('is_at_police', true)->count(),
        ];

        if ($user->isAdmin()) {
            $stats['total_clients'] = Client::count();
            $stats['total_docs']    = Document::count();
            $stats['income_month']  = Transaction::where('type', 'income')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount');
            $stats['expense_month'] = Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount');
            $stats['wins']     = LegalCase::where('score', 1)->count();
            $stats['losses']   = LegalCase::where('score', -1)->count();
            $stats['accounts'] = Account::where('is_active', true)->get();
        }

        $recentCases = (clone $casesQuery)
            ->with(['client', 'mainOfficer'])
            ->latest()
            ->limit(8)
            ->get();

        $recentTransactions = $user->isAdmin()
            ? Transaction::with(['account', 'client', 'case'])->latest()->limit(8)->get()
            : collect();

        $casesByCategory = (clone $casesQuery)
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('admin.dashboard', compact('stats', 'recentCases', 'recentTransactions', 'casesByCategory', 'user'));
    }
}
