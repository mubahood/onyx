@extends('layouts.admin')
@section('title', 'New Case')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>New Case</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.cases.index') }}">Cases</a> <span>/</span> Create
    </div>
  </div>
  <a href="{{ route('admin.cases.index') }}" class="btn-ad btn-ad-ghost">
    <i class="fas fa-arrow-left"></i> Back
  </a>
</div>

@if($errors->any())
<div class="ad-alert ad-alert-error" style="margin-bottom:14px;">
  <i class="fas fa-exclamation-circle"></i>
  <div>
    <strong>Please fix the following:</strong>
    <ul style="margin:4px 0 0 16px;">
      @foreach($errors->all() as $e)<li style="font-size:.75rem;">{{ $e }}</li>@endforeach
    </ul>
  </div>
  <button class="ad-alert-x" onclick="this.closest('.ad-alert').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

<form method="POST" action="{{ route('admin.cases.store') }}" id="caseForm">
@csrf
<div style="display:grid;grid-template-columns:1fr 280px;gap:16px;align-items:start;">

  {{-- LEFT COLUMN --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Basic Info --}}
    <div class="ad-card">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-scale-balanced" style="color:var(--br);margin-right:6px;"></i>Case Information</span>
      </div>
      <div class="ad-card-body">
        <div class="ad-form-grid">
          <div class="ad-form-group span-2">
            <label>Case Title <span class="req">*</span></label>
            <input class="ad-input" type="text" name="title" id="caseTitle" value="{{ old('title') }}"
              placeholder="Brief descriptive title, e.g. Ssemakula v. Kawooya — Land Dispute" required>
          </div>
          <div class="ad-form-group">
            <label>Category <span class="req">*</span></label>
            <select class="ad-select ox-select2" name="category" required>
              <option value="">— Select Category —</option>
              @foreach(['civil_litigation'=>'Civil Litigation','criminal_defense'=>'Criminal Defence','family_law'=>'Family & Matrimonial','land_property'=>'Land & Property','commercial_corporate'=>'Commercial & Corporate','employment_labour'=>'Employment & Labour','human_rights'=>'Human Rights','constitutional'=>'Constitutional Law','succession_probate'=>'Succession & Probate','debt_recovery'=>'Debt Recovery','immigration'=>'Immigration & Citizenship','other'=>'Other'] as $k=>$l)
              <option value="{{ $k }}" {{ old('category')==$k?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select>
          </div>
          <div class="ad-form-group">
            <label>Priority <span class="req">*</span></label>
            <select class="ad-select" name="priority" required>
              <option value="low"    {{ old('priority')=='low'?'selected':'' }}>🟢 Low</option>
              <option value="medium" {{ old('priority','medium')=='medium'?'selected':'' }}>🟡 Medium</option>
              <option value="high"   {{ old('priority')=='high'?'selected':'' }}>🔴 High</option>
              <option value="urgent" {{ old('priority')=='urgent'?'selected':'' }}>🚨 Urgent</option>
            </select>
          </div>
          <div class="ad-form-group">
            <label style="display:flex;align-items:center;justify-content:space-between;">
              <span>Client <span class="req">*</span></span>
              <span style="font-size:.65rem;font-weight:400;color:var(--mt);">Can't find client?
                <a href="#" class="ox-new-client-trigger"
                   onclick="event.preventDefault(); ONYX.clients.quickAddToSelect('caseClientSelect')"
                   style="color:var(--br);font-weight:600;">
                  <i class="fas fa-user-plus"></i> Add New
                </a>
              </span>
            </label>
            <div class="ox-select-with-add">
              <select id="caseClientSelect" class="ad-select ox-select2" name="client_id"
                      required data-placeholder="Search or select client…">
                <option value="">Search or select client…</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ (old('client_id', request('client_id'))==$client->id)?'selected':'' }}>
                  {{ $client->full_name }} ({{ $client->client_number }})
                </option>
                @endforeach
              </select>
              <button type="button" class="btn-ad btn-ad-primary ox-add-btn"
                      onclick="ONYX.clients.quickAddToSelect('caseClientSelect')"
                      title="Add a new client without leaving this page">
                <i class="fas fa-user-plus"></i>
                <span class="btn-text">New</span>
              </button>
            </div>
          </div>
          <div class="ad-form-group">
            <label>Filing Date <span class="req">*</span></label>
            <input class="ad-input ox-datepicker" type="text" name="filing_date"
              value="{{ old('filing_date', now()->format('Y-m-d')) }}"
              placeholder="Pick date…" required autocomplete="off">
          </div>
          <div class="ad-form-group span-2">
            <label>Description</label>
            <textarea class="ad-textarea" name="description" rows="3"
              placeholder="Brief summary of the case…">{{ old('description') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- Officers --}}
    <div class="ad-card">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-user-tie" style="color:var(--br);margin-right:6px;"></i>Officers</span>
      </div>
      <div class="ad-card-body">
        <div class="ad-form-grid">
          <div class="ad-form-group">
            <label>Lead Officer</label>
            <select class="ad-select ox-select2" name="main_officer_id" data-placeholder="— Unassigned —">
              <option value="">— Unassigned —</option>
              @foreach($officers as $officer)
              <option value="{{ $officer->id }}" {{ old('main_officer_id')==$officer->id?'selected':'' }}>
                {{ $officer->name }} · {{ $officer->role_label }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="ad-form-group">
            <label>Team Members</label>
            <select class="ox-select2-multi" name="team_officers[]" multiple data-placeholder="Add team members…">
              @foreach($officers as $officer)
              <option value="{{ $officer->id }}" {{ in_array($officer->id, old('team_officers', [])) ? 'selected' : '' }}>
                {{ $officer->name }}
              </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>

    {{-- Court Tracking --}}
    <div class="ad-card">
      <div class="ad-card-header" style="cursor:pointer;" onclick="toggleSection('court')">
        <div class="ad-card-title">
          <i class="fas fa-gavel" style="color:var(--br);margin-right:6px;"></i>Court Tracking
          <span class="badge-ad badge-ongoing" id="courtBadge" style="margin-left:8px;{{ old('is_in_court') ? '' : 'display:none;' }}">Active</span>
        </div>
        <label class="ox-switch" onclick="event.stopPropagation()">
          <input type="hidden"   name="is_in_court" value="0">
          <input type="checkbox" name="is_in_court" value="1" id="isInCourt" {{ old('is_in_court') ? 'checked' : '' }}>
          <span class="ox-switch-track"></span>
        </label>
      </div>
      <div class="cascade-section {{ old('is_in_court') ? 'open' : '' }}" id="courtSection">
        <div class="ad-card-body" style="padding-top:10px;">
          <div class="ad-form-grid">
            <div class="ad-form-group">
              <label>Court Name</label>
              <input class="ad-input" type="text" name="court_name" value="{{ old('court_name') }}"
                placeholder="e.g. High Court of Uganda">
            </div>
            <div class="ad-form-group">
              <label>Division</label>
              <select class="ad-select" name="court_division">
                <option value="">— Select —</option>
                @foreach(['Civil Division','Criminal Division','Commercial Division','Family Division','Land Division','Anti-Corruption Court','International Crimes Division','Constitutional Court'] as $div)
                <option value="{{ $div }}" {{ old('court_division')==$div?'selected':'' }}>{{ $div }}</option>
                @endforeach
              </select>
            </div>
            <div class="ad-form-group">
              <label>Court Case Number</label>
              <input class="ad-input" type="text" name="court_case_number" value="{{ old('court_case_number') }}"
                placeholder="e.g. HCCS 001 of 2025">
            </div>
            <div class="ad-form-group">
              <label>Judge / Magistrate</label>
              <input class="ad-input" type="text" name="judge_name" value="{{ old('judge_name') }}"
                placeholder="Full name">
            </div>
            <div class="ad-form-group span-2">
              <label>Next Hearing Date</label>
              <input class="ad-input ox-datepicker" type="text" name="next_hearing_date"
                value="{{ old('next_hearing_date') }}"
                placeholder="Pick date…" autocomplete="off">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Police Tracking --}}
    <div class="ad-card">
      <div class="ad-card-header" style="cursor:pointer;" onclick="toggleSection('police')">
        <div class="ad-card-title">
          <i class="fas fa-shield-halved" style="color:#B45309;margin-right:6px;"></i>Police / Station Tracking
          <span class="badge-ad badge-pending" id="policeBadge" style="margin-left:8px;{{ old('is_at_police') ? '' : 'display:none;' }}">Active</span>
        </div>
        <label class="ox-switch" onclick="event.stopPropagation()">
          <input type="hidden"   name="is_at_police" value="0">
          <input type="checkbox" name="is_at_police" value="1" id="isAtPolice" {{ old('is_at_police') ? 'checked' : '' }}>
          <span class="ox-switch-track"></span>
        </label>
      </div>
      <div class="cascade-section {{ old('is_at_police') ? 'open' : '' }}" id="policeSection">
        <div class="ad-card-body" style="padding-top:10px;">
          <div class="ad-form-grid">
            <div class="ad-form-group">
              <label>Police Station</label>
              <input class="ad-input" type="text" name="police_station" value="{{ old('police_station') }}"
                placeholder="e.g. Kampala Central Police">
            </div>
            <div class="ad-form-group">
              <label>OB / Reference Number</label>
              <input class="ad-input" type="text" name="police_ref_number" value="{{ old('police_ref_number') }}"
                placeholder="e.g. OB 47/2025">
            </div>
            <div class="ad-form-group span-2">
              <label>Investigating Officer</label>
              <input class="ad-input" type="text" name="investigating_officer" value="{{ old('investigating_officer') }}"
                placeholder="Full name">
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /left --}}

  {{-- RIGHT COLUMN --}}
  <div style="position:sticky;top:66px;display:flex;flex-direction:column;gap:14px;">

    <div class="ad-card">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-paper-plane" style="color:var(--br);margin-right:6px;"></i>Submit</span>
      </div>
      <div class="ad-card-body" style="display:flex;flex-direction:column;gap:10px;">
        <button type="submit" class="btn-ad btn-ad-primary" style="justify-content:center;" id="submitBtn">
          <i class="fas fa-check"></i> Create Case
        </button>
        <a href="{{ route('admin.cases.index') }}" class="btn-ad btn-ad-ghost" style="justify-content:center;">
          Cancel
        </a>
      </div>
    </div>

    {{-- Character counter for title --}}
    <div class="ad-card">
      <div class="ad-card-body" style="padding:12px;">
        <div style="font-size:.6875rem;color:var(--mt);margin-bottom:6px;">Case Title</div>
        <div style="font-size:1.125rem;font-weight:700;color:var(--br);" id="titlePreview">—</div>
        <div style="font-size:.625rem;color:var(--mt);margin-top:4px;" id="titleCount">0 / 200 characters</div>
      </div>
    </div>

  </div>{{-- /right --}}

