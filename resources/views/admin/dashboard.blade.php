@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Dashboard</h1>
    <div class="ad-breadcrumb">
      <i class="fas fa-gauge-high"></i>
      <span>Welcome back, {{ Auth::user()->name }}</span>
      <span>&mdash;</span>
      <span>{{ Auth::user()->role_label }}</span>
    </div>
  </div>
  <div class="hd-actions">
    <a href="{{ route('admin.cases.create') }}" class="btn-ad btn-ad-primary">
      <i class="fas fa-plus"></i> <span class="btn-text">New Case</span>
    </a>
    @if(Auth::user()->isAdmin() || Auth::user()->isFrontdesk())
    <a href="{{ route('admin.clients.create') }}" class="btn-ad btn-ad-outline">
      <i class="fas fa-user-plus"></i> <span class="btn-text">New Client</span>
    </a>
    @endif
  </div>
</div>

{{-- ── Stat Cards ──────────────────────────────────────── --}}
<div class="ad-stats-grid">
  <div class="ad-stat-card">
    <div class="ad-stat-icon brown"><i class="fas fa-scale-balanced"></i></div>
    <div>
      <div class="ad-stat-value">{{ $stats['total_cases'] }}</div>
      <div class="ad-stat-label">Total Cases</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon green"><i class="fas fa-briefcase"></i></div>
    <div>
      <div class="ad-stat-value">{{ $stats['active_cases'] }}</div>
      <div class="ad-stat-label">Active / Ongoing</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
    <div>
      <div class="ad-stat-value">{{ $stats['pending_cases'] }}</div>
      <div class="ad-stat-label">Pending Cases</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon blue"><i class="fas fa-gavel"></i></div>
    <div>
      <div class="ad-stat-value">{{ $stats['in_court'] }}</div>
      <div class="ad-stat-label">In Court</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon amber"><i class="fas fa-shield-halved"></i></div>
    <div>
      <div class="ad-stat-value">{{ $stats['at_police'] }}</div>
      <div class="ad-stat-label">At Police</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon green"><i class="fas fa-check-circle"></i></div>
    <div>
      <div class="ad-stat-value">{{ $stats['closed_cases'] }}</div>
      <div class="ad-stat-label">Closed Cases</div>
    </div>
  </div>

  @if($user->isAdmin())
  <div class="ad-stat-card">
    <div class="ad-stat-icon teal"><i class="fas fa-users"></i></div>
    <div>
      <div class="ad-stat-value">{{ $stats['total_clients'] }}</div>
      <div class="ad-stat-label">Total Clients</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon green"><i class="fas fa-arrow-trend-up"></i></div>
    <div>
      <div class="ad-stat-value">UGX {{ number_format($stats['income_month'], 0) }}</div>
      <div class="ad-stat-label">Income This Month</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon red"><i class="fas fa-arrow-trend-down"></i></div>
    <div>
      <div class="ad-stat-value">UGX {{ number_format($stats['expense_month'], 0) }}</div>
      <div class="ad-stat-label">Expenses This Month</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon green"><i class="fas fa-trophy"></i></div>
    <div>
      <div class="ad-stat-value">{{ $stats['wins'] }}</div>
      <div class="ad-stat-label">Cases Won</div>
    </div>
  </div>
  @endif
</div>

{{-- ── Main Grid ────────────────────────────────────────── --}}
<div class="ox-grid-2">

  {{-- Recent Cases --}}
  <div class="ad-card" style="grid-column:{{ $user->isAdmin() ? '1' : 'span 2' }}">
    <div class="ad-card-header">
      <span class="ad-card-title"><i class="fas fa-scale-balanced" style="color:var(--ad-primary);margin-right:8px;"></i>Recent Cases</span>
      <a href="{{ route('admin.cases.index') }}" class="btn-ad btn-ad-ghost btn-ad-sm">View All</a>
    </div>
    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead>
          <tr>
            <th>Case #</th>
            <th>Title</th>
            <th>Client</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Officer</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentCases as $case)
          <tr>
            <td>
              <a href="{{ route('admin.cases.show', $case) }}" class="case-number-badge">{{ $case->case_number }}</a>
            </td>
            <td>
              <a href="{{ route('admin.cases.show', $case) }}" style="color:var(--ad-text);font-weight:500;">
                {{ Str::limit($case->title, 35) }}
              </a>
            </td>
            <td>{{ $case->client?->full_name ?? '—' }}</td>
            <td><span class="badge-ad badge-{{ $case->status }}">{{ ucfirst($case->status) }}</span></td>
            <td><span class="badge-ad badge-{{ $case->priority }}">{{ ucfirst($case->priority) }}</span></td>
            <td>{{ $case->mainOfficer?->name ?? '—' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="6">
              <div class="ad-empty" style="padding:30px">
                <i class="fas fa-scale-balanced"></i>
                <p>No cases yet. <a href="{{ route('admin.cases.create') }}">Create one</a>.</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($user->isAdmin())
  {{-- Finance Summary --}}
  <div class="ad-card">
    <div class="ad-card-header">
      <span class="ad-card-title"><i class="fas fa-money-bill-transfer" style="color:var(--ad-primary);margin-right:8px;"></i>Recent Transactions</span>
      <a href="{{ route('admin.transactions.index') }}" class="btn-ad btn-ad-ghost btn-ad-sm">View All</a>
    </div>
    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead>
          <tr><th>Date</th><th>Type</th><th>Description</th><th>Amount</th></tr>
        </thead>
        <tbody>
          @forelse($recentTransactions as $txn)
          <tr>
            <td style="white-space:nowrap;font-size:0.75rem;">{{ $txn->transaction_date->format('d M Y') }}</td>
            <td><span class="badge-ad badge-{{ $txn->type }}">{{ ucfirst($txn->type) }}</span></td>
            <td>{{ Str::limit($txn->description, 28) }}</td>
            <td class="finance-amount-{{ $txn->type }}">
              {{ $txn->type === 'income' ? '+' : '-' }} {{ number_format($txn->amount, 0) }}
            </td>
          </tr>
          @empty
          <tr><td colspan="4"><div class="ad-empty" style="padding:20px"><p>No transactions yet.</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @endif

</div>

{{-- ── Accounts (admin) ─────────────────────────────────── --}}
@if($user->isAdmin() && !empty($stats['accounts']) && $stats['accounts']->count())
<div style="margin-top:20px;">
  <div class="ad-card-title" style="margin-bottom:12px;font-size:0.875rem;font-weight:700;color:var(--ad-muted);text-transform:uppercase;letter-spacing:0.06em;">
    <i class="fas fa-building-columns" style="margin-right:6px;"></i>Account Balances
  </div>
  <div class="ox-grid-auto">
    @foreach($stats['accounts'] as $account)
    <div class="account-card">
      <div style="display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:0.75rem;font-weight:600;color:var(--ad-muted);text-transform:uppercase;">{{ $account->type }}</span>
        <span class="badge-ad badge-active">Active</span>
      </div>
      <div class="account-balance">UGX {{ number_format($account->current_balance, 0) }}</div>
      <div style="font-size:0.8125rem;color:var(--ad-text);font-weight:600;">{{ $account->name }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection
