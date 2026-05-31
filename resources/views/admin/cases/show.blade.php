@extends('layouts.admin')
@section('title', $case->case_number)

@section('content')

<div class="ad-page-header">
  <div>
    <h1>{{ $case->title }}</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.cases.index') }}">Cases</a> <span>/</span>
      <span class="case-number-badge">{{ $case->case_number }}</span>
    </div>
  </div>
  <div class="hd-actions">
    @if($case->status !== 'closed' && $case->status !== 'archived')
    <button onclick="document.getElementById('closeModal').style.display='flex'" class="btn-ad btn-ad-outline" style="border-color:#DC2626;color:#DC2626;">
      <i class="fas fa-lock"></i> <span class="btn-text">Close Case</span>
    </button>
    @else
    <form method="POST" action="{{ route('admin.cases.reopen', $case) }}">
      @csrf
      <button type="submit" class="btn-ad btn-ad-outline">
        <i class="fas fa-lock-open"></i> <span class="btn-text">Reopen</span>
      </button>
    </form>
    @endif
    <a href="{{ route('admin.cases.edit', $case) }}" class="btn-ad btn-ad-primary">
      <i class="fas fa-pen"></i> <span class="btn-text">Edit</span>
    </a>
  </div>
</div>

{{-- Status bar --}}
<div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:center;">
  <span class="badge-ad badge-{{ $case->status }}" style="font-size:0.875rem;padding:5px 14px;">
    <i class="fas fa-circle" style="font-size:0.5rem;"></i> {{ ucfirst($case->status) }}
  </span>
  <span class="badge-ad badge-{{ $case->priority }}">{{ ucfirst($case->priority) }} Priority</span>
  @if($case->is_in_court)
    <span class="ad-tracker-badge tracker-court"><i class="fas fa-gavel"></i> In Court</span>
  @endif
  @if($case->is_at_police)
    <span class="ad-tracker-badge tracker-police"><i class="fas fa-shield-halved"></i> At Police</span>
  @endif
  @if($case->score !== null)
    <span class="score-display score-{{ $case->score == 1 ? 'win' : ($case->score == -1 ? 'lost' : 'neutral') }}">
      <i class="fas fa-{{ $case->score == 1 ? 'trophy' : ($case->score == -1 ? 'times-circle' : 'minus-circle') }}"></i>
      {{ $case->score_label }}
    </span>
  @endif
</div>

