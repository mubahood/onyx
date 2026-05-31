@extends('layouts.admin')
@section('title', $account->name)

@section('content')

<div class="ad-page-header">
  <div>
    <h1>{{ $account->name }}</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.accounts.index') }}">Accounts</a> <span>/</span> {{ $account->name }}</div>
  </div>
  <a href="{{ route('admin.accounts.edit', $account) }}" class="btn-ad btn-ad-primary"><i class="fas fa-pen"></i> Edit</a>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;">
  <div class="account-card">
    <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--ad-muted);">{{ \App\Models\Account::typeLabel($account->type) }}</div>
    <div class="account-balance">UGX {{ number_format($account->current_balance, 0) }}</div>
    <div style="font-size:1rem;font-weight:700;">{{ $account->name }}</div>
    @if($account->bank_name)
    <div style="font-size:0.75rem;color:var(--ad-muted);margin-top:4px;">{{ $account->bank_name }}{{ $account->account_number?' · '.$account->account_number:'' }}</div>
    @endif
    <div style="margin-top:14px;display:flex;justify-content:space-between;font-size:0.8125rem;">
      <div>
        <div class="finance-amount-income">+{{ number_format($account->transactions->where('type','income')->sum('amount'), 0) }}</div>
        <div style="font-size:0.7rem;color:var(--ad-muted);">Total Income</div>
      </div>
      <div style="text-align:right;">
        <div class="finance-amount-expense">-{{ number_format($account->transactions->where('type','expense')->sum('amount'), 0) }}</div>
        <div style="font-size:0.7rem;color:var(--ad-muted);">Total Expenses</div>
      </div>
    </div>
  </div>

  <div class="ad-card">
    <div class="ad-card-header">
      <span class="ad-card-title">Recent Transactions</span>
      <a href="{{ route('admin.transactions.create', ['account_id'=>$account->id]) }}" class="btn-ad btn-ad-primary btn-ad-sm">
        <i class="fas fa-plus"></i> Record Transaction
      </a>
    </div>
    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Amount</th><th></th></tr></thead>
        <tbody>
          @forelse($account->transactions as $txn)
          <tr>
            <td style="font-size:0.75rem;">{{ $txn->transaction_date->format('d M Y') }}</td>
            <td><span class="badge-ad badge-{{ $txn->type }}">{{ ucfirst($txn->type) }}</span></td>
            <td>{{ Str::limit($txn->description,40) }}</td>
            <td class="finance-amount-{{ $txn->type }}">{{ $txn->type==='income'?'+':'-' }} {{ number_format($txn->amount,0) }}</td>
            <td><a href="{{ route('admin.transactions.show', $txn) }}" class="btn-ad btn-ad-ghost btn-ad-icon"><i class="fas fa-eye"></i></a></td>
          </tr>
          @empty
          <tr><td colspan="5"><div class="ad-empty" style="padding:20px"><p>No transactions yet.</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
