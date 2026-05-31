@extends('layouts.admin')
@section('title', $user->name)

@section('content')

<div class="ad-page-header">
  <div>
    <h1>{{ $user->name }}</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.users.index') }}">Users</a> <span>/</span> {{ $user->name }}</div>
  </div>
  <a href="{{ route('admin.users.edit', $user) }}" class="btn-ad btn-ad-primary"><i class="fas fa-pen"></i> Edit</a>
</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;">
  <div class="ad-card">
    <div class="ad-card-body" style="text-align:center;">
      <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--ad-primary),var(--ad-accent));display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem;font-weight:700;color:#fff;">
        {{ strtoupper(substr($user->name,0,2)) }}
      </div>
      <h3 style="font-size:1rem;font-weight:700;">{{ $user->name }}</h3>
      <div style="margin:8px 0;">
        <span class="badge-ad {{ $user->role==='admin'?'badge-high':($user->role==='officer'?'badge-active':'badge-ongoing') }}">{{ $user->role_label }}</span>
      </div>
      <p style="font-size:0.8125rem;color:var(--ad-muted);">{{ $user->email }}</p>
      @if($user->phone)<p style="font-size:0.8125rem;margin-top:4px;">{{ $user->phone }}</p>@endif
      @if($user->bio)
      <p style="font-size:0.8rem;color:var(--ad-muted);margin-top:10px;line-height:1.5;">{{ $user->bio }}</p>
      @endif
    </div>
  </div>

  <div class="ad-card">
    <div class="ad-card-header"><span class="ad-card-title">Assigned Cases ({{ $user->assignedCases->count() }})</span></div>
    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead><tr><th>Case #</th><th>Title</th><th>Status</th><th>Client</th></tr></thead>
        <tbody>
          @forelse($user->assignedCases->take(10) as $case)
          <tr>
            <td><a href="{{ route('admin.cases.show', $case) }}" class="case-number-badge">{{ $case->case_number }}</a></td>
            <td>{{ Str::limit($case->title, 40) }}</td>
            <td><span class="badge-ad badge-{{ $case->status }}">{{ ucfirst($case->status) }}</span></td>
            <td>{{ $case->client?->full_name ?? '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="4"><div class="ad-empty" style="padding:20px"><p>No cases assigned yet.</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
