<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\LegalCase;
use App\Models\Client;
use App\Models\FinancialPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'case', 'client', 'createdBy']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%$search%")
                  ->orWhere('transaction_number', 'like', "%$search%")
                  ->orWhere('receipt_number', 'like', "%$search%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($period = $request->get('period_id')) {
            $query->where('financial_period_id', $period);
        }

        // Compute totals from a clone BEFORE paginate mutates the builder
        $totals = [
            'income'  => (clone $query)->where('type', 'income')->sum('amount'),
            'expense' => (clone $query)->where('type', 'expense')->sum('amount'),
        ];

        $transactions = $query->latest('transaction_date')->paginate(25);
        $periods      = FinancialPeriod::orderBy('name')->get();

        return view('admin.transactions.index', compact('transactions', 'periods', 'totals'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $cases    = LegalCase::whereIn('status', ['active', 'ongoing'])->with('client')->orderBy('case_number')->get();
        $clients  = Client::orderBy('first_name')->get();
        $period   = FinancialPeriod::where('is_active', true)->first();

        return view('admin.transactions.create', compact('accounts', 'cases', 'clients', 'period'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'                 => 'required|in:income,expense',
            'amount'               => 'required|numeric|min:0.01',
            'description'          => 'required|string|max:255',
            'details'              => 'nullable|string',
            'account_id'           => 'required|exists:accounts,id',
            'case_id'              => 'nullable|exists:legal_cases,id',
            'client_id'            => 'nullable|exists:clients,id',
            'financial_period_id'  => 'nullable|exists:financial_periods,id',
            'payment_method'       => 'required|in:cash,bank_transfer,cheque,mobile_money',
            'reference_number'     => 'nullable|string|max:100',
            'transaction_date'     => 'required|date',
        ]);

        $data['transaction_number'] = Transaction::generateNumber();
        $data['created_by']         = Auth::id();

        if ($data['type'] === 'income') {
            $data['receipt_number'] = Transaction::generateReceiptNumber();
        }

        $transaction = Transaction::create($data);

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', "Transaction {$transaction->transaction_number} recorded.");
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['account', 'case.client', 'client', 'period', 'createdBy', 'approvedBy']);

        return view('admin.transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        $number = $transaction->transaction_number;
        $transaction->delete();

        return redirect()->route('admin.transactions.index')
            ->with('success', "Transaction {$number} deleted.");
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['account', 'case.client', 'client', 'createdBy']);

        return view('admin.transactions.receipt', compact('transaction'));
    }

    public function pdf(Transaction $transaction)
    {
        $transaction->load(['account', 'case.client', 'client', 'createdBy']);

        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()->route('admin.transactions.receipt', $transaction)
                ->with('warning', 'PDF export not available. Showing printable receipt instead.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.transactions.pdf', compact('transaction'));

        return $pdf->download("receipt-{$transaction->receipt_number}.pdf");
    }
}
