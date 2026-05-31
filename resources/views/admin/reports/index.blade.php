@extends('layouts.admin')
@section('title', 'Reports')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Reports & Analytics</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span> Reports</div>
  </div>
</div>

<div class="ad-stats-grid" style="grid-template-columns:repeat(4,1fr);">
  <div class="ad-stat-card">
    <div class="ad-stat-icon brown"><i class="fas fa-scale-balanced"></i></div>
    <div><div class="ad-stat-value">{{ $caseStats['total'] }}</div><div class="ad-stat-label">Total Cases</div></div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon green"><i class="fas fa-trophy"></i></div>
    <div>
      <div class="ad-stat-value">{{ $caseStats['wins'] }}</div>
      <div class="ad-stat-label">Cases Won</div>
      <div style="font-size:0.7rem;color:#15803D;margin-top:2px;">{{ $winRate }}% win rate</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon red"><i class="fas fa-times-circle"></i></div>
    <div><div class="ad-stat-value">{{ $caseStats['losses'] }}</div><div class="ad-stat-label">Cases Lost</div></div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon amber"><i class="fas fa-minus-circle"></i></div>
    <div><div class="ad-stat-value">{{ $caseStats['neutral'] }}</div><div class="ad-stat-label">Settled / Neutral</div></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

  <div class="ad-card">
    <div class="ad-card-header"><span class="ad-card-title">Cases by Status</span></div>
    <div class="ad-card-body">
      @foreach(['pending','active','ongoing','closed'] as $s)
      @php $count = $caseStats[$s] ?? 0; $pct = $caseStats['total'] > 0 ? ($count / $caseStats['total'] * 100) : 0; @endphp
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <span class="badge-ad badge-{{ $s }}" style="width:72px;justify-content:center;">{{ ucfirst($s) }}</span>
        <div style="flex:1;height:8px;background:var(--ad-border);border-radius:4px;overflow:hidden;">
          <div style="height:100%;width:{{ $pct }}%;background:var(--ad-primary);"></div>
        </div>
        <span style="font-size:0.875rem;font-weight:700;min-width:30px;text-align:right;">{{ $count }}</span>
      </div>
      @endforeach
    </div>
  </div>

  <div class="ad-card">
    <div class="ad-card-header"><span class="ad-card-title">Cases by Category</span></div>
    <div class="ad-table-wrap" style="max-height:240px;overflow-y:auto;">
      <table class="ad-table">
        <thead><tr><th>Category</th><th>Count</th></tr></thead>
        <tbody>
          @foreach($byCategory as $cat)
          <tr>
            <td>{{ \App\Models\LegalCase::categoryLabel($cat->category) }}</td>
            <td><span class="badge-ad badge-brown">{{ $cat->total }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>

<div class="ad-stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
  <div class="ad-stat-card">
    <div class="ad-stat-icon green"><i class="fas fa-arrow-trend-up"></i></div>
    <div>
      <div class="ad-stat-value finance-amount-income" style="font-size:1.125rem;">{{ number_format($financeStats['total_income'],0) }}</div>
      <div class="ad-stat-label">Total Income (UGX)</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon red"><i class="fas fa-arrow-trend-down"></i></div>
    <div>
      <div class="ad-stat-value finance-amount-expense" style="font-size:1.125rem;">{{ number_format($financeStats['total_expense'],0) }}</div>
      <div class="ad-stat-label">Total Expenses (UGX)</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon {{ $financeStats['net']>=0?'green':'red' }}"><i class="fas fa-scale-balanced"></i></div>
    <div>
      <div class="ad-stat-value" style="font-size:1.125rem;color:{{ $financeStats['net']>=0?'#15803D':'#DC2626' }};">
        {{ number_format($financeStats['net'],0) }}
      </div>
      <div class="ad-stat-label">Net Balance (UGX)</div>
    </div>
  </div>
  <div class="ad-stat-card">
    <div class="ad-stat-icon teal"><i class="fas fa-users"></i></div>
    <div><div class="ad-stat-value">{{ $totalClients }}</div><div class="ad-stat-label">Total Clients</div></div>
  </div>
</div>

<div class="ad-card">
  <div class="ad-card-header"><span class="ad-card-title">Case Load by Officer</span></div>
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead><tr><th>Officer</th><th>Role</th><th>Cases Assigned</th></tr></thead>
      <tbody>
        @forelse($byOfficer as $officer)
        <tr>
          <td style="font-weight:600;">{{ $officer->name }}</td>
          <td><span class="badge-ad {{ $officer->role==='admin'?'badge-high':'badge-active' }}">{{ $officer->role_label }}</span></td>
          <td><span class="badge-ad badge-brown">{{ $officer->assigned_cases_count }}</span></td>
        </tr>
        @empty
        <tr><td colspan="3"><div class="ad-empty" style="padding:20px"><p>No officers yet.</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
