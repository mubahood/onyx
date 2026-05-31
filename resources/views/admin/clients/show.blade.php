@extends('layouts.admin')
@section('title', $client->full_name)

@section('content')

<div class="ad-page-header">
  <div>
    <h1>{{ $client->full_name }}</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span>
      <a href="{{ route('admin.clients.index') }}">Clients</a> <span>/</span>
      <span class="case-number-badge">{{ $client->client_number }}</span>
    </div>
  </div>
  <div class="hd-actions">
    <a href="{{ route('admin.clients.edit', $client) }}" class="btn-ad btn-ad-outline">
      <i class="fas fa-pen"></i> <span class="btn-text">Edit</span>
    </a>
    <a href="{{ route('admin.cases.create', ['client_id' => $client->id]) }}" class="btn-ad btn-ad-primary">
      <i class="fas fa-plus"></i> <span class="btn-text">New Case</span>
    </a>
  </div>
</div>

<div class="ox-grid-aside" style="grid-template-columns:1fr 2fr;">

  {{-- Profile Card --}}
  <div class="ad-card">
    <div class="ad-card-body" style="text-align:center;padding-bottom:16px;">
      <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--ad-primary),var(--ad-accent));display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem;font-weight:700;color:#fff;">
        {{ strtoupper(substr($client->first_name,0,1).substr($client->last_name,0,1)) }}
      </div>
      <h3 style="font-size:1rem;font-weight:700;">{{ $client->full_name }}</h3>
      <p style="font-size:0.75rem;color:var(--ad-muted);">{{ $client->client_number }}</p>
      @if($client->company)
        <p style="font-size:0.8125rem;margin-top:4px;">{{ $client->company }}</p>
      @endif
    </div>
    <div style="border-top:1px solid var(--ad-border);padding:14px 20px;display:flex;flex-direction:column;gap:10px;">
      @if($client->phone)
      <div style="display:flex;align-items:center;gap:9px;font-size:0.8125rem;">
        <i class="fas fa-phone" style="width:16px;color:var(--ad-muted);"></i>
        <span>{{ $client->phone }}</span>
      </div>
      @endif
      @if($client->phone_alt)
      <div style="display:flex;align-items:center;gap:9px;font-size:0.8125rem;">
        <i class="fas fa-phone" style="width:16px;color:var(--ad-muted);"></i>
        <span>{{ $client->phone_alt }} (alt)</span>
      </div>
      @endif
      @if($client->email)
      <div style="display:flex;align-items:center;gap:9px;font-size:0.8125rem;">
        <i class="fas fa-envelope" style="width:16px;color:var(--ad-muted);"></i>
        <span>{{ $client->email }}</span>
      </div>
      @endif
      <div style="display:flex;align-items:center;gap:9px;font-size:0.8125rem;">
        <i class="fas fa-location-dot" style="width:16px;color:var(--ad-muted);"></i>
        <span>{{ $client->address }}{{ $client->district ? ', '.$client->district : '' }}</span>
      </div>
    </div>
  </div>

  {{-- Details --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Info Grid --}}
    <div class="ad-card">
      <div class="ad-card-header"><span class="ad-card-title">Profile Details</span></div>
      <div class="ad-card-body">
        <div class="ad-detail-grid">
          <div class="ad-detail-item"><div class="ad-detail-label">Gender</div><div class="ad-detail-value">{{ $client->gender ? ucfirst($client->gender) : '—' }}</div></div>
          <div class="ad-detail-item"><div class="ad-detail-label">Date of Birth</div><div class="ad-detail-value">{{ $client->dob ? $client->dob->format('d M Y') : '—' }}</div></div>
          <div class="ad-detail-item"><div class="ad-detail-label">ID Type</div><div class="ad-detail-value">{{ $client->id_type ? ucwords(str_replace('_',' ',$client->id_type)) : '—' }}</div></div>
          <div class="ad-detail-item"><div class="ad-detail-label">ID Number</div><div class="ad-detail-value">{{ $client->id_number ?? '—' }}</div></div>
          <div class="ad-detail-item"><div class="ad-detail-label">Occupation</div><div class="ad-detail-value">{{ $client->occupation ?? '—' }}</div></div>
          <div class="ad-detail-item"><div class="ad-detail-label">Registered</div><div class="ad-detail-value">{{ $client->created_at->format('d M Y') }}</div></div>
        </div>
        @if($client->notes)
        <div style="margin-top:14px;padding:12px;background:var(--ad-body-bg);border-radius:var(--ad-radius);font-size:0.8125rem;line-height:1.6;">
          {{ $client->notes }}
        </div>
        @endif
      </div>
    </div>

    {{-- Cases --}}
    <div class="ad-card">
      <div class="ad-card-header">
        <span class="ad-card-title">Cases ({{ $client->cases->count() }})</span>
        <a href="{{ route('admin.cases.create', ['client_id' => $client->id]) }}" class="btn-ad btn-ad-ghost btn-ad-sm">
          <i class="fas fa-plus"></i> Add Case
        </a>
      </div>
      <div class="ad-table-wrap">
        <table class="ad-table">
          <thead><tr><th>Case #</th><th>Title</th><th>Status</th><th>Stage</th><th>Officer</th></tr></thead>
          <tbody>
            @forelse($client->cases as $case)
            <tr>
              <td><a href="{{ route('admin.cases.show', $case) }}" class="case-number-badge">{{ $case->case_number }}</a></td>
              <td><a href="{{ route('admin.cases.show', $case) }}" style="color:var(--ad-text);">{{ Str::limit($case->title,40) }}</a></td>
              <td><span class="badge-ad badge-{{ $case->status }}">{{ ucfirst($case->status) }}</span></td>
              <td style="font-size:0.75rem;color:var(--ad-muted);">{{ \App\Models\LegalCase::stageLabel($case->stage) }}</td>
              <td>{{ $case->mainOfficer?->name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="ad-empty" style="padding:20px"><p>No cases yet.</p></div></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>
@endsection
