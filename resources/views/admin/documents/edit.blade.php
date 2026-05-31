@extends('layouts.admin')
@section('title', 'Edit — ' . $document->doc_number)

@section('content')

@php
  $mime      = $document->mime_type;
  $ext       = strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION));
  $previewUrl = Storage::url($document->file_path);
  [$fileIcon, $fileColor] = match(true) {
    str_starts_with($mime, 'image/')              => ['fa-file-image',   '#10B981'],
    $mime === 'application/pdf'                   => ['fa-file-pdf',     '#EF4444'],
    str_contains($mime, 'word')                   => ['fa-file-word',    '#3B82F6'],
    str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet') => ['fa-file-excel', '#16A34A'],
    str_contains($mime, 'zip')  || str_contains($mime, 'compressed')   => ['fa-file-archive','#F59E0B'],
    default                                       => ['fa-file-alt',    '#6B7280'],
  };
@endphp

<div class="ad-page-header">
  <div>
    <h1>Edit Document</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.documents.index') }}">Documents</a> <span>/</span>
      <a href="{{ route('admin.documents.show', $document) }}">{{ $document->doc_number }}</a> <span>/</span>
      <span>Edit</span>
    </div>
  </div>
  <a href="{{ route('admin.documents.show', $document) }}" class="btn-ad btn-ad-ghost">
    <i class="fas fa-eye"></i> View Document
  </a>
</div>

