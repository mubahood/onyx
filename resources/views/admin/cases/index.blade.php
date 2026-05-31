@extends('layouts.admin')
@section('title', 'Cases')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Legal Cases</h1>
    <div class="ad-breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> <span>/</span> Cases</div>
  </div>
  <a href="{{ route('admin.cases.create') }}" class="btn-ad btn-ad-primary">
    <i class="fas fa-plus"></i> New Case
  </a>
</div>

{{-- Live stat chips --}}
<div class="ox-stat-strip" id="oxStatStrip">
  <div class="ox-stat-chip active" data-filter="">
    <i class="fas fa-scale-balanced"></i> All <span class="count" id="chip-all">{{ $allCount }}</span>
  </div>
  <div class="ox-stat-chip" data-filter="pending">
    <i class="fas fa-hourglass-half"></i> Pending <span class="count" id="chip-pending">{{ $pendingCount }}</span>
  </div>
  <div class="ox-stat-chip" data-filter="active">
    <i class="fas fa-briefcase"></i> Active <span class="count" id="chip-active">{{ $activeCount }}</span>
  </div>
  <div class="ox-stat-chip" data-filter="ongoing">
    <i class="fas fa-gavel"></i> Ongoing <span class="count" id="chip-ongoing">{{ $ongoingCount }}</span>
  </div>
  <div class="ox-stat-chip" data-filter="closed">
    <i class="fas fa-lock"></i> Closed <span class="count" id="chip-closed">{{ $closedCount }}</span>
  </div>
</div>

