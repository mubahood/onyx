@extends('layouts.admin')
@section('title', 'Accounts')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Accounts</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span> Finance <span>/</span> Accounts</div>
  </div>
  <a href="{{ route('admin.accounts.create') }}" class="btn-ad btn-ad-primary">
    <i class="fas fa-plus"></i> New Account
  </a>
</div>

<div class="ox-grid-auto" style="margin-bottom:24px;">
  @foreach($accounts as $account)
  <div class="account-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
      <span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--ad-muted);">
        {{ \App\Models\Account::typeLabel($account->type) }}
      </span>
      <span class="badge-ad {{ $account->is_active ? 'badge-active' : 'badge-closed' }}">
        {{ $account->is_active ? 'Active' : 'Inactive' }}
      </span>
    </div>
    <div class="account-balance">UGX {{ number_format($account->current_balance, 0) }}</div>
    <div style="font-size:0.9375rem;font-weight:700;color:var(--ad-text);margin-bottom:4px;">{{ $account->name }}</div>
    @if($account->bank_name)
      <div style="font-size:0.75rem;color:var(--ad-muted);">{{ $account->bank_name }}{{ $account->account_number ? ' · '.$account->account_number : '' }}</div>
    @endif
    <div style="display:flex;gap:8px;margin-top:12px;">
      <a href="{{ route('admin.accounts.show', $account) }}" class="btn-ad btn-ad-ghost btn-ad-sm">View</a>
      <a href="{{ route('admin.accounts.edit', $account) }}" class="btn-ad btn-ad-ghost btn-ad-sm">Edit</a>
    </div>
  </div>
  @endforeach
  @if($accounts->isEmpty())
  <div class="ad-card" style="grid-column:1/-1">
    <div class="ad-empty">
      <i class="fas fa-building-columns"></i>
      <h3>No accounts yet</h3>
      <p>Set up your first account to start recording transactions.</p>
      <a href="{{ route('admin.accounts.create') }}" class="btn-ad btn-ad-primary">Create Account</a>
    </div>
  </div>
  @endif
</div>
@endsection
