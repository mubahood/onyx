@extends('layouts.admin')
@section('title', 'Upload Document')

@section('content')

<div class="ad-page-header">
  <div>
    <h1>Upload Document</h1>
    <div class="ad-breadcrumb">
      <a href="{{ route('admin.documents.index') }}">Documents</a> <span>/</span> Upload
    </div>
  </div>
  <a href="{{ route('admin.documents.index') }}" class="btn-ad btn-ad-ghost">
    <i class="fas fa-arrow-left"></i> Back
  </a>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

  {{-- LEFT: Drop zone --}}
  <div>
    <div class="ad-card" style="margin-bottom:16px;">
      <div class="ad-card-header">
        <span class="ad-card-title"><i class="fas fa-cloud-arrow-up" style="color:var(--br);margin-right:6px;"></i>Drop Files Here</span>
        <span style="font-size:.7rem;color:var(--mt);">PDF, DOCX, JPG, PNG — max 20 MB each</span>
      </div>
      <div class="ad-card-body">

        {{-- Dropzone target --}}
        <div class="dz-zone" id="oxDropzone">
          <div class="dz-drop-icon"><i class="fas fa-cloud-arrow-up"></i></div>
          <div class="dz-drop-label"><strong>Drag &amp; drop files here</strong> or click to browse</div>
          <div class="dz-drop-hint">Multiple files supported. Metadata will be applied to all.</div>
          <input type="file" id="dzFileInput" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.txt" style="display:none;">
        </div>

        {{-- Previews container --}}
        <div class="dz-preview-grid" id="dzPreviews"></div>

        {{-- Upload status bar --}}
        <div class="ox-upload-status" id="oxUploadStatus" style="display:none;">
          <i class="fas fa-spinner fa-spin"></i>
          <span id="oxUploadLabel">Uploading…</span>
          <div class="ox-upload-bar">
            <div class="ox-upload-bar-fill" id="oxUploadFill"></div>
          </div>
        </div>

      </div>
    </div>

    {{-- Result list (after upload) --}}
    <div id="oxUploadedDocs" style="display:none;">
      <div class="ad-card">
        <div class="ad-card-header">
          <span class="ad-card-title"><i class="fas fa-check-circle" style="color:#15803D;margin-right:5px;"></i>Uploaded Successfully</span>
        </div>
        <div class="ad-table-wrap">
          <table class="ad-table" id="oxUploadedTable">
            <thead>
              <tr><th>Doc #</th><th>Title</th><th>Category</th><th>Size</th><th>Actions</th></tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="ad-card-footer">
          <a href="{{ route('admin.documents.index') }}" class="btn-ad btn-ad-ghost">View All Documents</a>
          <button type="button" class="btn-ad btn-ad-primary" onclick="resetDropzone()">
            <i class="fas fa-plus"></i> Upload More
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- RIGHT: Metadata --}}
  <div class="ad-card">
    <div class="ad-card-header">
      <span class="ad-card-title"><i class="fas fa-tags" style="color:var(--br);margin-right:6px;"></i>Document Metadata</span>
    </div>
    <div class="ad-card-body" style="display:flex;flex-direction:column;gap:14px;">

      <div class="ad-form-group">
        <label>Document Title <span class="req">*</span></label>
        <input class="ad-input" type="text" id="dz_title" placeholder="e.g. Notice to Sue — Ssemakula">
        <span class="ad-form-hint">If multiple files, title will be used as prefix</span>
      </div>

      <div class="ad-form-group">
        <label>Category <span class="req">*</span></label>
        <select class="ad-select" id="dz_category">
          <option value="">— Select Category —</option>
          @foreach(['notice_to_sue'=>'Notice to Sue','court_order'=>'Court Order / Ruling','affidavit'=>'Affidavit','power_of_attorney'=>'Power of Attorney','contract_agreement'=>'Contract / Agreement','evidence'=>'Evidence / Exhibit','police_report'=>'Police Report (OB)','correspondence'=>'Correspondence','legal_opinion'=>'Legal Opinion','judgment'=>'Judgment / Decree','land_title'=>'Land Title','company_docs'=>'Company Documents','id_documents'=>'ID Documents','summons'=>'Summons','pleadings'=>'Pleadings','other'=>'Other'] as $k=>$l)
          <option value="{{ $k }}" {{ old('category')==$k?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>

      <div class="ad-form-group">
        <label>Link to Case</label>
        <select class="ad-select ox-select2" id="dz_case_id" data-placeholder="— No case —">
          <option value="">— No case —</option>
          @foreach($cases as $case)
          <option value="{{ $case->id }}" {{ old('case_id', request('case_id'))==$case->id?'selected':'' }}>
            {{ $case->case_number }} — {{ $case->client?->full_name }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="ad-form-group">
        <label>Link to Client</label>
        <select class="ad-select ox-select2" id="dz_client_id" data-placeholder="— No client —">
          <option value="">— No client —</option>
          @foreach($clients as $client)
          <option value="{{ $client->id }}" {{ old('client_id')==$client->id?'selected':'' }}>
            {{ $client->full_name }} ({{ $client->client_number }})
          </option>
          @endforeach
        </select>
      </div>

      <div class="ad-form-group">
        <label>Description</label>
        <textarea class="ad-textarea" id="dz_description" rows="2" placeholder="Optional notes about this document…"></textarea>
      </div>

      <label class="ad-check-group">
        <input type="checkbox" id="dz_confidential">
        <span>Mark as Confidential</span>
      </label>

      <button type="button" class="btn-ad btn-ad-primary" id="oxUploadBtn" style="width:100%;justify-content:center;">
        <i class="fas fa-cloud-arrow-up"></i> Upload Files
      </button>
      <div id="oxMetaError" class="ad-form-error" style="display:none;"></div>

    </div>
  </div>

</div>

@endsection

@push('scripts')
<script>
(function($) {
  var files = [];
  var uploaded = [];
  Dropzone.autoDiscover = false;

  var $zone    = $('#oxDropzone');
  var $input   = $('#dzFileInput');
  var $previews = $('#dzPreviews');

  /* --- File type icon --- */
  function fileIcon(name) {
    var ext = (name || '').split('.').pop().toLowerCase();
    var map = {
      pdf: 'fa-file-pdf', doc: 'fa-file-word', docx: 'fa-file-word',
      xls: 'fa-file-excel', xlsx: 'fa-file-excel',
      jpg: 'fa-file-image', jpeg: 'fa-file-image', png: 'fa-file-image', gif: 'fa-file-image',
      zip: 'fa-file-zipper', txt: 'fa-file-lines', ppt: 'fa-file-powerpoint', pptx: 'fa-file-powerpoint',
    };
    return map[ext] || 'fa-file-alt';
  }

  function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes/1024).toFixed(1) + ' KB';
    return (bytes/1048576).toFixed(1) + ' MB';
  }

  function addFileCard(file, idx) {
    var icon = fileIcon(file.name);
    var card = $('<div class="dz-file-card" id="dzCard'+idx+'">' +
      '<i class="fas '+icon+' dz-icon"></i>' +
      '<div class="dz-name">'+file.name+'</div>' +
      '<div class="dz-size">'+formatSize(file.size)+'</div>' +
      '<div class="dz-progress-bar"><div class="dz-progress-fill" id="dzFill'+idx+'"></div></div>' +
      '<button class="dz-remove-btn" title="Remove" data-idx="'+idx+'"><i class="fas fa-times"></i></button>' +
    '</div>');
    $previews.append(card);
  }

  function updatePreviews() {
    $previews.empty();
    files.forEach(function(f, i) { addFileCard(f, i); });
  }

  /* --- Drag & drop on zone --- */
  $zone.on('dragover', function(e) {
    e.preventDefault(); e.stopPropagation();
    $zone.addClass('dz-drag-hover');
  }).on('dragleave drop', function(e) {
    e.preventDefault(); e.stopPropagation();
    $zone.removeClass('dz-drag-hover');
    if (e.type === 'drop') {
      var dropped = Array.from(e.originalEvent.dataTransfer.files);
      files = files.concat(dropped);
      updatePreviews();
    }
  });

  /* --- Click to browse --- */
  $zone.on('click', function() { $input.click(); });
  $input.on('change', function() {
    files = files.concat(Array.from(this.files));
    updatePreviews();
    this.value = '';
  });

  /* --- Remove file card --- */
  $previews.on('click', '.dz-remove-btn', function(e) {
    e.stopPropagation();
    var idx = parseInt($(this).data('idx'));
    files.splice(idx, 1);
    updatePreviews();
  });

  /* --- Upload --- */
  $('#oxUploadBtn').on('click', function() {
    var title    = $('#dz_title').val().trim();
    var category = $('#dz_category').val();
    var $err     = $('#oxMetaError');
    $err.hide();

    if (!title) { $err.text('Please enter a document title.').show(); return; }
    if (!category) { $err.text('Please select a category.').show(); return; }
    if (!files.length) { $err.text('Please add at least one file.').show(); return; }

    var total   = files.length;
    var done    = 0;
    var $btn    = $(this).addClass('ox-loading').prop('disabled', true);
    var $status = $('#oxUploadStatus').show();
    var $fill   = $('#oxUploadFill');
    var $label  = $('#oxUploadLabel');

    function uploadNext(idx) {
      if (idx >= total) {
        $btn.removeClass('ox-loading').prop('disabled', false);
        $status.hide();
        if (uploaded.length) {
          files = [];
          $previews.empty();
          renderUploaded();
          $('#oxUploadedDocs').show();
          Toast.success(uploaded.length + ' document' + (uploaded.length > 1 ? 's' : '') + ' uploaded!');
        }
        return;
      }

      var file     = files[idx];
      var fileTitle = total > 1 ? title + ' (' + (idx + 1) + ')' : title;
      $label.text('Uploading ' + (idx+1) + ' of ' + total + ': ' + file.name);

      var fd = new FormData();
      fd.append('file',            file);
      fd.append('title',           fileTitle);
      fd.append('category',        category);
      fd.append('case_id',         $('#dz_case_id').val() || '');
      fd.append('client_id',       $('#dz_client_id').val() || '');
      fd.append('description',     $('#dz_description').val() || '');
      fd.append('is_confidential', $('#dz_confidential').is(':checked') ? '1' : '0');

      var xhr = new XMLHttpRequest();
      xhr.open('POST', ONYX_CONFIG.api + '/documents/upload');
      xhr.setRequestHeader('X-CSRF-TOKEN', ONYX_CONFIG.token);
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
          var pct = Math.round((e.loaded / e.total) * 100);
          var overall = Math.round(((idx + pct/100) / total) * 100);
          $fill.css('width', overall + '%');
          $('#dzFill'+idx).css('width', pct+'%');
        }
      };
      xhr.onload = function() {
        done++;
        if (xhr.status === 201) {
          var res = JSON.parse(xhr.responseText);
          uploaded.push(res);
          $('#dzCard'+idx).addClass('dz-success');
        } else {
          $('#dzCard'+idx).addClass('dz-error');
          Toast.error('Failed: ' + file.name);
        }
        uploadNext(idx + 1);
      };
      xhr.onerror = function() {
        $('#dzCard'+idx).addClass('dz-error');
        Toast.error('Error uploading: ' + file.name);
        uploadNext(idx + 1);
      };
      xhr.send(fd);
    }

    uploadNext(0);
  });

  function renderUploaded() {
    var $tbody = $('#oxUploadedTable tbody').empty();
    uploaded.forEach(function(doc) {
      $tbody.append('<tr>' +
        '<td><span class="case-number-badge" style="font-size:.65rem;">'+doc.doc_number+'</span></td>' +
        '<td style="font-weight:600;">'+doc.title+'</td>' +
        '<td style="font-size:.75rem;color:var(--mt);">'+doc.category+'</td>' +
        '<td style="font-size:.75rem;">'+doc.file_size+'</td>' +
        '<td>' +
          '<div class="ad-table-actions">' +
            '<a href="'+doc.download_url+'" class="btn-ad btn-ad-ghost btn-ad-icon" title="Download"><i class="fas fa-download"></i></a>' +
            '<a href="'+doc.show_url+'" class="btn-ad btn-ad-ghost btn-ad-icon" title="View"><i class="fas fa-eye"></i></a>' +
          '</div>' +
        '</td>' +
      '</tr>');
    });
  }

  window.resetDropzone = function() {
    uploaded = [];
    $('#oxUploadedDocs').hide();
    $('#oxUploadedTable tbody').empty();
    $('#dz_title').val('');
    $('#dz_category').val('');
    $('#dz_description').val('');
    $('#dz_confidential').prop('checked', false);
    $('#oxUploadFill').css('width','0%');
  };

  /* Init Select2 */
  $('.ox-select2').each(function() {
    $(this).select2({
      theme: 'default',
      allowClear: true,
      width: '100%',
      placeholder: $(this).data('placeholder') || '— Select —',
    });
  });

})(jQuery);
</script>
@endpush