<div class="ad-card">

  {{-- Toolbar --}}
  <div class="ad-card-header" style="flex-wrap:wrap;gap:8px;">
    <div class="ox-live-search">
      <i class="fas fa-search"></i>
      <input class="ad-input" type="text" id="oxCaseSearch" placeholder="Search case #, title, client…" style="padding-left:28px;width:240px;">
    </div>
    <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
      <select class="ad-select" id="oxCategoryFilter" style="width:160px;font-size:.75rem;">
        <option value="">All Categories</option>
        @foreach(['civil_litigation'=>'Civil Litigation','criminal_defense'=>'Criminal Defence','family_law'=>'Family Law','land_property'=>'Land & Property','commercial_corporate'=>'Commercial','employment_labour'=>'Employment','human_rights'=>'Human Rights','constitutional'=>'Constitutional','succession_probate'=>'Succession','debt_recovery'=>'Debt Recovery','immigration'=>'Immigration','other'=>'Other'] as $k=>$l)
        <option value="{{ $k }}">{{ $l }}</option>
        @endforeach
      </select>
      <select class="ad-select" id="oxPriorityFilter" style="width:130px;font-size:.75rem;">
        <option value="">All Priorities</option>
        <option value="urgent">🚨 Urgent</option>
        <option value="high">🔴 High</option>
        <option value="medium">🟡 Medium</option>
        <option value="low">🟢 Low</option>
      </select>
      <button class="btn-ad btn-ad-ghost btn-ad-sm" id="oxClearFilters" style="display:none;">
        <i class="fas fa-times"></i> Clear
      </button>
    </div>
    <span class="ad-card-title" style="margin-left:auto;font-size:.75rem;color:var(--mt);">
      <span id="oxResultCount">{{ $cases->total() }}</span> record(s)
    </span>
  </div>

  {{-- Table --}}
  <div class="ad-table-wrap">
    <table class="ad-table" id="casesTable">
      <thead>
        <tr>
          <th data-sortable>Case # <span class="sort-icon"></span></th>
          <th data-sortable>Title <span class="sort-icon"></span></th>
          <th>Client</th>
          <th>Category</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Trackers</th>
          <th data-sortable>Filed <span class="sort-icon"></span></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="casesTableBody">
        @forelse($cases as $case)
        <tr data-case-id="{{ $case->id }}">
          <td>
            <a href="{{ route('admin.cases.show', $case) }}" class="case-number-badge">{{ $case->case_number }}</a>
          </td>
          <td style="max-width:200px;">
            <div style="font-weight:600;font-size:.8rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              {{ $case->title }}
            </div>
          </td>
          <td style="font-size:.8rem;">{{ $case->client?->full_name ?? '—' }}</td>
          <td style="font-size:.75rem;color:var(--mt);">{{ \App\Models\LegalCase::categoryLabel($case->category) }}</td>
          <td>
            <select class="ox-status-sel st-{{ $case->status }}" data-case-id="{{ $case->id }}">
              @foreach(['pending','active','ongoing','closed','archived'] as $s)
              <option value="{{ $s }}" {{ $case->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </td>
          <td><span class="badge-ad badge-{{ $case->priority }}">{{ ucfirst($case->priority) }}</span></td>
          <td>
            <div style="display:flex;gap:3px;flex-wrap:wrap;">
              @if($case->is_in_court)<span class="ad-tracker-badge tracker-court" title="In Court"><i class="fas fa-gavel"></i></span>@endif
              @if($case->is_at_police)<span class="ad-tracker-badge tracker-police" title="At Police"><i class="fas fa-shield-halved"></i></span>@endif
            </div>
          </td>
          <td style="font-size:.75rem;white-space:nowrap;color:var(--mt);">{{ $case->filing_date->format('d M Y') }}</td>
          <td>
            <div class="ad-table-actions">
              <button type="button" class="btn-ad btn-ad-ghost btn-ad-icon" onclick="ONYX.cases.showDetail({{ $case->id }})" title="Quick View"><i class="fas fa-eye"></i></button>
              <a href="{{ route('admin.cases.edit', $case) }}" class="btn-ad btn-ad-ghost btn-ad-icon" title="Edit"><i class="fas fa-pen"></i></a>
              <form method="POST" action="{{ route('admin.cases.destroy', $case) }}" class="ad-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="btn-ad btn-ad-ghost btn-ad-icon ox-delete-btn" style="color:#DC2626" data-label="{{ $case->case_number }}"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="9">
          <div class="ad-empty">
            <i class="fas fa-scale-balanced"></i>
            <h3>No cases found</h3>
            <p>Start by creating your first case.</p>
            <a href="{{ route('admin.cases.create') }}" class="btn-ad btn-ad-primary">New Case</a>
          </div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Loading overlay --}}
  <div id="oxTableLoader" style="display:none;padding:30px;text-align:center;color:var(--mt);font-size:.8rem;">
    <i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i> Loading…
  </div>

  {{-- Pagination --}}
  @if($cases->hasPages())
  <div class="ad-card-footer" id="oxPaginationWrap">
    <span style="font-size:.8125rem;color:var(--mt);" id="oxPagMeta">
      {{ $cases->firstItem() }}–{{ $cases->lastItem() }} of {{ $cases->total() }}
    </span>
    <div class="ad-pagination" id="oxPagination">
      {!! $cases->withQueryString()->links('vendor.pagination.simple-default') !!}
    </div>
  </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
