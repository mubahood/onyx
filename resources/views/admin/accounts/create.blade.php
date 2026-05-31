@extends('layouts.admin')
@section('title', 'New Account')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>New Account</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.accounts.index') }}">Accounts</a> <span>/</span> Create</div>
  </div>
</div>

<form method="POST" action="{{ route('admin.accounts.store') }}">
@csrf
<div class="ad-card">
  <div class="ad-card-header"><span class="ad-card-title">Account Details</span></div>
  <div class="ad-card-body">
    <div class="ad-form-grid">
      <div class="ad-form-group">
        <label>Account Name <span class="req">*</span></label>
        <input class="ad-input" type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Main Operations Account">
      </div>
      <div class="ad-form-group">
        <label>Type <span class="req">*</span></label>
        <select class="ad-select" name="type" id="accountType" required>
          <option value="cash" {{ old('type')=='cash'?'selected':'' }}>Cash</option>
          <option value="bank" {{ old('type')=='bank'?'selected':'' }}>Bank Account</option>
          <option value="mobile_money" {{ old('type')=='mobile_money'?'selected':'' }}>Mobile Money</option>
        </select>
      </div>
      <div class="ad-form-group" id="bankNameGroup">
        <label>Bank Name</label>
        <input class="ad-input" type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="e.g. Stanbic Bank Uganda">
      </div>
      <div class="ad-form-group" id="accNumGroup">
        <label>Account Number</label>
        <input class="ad-input" type="text" name="account_number" value="{{ old('account_number') }}">
      </div>
      <div class="ad-form-group">
        <label>Branch</label>
        <input class="ad-input" type="text" name="branch" value="{{ old('branch') }}">
      </div>
      <div class="ad-form-group">
        <label>Opening Balance (UGX)</label>
        <input class="ad-input" type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', 0) }}">
      </div>
      <div class="ad-form-group span-2">
        <label>Description</label>
        <textarea class="ad-textarea" name="description" rows="2">{{ old('description') }}</textarea>
      </div>
      <div class="ad-form-group">
        <label class="ad-check-group" style="margin-top:20px;">
          <input type="checkbox" name="is_active" value="1" checked>
          <span>Account is active</span>
        </label>
      </div>
    </div>
  </div>
  <div class="ad-card-footer">
    <a href="{{ route('admin.accounts.index') }}" class="btn-ad btn-ad-ghost">Cancel</a>
    <button type="submit" class="btn-ad btn-ad-primary"><i class="fas fa-check"></i> Create Account</button>
  </div>
</div>
</form>
@endsection
