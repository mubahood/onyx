@extends('layouts.admin')
@section('title', $document->doc_number . ' — ' . $document->title)

@section('content')

<div class="ad-page-header">
  <div>
    <h1>{{ $document->title }}</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.documents.index') }}">Documents</a>
      <span>/</span>
      <span class="case-number-badge">{{ $document->doc_number }}</span>
    </div>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    @if(in_array($document->mime_type, ['image/jpeg','image/png','image/gif','image/webp','application/pdf']))
    <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn-ad btn-ad-ghost">
      <i class="fas fa-eye"></i> View File
    </a>
    @endif
    <a href="{{ route('admin.documents.download', $document) }}" class="btn-ad btn-ad-outline">
      <i class="fas fa-download"></i> Download
    </a>
    <a href="{{ route('admin.documents.edit', $document) }}" class="btn-ad btn-ad-primary">
      <i class="fas fa-pen"></i> Edit
    </a>
    <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" style="margin:0;"
          onsubmit="return confirm('Delete {{ $document->doc_number }}? This cannot be undone.')">
      @csrf @method('DELETE')
      <button type="submit" class="btn-ad btn-ad-danger"><i class="fas fa-trash"></i> Delete</button>
    </form>
  </div>
</div>

@if(session('success'))
<div class="ad-alert ad-alert-success" style="margin-bottom:14px;">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 360px;gap:14px;align-items:start;">

  {{-- LEFT COLUMN --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Core details --}}
    <div class="ad-card">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-file-alt" style="color:var(--br);margin-right:6px;"></i>Document Details</span>
        @if($document->is_confidential)
          <span class="badge-ad badge-high"><i class="fas fa-lock" style="font-size:.55rem;margin-right:3px;"></i>Confidential</span>
        @endif
      </div>
      <div class="ad-card-body">
        <div class="ad-detail-grid" style="grid-template-columns:repeat(3,1fr);">
          <div class="ad-detail-item">
            <div class="ad-detail-label">Document #</div>
            <div class="ad-detail-value"><span class="case-number-badge">{{ $document->doc_number }}</span></div>
          </div>
          <div class="ad-detail-item">
            <div class="ad-detail-label">Category</div>
            <div class="ad-detail-value">{{ \App\Models\Document::categoryLabel($document->category) }}</div>
          </div>
          <div class="ad-detail-item">
            <div class="ad-detail-label">Status</div>
            <div class="ad-detail-value">
              @if($document->is_confidential)
                <span class="badge-ad badge-high">Confidential</span>
              @else
                <span class="badge-ad badge-gray">Standard</span>
              @endif
            </div>
          </div>
          <div class="ad-detail-item">
            <div class="ad-detail-label">Uploaded By</div>
            <div class="ad-detail-value">{{ $document->uploader?->name ?? '—' }}</div>
          </div>
          <div class="ad-detail-item">
            <div class="ad-detail-label">Upload Date</div>
            <div class="ad-detail-value">{{ $document->created_at->format('d M Y, H:i') }}</div>
          </div>
          <div class="ad-detail-item">
            <div class="ad-detail-label">Last Updated</div>
            <div class="ad-detail-value">{{ $document->updated_at->format('d M Y, H:i') }}</div>
          </div>
        </div>

        @if($document->description)
        <div style="margin-top:14px;">
          <div class="ad-detail-label" style="margin-bottom:6px;">Description</div>
          <div style="padding:12px 14px;background:var(--ad-body-bg);border:1px solid var(--bd);border-radius:var(--r);font-size:.875rem;line-height:1.7;color:var(--tx);">
            {{ $document->description }}
          </div>
        </div>
        @endif
      </div>
    </div>

    {{-- File information --}}
    <div class="ad-card">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-paperclip" style="color:var(--br);margin-right:6px;"></i>File Information</span>
      </div>
      <div class="ad-card-body">
        @php
          $mime = $document->mime_type;
          $ext  = strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION));
          [$fileIcon, $fileColor] = match(true) {
            str_starts_with($mime, 'image/')              => ['fa-file-image',   '#10B981'],
            $mime === 'application/pdf'                   => ['fa-file-pdf',     '#EF4444'],
            str_contains($mime, 'word')                   => ['fa-file-word',    '#3B82F6'],
            str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet') => ['fa-file-excel', '#16A34A'],
            str_contains($mime, 'zip')  || str_contains($mime, 'compressed')   => ['fa-file-archive','#F59E0B'],
            str_contains($mime, 'text')                   => ['fa-file-alt',    '#6B7280'],
            default                                       => ['fa-file',         '#6B7280'],
          };
        @endphp
        <div style="display:flex;align-items:center;gap:14px;padding:12px 14px;background:var(--ad-body-bg);border:1px solid var(--bd);border-radius:var(--r);">
          <i class="fas {{ $fileIcon }}" style="font-size:2.25rem;color:{{ $fileColor }};flex-shrink:0;"></i>
          <div style="flex:1;min-width:0;">
            <div style="font-weight:600;font-size:.875rem;word-break:break-all;margin-bottom:2px;">{{ $document->file_name }}</div>
            <div style="font-size:.7rem;color:var(--mt);">{{ $document->file_size_formatted }} &nbsp;·&nbsp; {{ $ext }} &nbsp;·&nbsp; <span style="font-family:monospace;">{{ $mime }}</span></div>
          </div>
          <div style="display:flex;gap:6px;flex-shrink:0;">
            @if(in_array($mime, ['image/jpeg','image/png','image/gif','image/webp','application/pdf']))
            <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn-ad btn-ad-ghost" style="font-size:.75rem;padding:5px 10px;">
              <i class="fas fa-eye"></i> View
            </a>
            @endif
            <a href="{{ route('admin.documents.download', $document) }}" class="btn-ad btn-ad-outline" style="font-size:.75rem;padding:5px 10px;">
              <i class="fas fa-download"></i> Download
            </a>
          </div>
        </div>
        <div class="ad-detail-grid" style="grid-template-columns:repeat(3,1fr);margin-top:12px;">
          <div class="ad-detail-item">
            <div class="ad-detail-label">File Name</div>
            <div class="ad-detail-value" style="font-family:monospace;font-size:.75rem;word-break:break-all;">{{ $document->file_name }}</div>
          </div>
          <div class="ad-detail-item">
            <div class="ad-detail-label">File Size</div>
            <div class="ad-detail-value">{{ $document->file_size_formatted }}</div>
          </div>
          <div class="ad-detail-item">
            <div class="ad-detail-label">File Type</div>
            <div class="ad-detail-value" style="font-family:monospace;font-size:.75rem;">{{ $mime }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Linked records --}}
    <div class="ad-card">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-link" style="color:var(--br);margin-right:6px;"></i>Linked Records</span>
      </div>
      <div class="ad-card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div>
            <div class="ad-detail-label" style="margin-bottom:6px;">Case</div>
            @if($document->case)
            <a href="{{ route('admin.cases.show', $document->case) }}"
               style="display:flex;align-items:center;gap:10px;padding:12px;background:var(--ad-body-bg);border:1px solid var(--bd);border-radius:var(--r);text-decoration:none;color:var(--tx);transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--br)'" onmouseout="this.style.borderColor='var(--bd)'">
              <i class="fas fa-gavel" style="color:var(--br);font-size:1.1rem;flex-shrink:0;"></i>
              <div style="min-width:0;">
                <div style="font-weight:600;font-size:.8125rem;">{{ $document->case->case_number }}</div>
                <div style="font-size:.7rem;color:var(--mt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($document->case->title ?? 'Case', 35) }}</div>
              </div>
              <i class="fas fa-chevron-right" style="color:var(--mt);font-size:.6rem;margin-left:auto;"></i>
            </a>
            @else
            <div style="padding:12px;background:var(--ad-body-bg);border:1px dashed var(--bd);border-radius:var(--r);font-size:.8125rem;color:var(--mt);text-align:center;">
              No case linked
            </div>
            @endif
          </div>
          <div>
            <div class="ad-detail-label" style="margin-bottom:6px;">Client</div>
            @if($document->client)
            <a href="{{ route('admin.clients.show', $document->client) }}"
               style="display:flex;align-items:center;gap:10px;padding:12px;background:var(--ad-body-bg);border:1px solid var(--bd);border-radius:var(--r);text-decoration:none;color:var(--tx);transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--br)'" onmouseout="this.style.borderColor='var(--bd)'">
              <i class="fas fa-user" style="color:var(--br);font-size:1.1rem;flex-shrink:0;"></i>
              <div style="min-width:0;">
                <div style="font-weight:600;font-size:.8125rem;">{{ $document->client->full_name }}</div>
                <div style="font-size:.7rem;color:var(--mt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $document->client->email ?? 'No email on file' }}</div>
              </div>
              <i class="fas fa-chevron-right" style="color:var(--mt);font-size:.6rem;margin-left:auto;"></i>
            </a>
            @else
            <div style="padding:12px;background:var(--ad-body-bg);border:1px dashed var(--bd);border-radius:var(--r);font-size:.8125rem;color:var(--mt);text-align:center;">
              No client linked
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /left --}}

  {{-- RIGHT COLUMN: Preview --}}
  <div>
    <div class="ad-card" style="position:sticky;top:70px;">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-eye" style="color:var(--br);margin-right:6px;"></i>Preview</span>
        @if(in_array($document->mime_type, ['image/jpeg','image/png','image/gif','image/webp','application/pdf']))
        <a href="{{ Storage::url($document->file_path) }}" target="_blank"
           style="font-size:.7rem;color:var(--br);text-decoration:none;display:flex;align-items:center;gap:4px;">
          Open full <i class="fas fa-external-link-alt" style="font-size:.6rem;"></i>
        </a>
        @endif
      </div>
      <div class="ad-card-body" style="padding:0;">
        @php $previewUrl = Storage::url($document->file_path); @endphp

        @if(str_starts_with($document->mime_type, 'image/'))
          <a href="{{ $previewUrl }}" target="_blank" style="display:block;">
            <img src="{{ $previewUrl }}" alt="{{ $document->file_name }}"
                 style="width:100%;height:auto;display:block;max-height:500px;object-fit:contain;background:#f8f8f8;padding:8px;">
          </a>
          <div style="padding:8px 12px;background:var(--ad-body-bg);border-top:1px solid var(--bd);font-size:.7rem;color:var(--mt);display:flex;justify-content:space-between;">
            <span>{{ $ext }} image</span>
            <span>{{ $document->file_size_formatted }}</span>
          </div>

        @elseif($document->mime_type === 'application/pdf')
          <iframe src="{{ $previewUrl }}" style="width:100%;height:540px;border:none;display:block;"
                  title="{{ $document->title }}"></iframe>
          <div style="padding:8px 12px;background:var(--ad-body-bg);border-top:1px solid var(--bd);font-size:.7rem;color:var(--mt);display:flex;justify-content:space-between;">
            <span>PDF Document</span>
            <span>{{ $document->file_size_formatted }}</span>
          </div>

        @else
          <div style="padding:48px 20px;text-align:center;">
            <i class="fas {{ $fileIcon }}" style="font-size:3.5rem;color:{{ $fileColor }};display:block;margin-bottom:12px;"></i>
            <div style="font-size:.875rem;font-weight:600;margin-bottom:4px;color:var(--tx);">{{ $document->file_name }}</div>
            <div style="font-size:.75rem;color:var(--mt);margin-bottom:20px;">{{ $document->file_size_formatted }} &nbsp;·&nbsp; {{ $ext }}</div>
            <div style="font-size:.75rem;color:var(--mt);margin-bottom:20px;padding:0 20px;">
              This file type cannot be previewed in the browser.
            </div>
            <a href="{{ route('admin.documents.download', $document) }}" class="btn-ad btn-ad-primary" style="font-size:.8rem;">
              <i class="fas fa-download"></i> Download to View
            </a>
          </div>
        @endif

      </div>
      <div class="ad-card-footer">
        <a href="{{ route('admin.documents.edit', $document) }}" class="btn-ad btn-ad-ghost" style="font-size:.75rem;">
          <i class="fas fa-pen"></i> Edit Details
        </a>
        <a href="{{ route('admin.documents.download', $document) }}" class="btn-ad btn-ad-outline" style="font-size:.75rem;">
          <i class="fas fa-download"></i> Download
        </a>
      </div>
    </div>
  </div>{{-- /right --}}

</div>{{-- /grid --}}

@endsection
