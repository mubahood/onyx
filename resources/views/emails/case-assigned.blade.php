@php
  $subject = "Case Assignment — {$case->case_number}";
  $roleLabel = $role === 'main' ? 'Lead Officer' : 'Team Member';
  $roleColor = $role === 'main' ? '#5D3A1A' : '#7A4E2D';
@endphp
@include('emails._header')

  <p style="font-size:15px;color:#1A0F07;font-weight:700;margin:0 0 6px;">Hello, {{ $officer->name }},</p>
  <p style="font-size:14px;color:#5A4A3A;line-height:1.7;margin:0 0 24px;">
    You have been assigned as <strong>{{ $roleLabel }}</strong> on the following case.
    Please review the details and take appropriate action.
  </p>

  {{-- Case details card --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
         style="background:#FDF9F5;border:1.5px solid #E8DDD4;border-radius:8px;margin-bottom:24px;overflow:hidden;">
    <tr>
      <td style="background:linear-gradient(135deg,#5D3A1A,#3A2010);padding:16px 24px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
          <tr>
            <td>
              <p style="font-size:11px;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:0.14em;margin:0 0 3px;">Case Number</p>
              <p style="font-size:20px;font-weight:800;color:#FFFFFF;margin:0;letter-spacing:0.04em;">{{ $case->case_number }}</p>
            </td>
            <td align="right" style="vertical-align:top;">
              <span style="display:inline-block;background:{{ $roleColor }};border:1.5px solid rgba(255,255,255,.3);
                           color:#fff;font-size:10px;font-weight:700;padding:4px 12px;border-radius:20px;
                           letter-spacing:0.1em;text-transform:uppercase;">
                {{ $roleLabel }}
              </span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="padding:20px 24px;">
        <p style="font-size:15px;font-weight:700;color:#1A0F07;margin:0 0 16px;line-height:1.4;">{{ $case->title }}</p>

        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
          <tr>
            <td width="50%" style="padding-bottom:12px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Client</p>
              <p style="font-size:13px;font-weight:600;color:#2A1A0A;margin:0;">{{ $case->client?->full_name ?? '—' }}</p>
            </td>
            <td width="50%" style="padding-bottom:12px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Category</p>
              <p style="font-size:13px;font-weight:600;color:#2A1A0A;margin:0;">{{ \App\Models\LegalCase::categoryLabel($case->category) }}</p>
            </td>
          </tr>
          <tr>
            <td width="50%" style="padding-bottom:12px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Status</p>
              <p style="font-size:13px;font-weight:600;color:#2A1A0A;margin:0;">{{ ucfirst($case->status) }}</p>
            </td>
            <td width="50%" style="padding-bottom:12px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Priority</p>
              <p style="font-size:13px;font-weight:600;color:{{ $case->priority === 'urgent' ? '#DC2626' : ($case->priority === 'high' ? '#D97706' : '#2A1A0A') }};margin:0;">
                {{ ucfirst($case->priority) }}
              </p>
            </td>
          </tr>
          <tr>
            <td width="50%" style="vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Filing Date</p>
              <p style="font-size:13px;font-weight:600;color:#2A1A0A;margin:0;">{{ $case->filing_date->format('d M Y') }}</p>
            </td>
            <td width="50%" style="vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Stage</p>
              <p style="font-size:13px;font-weight:600;color:#2A1A0A;margin:0;">{{ \App\Models\LegalCase::stageLabel($case->stage) }}</p>
            </td>
          </tr>
          @if($case->is_in_court && $case->next_hearing_date)
          <tr>
            <td colspan="2" style="padding-top:12px;border-top:1px solid #E8DDD4;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Next Hearing</p>
              <p style="font-size:13px;font-weight:700;color:#5D3A1A;margin:0;">
                {{ $case->next_hearing_date->format('d M Y') }}
                &nbsp;&mdash;&nbsp; {{ $case->court_name }}
              </p>
            </td>
          </tr>
          @endif
        </table>
      </td>
    </tr>
  </table>

  {{-- CTA --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:24px;">
    <tr>
      <td align="center">
        <a href="{{ route('admin.cases.show', $case) }}"
           style="display:inline-block;background:#5D3A1A;color:#ffffff;font-size:14px;font-weight:700;
                  letter-spacing:0.04em;padding:13px 36px;border-radius:7px;text-decoration:none;">
          View Case Details
        </a>
      </td>
    </tr>
  </table>

  <p style="font-size:12px;color:#9A8A7A;line-height:1.7;margin:0;">
    If you have any questions about this case assignment, please contact your supervisor or the administrator.
  </p>

@include('emails._footer')
