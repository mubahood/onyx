@extends('layouts.admin')
@section('title', $transaction->transaction_number)

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Transaction: {{ $transaction->transaction_number }}</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.transactions.index') }}">Transactions</a> <span>/</span> {{ $transaction->transaction_number }}</div>
  </div>
  <div style="display:flex;gap:10px;">
    @if($transaction->type === 'income')
      <a href="{{ route('admin.transactions.receipt', $transaction) }}" class="btn-ad btn-ad-outline">
        <i class="fas fa-receipt"></i> View Receipt
      </a>
      <a href="{{ route('admin.transactions.pdf', $transaction) }}" class="btn-ad btn-ad-primary">
        <i class="fas fa-file-pdf"></i> Download PDF
      </a>
    @endif
  </div>
</div>

<div class="ad-card">
  <div class="ad-card-header">
    <span class="ad-card-title">
      <span class="badge-ad badge-{{ $transaction->type }}" style="font-size:0.875rem;padding:5px 14px;">
        {{ strtoupper($transaction->type) }}
      </span>
    </span>
    <div style="font-size:1.5rem;font-weight:700;" class="finance-amount-{{ $transaction->type }}">
      {{ $transaction->type==='income'?'+':'-' }} UGX {{ number_format($transaction->amount, 0) }}
    </div>
  </div>
  <div class="ad-card-body">
    <div class="ad-detail-grid">
      <div class="ad-detail-item"><div class="ad-detail-label">Transaction #</div><div class="ad-detail-value" style="font-family:monospace;">{{ $transaction->transaction_number }}</div></div>
      @if($transaction->receipt_number)
      <div class="ad-detail-item"><div class="ad-detail-label">Receipt #</div><div class="ad-detail-value" style="font-family:monospace;color:var(--ad-primary);">{{ $transaction->receipt_number }}</div></div>
      @endif
      <div class="ad-detail-item"><div class="ad-detail-label">Date</div><div class="ad-detail-value">{{ $transaction->transaction_date->format('d M Y') }}</div></div>
      <div class="ad-detail-item"><div class="ad-detail-label">Account</div><div class="ad-detail-value">{{ $transaction->account?->name }}</div></div>
      <div class="ad-detail-item"><div class="ad-detail-label">Payment Method</div><div class="ad-detail-value">{{ \App\Models\Transaction::methodLabel($transaction->payment_method) }}</div></div>
      @if($transaction->reference_number)
      <div class="ad-detail-item"><div class="ad-detail-label">Reference #</div><div class="ad-detail-value">{{ $transaction->reference_number }}</div></div>
      @endif
      <div class="ad-detail-item"><div class="ad-detail-label">Case</div><div class="ad-detail-value">
        @if($transaction->case)
          <a href="{{ route('admin.cases.show', $transaction->case) }}" style="color:var(--ad-primary);">{{ $transaction->case->case_number }}</a>
        @else — @endif
      </div></div>
      <div class="ad-detail-item"><div class="ad-detail-label">Client</div><div class="ad-detail-value">{{ $transaction->client?->full_name ?? '—' }}</div></div>
      <div class="ad-detail-item"><div class="ad-detail-label">Period</div><div class="ad-detail-value">{{ $transaction->period?->name ?? '—' }}</div></div>
      <div class="ad-detail-item"><div class="ad-detail-label">Recorded By</div><div class="ad-detail-value">{{ $transaction->createdBy?->name }}</div></div>
      <div class="ad-detail-item"><div class="ad-detail-label">Recorded At</div><div class="ad-detail-value">{{ $transaction->created_at->format('d M Y, H:i') }}</div></div>
    </div>
    <div style="margin-top:16px;">
      <div class="ad-detail-label">Description</div>
      <p style="font-size:0.875rem;margin-top:4px;">{{ $transaction->description }}</p>
      @if($transaction->details)
      <p style="font-size:0.8125rem;color:var(--ad-muted);margin-top:8px;">{{ $transaction->details }}</p>
      @endif
    </div>
  </div>
</div>
@endsection
