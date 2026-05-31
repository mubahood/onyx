<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use App\Models\Transaction;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $caseStats = [
            'total'    => LegalCase::count(),
            'pending'  => LegalCase::where('status', 'pending')->count(),
            'active'   => LegalCase::whereIn('status', ['active', 'ongoing'])->count(),
            'closed'   => LegalCase::where('status', 'closed')->count(),
            'wins'     => LegalCase::where('score', 1)->count(),
            'losses'   => LegalCase::where('score', -1)->count(),
            'neutral'  => LegalCase::where('score', 0)->count(),
            'in_court' => LegalCase::where('is_in_court', true)->count(),
        ];

        $winRate = $caseStats['closed'] > 0
            ? round(($caseStats['wins'] / $caseStats['closed']) * 100, 1)
            : 0;

        $byCategory = LegalCase::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $byOfficer = User::whereIn('role', ['admin', 'officer'])
            ->withCount('assignedCases')
            ->orderByDesc('assigned_cases_count')
            ->get();

        $financeStats = [
            'total_income'       => Transaction::where('type', 'income')->sum('amount'),
            'total_expense'      => Transaction::where('type', 'expense')->sum('amount'),
            'this_month_income'  => Transaction::where('type', 'income')
                ->whereMonth('transaction_date', now()->month)->sum('amount'),
            'this_month_expense' => Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)->sum('amount'),
        ];
        $financeStats['net'] = $financeStats['total_income'] - $financeStats['total_expense'];

        $monthly = Transaction::select(
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('MONTH(transaction_date) as month'),
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->groupBy('year', 'month', 'type')
            ->orderBy('year')->orderBy('month')
            ->get();

        $totalClients = Client::count();

        return view('admin.reports.index', compact(
            'caseStats', 'winRate', 'byCategory', 'byOfficer',
            'financeStats', 'monthly', 'totalClients'
        ));
    }
}
