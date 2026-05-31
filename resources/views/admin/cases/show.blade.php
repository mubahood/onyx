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
    <button onclick="oxOpenCloseModal()" class="btn-ad btn-ad-danger">
      <i class="fas fa-lock"></i> <span class="btn-text">Close Case &amp; Record Outcome</span>
    </button>
    @else
    <form method="POST" action="{{ route('admin.cases.reopen', $case) }}" style="display:inline;">
      @csrf
      <button type="submit" class="btn-ad btn-ad-outline">
        <i class="fas fa-lock-open"></i> <span class="btn-text">Reopen Case</span>
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

{{-- ── OUTCOME BANNER (shown when closed) ── --}}
@if(in_array($case->status, ['closed', 'archived']) && $case->score !== null)
<div style="margin-bottom:20px;border-radius:8px;overflow:hidden;border:2px solid
  {{ $case->score == 1 ? '#15803D' : ($case->score == -1 ? '#DC2626' : '#B45309') }};">
  <div style="background:{{ $case->score == 1 ? 'rgba(21,128,61,.08)' : ($case->score == -1 ? 'rgba(220,38,38,.08)' : 'rgba(180,83,9,.08)') }};
              padding:18px 24px;display:flex;align-items:center;gap:18px;">
    <div style="width:56px;height:56px;border-radius:50%;flex-shrink:0;
                background:{{ $case->score == 1 ? '#15803D' : ($case->score == -1 ? '#DC2626' : '#B45309') }};
                display:flex;align-items:center;justify-content:center;">
      <i class="fas fa-{{ $case->score == 1 ? 'trophy' : ($case->score == -1 ? 'times-circle' : 'equals') }}"
         style="font-size:1.375rem;color:#fff;"></i>
    </div>
    <div style="flex:1;">
      <div style="font-size:.625rem;font-weight:800;text-transform:uppercase;letter-spacing:.14em;
                  color:{{ $case->score == 1 ? '#15803D' : ($case->score == -1 ? '#DC2626' : '#B45309') }};margin-bottom:2px;">
        Case Outcome
      </div>
      <div style="font-size:1.5rem;font-weight:900;letter-spacing:-.01em;
                  color:{{ $case->score == 1 ? '#15803D' : ($case->score == -1 ? '#DC2626' : '#B45309') }};">
        {{ $case->score == 1 ? 'WIN — Case Won' : ($case->score == -1 ? 'LOST — Case Lost' : 'NEUTRAL — Settled / No Win/Loss') }}
      </div>
      @if($case->closing_remarks)
      <div style="font-size:.8125rem;color:var(--mt);margin-top:4px;font-style:italic;">
        &ldquo;{{ Str::limit($case->closing_remarks, 120) }}&rdquo;
      </div>
      @endif
    </div>
    @if($case->closed_date)
    <div style="text-align:right;flex-shrink:0;">
      <div style="font-size:.625rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--mt);">Closed On</div>
      <div style="font-size:.9375rem;font-weight:700;color:var(--tx);">{{ $case->closed_date->format('d M Y') }}</div>
    </div>
    @endif
  </div>
</div>
@endif

{{-- ── CLOSE CASE PROMPT (shown for open cases) ── --}}
@if(!in_array($case->status, ['closed', 'archived']))
<div onclick="oxOpenCloseModal()" style="cursor:pointer;margin-bottom:20px;border-radius:8px;
     border:2px dashed rgba(220,38,38,.35);background:rgba(220,38,38,.04);
     padding:14px 20px;display:flex;align-items:center;gap:14px;
     transition:background .15s,border-color .15s;"
     onmouseover="this.style.background='rgba(220,38,38,.08)';this.style.borderColor='rgba(220,38,38,.6)'"
     onmouseout="this.style.background='rgba(220,38,38,.04)';this.style.borderColor='rgba(220,38,38,.35)'">
  <div style="width:40px;height:40px;border-radius:8px;background:rgba(220,38,38,.12);
              display:flex;align-items:center;justify-content:center;flex-shrink:0;">
    <i class="fas fa-lock" style="color:#DC2626;font-size:1rem;"></i>
  </div>
  <div style="flex:1;">
    <div style="font-size:.875rem;font-weight:700;color:#DC2626;">Ready to close this case?</div>
    <div style="font-size:.75rem;color:var(--mt);margin-top:1px;">
      Click here to record the outcome — <strong>Win</strong>, <strong>Neutral</strong>, or <strong>Lost</strong> — and archive this case.
    </div>
  </div>
  <i class="fas fa-chevron-right" style="color:#DC2626;opacity:.6;"></i>
