@extends('layouts.admin')
@section('title', 'Settings')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Settings</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span> Settings</div>
  </div>
</div>

<div class="ad-card">
  <div class="ad-card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;">
      <a href="{{ route('admin.users.index') }}" class="ad-stat-card" style="text-decoration:none;flex-direction:column;align-items:flex-start;gap:10px;">
        <div class="ad-stat-icon brown"><i class="fas fa-user-shield"></i></div>
        <div>
          <div style="font-weight:700;">User Management</div>
          <div style="font-size:0.8125rem;color:var(--ad-muted);margin-top:3px;">Manage staff accounts and roles</div>
        </div>
      </a>
      <a href="{{ route('admin.accounts.index') }}" class="ad-stat-card" style="text-decoration:none;flex-direction:column;align-items:flex-start;gap:10px;">
        <div class="ad-stat-icon teal"><i class="fas fa-building-columns"></i></div>
        <div>
          <div style="font-weight:700;">Accounts</div>
          <div style="font-size:0.8125rem;color:var(--ad-muted);margin-top:3px;">Bank accounts and cash registers</div>
        </div>
      </a>
      <a href="{{ route('admin.periods.index') }}" class="ad-stat-card" style="text-decoration:none;flex-direction:column;align-items:flex-start;gap:10px;">
        <div class="ad-stat-icon amber"><i class="fas fa-calendar-alt"></i></div>
        <div>
          <div style="font-weight:700;">Financial Periods</div>
          <div style="font-size:0.8125rem;color:var(--ad-muted);margin-top:3px;">Define reporting periods</div>
        </div>
      </a>
    </div>

    <div style="margin-top:28px;padding:20px;background:var(--ad-body-bg);border-radius:var(--ad-radius);">
      <div style="font-size:0.875rem;font-weight:700;margin-bottom:8px;">System Information</div>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;font-size:0.8125rem;">
        <div style="display:flex;justify-content:space-between;"><span style="color:var(--ad-muted);">System</span><span style="font-weight:600;">ONYX Legal</span></div>
        <div style="display:flex;justify-content:space-between;"><span style="color:var(--ad-muted);">Framework</span><span>Laravel {{ app()->version() }}</span></div>
        <div style="display:flex;justify-content:space-between;"><span style="color:var(--ad-muted);">PHP Version</span><span>{{ PHP_VERSION }}</span></div>
        <div style="display:flex;justify-content:space-between;"><span style="color:var(--ad-muted);">Environment</span><span class="badge-ad badge-active">{{ config('app.env') }}</span></div>
      </div>
    </div>
  </div>
</div>

@endsection
