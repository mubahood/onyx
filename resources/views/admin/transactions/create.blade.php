@extends('layouts.admin')
@section('title', 'Record Transaction')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Record Transaction</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.transactions.index') }}">Transactions</a> <span>/</span> Create</div>
  </div>
</div>

<form method="POST" action="{{ route('admin.transactions.store') }}">
@csrf
<div class="ad-card">
  <div class="ad-card-header"><span class="ad-card-title">Transaction Details</span></div>
  <div class="ad-card-body">
    <div class="ad-form-grid">
      <div class="ad-form-group">
        <label>Type <span class="req">*</span></label>
        <select class="ad-select" name="type" required>
          <option value="income"  {{ old('type','income')=='income'  ?'selected':'' }}>Income</option>
          <option value="expense" {{ old('type')=='expense'?'selected':'' }}>Expense</option>
        </select>
      </div>
      <div class="ad-form-group">
        <label>Amount (UGX) <span class="req">*</span></label>
        <input class="ad-input" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required placeholder="0.00">
      </div>
      <div class="ad-form-group span-2">
        <label>Description <span class="req">*</span></label>
        <input class="ad-input" type="text" name="description" value="{{ old('description') }}" required placeholder="Brief description of the transaction">
      </div>
      <div class="ad-form-group">
        <label>Account <span class="req">*</span></label>
        <select class="ad-select" name="account_id" required>
          <option value="">— Select Account —</option>
          @foreach($accounts as $account)
          <option value="{{ $account->id }}" {{ old('account_id', request('account_id'))==$account->id?'selected':'' }}>
            {{ $account->name }} ({{ \App\Models\Account::typeLabel($account->type) }})
          </option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Payment Method <span class="req">*</span></label>
        <select class="ad-select" name="payment_method" required>
          <option value="cash"          {{ old('payment_method','cash')=='cash'?'selected':'' }}>Cash</option>
          <option value="bank_transfer" {{ old('payment_method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
          <option value="cheque"        {{ old('payment_method')=='cheque'?'selected':'' }}>Cheque</option>
          <option value="mobile_money"  {{ old('payment_method')=='mobile_money'?'selected':'' }}>Mobile Money</option>
        </select>
      </div>
      <div class="ad-form-group">
        <label>Transaction Date <span class="req">*</span></label>
        <input class="ad-input" type="date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
      </div>
      <div class="ad-form-group">
        <label>Reference / Cheque Number</label>
        <input class="ad-input" type="text" name="reference_number" value="{{ old('reference_number') }}">
      </div>
      <div class="ad-form-group">
        <label>Link to Case (optional)</label>
        <select class="ad-select" name="case_id">
          <option value="">— None —</option>
          @foreach($cases as $case)
          <option value="{{ $case->id }}" {{ old('case_id', request('case_id'))==$case->id?'selected':'' }}>
            {{ $case->case_number }} — {{ $case->client?->full_name }}
          </option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Link to Client (optional)</label>
        <select class="ad-select" name="client_id">
          <option value="">— None —</option>
          @foreach($clients as $client)
          <option value="{{ $client->id }}" {{ old('client_id')==$client->id?'selected':'' }}>
            {{ $client->full_name }} ({{ $client->client_number }})
          </option>
          @endforeach
        </select>
      </div>
      <div class="ad-form-group">
        <label>Financial Period</label>
        <select class="ad-select" name="financial_period_id">
          <option value="">— None —</option>
          @if($period)
            <option value="{{ $period->id }}" selected>{{ $period->name }} (Active)</option>
          @endif
        </select>
      </div>
      <div class="ad-form-group span-2">
        <label>Additional Details</label>
        <textarea class="ad-textarea" name="details" rows="2" placeholder="Optional additional notes…">{{ old('details') }}</textarea>
      </div>
    </div>
  </div>
  <div class="ad-card-footer">
    <a href="{{ route('admin.transactions.index') }}" class="btn-ad btn-ad-ghost">Cancel</a>
    <button type="submit" class="btn-ad btn-ad-primary"><i class="fas fa-check"></i> Record Transaction</button>
  </div>
</div>
</form>
@endsection