</div>
@endif

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

          @if($case->closing_remarks && !in_array($case->status, ['closed','archived']))
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

{{-- ══ CLOSE CASE MODAL ═══════════════════════════════════════════════════════ --}}
<div id="oxCloseModal" style="display:none;position:fixed;inset:0;z-index:9900;
     background:rgba(10,6,2,.65);align-items:center;justify-content:center;padding:20px;">
  <div style="background:var(--wh);border-radius:12px;width:100%;max-width:520px;
              box-shadow:0 32px 80px rgba(10,6,2,.4);animation:ox-modal-in .18s ease;overflow:hidden;">

    {{-- Modal header --}}
    <div style="background:linear-gradient(135deg,#1A0E07,#3A2010);padding:20px 24px;display:flex;align-items:center;gap:12px;">
      <div style="width:38px;height:38px;border-radius:8px;background:rgba(220,38,38,.2);
                  display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-lock" style="color:#F87171;font-size:.9375rem;"></i>
      </div>
      <div>
        <div style="font-size:.625rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:rgba(255,255,255,.5);">Closing</div>
        <div style="font-size:1rem;font-weight:800;color:#fff;">{{ $case->case_number }} &mdash; Record Outcome</div>
      </div>
      <button onclick="oxCloseModal()" type="button"
              style="margin-left:auto;background:none;border:none;cursor:pointer;color:rgba(255,255,255,.45);font-size:1rem;padding:4px;">
        <i class="fas fa-times"></i>
      </button>
    </div>

    {{-- Modal body --}}
    <div style="padding:24px;">
      <p style="font-size:.8125rem;color:var(--mt);margin-bottom:20px;line-height:1.6;">
        Select the outcome of this case. This will close the case and notify all assigned officers by email.
      </p>

      <form method="POST" action="{{ route('admin.cases.close', $case) }}" id="closeForm">
        @csrf

        {{-- Outcome cards --}}
        <div style="margin-bottom:20px;">
          <label style="font-size:.6875rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--tx);display:block;margin-bottom:10px;">
            Outcome <span style="color:#DC2626;">*</span>
          </label>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">

            {{-- WIN --}}
            <label style="cursor:pointer;" id="lbl-win">
              <input type="radio" name="score" value="1" required style="display:none;" onchange="oxPickOutcome('win')">
              <div id="card-win" style="border:2px solid var(--bd);border-radius:8px;padding:14px 10px;text-align:center;
                                        transition:all .15s;background:#fff;">
                <div style="width:44px;height:44px;border-radius:50%;background:rgba(21,128,61,.12);
                            display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                  <i class="fas fa-trophy" style="font-size:1.125rem;color:#15803D;"></i>
                </div>
                <div style="font-size:.9375rem;font-weight:800;color:#15803D;">WIN</div>
                <div style="font-size:.6875rem;color:var(--mt);margin-top:2px;">Case won &nbsp;+1</div>
              </div>
            </label>

            {{-- NEUTRAL --}}
            <label style="cursor:pointer;" id="lbl-neutral">
              <input type="radio" name="score" value="0" style="display:none;" onchange="oxPickOutcome('neutral')">
              <div id="card-neutral" style="border:2px solid var(--bd);border-radius:8px;padding:14px 10px;text-align:center;
                                            transition:all .15s;background:#fff;">
                <div style="width:44px;height:44px;border-radius:50%;background:rgba(180,83,9,.12);
                            display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                  <i class="fas fa-equals" style="font-size:1.125rem;color:#B45309;"></i>
                </div>
                <div style="font-size:.9375rem;font-weight:800;color:#B45309;">NEUTRAL</div>
                <div style="font-size:.6875rem;color:var(--mt);margin-top:2px;">Settled &nbsp;0</div>
              </div>
            </label>

            {{-- LOST --}}
            <label style="cursor:pointer;" id="lbl-lost">
              <input type="radio" name="score" value="-1" style="display:none;" onchange="oxPickOutcome('lost')">
              <div id="card-lost" style="border:2px solid var(--bd);border-radius:8px;padding:14px 10px;text-align:center;
                                         transition:all .15s;background:#fff;">
                <div style="width:44px;height:44px;border-radius:50%;background:rgba(220,38,38,.12);
                            display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                  <i class="fas fa-times-circle" style="font-size:1.125rem;color:#DC2626;"></i>
                </div>
                <div style="font-size:.9375rem;font-weight:800;color:#DC2626;">LOST</div>
                <div style="font-size:.6875rem;color:var(--mt);margin-top:2px;">Case lost &nbsp;−1</div>
              </div>
            </label>

          </div>
        </div>

        {{-- Closing remarks --}}
        <div class="ad-form-group" style="margin-bottom:20px;">
          <label>Closing Remarks <span style="font-weight:400;color:var(--mt);">(optional)</span></label>
          <textarea class="ad-textarea" name="closing_remarks" rows="3"
                    placeholder="Brief summary of how the case concluded…"></textarea>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--bd);">
          <button type="button" onclick="oxCloseModal()" class="btn-ad btn-ad-ghost">Cancel</button>
          <button type="submit" class="btn-ad btn-ad-danger" id="closeSubmitBtn" disabled
                  style="opacity:.5;cursor:not-allowed;">
            <i class="fas fa-lock"></i> Close Case
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // ── Tab switcher ───────────────────────────────────────────
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

  // ── Close Case modal ───────────────────────────────────────
  const OUTCOME_STYLES = {
    win:     { border: '#15803D', bg: 'rgba(21,128,61,.1)',  shadow: '0 0 0 3px rgba(21,128,61,.2)'  },
    neutral: { border: '#B45309', bg: 'rgba(180,83,9,.1)',   shadow: '0 0 0 3px rgba(180,83,9,.2)'   },
    lost:    { border: '#DC2626', bg: 'rgba(220,38,38,.1)',  shadow: '0 0 0 3px rgba(220,38,38,.2)'  },
  };

  function oxOpenCloseModal() {
    document.getElementById('oxCloseModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  function oxCloseModal() {
    document.getElementById('oxCloseModal').style.display = 'none';
    document.body.style.overflow = '';
  }
  function oxPickOutcome(key) {
    // Reset all cards
    ['win', 'neutral', 'lost'].forEach(k => {
      const card = document.getElementById('card-' + k);
      card.style.border   = '2px solid var(--bd)';
      card.style.background = '#fff';
      card.style.boxShadow = 'none';
    });
    // Highlight selected
    const s = OUTCOME_STYLES[key];
    const sel = document.getElementById('card-' + key);
    sel.style.border     = '2px solid ' + s.border;
    sel.style.background = s.bg;
    sel.style.boxShadow  = s.shadow;
    // Enable submit
    const btn = document.getElementById('closeSubmitBtn');
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor  = 'pointer';
  }

  // Close modal on backdrop click
  document.getElementById('oxCloseModal').addEventListener('click', function(e) {
    if (e.target === this) oxCloseModal();
  });

  // Close on Esc
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') oxCloseModal();
  });
</script>
@endpush
@endsection