<div class="ox-grid-aside">

  {{-- Left: Details + Notes --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

    {{-- Tabs --}}
    <div class="ad-card">
      <div class="ad-tabs" style="padding:0 20px;">
        <a class="ad-tab active" href="#details" data-tab="details">
          <i class="fas fa-info-circle"></i> Details
        </a>
        <a class="ad-tab" href="#notes" data-tab="notes">
          <i class="fas fa-note-sticky"></i> Notes ({{ $case->notes->count() }})
        </a>
        <a class="ad-tab" href="#documents" data-tab="documents">
          <i class="fas fa-folder"></i> Documents ({{ $case->documents->count() }})
        </a>
        <a class="ad-tab" href="#finance" data-tab="finance">
          <i class="fas fa-coins"></i> Finance ({{ $case->transactions->count() }})
        </a>
      </div>

      {{-- Details Tab --}}
      <div class="ad-tab-pane active" id="details">
        <div class="ad-card-body">
          @if($case->description)
          <p style="font-size:0.875rem;line-height:1.6;margin-bottom:18px;color:var(--ad-text);">{{ $case->description }}</p>
          @endif
          <div class="ad-detail-grid">
            <div class="ad-detail-item"><div class="ad-detail-label">Category</div><div class="ad-detail-value">{{ \App\Models\LegalCase::categoryLabel($case->category) }}</div></div>
            <div class="ad-detail-item"><div class="ad-detail-label">Stage</div><div class="ad-detail-value">{{ \App\Models\LegalCase::stageLabel($case->stage) }}</div></div>
            <div class="ad-detail-item"><div class="ad-detail-label">Client</div><div class="ad-detail-value"><a href="{{ route('admin.clients.show', $case->client) }}" style="color:var(--ad-primary);">{{ $case->client?->full_name }}</a></div></div>
            <div class="ad-detail-item"><div class="ad-detail-label">Filed On</div><div class="ad-detail-value">{{ $case->filing_date->format('d M Y') }}</div></div>
            @if($case->closed_date)
            <div class="ad-detail-item"><div class="ad-detail-label">Closed On</div><div class="ad-detail-value">{{ $case->closed_date->format('d M Y') }}</div></div>
            @endif
          </div>

          @if($case->is_in_court)
          <div class="ad-form-section" style="margin-top:16px;">
            <div class="ad-form-section-title"><i class="fas fa-gavel"></i> Court Details</div>
            <div class="ad-detail-grid">
              <div class="ad-detail-item"><div class="ad-detail-label">Court</div><div class="ad-detail-value">{{ $case->court_name ?? '—' }}</div></div>
              <div class="ad-detail-item"><div class="ad-detail-label">Division</div><div class="ad-detail-value">{{ $case->court_division ?? '—' }}</div></div>
              <div class="ad-detail-item"><div class="ad-detail-label">Court Case #</div><div class="ad-detail-value">{{ $case->court_case_number ?? '—' }}</div></div>
              <div class="ad-detail-item"><div class="ad-detail-label">Judge</div><div class="ad-detail-value">{{ $case->judge_name ?? '—' }}</div></div>
              <div class="ad-detail-item"><div class="ad-detail-label">Next Hearing</div><div class="ad-detail-value">{{ $case->next_hearing_date ? $case->next_hearing_date->format('d M Y') : '—' }}</div></div>
            </div>
          </div>
          @endif

          @if($case->is_at_police)
          <div class="ad-form-section" style="margin-top:16px;">
            <div class="ad-form-section-title"><i class="fas fa-shield-halved"></i> Police Details</div>
            <div class="ad-detail-grid">
              <div class="ad-detail-item"><div class="ad-detail-label">Station</div><div class="ad-detail-value">{{ $case->police_station ?? '—' }}</div></div>
              <div class="ad-detail-item"><div class="ad-detail-label">OB / Ref #</div><div class="ad-detail-value">{{ $case->police_ref_number ?? '—' }}</div></div>
              <div class="ad-detail-item"><div class="ad-detail-label">Inv. Officer</div><div class="ad-detail-value">{{ $case->investigating_officer ?? '—' }}</div></div>
            </div>
          </div>
          @endif

          @if($case->closing_remarks)
          <div style="margin-top:16px;padding:14px;background:rgba(220,38,38,0.05);border:1px solid rgba(220,38,38,0.15);border-radius:var(--ad-radius);">
            <div style="font-size:0.75rem;font-weight:700;color:#DC2626;margin-bottom:6px;">CLOSING REMARKS</div>
            <p style="font-size:0.875rem;line-height:1.5;">{{ $case->closing_remarks }}</p>
          </div>
          @endif
        </div>
      </div>

      {{-- Notes Tab --}}
      <div class="ad-tab-pane" id="notes">
        <div class="ad-card-body">
          <form method="POST" action="{{ route('admin.cases.notes.store', $case) }}" style="margin-bottom:20px;">
            @csrf
            <div class="ad-form-group" style="margin-bottom:10px;">
              <label>Add Note</label>
              <textarea class="ad-textarea" name="note" rows="3" placeholder="Add a case note or update…" required></textarea>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <label class="ad-check-group">
                <input type="checkbox" name="is_private" value="1">
                <span style="font-size:0.75rem;">Private (only admins see this)</span>
              </label>
              <button type="submit" class="btn-ad btn-ad-primary btn-ad-sm">
                <i class="fas fa-plus"></i> Add Note
              </button>
            </div>
          </form>

          <div class="ad-timeline">
            @forelse($case->notes as $note)
            <div class="ad-timeline-item">
              <div class="ad-timeline-dot">{{ strtoupper(substr($note->author?->name ?? 'U', 0, 2)) }}</div>
              <div class="ad-timeline-body">
                <div class="ad-timeline-meta">
                  <strong>{{ $note->author?->name ?? 'Unknown' }}</strong>
                  &mdash; {{ $note->created_at->diffForHumans() }}
                  @if($note->is_private) <span class="badge-ad badge-gray" style="margin-left:6px;">Private</span> @endif
                </div>
                <div class="ad-timeline-text">{{ $note->note }}</div>
              </div>
              @if(Auth::id() === $note->user_id || Auth::user()->isAdmin())
              <form method="POST" action="{{ route('admin.cases.notes.destroy', [$case, $note]) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn-ad btn-ad-ghost btn-ad-icon" style="color:#DC2626;margin-top:4px;" title="Delete note">
                  <i class="fas fa-times"></i>
                </button>
              </form>
              @endif
            </div>
            @empty
            <div class="ad-empty" style="padding:30px"><p>No notes yet. Be the first to add one.</p></div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- Documents Tab --}}
      <div class="ad-tab-pane" id="documents">
        <div class="ad-card-body">
          <div style="display:flex;justify-content:flex-end;margin-bottom:14px;">
            <a href="{{ route('admin.documents.create', ['case_id'=>$case->id]) }}" class="btn-ad btn-ad-primary btn-ad-sm">
              <i class="fas fa-upload"></i> Upload Document
            </a>
          </div>
          <table class="ad-table">
            <thead><tr><th>Doc #</th><th>Title</th><th>Category</th><th>Size</th><th>Uploaded</th><th>Actions</th></tr></thead>
            <tbody>
              @forelse($case->documents as $doc)
              <tr>
                <td><span class="case-number-badge" style="font-size:0.7rem;">{{ $doc->doc_number }}</span></td>
                <td>{{ $doc->title }}</td>
                <td style="font-size:0.75rem;color:var(--ad-muted);">{{ \App\Models\Document::categoryLabel($doc->category) }}</td>
                <td style="font-size:0.75rem;">{{ $doc->file_size_formatted }}</td>
                <td style="font-size:0.75rem;color:var(--ad-muted);">{{ $doc->created_at->format('d M Y') }}</td>
                <td>
                  <a href="{{ route('admin.documents.download', $doc) }}" class="btn-ad btn-ad-ghost btn-ad-icon"><i class="fas fa-download"></i></a>
                  <a href="{{ route('admin.documents.show', $doc) }}" class="btn-ad btn-ad-ghost btn-ad-icon"><i class="fas fa-eye"></i></a>
                </td>
              </tr>
              @empty
              <tr><td colspan="6"><div class="ad-empty" style="padding:20px"><p>No documents attached.</p></div></td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Finance Tab --}}
      <div class="ad-tab-pane" id="finance">
        <div class="ad-card-body">
          <div style="display:flex;justify-content:flex-end;margin-bottom:14px;">
            <a href="{{ route('admin.transactions.create', ['case_id'=>$case->id]) }}" class="btn-ad btn-ad-primary btn-ad-sm">
              <i class="fas fa-plus"></i> Record Transaction
            </a>
          </div>
          <table class="ad-table">
            <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Amount</th><th>Method</th></tr></thead>
            <tbody>
              @forelse($case->transactions as $txn)
              <tr>
                <td style="font-size:0.75rem;">{{ $txn->transaction_date->format('d M Y') }}</td>
                <td><span class="badge-ad badge-{{ $txn->type }}">{{ ucfirst($txn->type) }}</span></td>
                <td>{{ $txn->description }}</td>
                <td class="finance-amount-{{ $txn->type }}">{{ $txn->type==='income'?'+':'-' }} {{ number_format($txn->amount,0) }}</td>
                <td style="font-size:0.75rem;color:var(--ad-muted);">{{ \App\Models\Transaction::methodLabel($txn->payment_method) }}</td>
              </tr>
              @empty
              <tr><td colspan="5"><div class="ad-empty" style="padding:20px"><p>No transactions linked.</p></div></td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  {{-- Right Sidebar --}}
  <div style="display:flex;flex-direction:column;gap:16px;">

    {{-- Officers --}}
    <div class="ad-card">
      <div class="ad-card-header"><span class="ad-card-title">Officers</span></div>
      <div class="ad-card-body" style="padding:16px;">
        @if($case->mainOfficer)
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
          <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--ad-primary),var(--ad-accent));display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;flex-shrink:0;">
            {{ strtoupper(substr($case->mainOfficer->name,0,2)) }}
          </div>
          <div>
            <div style="font-size:0.8125rem;font-weight:600;">{{ $case->mainOfficer->name }}</div>
            <div style="font-size:0.7rem;color:var(--ad-accent);">Lead Officer</div>
          </div>
        </div>
        @endif
        @foreach($case->officers->where('pivot.role','team') as $officer)
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
          <div style="width:30px;height:30px;border-radius:50%;background:rgba(93,58,26,0.1);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:var(--ad-primary);flex-shrink:0;">
            {{ strtoupper(substr($officer->name,0,2)) }}
          </div>
          <div style="font-size:0.8125rem;">{{ $officer->name }}</div>
        </div>
        @endforeach
        @if(!$case->mainOfficer && $case->officers->isEmpty())
        <p style="font-size:0.8125rem;color:var(--ad-muted);">No officers assigned yet.</p>
        @endif
      </div>
    </div>

    {{-- Quick Facts --}}
    <div class="ad-card">
      <div class="ad-card-header"><span class="ad-card-title">Quick Facts</span></div>
      <div class="ad-card-body" style="padding:16px;">
        <div style="display:flex;flex-direction:column;gap:10px;font-size:0.8125rem;">
          <div style="display:flex;justify-content:space-between;">
            <span style="color:var(--ad-muted);">Stage</span>
            <span style="font-weight:600;">{{ \App\Models\LegalCase::stageLabel($case->stage) }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:var(--ad-muted);">Filed</span>
            <span>{{ $case->filing_date->format('d M Y') }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:var(--ad-muted);">Days Open</span>
            <span>{{ $case->filing_date->diffInDays(now()) }} days</span>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:var(--ad-muted);">Notes</span>
            <span>{{ $case->notes->count() }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="color:var(--ad-muted);">Documents</span>
            <span>{{ $case->documents->count() }}</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Close Case Modal --}}
<div id="closeModal" style="display:none;position:fixed;inset:0;background:rgba(28,18,10,0.55);z-index:9000;align-items:center;justify-content:center;">
  <div class="ad-confirm-box" style="max-width:460px;">
    <h4><i class="fas fa-lock" style="color:var(--ad-primary);margin-right:8px;"></i>Close Case: {{ $case->case_number }}</h4>
    <p style="margin-bottom:16px;">Record the outcome of this case before archiving it.</p>
    <form method="POST" action="{{ route('admin.cases.close', $case) }}">
      @csrf
      <div class="ad-form-group" style="margin-bottom:14px;">
        <label>Outcome <span class="req">*</span></label>
        <select class="ad-select" name="score" required>
          <option value="">— Select outcome —</option>
          <option value="1">Win (+1)</option>
          <option value="0">Neutral / Settled (0)</option>
          <option value="-1">Lost (−1)</option>
        </select>
      </div>
      <div class="ad-form-group" style="margin-bottom:18px;">
        <label>Closing Remarks</label>
        <textarea class="ad-textarea" name="closing_remarks" rows="3" placeholder="Summary of the outcome…"></textarea>
      </div>
      <div class="ad-confirm-actions">
        <button type="button" onclick="document.getElementById('closeModal').style.display='none'" class="btn-ad btn-ad-ghost">Cancel</button>
        <button type="submit" class="btn-ad btn-ad-danger"><i class="fas fa-lock"></i> Close Case</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  // Simple tab switcher
  document.querySelectorAll('.ad-tab').forEach(tab => {
    tab.addEventListener('click', e => {
      e.preventDefault();
      const target = tab.dataset.tab;
      document.querySelectorAll('.ad-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.ad-tab-pane').forEach(p => p.classList.remove('active'));
      tab.classList.add('active');
      document.getElementById(target).classList.add('active');
    });
  });
</script>
@endpush
@endsection