@if($errors->any())
<div class="ad-alert ad-alert-error" style="margin-bottom:14px;">
  @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:14px;align-items:start;">

  {{-- LEFT: Form --}}
  <div>
    <form method="POST" action="{{ route('admin.documents.update', $document) }}"
          enctype="multipart/form-data" id="editDocForm">
      @csrf @method('PUT')

      {{-- Document details --}}
      <div class="ad-card" style="margin-bottom:14px;">
        <div class="ad-card-header">
          <span class="ad-card-title"><i class="fas fa-file-alt" style="color:var(--br);margin-right:6px;"></i>Document Details</span>
          <span class="case-number-badge">{{ $document->doc_number }}</span>
        </div>
        <div class="ad-card-body">
          <div class="ad-form-grid">
            <div class="ad-form-group span-2">
              <label>Document Title <span class="req">*</span></label>
              <input class="ad-input" type="text" name="title"
                     value="{{ old('title', $document->title) }}" required>
            </div>
            <div class="ad-form-group">
              <label>Category <span class="req">*</span></label>
              <select class="ad-select" name="category" required>
                @foreach([
                  'notice_to_sue'      => 'Notice to Sue',
                  'court_order'        => 'Court Order',
                  'affidavit'          => 'Affidavit',
                  'power_of_attorney'  => 'Power of Attorney',
                  'contract_agreement' => 'Contract / Agreement',
                  'evidence'           => 'Evidence / Exhibit',
                  'police_report'      => 'Police Report',
                  'correspondence'     => 'Correspondence',
                  'legal_opinion'      => 'Legal Opinion',
                  'judgment'           => 'Judgment / Decree',
                  'land_title'         => 'Land Title',
                  'company_docs'       => 'Company Documents',
                  'id_documents'       => 'ID Documents',
                  'summons'            => 'Summons',
                  'pleadings'          => 'Pleadings',
                  'other'              => 'Other',
                ] as $k => $l)
                <option value="{{ $k }}"
                  {{ old('category', $document->category) == $k ? 'selected' : '' }}>
                  {{ $l }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="ad-form-group" style="justify-content:center;">
              <label style="visibility:hidden;font-size:.6875rem;">Options</label>
              <label class="ad-check-group">
                <input type="checkbox" name="is_confidential" value="1"
                  {{ $document->is_confidential ? 'checked' : '' }}>
                <span>Mark as Confidential</span>
              </label>
            </div>
            <div class="ad-form-group">
              <label>Link to Case</label>
              <select class="ad-select" name="case_id">
                <option value="">— None —</option>
                @foreach($cases as $case)
                <option value="{{ $case->id }}"
                  {{ old('case_id', $document->case_id) == $case->id ? 'selected' : '' }}>
                  {{ $case->case_number }} — {{ $case->client?->full_name }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="ad-form-group">
              <label>Link to Client</label>
              <select class="ad-select" name="client_id">
                <option value="">— None —</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}"
                  {{ old('client_id', $document->client_id) == $client->id ? 'selected' : '' }}>
                  {{ $client->full_name }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="ad-form-group span-2">
              <label>Description</label>
              <textarea class="ad-textarea" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      {{-- File section --}}
      <div class="ad-card" style="margin-bottom:14px;">
        <div class="ad-card-header">
          <span class="ad-card-title"><i class="fas fa-paperclip" style="color:var(--br);margin-right:6px;"></i>File</span>
          <span style="font-size:.7rem;color:var(--mt);">Leave blank to keep existing file</span>
        </div>
        <div class="ad-card-body">

          {{-- Current file --}}
          <div style="margin-bottom:14px;">
            <div class="ad-detail-label" style="margin-bottom:6px;">Current File</div>
            <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--ad-body-bg);border:1px solid var(--bd);border-radius:var(--r);">
              <i class="fas {{ $fileIcon }}" style="font-size:2rem;color:{{ $fileColor }};flex-shrink:0;"></i>
              <div style="flex:1;min-width:0;">
                <div style="font-weight:600;font-size:.8125rem;word-break:break-all;">{{ $document->file_name }}</div>
                <div style="font-size:.7rem;color:var(--mt);margin-top:2px;">
                  {{ $document->file_size_formatted }} &nbsp;·&nbsp; {{ $ext }}
                </div>
              </div>
              <div style="display:flex;gap:6px;flex-shrink:0;">
                @if(in_array($mime, ['image/jpeg','image/png','image/gif','image/webp','application/pdf']))
                <a href="{{ $previewUrl }}" target="_blank"
                   class="btn-ad btn-ad-ghost" style="font-size:.7rem;padding:4px 9px;">
                  <i class="fas fa-eye"></i> View
                </a>
                @endif
                <a href="{{ route('admin.documents.download', $document) }}"
                   class="btn-ad btn-ad-outline" style="font-size:.7rem;padding:4px 9px;">
                  <i class="fas fa-download"></i> Download
                </a>
              </div>
            </div>
          </div>

          {{-- Replace file --}}
          <div class="ad-form-section">
            <div class="ad-form-section-title">
              <i class="fas fa-upload"></i> Replace File
            </div>
            <div class="ad-form-group">
              <label>New File <span style="font-weight:400;color:var(--mt);">(optional — max 20 MB)</span></label>
              <input class="ad-input" type="file" name="file" id="replaceFileInput"
                     style="padding:6px;"
                     accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.txt,.zip,.rar">
              <div class="ad-form-hint">
                Accepted formats: PDF, Word, Excel, images (JPG, PNG, GIF), text, ZIP.
              </div>
              <div id="replaceWarning" style="display:none;margin-top:6px;padding:8px 10px;background:#FEF2F2;border:1px solid #FECACA;border-radius:var(--r);font-size:.7rem;color:#DC2626;font-weight:600;">
                <i class="fas fa-exclamation-triangle" style="margin-right:4px;"></i>
                The current file will be permanently replaced. This action cannot be undone.
              </div>
            </div>
          </div>

        </div>
      </div>

      <div style="display:flex;gap:8px;justify-content:flex-end;">
        <a href="{{ route('admin.documents.show', $document) }}" class="btn-ad btn-ad-ghost">Cancel</a>
        <button type="submit" class="btn-ad btn-ad-primary">
          <i class="fas fa-check"></i> Update Document
        </button>
      </div>

    </form>
  </div>{{-- /left --}}

  {{-- RIGHT: Preview panel --}}
  <div>
    <div class="ad-card" style="position:sticky;top:70px;">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-eye" style="color:var(--br);margin-right:6px;"></i>File Preview</span>
        @if(in_array($mime, ['image/jpeg','image/png','image/gif','image/webp','application/pdf']))
        <a href="{{ $previewUrl }}" target="_blank"
           style="font-size:.7rem;color:var(--br);text-decoration:none;display:flex;align-items:center;gap:4px;">
          Full view <i class="fas fa-external-link-alt" style="font-size:.6rem;"></i>
        </a>
        @endif
      </div>
      <div class="ad-card-body" style="padding:0;">

        @if(str_starts_with($mime, 'image/'))
          <a href="{{ $previewUrl }}" target="_blank" style="display:block;">
            <img src="{{ $previewUrl }}" alt="{{ $document->file_name }}"
                 style="width:100%;height:auto;display:block;max-height:420px;object-fit:contain;background:#f8f8f8;padding:8px;">
          </a>
          <div style="padding:8px 12px;background:var(--ad-body-bg);border-top:1px solid var(--bd);font-size:.7rem;color:var(--mt);display:flex;justify-content:space-between;">
            <span>{{ $ext }} image</span><span>{{ $document->file_size_formatted }}</span>
          </div>

        @elseif($mime === 'application/pdf')
          <iframe src="{{ $previewUrl }}" style="width:100%;height:460px;border:none;display:block;"
                  title="{{ $document->title }}"></iframe>
          <div style="padding:8px 12px;background:var(--ad-body-bg);border-top:1px solid var(--bd);font-size:.7rem;color:var(--mt);display:flex;justify-content:space-between;">
            <span>PDF Document</span><span>{{ $document->file_size_formatted }}</span>
          </div>

        @else
          <div style="padding:40px 20px;text-align:center;">
            <i class="fas {{ $fileIcon }}" style="font-size:3rem;color:{{ $fileColor }};display:block;margin-bottom:10px;"></i>
            <div style="font-size:.875rem;font-weight:600;margin-bottom:4px;color:var(--tx);">{{ $document->file_name }}</div>
            <div style="font-size:.75rem;color:var(--mt);margin-bottom:16px;">{{ $document->file_size_formatted }} &nbsp;·&nbsp; {{ $ext }}</div>
            <div style="font-size:.75rem;color:var(--mt);margin-bottom:16px;padding:0 16px;">
              Preview not available for this file type.
            </div>
            <a href="{{ route('admin.documents.download', $document) }}" class="btn-ad btn-ad-outline" style="font-size:.75rem;">
              <i class="fas fa-download"></i> Download to View
            </a>
          </div>
        @endif

      </div>
      <div class="ad-card-footer">
        <a href="{{ route('admin.documents.show', $document) }}" class="btn-ad btn-ad-ghost" style="font-size:.75rem;">
          <i class="fas fa-arrow-left"></i> Back to Details
        </a>
        @if(in_array($mime, ['image/jpeg','image/png','image/gif','image/webp','application/pdf']))
        <a href="{{ $previewUrl }}" target="_blank" class="btn-ad btn-ad-outline" style="font-size:.75rem;">
          <i class="fas fa-external-link-alt"></i> Open File
        </a>
        @endif
      </div>
    </div>
  </div>{{-- /right --}}

</div>{{-- /grid --}}

<script>
document.getElementById('replaceFileInput').addEventListener('change', function () {
  document.getElementById('replaceWarning').style.display = this.files.length ? 'block' : 'none';
});
</script>

@endsection
