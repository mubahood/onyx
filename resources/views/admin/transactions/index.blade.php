@extends('layouts.admin')
@section('title', 'Transactions')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Transactions</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span> Finance <span>/</span> Transactions</div>
  </div>
  <a href="{{ route('admin.transactions.create') }}" class="btn-ad btn-ad-primary">
    <i class="fas fa-plus"></i> Record Transaction
  </a>
</div>

{{-- Summary Cards --}}
<div class="ad-stats-grid ox-grid-3" style="margin-bottom:20px;">
  <div class="ad-stat-card">
    <div class="ad-stat-icon green"><i class="fas fa-arrow-trend-up"></i></div>
    <div>
      <div class="ad-stat-value finance-amount-income">{{ number_format($totals['income'], 0) }}</div>
      <div class="ad-stat-label">Total Income (filtered)</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon red"><i class="fas fa-arrow-trend-down"></i></div>
    <div>
      <div class="ad-stat-value finance-amount-expense">{{ number_format($totals['expense'], 0) }}</div>
      <div class="ad-stat-label">Total Expenses (filtered)</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon {{ ($totals['income']-$totals['expense'])>=0?'green':'red' }}">
      <i class="fas fa-scale-balanced"></i>
    </div>
    <div>
      <div class="ad-stat-value" style="color:{{ ($totals['income']-$totals['expense'])>=0?'#15803D':'#DC2626' }};">
        {{ number_format($totals['income']-$totals['expense'], 0) }}
      </div>
      <div class="ad-stat-label">Net Balance</div>
    </div>
  </div>
</div>

<div class="ad-card">
  <div class="ad-card-header">
    <form class="ad-filter-bar" method="GET" style="margin:0;width:100%;">
      <div class="ad-search-wrap">
        <i class="fas fa-search"></i>
        <input class="ad-input" type="text" name="search" placeholder="Search transactions…" value="{{ request('search') }}">
      </div>
      <select class="ad-select" name="type" style="width:130px;">
        <option value="">All Types</option>
        <option value="income"  {{ request('type')=='income'  ?'selected':'' }}>Income</option>
        <option value="expense" {{ request('type')=='expense' ?'selected':'' }}>Expense</option>
      </select>
      <select class="ad-select" name="period_id" style="width:160px;">
        <option value="">All Periods</option>
        @foreach($periods as $period)
        <option value="{{ $period->id }}" {{ request('period_id')==$period->id?'selected':'' }}>
          {{ $period->name }}
        </option>
        @endforeach
      </select>
      <button class="btn-ad btn-ad-primary btn-ad-sm" type="submit">Filter</button>
      @if(request()->anyFilled(['search','type','period_id']))
        <a href="{{ route('admin.transactions.index') }}" class="btn-ad btn-ad-ghost btn-ad-sm">Clear</a>
      @endif
    </form>
  </div>

  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>TXN #</th><th>Date</th><th>Type</th><th>Description</th>
          <th>Account</th><th>Case</th><th>Amount (UGX)</th><th>Method</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $txn)
        <tr>
          <td>
            <div style="font-family:monospace;font-size:0.75rem;color:var(--ad-muted);">{{ $txn->transaction_number }}</div>
            @if($txn->receipt_number)
              <div style="font-size:0.65rem;color:var(--ad-accent);">{{ $txn->receipt_number }}</div>
            @endif
          </td>
          <td style="font-size:0.8125rem;white-space:nowrap;">{{ $txn->transaction_date->format('d M Y') }}</td>
          <td><span class="badge-ad badge-{{ $txn->type }}">{{ ucfirst($txn->type) }}</span></td>
          <td>{{ Str::limit($txn->description, 35) }}</td>
          <td style="font-size:0.8125rem;">{{ $txn->account?->name ?? '—' }}</td>
          <td>
            @if($txn->case)
              <a href="{{ route('admin.cases.show', $txn->case) }}" class="case-number-badge" style="font-size:0.65rem;">{{ $txn->case->case_number }}</a>
            @else —
            @endif
          </td>
          <td class="finance-amount-{{ $txn->type }}" style="font-weight:700;">
            {{ $txn->type==='income'?'+':'-' }} {{ number_format($txn->amount, 0) }}
          </td>
          <td style="font-size:0.75rem;color:var(--ad-muted);">{{ \App\Models\Transaction::methodLabel($txn->payment_method) }}</td>
          <td>
            <div class="ad-table-actions">
              <a href="{{ route('admin.transactions.show', $txn) }}" class="btn-ad btn-ad-ghost btn-ad-icon"><i class="fas fa-eye"></i></a>
              @if($txn->type === 'income')
                <a href="{{ route('admin.transactions.receipt', $txn) }}" class="btn-ad btn-ad-ghost btn-ad-icon" title="Receipt"><i class="fas fa-receipt"></i></a>
              @endif
              <form method="POST" action="{{ route('admin.transactions.destroy', $txn) }}" class="ad-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn-ad btn-ad-ghost btn-ad-icon ox-delete-btn" style="color:#DC2626" data-label="{{ $txn->transaction_number }}"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div class="ad-empty">
              <i class="fas fa-money-bill-transfer"></i>
              <h3>No transactions</h3>
              <a href="{{ route('admin.transactions.create') }}" class="btn-ad btn-ad-primary">Record Transaction</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($transactions->hasPages())
  <div class="ad-card-footer">
    <span style="font-size:0.8125rem;color:var(--ad-muted);">{{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}</span>
    <div class="ad-pagination">{!! $transactions->withQueryString()->links('vendor.pagination.simple-default') !!}</div>
  </div>
  @endif
</div>

@endsection
