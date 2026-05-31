@extends('layouts.admin')
@section('title', 'Financial Periods')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Financial Periods</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span> Finance <span>/</span> Periods</div>
  </div>
  <a href="{{ route('admin.periods.create') }}" class="btn-ad btn-ad-primary"><i class="fas fa-plus"></i> New Period</a>
</div>

<div class="ad-card">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead><tr><th>Name</th><th>Start Date</th><th>End Date</th><th>Transactions</th><th>Income</th><th>Expense</th><th>Net</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($periods as $period)
        <tr>
          <td style="font-weight:600;">{{ $period->name }}</td>
          <td>{{ $period->start_date->format('d M Y') }}</td>
          <td>{{ $period->end_date->format('d M Y') }}</td>
          <td>{{ $period->transactions_count }}</td>
          <td class="finance-amount-income">+{{ number_format($period->total_income, 0) }}</td>
          <td class="finance-amount-expense">-{{ number_format($period->total_expense, 0) }}</td>
          <td style="font-weight:700;color:{{ $period->net >= 0 ? '#15803D' : '#DC2626' }};">
            {{ number_format($period->net, 0) }}
          </td>
          <td>
            @if($period->is_active)
              <span class="badge-ad badge-active">Active</span>
            @else
              <span class="badge-ad badge-gray">Inactive</span>
            @endif
          </td>
          <td>
            <div class="ad-table-actions">
              @if(!$period->is_active)
              <form method="POST" action="{{ route('admin.periods.activate', $period) }}">
                @csrf
                <button type="submit" class="btn-ad btn-ad-ghost btn-ad-sm" title="Set as Active">
                  <i class="fas fa-check"></i>
                </button>
              </form>
              @endif
              <a href="{{ route('admin.periods.edit', $period) }}" class="btn-ad btn-ad-ghost btn-ad-icon"><i class="fas fa-pen"></i></a>
              <form method="POST" action="{{ route('admin.periods.destroy', $period) }}" class="ad-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn-ad btn-ad-ghost btn-ad-icon ox-delete-btn" style="color:#DC2626" data-label="{{ $period->name }}"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div class="ad-empty">
              <i class="fas fa-calendar-alt"></i>
              <h3>No financial periods</h3>
              <p>Create a period to organise your transactions.</p>
              <a href="{{ route('admin.periods.create') }}" class="btn-ad btn-ad-primary">Create Period</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
