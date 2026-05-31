@extends('layouts.admin')
@section('title', 'Edit Case')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Edit Case</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.cases.index') }}">Cases</a> <span>/</span>
      <a href="{{ route('admin.cases.show', $case) }}">{{ $case->case_number }}</a> <span>/</span>
      <span>Edit</span>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('admin.cases.update', $case) }}">
@csrf @method('PUT')

<div class="ad-card">
  <div class="ad-card-header">
    <span class="ad-card-title">Case: <span class="case-number-badge">{{ $case->case_number }}</span></span>
  </div>
  <div class="ad-card-body">

    <div class="ad-form-section-title"><i class="fas fa-scale-balanced"></i> Basic Details</div>
    <div class="ad-form-grid">
      <div class="ad-form-group span-2">
        <label>Case Title <span class="req">*</span></label>
        <input class="ad-input" type="text" name="title" value="{{ old('title', $case->title) }}" required>
      </div>
      <div class="ad-form-group">
        <label>Category <span class="req">*</span></label>
        <select class="ad-select" name="category" required>
          @foreach(['civil_litigation'=>'Civil Litigation','criminal_defense'=>'Criminal Defence','family_law'=>'Family & Matrimonial','land_property'=>'Land & Property','commercial_corporate'=>'Commercial & Corporate','employment_labour'=>'Employment & Labour','human_rights'=>'Human Rights','constitutional'=>'Constitutional Law','succession_probate'=>'Succession & Probate','debt_recovery'=>'Debt Recovery','immigration'=>'Immigration & Citizenship','other'=>'Other'] as $k=>$l)
          <option value="{{ $k }}" {{ old('category',$case->category)==$k?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Status <span class="req">*</span></label>
        <select class="ad-select" name="status" required>
          @foreach(['pending','active','ongoing','closed','archived'] as $s)
          <option value="{{ $s }}" {{ old('status',$case->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Stage <span class="req">*</span></label>
        <select class="ad-select" name="stage" required>
          @foreach(['intake'=>'Initial Intake','investigation'=>'Investigation','pre_trial'=>'Pre-Trial','mediation'=>'Mediation','trial'=>'Active Trial','appeal'=>'Appeal','settlement'=>'Settlement','enforcement'=>'Enforcement','closed'=>'Closed'] as $k=>$l)
          <option value="{{ $k }}" {{ old('stage',$case->stage)==$k?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Priority <span class="req">*</span></label>
        <select class="ad-select" name="priority" required>
          @foreach(['low','medium','high','urgent'] as $p)
          <option value="{{ $p }}" {{ old('priority',$case->priority)==$p?'selected':'' }}>{{ ucfirst($p) }}</option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Client <span class="req">*</span></label>
        <select class="ad-select" name="client_id" required>
          @foreach($clients as $client)
          <option value="{{ $client->id }}" {{ old('client_id',$case->client_id)==$client->id?'selected':'' }}>
            {{ $client->full_name }} ({{ $client->client_number }})
          </option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Filing Date <span class="req">*</span></label>
        <input class="ad-input" type="date" name="filing_date" value="{{ old('filing_date', $case->filing_date->format('Y-m-d')) }}" required>
      </div>
      <div class="ad-form-group span-2">
        <label>Description</label>
        <textarea class="ad-textarea" name="description" rows="3">{{ old('description', $case->description) }}</textarea>
      </div>
    </div>

    <div class="ad-form-section">
      <div class="ad-form-section-title"><i class="fas fa-user-tie"></i> Officers</div>
      <div class="ad-form-grid">
        <div class="ad-form-group">
          <label>Main Officer (Lead)</label>
          <select class="ad-select" name="main_officer_id">
            <option value="">— Unassigned —</option>
            @foreach($officers as $officer)
            <option value="{{ $officer->id }}" {{ old('main_officer_id',$case->main_officer_id)==$officer->id?'selected':'' }}>
              {{ $officer->name }} ({{ $officer->role_label }})
            </option>
            @endforeach
          </select>
        </div>
        <div class="ad-form-group">
          <label>Team Members</label>
          <select class="ad-select" name="team_officers[]" multiple style="height:90px;">
            @foreach($officers as $officer)
            <option value="{{ $officer->id }}" {{ in_array($officer->id, old('team_officers', $teamIds)) ? 'selected' : '' }}>
              {{ $officer->name }}
            </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    {{-- Court tracking --}}
    <div class="ad-form-section">
      <div class="ad-form-section-title"><i class="fas fa-gavel"></i> Court Tracking</div>
      <div style="margin-bottom:14px;">
        <label class="ad-check-group">
          <input type="hidden" name="is_in_court" value="0">
          <input type="checkbox" id="isInCourt" name="is_in_court" value="1"
            {{ old('is_in_court', $case->is_in_court) ? 'checked' : '' }}>
          <span><strong>Case is currently in court</strong></span>
        </label>
      </div>
      <div id="courtFields" style="{{ old('is_in_court', $case->is_in_court) ? '' : 'display:none;' }}">
        <div class="ad-form-grid">
          <div class="ad-form-group">
            <label>Court Name</label>
            <input class="ad-input" type="text" name="court_name" value="{{ old('court_name', $case->court_name) }}">
          </div>
          <div class="ad-form-group">
            <label>Court Division</label>
            <input class="ad-input" type="text" name="court_division" value="{{ old('court_division', $case->court_division) }}">
          </div>
          <div class="ad-form-group">
            <label>Court Case Number</label>
            <input class="ad-input" type="text" name="court_case_number" value="{{ old('court_case_number', $case->court_case_number) }}">
          </div>
          <div class="ad-form-group">
            <label>Judge Name</label>
            <input class="ad-input" type="text" name="judge_name" value="{{ old('judge_name', $case->judge_name) }}">
          </div>
          <div class="ad-form-group">
            <label>Next Hearing Date</label>
            <input class="ad-input" type="date" name="next_hearing_date" value="{{ old('next_hearing_date', $case->next_hearing_date?->format('Y-m-d')) }}">
          </div>
        </div>
      </div>
    </div>

    {{-- Police tracking --}}
    <div class="ad-form-section">
      <div class="ad-form-section-title"><i class="fas fa-shield-halved"></i> Police Tracking</div>
      <div style="margin-bottom:14px;">
        <label class="ad-check-group">
          <input type="hidden" name="is_at_police" value="0">
          <input type="checkbox" id="isAtPolice" name="is_at_police" value="1"
            {{ old('is_at_police', $case->is_at_police) ? 'checked' : '' }}>
          <span><strong>Case is at a police station</strong></span>
        </label>
      </div>
      <div id="policeFields" style="{{ old('is_at_police', $case->is_at_police) ? '' : 'display:none;' }}">
        <div class="ad-form-grid">
          <div class="ad-form-group">
            <label>Police Station</label>
            <input class="ad-input" type="text" name="police_station" value="{{ old('police_station', $case->police_station) }}">
          </div>
          <div class="ad-form-group">
            <label>OB / Reference Number</label>
            <input class="ad-input" type="text" name="police_ref_number" value="{{ old('police_ref_number', $case->police_ref_number) }}">
          </div>
          <div class="ad-form-group">
            <label>Investigating Officer</label>
            <input class="ad-input" type="text" name="investigating_officer" value="{{ old('investigating_officer', $case->investigating_officer) }}">
          </div>
        </div>
      </div>
    </div>

  </div>
  <div class="ad-card-footer">
    <a href="{{ route('admin.cases.show', $case) }}" class="btn-ad btn-ad-ghost">Cancel</a>
    <button type="submit" class="btn-ad btn-ad-primary"><i class="fas fa-check"></i> Update Case</button>
  </div>
</div>

</form>

@push('scripts')
<script>
  const courtCb = document.getElementById('isInCourt');
  const courtFields = document.getElementById('courtFields');
  courtCb.addEventListener('change', () => courtFields.style.display = courtCb.checked ? '' : 'none');

  const policeCb = document.getElementById('isAtPolice');
  const policeFields = document.getElementById('policeFields');
  policeCb.addEventListener('change', () => policeFields.style.display = policeCb.checked ? '' : 'none');
</script>
@endpush
@endsection
