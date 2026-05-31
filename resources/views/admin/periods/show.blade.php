@extends('layouts.admin')
@section('title', $period->name)

@section('content')

<div class="ad-page-header">
  <div>
    <h1>{{ $period->name }}</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.periods.index') }}">Financial Periods</a>
      <span>/</span>
      <span>{{ $period->name }}</span>
    </div>
  </div>
  <div style="display:flex;gap:10px;">
    @if(!$period->is_active)
    <form method="POST" action="{{ route('admin.periods.activate', $period) }}">
      @csrf
      <button type="submit" class="btn-ad btn-ad-outline">
        <i class="fas fa-check-circle"></i> Set as Active
      </button>
    </form>
    @endif
    <a href="{{ route('admin.periods.edit', $period) }}" class="btn-ad btn-ad-primary">
      <i class="fas fa-pen"></i> Edit
    </a>
  </div>
</div>

{{-- Summary Cards --}}
<div class="ad-stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
  <div class="ad-stat-card">
    <div class="ad-stat-icon green"><i class="fas fa-arrow-trend-up"></i></div>
    <div>
      <div class="ad-stat-value finance-amount-income" style="font-size:1.2rem;">UGX {{ number_format($income, 0) }}</div>
      <div class="ad-stat-label">Total Income</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon red"><i class="fas fa-arrow-trend-down"></i></div>
    <div>
      <div class="ad-stat-value finance-amount-expense" style="font-size:1.2rem;">UGX {{ number_format($expense, 0) }}</div>
      <div class="ad-stat-label">Total Expenses</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon {{ ($income - $expense) >= 0 ? 'green' : 'red' }}">
      <i class="fas fa-scale-balanced"></i>
    </div>
    <div>
      <div class="ad-stat-value" style="font-size:1.2rem;color:{{ ($income-$expense)>=0?'#15803D':'#DC2626' }};">
        UGX {{ number_format($income - $expense, 0) }}
      </div>
      <div class="ad-stat-label">Net</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon brown"><i class="fas fa-receipt"></i></div>
    <div>
      <div class="ad-stat-value">{{ $period->transactions_count }}</div>
      <div class="ad-stat-label">Transactions</div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;">

  {{-- Period Info --}}
  <div class="ad-card">
    <div class="ad-card-header"><span class="ad-card-title">Period Details</span></div>
    <div class="ad-card-body">
      <div style="display:flex;flex-direction:column;gap:10px;font-size:0.8125rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="color:var(--ad-muted);">Status</span>
          <span class="badge-ad {{ $period->is_active ? 'badge-active' : 'badge-gray' }}">
            {{ $period->is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:var(--ad-muted);">Start Date</span>
          <span style="font-weight:600;">{{ $period->start_date->format('d M Y') }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:var(--ad-muted);">End Date</span>
          <span style="font-weight:600;">{{ $period->end_date->format('d M Y') }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:var(--ad-muted);">Duration</span>
          <span>{{ $period->start_date->diffInDays($period->end_date) }} days</span>
        </div>
      </div>
      @if($period->description)
      <div style="margin-top:14px;padding:10px;background:var(--ad-body-bg);border-radius:var(--ad-radius);font-size:0.8125rem;line-height:1.5;color:var(--ad-muted);">
        {{ $period->description }}
      </div>
      @endif
    </div>
  </div>

  {{-- Transactions --}}
  <div class="ad-card">
    <div class="ad-card-header">
      <span class="ad-card-title">Transactions in this Period</span>
      <a href="{{ route('admin.transactions.create') }}" class="btn-ad btn-ad-primary btn-ad-sm">
        <i class="fas fa-plus"></i> Record Transaction
      </a>
    </div>
    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead>
          <tr>
            <th>Date</th><th>TXN #</th><th>Type</th><th>Description</th>
            <th>Account</th><th>Case</th><th>Amount (UGX)</th><th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($period->transactions as $txn)
          <tr>
            <td style="white-space:nowrap;font-size:0.75rem;">{{ $txn->transaction_date->format('d M Y') }}</td>
            <td style="font-family:monospace;font-size:0.7rem;color:var(--ad-muted);">{{ $txn->transaction_number }}</td>
            <td><span class="badge-ad badge-{{ $txn->type }}">{{ ucfirst($txn->type) }}</span></td>
            <td>{{ Str::limit($txn->description, 35) }}</td>
            <td style="font-size:0.8125rem;">{{ $txn->account?->name ?? '—' }}</td>
            <td>
              @if($txn->case)
                <a href="{{ route('admin.cases.show', $txn->case) }}" class="case-number-badge" style="font-size:0.65rem;">
                  {{ $txn->case->case_number }}
                </a>
              @else —
              @endif
            </td>
            <td class="finance-amount-{{ $txn->type }}" style="font-weight:700;">
              {{ $txn->type === 'income' ? '+' : '−' }} {{ number_format($txn->amount, 0) }}
            </td>
            <td>
              <a href="{{ route('admin.transactions.show', $txn) }}" class="btn-ad btn-ad-ghost btn-ad-icon">
                <i class="fas fa-eye"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8">
              <div class="ad-empty" style="padding:30px;">
                <i class="fas fa-receipt"></i>
                <h3>No transactions in this period</h3>
                <p>Start recording income and expenses for <strong>{{ $period->name }}</strong>.</p>
                <a href="{{ route('admin.transactions.create') }}" class="btn-ad btn-ad-primary btn-ad-sm">
                  <i class="fas fa-plus"></i> Record Transaction
                </a>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($period->transactions->count() === 30)
    <div class="ad-card-footer">
      <span style="font-size:0.75rem;color:var(--ad-muted);">Showing last 30 transactions.</span>
      <a href="{{ route('admin.transactions.index', ['period_id' => $period->id]) }}" class="btn-ad btn-ad-ghost btn-ad-sm">
        View All
      </a>
    </div>
    @endif
  </div>

</div>

@endsection
