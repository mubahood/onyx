@extends('layouts.admin')
@section('title', 'Edit Account')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Edit Account</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.accounts.index') }}">Accounts</a> <span>/</span> {{ $account->name }} <span>/</span> Edit</div>
  </div>
</div>

<form method="POST" action="{{ route('admin.accounts.update', $account) }}">
@csrf @method('PUT')
<div class="ad-card">
  <div class="ad-card-header"><span class="ad-card-title">{{ $account->name }}</span></div>
  <div class="ad-card-body">
    <div class="ad-form-grid">
      <div class="ad-form-group">
        <label>Account Name <span class="req">*</span></label>
        <input class="ad-input" type="text" name="name" value="{{ old('name', $account->name) }}" required>
      </div>
      <div class="ad-form-group">
        <label>Type <span class="req">*</span></label>
        <select class="ad-select" name="type" required>
          @foreach(['cash'=>'Cash','bank'=>'Bank Account','mobile_money'=>'Mobile Money'] as $k=>$l)
          <option value="{{ $k }}" {{ old('type',$account->type)==$k?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Bank Name</label>
        <input class="ad-input" type="text" name="bank_name" value="{{ old('bank_name', $account->bank_name) }}">
      </div>
      <div class="ad-form-group">
        <label>Account Number</label>
        <input class="ad-input" type="text" name="account_number" value="{{ old('account_number', $account->account_number) }}">
      </div>
      <div class="ad-form-group">
        <label>Branch</label>
        <input class="ad-input" type="text" name="branch" value="{{ old('branch', $account->branch) }}">
      </div>
      <div class="ad-form-group span-2">
        <label>Description</label>
        <textarea class="ad-textarea" name="description" rows="2">{{ old('description', $account->description) }}</textarea>
      </div>
      <div class="ad-form-group">
        <label class="ad-check-group" style="margin-top:20px;">
          <input type="checkbox" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }}>
          <span>Account is active</span>
        </label>
      </div>
    </div>
  </div>
  <div class="ad-card-footer">
    <a href="{{ route('admin.accounts.index') }}" class="btn-ad btn-ad-ghost">Cancel</a>
    <button type="submit" class="btn-ad btn-ad-primary"><i class="fas fa-check"></i> Update Account</button>
  </div>
</div>
</form>
@endsection
