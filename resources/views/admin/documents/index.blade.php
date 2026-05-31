@extends('layouts.admin')
@section('title', 'Documents')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Document Vault</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span> Documents</div>
  </div>
  <a href="{{ route('admin.documents.create') }}" class="btn-ad btn-ad-primary">
    <i class="fas fa-upload"></i> Upload Document
  </a>
</div>

<div class="ad-card">
  <div class="ad-card-header">
    <form class="ad-filter-bar" method="GET" style="margin:0;width:100%;">
      <div class="ad-search-wrap">
        <i class="fas fa-search"></i>
        <input class="ad-input" type="text" name="search" placeholder="Search documents…" value="{{ request('search') }}">
      </div>
      <select class="ad-select" name="category" style="width:180px;">
        <option value="">All Categories</option>
        @foreach(['notice_to_sue'=>'Notice to Sue','court_order'=>'Court Order','affidavit'=>'Affidavit','power_of_attorney'=>'Power of Attorney','contract_agreement'=>'Contract','evidence'=>'Evidence','police_report'=>'Police Report','correspondence'=>'Correspondence','legal_opinion'=>'Legal Opinion','judgment'=>'Judgment','land_title'=>'Land Title','company_docs'=>'Company Docs','id_documents'=>'ID Docs','summons'=>'Summons','pleadings'=>'Pleadings','other'=>'Other'] as $k=>$l)
        <option value="{{ $k }}" {{ request('category')==$k?'selected':'' }}>{{ $l }}</option>
        @endforeach
      </select>
      <button class="btn-ad btn-ad-primary btn-ad-sm" type="submit">Filter</button>
      @if(request()->anyFilled(['search','category']))
        <a href="{{ route('admin.documents.index') }}" class="btn-ad btn-ad-ghost btn-ad-sm">Clear</a>
      @endif
    </form>
  </div>

  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Doc #</th><th>Title</th><th>Category</th>
          <th>Case</th><th>Client</th><th>Size</th>
          <th>Uploaded</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($documents as $doc)
        <tr>
          <td><span class="case-number-badge" style="font-size:0.7rem;">{{ $doc->doc_number }}</span></td>
          <td>
            <div style="font-weight:600;">{{ $doc->title }}</div>
            @if($doc->is_confidential)
              <span class="badge-ad badge-high" style="font-size:0.6rem;padding:2px 6px;">Confidential</span>
            @endif
          </td>
          <td style="font-size:0.75rem;color:var(--ad-muted);">{{ \App\Models\Document::categoryLabel($doc->category) }}</td>
          <td>
            @if($doc->case)
              <a href="{{ route('admin.cases.show', $doc->case) }}" class="case-number-badge" style="font-size:0.7rem;">{{ $doc->case->case_number }}</a>
            @else —
            @endif
          </td>
          <td>{{ $doc->client?->full_name ?? '—' }}</td>
          <td style="font-size:0.75rem;">{{ $doc->file_size_formatted }}</td>
          <td style="font-size:0.75rem;color:var(--ad-muted);">{{ $doc->created_at->format('d M Y') }}</td>
          <td>
            <div class="ad-table-actions">
              <a href="{{ route('admin.documents.download', $doc) }}" class="btn-ad btn-ad-ghost btn-ad-icon" title="Download"><i class="fas fa-download"></i></a>
              <a href="{{ route('admin.documents.show', $doc) }}" class="btn-ad btn-ad-ghost btn-ad-icon" title="View"><i class="fas fa-eye"></i></a>
              <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" class="ad-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn-ad btn-ad-ghost btn-ad-icon ox-delete-btn" style="color:#DC2626" data-label="{{ $doc->title }}"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div class="ad-empty">
              <i class="fas fa-folder-open"></i>
              <h3>No documents yet</h3>
              <p>Upload your first document to the vault.</p>
              <a href="{{ route('admin.documents.create') }}" class="btn-ad btn-ad-primary">Upload Document</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($documents->hasPages())
  <div class="ad-card-footer">
    <span style="font-size:0.8125rem;color:var(--ad-muted);">{{ $documents->firstItem() }}–{{ $documents->lastItem() }} of {{ $documents->total() }}</span>
    <div class="ad-pagination">{!! $documents->withQueryString()->links('vendor.pagination.simple-default') !!}</div>
  </div>
  @endif
</div>

@endsection
