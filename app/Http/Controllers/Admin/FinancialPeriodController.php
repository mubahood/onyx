<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialPeriodController extends Controller
{
    public function index()
    {
        $periods = FinancialPeriod::withCount('transactions')->latest()->get();

        return view('admin.periods.index', compact('periods'));
    }

    public function create()
    {
        return view('admin.periods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['created_by'] = Auth::id();
        $data['is_active']  = $request->boolean('is_active');

        if ($data['is_active']) {
            FinancialPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $period = FinancialPeriod::create($data);

        return redirect()->route('admin.periods.index')
            ->with('success', "Period '{$period->name}' created.");
    }

    public function show(FinancialPeriod $period)
    {
        $period->loadCount('transactions');
        $period->load(['transactions' => fn($q) => $q->with(['account', 'case', 'client', 'createdBy'])->latest('transaction_date')->limit(30)]);

        $income  = $period->transactions->where('type', 'income')->sum('amount');
        $expense = $period->transactions->where('type', 'expense')->sum('amount');

        return view('admin.periods.show', compact('period', 'income', 'expense'));
    }

    public function edit(FinancialPeriod $period)
    {
        return view('admin.periods.edit', compact('period'));
    }

    public function update(Request $request, FinancialPeriod $period)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_active']) {
            FinancialPeriod::where('id', '!=', $period->id)->update(['is_active' => false]);
        }

        $period->update($data);

        return redirect()->route('admin.periods.index')
            ->with('success', "Period '{$period->name}' updated.");
    }

    public function destroy(FinancialPeriod $period)
    {
        if ($period->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete period with existing transactions.');
        }

        $name = $period->name;
        $period->delete();

        return redirect()->route('admin.periods.index')
            ->with('success', "Period '{$name}' deleted.");
    }

    public function activate(FinancialPeriod $period)
    {
        FinancialPeriod::where('id', '!=', $period->id)->update(['is_active' => false]);
        $period->update(['is_active' => true]);

        return back()->with('success', "'{$period->name}' set as active period.");
    }
}
