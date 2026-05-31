<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::withCount('transactions')->orderBy('name')->get();

        return view('admin.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('admin.accounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:150',
            'type'            => 'required|in:bank,cash,mobile_money',
            'bank_name'       => 'nullable|string|max:150',
            'account_number'  => 'nullable|string|max:50',
            'branch'          => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric|min:0',
            'description'     => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $data['created_by']    = Auth::id();
        $data['is_active']     = $request->boolean('is_active', true);
        $data['opening_balance'] = $data['opening_balance'] ?? 0;

        $account = Account::create($data);

        return redirect()->route('admin.accounts.index')
            ->with('success', "Account '{$account->name}' created.");
    }

    public function show(Account $account)
    {
        $account->load(['transactions' => fn($q) => $q->latest()->limit(20)]);

        return view('admin.accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        return view('admin.accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'type'           => 'required|in:bank,cash,mobile_money',
            'bank_name'      => 'nullable|string|max:150',
            'account_number' => 'nullable|string|max:50',
            'branch'         => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'is_active'      => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $account->update($data);

        return redirect()->route('admin.accounts.index')
            ->with('success', "Account '{$account->name}' updated.");
    }

    public function destroy(Account $account)
    {
        if ($account->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete account with existing transactions.');
        }

        $name = $account->name;
        $account->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', "Account '{$name}' deleted.");
    }
}
