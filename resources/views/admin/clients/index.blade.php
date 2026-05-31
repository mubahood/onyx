@extends('layouts.admin')
@section('title', 'Clients')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Clients</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span> Clients</div>
  </div>
  <a href="{{ route('admin.clients.create') }}" class="btn-ad btn-ad-primary">
    <i class="fas fa-plus"></i> New Client
  </a>
</div>

<div class="ad-card">
  <div class="ad-card-header">
    <form class="ad-filter-bar" method="GET" style="margin:0;width:100%;">
      <div class="ad-search-wrap">
        <i class="fas fa-search"></i>
        <input class="ad-input" type="text" name="search" placeholder="Search by name, phone, email…" value="{{ request('search') }}">
      </div>
      <button class="btn-ad btn-ad-primary btn-ad-sm" type="submit">Search</button>
      @if(request('search'))
        <a href="{{ route('admin.clients.index') }}" class="btn-ad btn-ad-ghost btn-ad-sm">Clear</a>
      @endif
    </form>
  </div>

  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Client #</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>District</th>
          <th>Cases</th>
          <th>Added</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($clients as $client)
        <tr>
          <td><span class="case-number-badge">{{ $client->client_number }}</span></td>
          <td>
            <a href="{{ route('admin.clients.show', $client) }}" style="font-weight:600;color:var(--ad-text);">
              {{ $client->full_name }}
            </a>
            @if($client->company)
              <div style="font-size:0.7rem;color:var(--ad-muted);">{{ $client->company }}</div>
            @endif
          </td>
          <td>{{ $client->phone }}</td>
          <td>{{ $client->email ?? '—' }}</td>
          <td>{{ $client->district ?? '—' }}</td>
          <td>
            <span class="badge-ad badge-brown">{{ $client->cases_count ?? 0 }}</span>
          </td>
          <td style="font-size:0.75rem;color:var(--ad-muted);">{{ $client->created_at->format('d M Y') }}</td>
          <td>
            <div class="ad-table-actions">
              <button type="button" class="btn-ad btn-ad-ghost btn-ad-icon" onclick="ONYX.clients.showDetail({{ $client->id }})" title="Quick View">
                <i class="fas fa-eye"></i>
              </button>
              <a href="{{ route('admin.clients.edit', $client) }}" class="btn-ad btn-ad-ghost btn-ad-icon" title="Edit">
                <i class="fas fa-pen"></i>
              </a>
              <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" class="ad-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn-ad btn-ad-ghost btn-ad-icon ox-delete-btn" style="color:#DC2626" title="Delete" data-label="{{ $client->full_name }}">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div class="ad-empty">
              <i class="fas fa-users"></i>
              <h3>No clients found</h3>
              <p>Start by registering your first client.</p>
              <a href="{{ route('admin.clients.create') }}" class="btn-ad btn-ad-primary">Add Client</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($clients->hasPages())
  <div class="ad-card-footer">
    <span style="font-size:0.8125rem;color:var(--ad-muted);">
      Showing {{ $clients->firstItem() }}–{{ $clients->lastItem() }} of {{ $clients->total() }}
    </span>
    <div class="ad-pagination">
      {!! $clients->withQueryString()->links('vendor.pagination.simple-default') !!}
    </div>
  </div>
  @endif
</div>

@endsection
