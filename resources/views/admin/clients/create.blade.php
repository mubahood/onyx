@extends('layouts.admin')
@section('title', 'New Client')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>New Client</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span>
      <a href="{{ route('admin.clients.index') }}">Clients</a> <span>/</span>
      <span>Create</span>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('admin.clients.store') }}">
@csrf

<div class="ad-card">
  <div class="ad-card-header">
    <span class="ad-card-title">Client Information</span>
  </div>
  <div class="ad-card-body">

    <div class="ad-form-section-title"><i class="fas fa-user"></i> Personal Details</div>
    <div class="ad-form-grid">
      <div class="ad-form-group">
        <label>First Name <span class="req">*</span></label>
        <input class="ad-input" type="text" name="first_name" value="{{ old('first_name') }}" required>
      </div>
      <div class="ad-form-group">
        <label>Last Name <span class="req">*</span></label>
        <input class="ad-input" type="text" name="last_name" value="{{ old('last_name') }}" required>
      </div>
      <div class="ad-form-group">
        <label>Gender</label>
        <select class="ad-select" name="gender">
          <option value="">— Select —</option>
          <option value="male"   {{ old('gender')=='male'   ? 'selected' : '' }}>Male</option>
          <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
          <option value="other"  {{ old('gender')=='other'  ? 'selected' : '' }}>Other</option>
        </select>
      </div>
      <div class="ad-form-group">
        <label>Date of Birth</label>
        <input class="ad-input" type="date" name="dob" value="{{ old('dob') }}">
      </div>
    </div>

    <div class="ad-form-section">
      <div class="ad-form-section-title"><i class="fas fa-address-book"></i> Contact Details</div>
      <div class="ad-form-grid">
        <div class="ad-form-group">
          <label>Primary Phone <span class="req">*</span></label>
          <input class="ad-input" type="text" name="phone" value="{{ old('phone') }}" placeholder="+256 7XX XXX XXX" required>
        </div>
        <div class="ad-form-group">
          <label>Alternative Phone</label>
          <input class="ad-input" type="text" name="phone_alt" value="{{ old('phone_alt') }}">
        </div>
        <div class="ad-form-group">
          <label>Email Address</label>
          <input class="ad-input" type="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="ad-form-group">
          <label>District</label>
          <input class="ad-input" type="text" name="district" value="{{ old('district') }}" placeholder="e.g. Kampala">
        </div>
        <div class="ad-form-group span-2">
          <label>Physical Address <span class="req">*</span></label>
          <textarea class="ad-textarea" name="address" rows="2" required>{{ old('address') }}</textarea>
        </div>
      </div>
    </div>

    <div class="ad-form-section">
      <div class="ad-form-section-title"><i class="fas fa-id-card"></i> Identification</div>
      <div class="ad-form-grid">
        <div class="ad-form-group">
          <label>ID Type</label>
          <select class="ad-select" name="id_type">
            <option value="">— None —</option>
            <option value="national_id"     {{ old('id_type')=='national_id'     ? 'selected' : '' }}>National ID</option>
            <option value="passport"        {{ old('id_type')=='passport'        ? 'selected' : '' }}>Passport</option>
            <option value="driving_permit"  {{ old('id_type')=='driving_permit'  ? 'selected' : '' }}>Driving Permit</option>
            <option value="refugee_id"      {{ old('id_type')=='refugee_id'      ? 'selected' : '' }}>Refugee ID</option>
            <option value="other"           {{ old('id_type')=='other'           ? 'selected' : '' }}>Other</option>
          </select>
        </div>
        <div class="ad-form-group">
          <label>ID Number</label>
          <input class="ad-input" type="text" name="id_number" value="{{ old('id_number') }}">
        </div>
        <div class="ad-form-group">
          <label>Occupation</label>
          <input class="ad-input" type="text" name="occupation" value="{{ old('occupation') }}">
        </div>
        <div class="ad-form-group">
          <label>Company / Organisation</label>
          <input class="ad-input" type="text" name="company" value="{{ old('company') }}">
        </div>
      </div>
    </div>

    <div class="ad-form-section">
      <div class="ad-form-section-title"><i class="fas fa-note-sticky"></i> Additional Notes</div>
      <div class="ad-form-grid cols-1">
        <div class="ad-form-group">
          <label>Notes</label>
          <textarea class="ad-textarea" name="notes" rows="3" placeholder="Any relevant background information…">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>

  </div>
  <div class="ad-card-footer">
    <a href="{{ route('admin.clients.index') }}" class="btn-ad btn-ad-ghost">Cancel</a>
    <button type="submit" class="btn-ad btn-ad-primary">
      <i class="fas fa-check"></i> Create Client
    </button>
  </div>
</div>

</form>
@endsection