(function($) {

  /* --- Sortable table headers --- */
  $('th[data-sortable]').each(function() {
    $(this).on('click', function() {
      var $th  = $(this);
      var col  = $th.index();
      var asc  = !$th.hasClass('asc');
      $('th[data-sortable]').removeClass('asc desc');
      $th.addClass(asc ? 'asc' : 'desc');
      var rows = $('#casesTableBody tr').toArray();
      rows.sort(function(a, b) {
        var ta = $(a).children('td').eq(col).text().trim();
        var tb = $(b).children('td').eq(col).text().trim();
        return asc ? ta.localeCompare(tb) : tb.localeCompare(ta);
      });
      $('#casesTableBody').append(rows);
    });
  });

  /* --- Status chip filters --- */
  var activeStatus = '';
  $('.ox-stat-chip').on('click', function() {
    activeStatus = $(this).data('filter');
    $('.ox-stat-chip').removeClass('active');
    $(this).addClass('active');
    loadCases(1);
  });

  /* --- Live search + filters --- */
  var searchTimer;
  $('#oxCaseSearch, #oxCategoryFilter, #oxPriorityFilter').on('input change', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() { loadCases(1); }, 280);
    updateClearBtn();
  });

  $('#oxClearFilters').on('click', function() {
    $('#oxCaseSearch').val('');
    $('#oxCategoryFilter').val('');
    $('#oxPriorityFilter').val('');
    activeStatus = '';
    $('.ox-stat-chip').removeClass('active').first().addClass('active');
    $(this).hide();
    loadCases(1);
  });

  function updateClearBtn() {
    var hasFilters = $('#oxCaseSearch').val() || $('#oxCategoryFilter').val() || $('#oxPriorityFilter').val();
    $('#oxClearFilters').toggle(!!hasFilters);
  }

  /* --- AJAX load cases --- */
  function loadCases(page) {
    var params = {
      search:   $('#oxCaseSearch').val(),
      status:   activeStatus,
      category: $('#oxCategoryFilter').val(),
      priority: $('#oxPriorityFilter').val(),
      page:     page || 1,
    };

    $('#casesTableBody').css('opacity', .4);
    $('#oxTableLoader').show();

    $.get(ONYX_CONFIG.api + '/cases', params)
      .done(function(data) {
        renderRows(data.data);
        $('#oxResultCount').text(data.meta.total);
        updateChipCounts();
      })
      .fail(function() { Toast.error('Failed to load cases.'); })
      .always(function() {
        $('#casesTableBody').css('opacity', 1);
        $('#oxTableLoader').hide();
      });
  }

  function renderRows(cases) {
    var $tbody = $('#casesTableBody').empty();
    if (!cases || !cases.length) {
      $tbody.html('<tr><td colspan="9"><div class="ad-empty"><i class="fas fa-scale-balanced"></i><h3>No cases found</h3></div></td></tr>');
      return;
    }
    cases.forEach(function(c) {
      var trackers = '';
      if (c.is_in_court)  trackers += '<span class="ad-tracker-badge tracker-court" title="In Court"><i class="fas fa-gavel"></i></span>';
      if (c.is_at_police) trackers += '<span class="ad-tracker-badge tracker-police" title="At Police"><i class="fas fa-shield-halved"></i></span>';

      var statusOptions = ['pending','active','ongoing','closed','archived'].map(function(s) {
        return '<option value="'+s+'"'+(c.status===s?' selected':'')+'>'+s.charAt(0).toUpperCase()+s.slice(1)+'</option>';
      }).join('');

      $tbody.append('<tr data-case-id="'+c.id+'">' +
        '<td><a href="'+window.ONYX_CONFIG.base+'/cases/'+c.id+'" class="case-number-badge">'+c.case_number+'</a></td>' +
        '<td style="max-width:200px;"><div style="font-weight:600;font-size:.8rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">'+c.title+'</div></td>' +
        '<td style="font-size:.8rem;">'+(c.client?.full_name||'—')+'</td>' +
        '<td style="font-size:.75rem;color:var(--mt);">'+c.category_label+'</td>' +
        '<td><select class="ox-status-sel st-'+c.status+'" data-case-id="'+c.id+'">'+statusOptions+'</select></td>' +
        '<td><span class="badge-ad badge-'+c.priority+'">'+c.priority+'</span></td>' +
        '<td>'+trackers+'</td>' +
        '<td style="font-size:.75rem;white-space:nowrap;color:var(--mt);">'+c.filing_date+'</td>' +
        '<td><div class="ad-table-actions">' +
          '<button type="button" class="btn-ad btn-ad-ghost btn-ad-icon" onclick="ONYX.cases.showDetail('+c.id+')" title="Quick View"><i class="fas fa-eye"></i></button>' +
          '<a href="'+window.ONYX_CONFIG.base+'/cases/'+c.id+'/edit" class="btn-ad btn-ad-ghost btn-ad-icon" title="Edit"><i class="fas fa-pen"></i></a>' +
        '</div></td>' +
      '</tr>');
    });
  }

  function updateChipCounts() {
    $.get(ONYX_CONFIG.api + '/dashboard-stats').done(function(s) {
      $('#chip-all').text(s.total_cases || 0);
      $('#chip-pending').text(s.pending_cases || 0);
      $('#chip-active').text(s.active_cases || 0);
      $('#chip-ongoing').text((s.active_cases || 0));
      $('#chip-closed').text(s.closed_cases || 0);
    });
  }

})(jQuery);
</script>
@endpush
