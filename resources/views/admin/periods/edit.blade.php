@extends('layouts.admin')
@section('title', 'Edit Period')

@section('content')

<div class="ad-page-header">
  <div><h1>Edit Period</h1><div class="ad-breadcrumb"><a href="{{ route('admin.periods.index') }}">Periods</a> <span>/</span> {{ $period->name }}</div></div>
</div>

<form method="POST" action="{{ route('admin.periods.update', $period) }}">
@csrf @method('PUT')
<div class="ad-card">
  <div class="ad-card-body">
    <div class="ad-form-grid">
      <div class="ad-form-group span-2">
        <label>Period Name <span class="req">*</span></label>
        <input class="ad-input" type="text" name="name" value="{{ old('name', $period->name) }}" required>
      </div>
      <div class="ad-form-group">
        <label>Start Date <span class="req">*</span></label>
        <input class="ad-input" type="date" name="start_date" value="{{ old('start_date', $period->start_date->format('Y-m-d')) }}" required>
      </div>
      <div class="ad-form-group">
        <label>End Date <span class="req">*</span></label>
        <input class="ad-input" type="date" name="end_date" value="{{ old('end_date', $period->end_date->format('Y-m-d')) }}" required>
      </div>
      <div class="ad-form-group span-2">
        <label>Description</label>
        <textarea class="ad-textarea" name="description" rows="2">{{ old('description', $period->description) }}</textarea>
      </div>
      <div class="ad-form-group">
        <label class="ad-check-group">
          <input type="checkbox" name="is_active" value="1" {{ $period->is_active ? 'checked' : '' }}>
          <span>Active period</span>
        </label>
      </div>
    </div>
  </div>
  <div class="ad-card-footer">
    <a href="{{ route('admin.periods.index') }}" class="btn-ad btn-ad-ghost">Cancel</a>
    <button type="submit" class="btn-ad btn-ad-primary"><i class="fas fa-check"></i> Update</button>
  </div>
</div>
</form>
@endsection
