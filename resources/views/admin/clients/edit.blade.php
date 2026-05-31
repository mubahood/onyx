@extends('layouts.admin')
@section('title', 'Edit Client')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Edit Client</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.clients.index') }}">Clients</a> <span>/</span>
      <a href="{{ route('admin.clients.show', $client) }}">{{ $client->full_name }}</a> <span>/</span>
      <span>Edit</span>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('admin.clients.update', $client) }}">
@csrf @method('PUT')

<div class="ad-card">
  <div class="ad-card-header">
    <span class="ad-card-title">Client: <span class="case-number-badge">{{ $client->client_number }}</span></span>
  </div>
  <div class="ad-card-body">

    <div class="ad-form-section-title"><i class="fas fa-user"></i> Personal Details</div>
    <div class="ad-form-grid">
      <div class="ad-form-group">
        <label>First Name <span class="req">*</span></label>
        <input class="ad-input" type="text" name="first_name" value="{{ old('first_name', $client->first_name) }}" required>
      </div>
      <div class="ad-form-group">
        <label>Last Name <span class="req">*</span></label>
        <input class="ad-input" type="text" name="last_name" value="{{ old('last_name', $client->last_name) }}" required>
      </div>
      <div class="ad-form-group">
        <label>Gender</label>
        <select class="ad-select" name="gender">
          <option value="">— Select —</option>
          @foreach(['male'=>'Male','female'=>'Female','other'=>'Other'] as $v=>$l)
          <option value="{{ $v }}" {{ old('gender',$client->gender)==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Date of Birth</label>
        <input class="ad-input" type="date" name="dob" value="{{ old('dob', $client->dob?->format('Y-m-d')) }}">
      </div>
    </div>

    <div class="ad-form-section">
      <div class="ad-form-section-title"><i class="fas fa-address-book"></i> Contact Details</div>
      <div class="ad-form-grid">
        <div class="ad-form-group">
          <label>Primary Phone <span class="req">*</span></label>
          <input class="ad-input" type="text" name="phone" value="{{ old('phone', $client->phone) }}" required>
        </div>
        <div class="ad-form-group">
          <label>Alternative Phone</label>
          <input class="ad-input" type="text" name="phone_alt" value="{{ old('phone_alt', $client->phone_alt) }}">
        </div>
        <div class="ad-form-group">
          <label>Email</label>
          <input class="ad-input" type="email" name="email" value="{{ old('email', $client->email) }}">
        </div>
        <div class="ad-form-group">
          <label>District</label>
          <input class="ad-input" type="text" name="district" value="{{ old('district', $client->district) }}">
        </div>
        <div class="ad-form-group span-2">
          <label>Physical Address <span class="req">*</span></label>
          <textarea class="ad-textarea" name="address" rows="2" required>{{ old('address', $client->address) }}</textarea>
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
            @foreach(['national_id'=>'National ID','passport'=>'Passport','driving_permit'=>'Driving Permit','refugee_id'=>'Refugee ID','other'=>'Other'] as $v=>$l)
            <option value="{{ $v }}" {{ old('id_type',$client->id_type)==$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="ad-form-group">
          <label>ID Number</label>
          <input class="ad-input" type="text" name="id_number" value="{{ old('id_number', $client->id_number) }}">
        </div>
        <div class="ad-form-group">
          <label>Occupation</label>
          <input class="ad-input" type="text" name="occupation" value="{{ old('occupation', $client->occupation) }}">
        </div>
        <div class="ad-form-group">
          <label>Company</label>
          <input class="ad-input" type="text" name="company" value="{{ old('company', $client->company) }}">
        </div>
      </div>
    </div>

    <div class="ad-form-section">
      <div class="ad-form-section-title"><i class="fas fa-note-sticky"></i> Notes</div>
      <div class="ad-form-grid cols-1">
        <div class="ad-form-group">
          <textarea class="ad-textarea" name="notes" rows="3">{{ old('notes', $client->notes) }}</textarea>
        </div>
      </div>
    </div>

  </div>
  <div class="ad-card-footer">
    <a href="{{ route('admin.clients.show', $client) }}" class="btn-ad btn-ad-ghost">Cancel</a>
    <button type="submit" class="btn-ad btn-ad-primary"><i class="fas fa-check"></i> Update Client</button>
  </div>
</div>

</form>
@endsection