</div>
</form>

@endsection

@push('scripts')
<script>
(function($) {

  /* --- Select2 single (with proper clear button) --- */
  $('.ox-select2').each(function() {
    var placeholder = $(this).data('placeholder') || '— Select —';
    $(this).select2({
      theme: 'default',
      allowClear: true,
      width: '100%',
      placeholder: placeholder,
    });
  });

  /* --- Select2 multi (tag-style) --- */
  $('.ox-select2-multi').select2({
    theme: 'default',
    multiple: true,
    width: '100%',
    placeholder: 'Add team members…',
    closeOnSelect: false,
  });

  /* --- Flatpickr dates --- */
  flatpickr('.ox-datepicker', {
    dateFormat: 'Y-m-d',
    allowInput: true,
    disableMobile: true,
  });

  /* --- Title live preview --- */
  $('#caseTitle').on('input', function() {
    var v = $(this).val();
    $('#titlePreview').text(v || '—');
    $('#titleCount').text(v.length + ' / 200 characters');
  });

  /* --- Cascading toggles --- */
  window.toggleSection = function(type) {
    var cb   = document.getElementById(type === 'court' ? 'isInCourt' : 'isAtPolice');
    cb.checked = !cb.checked;
    updateSection(type, cb.checked);
  };

  function updateSection(type, open) {
    var $sec   = $('#' + type + 'Section');
    var $badge = $('#' + type + 'Badge');
    if (open) { $sec.addClass('open'); $badge.show(); }
    else       { $sec.removeClass('open'); $badge.hide(); }
  }

  $('#isInCourt').on('change', function() { updateSection('court', this.checked); });
  $('#isAtPolice').on('change', function() { updateSection('police', this.checked); });

  /* --- Submit loading state --- */
  $('#caseForm').on('submit', function() {
    $('#submitBtn').addClass('ox-loading').prop('disabled', true);
  });

})(jQuery);
</script>
@endpush
