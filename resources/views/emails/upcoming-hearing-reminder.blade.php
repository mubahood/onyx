@php
  $subject = "Hearing Reminder — {$case->case_number}";
  $urgencyColor = match(true) {
      $daysUntil === 0 => '#DC2626',
      $daysUntil === 1 => '#D97706',
      $daysUntil <= 3  => '#D97706',
      default          => '#5D3A1A',
  };
  $urgencyBg = match(true) {
      $daysUntil === 0 => '#FEF2F2',
      $daysUntil <= 3  => '#FFFBEB',
      default          => '#FDF9F5',
  };
  $whenText = match(true) {
      $daysUntil === 0 => 'TODAY',
      $daysUntil === 1 => 'TOMORROW',
      default          => "IN {$daysUntil} DAYS",
  };
@endphp
@include('emails._header')

  <p style="font-size:15px;color:#1A0F07;font-weight:700;margin:0 0 6px;">Hello, {{ $officer->name }},</p>
  <p style="font-size:14px;color:#5A4A3A;line-height:1.7;margin:0 0 20px;">
    This is a reminder that you have a <strong>court hearing scheduled {{ strtolower($whenText) }}</strong>
    on the following case. Please ensure all preparations are in order.
  </p>

  {{-- Urgency banner --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
         style="background:{{ $urgencyBg }};border:2px solid {{ $urgencyColor }};border-radius:8px;margin-bottom:20px;">
    <tr>
      <td style="padding:14px 24px;text-align:center;">
        <p style="font-size:11px;font-weight:700;color:{{ $urgencyColor }};text-transform:uppercase;letter-spacing:0.2em;margin:0 0 4px;">Hearing</p>
        <p style="font-size:26px;font-weight:900;color:{{ $urgencyColor }};margin:0;letter-spacing:0.06em;">{{ $whenText }}</p>
        <p style="font-size:14px;font-weight:700;color:{{ $urgencyColor }};margin:4px 0 0;">
          {{ $case->next_hearing_date->format('l, d F Y') }}
        </p>
      </td>
    </tr>
  </table>

  {{-- Case + Court info --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
         style="background:#FDF9F5;border:1.5px solid #E8DDD4;border-radius:8px;margin-bottom:20px;">
    <tr>
      <td style="background:linear-gradient(135deg,#5D3A1A,#3A2010);border-radius:7px 7px 0 0;padding:14px 24px;">
        <p style="font-size:11px;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:0.14em;margin:0 0 2px;">Case</p>
        <p style="font-size:18px;font-weight:800;color:#FFFFFF;margin:0;">
          {{ $case->case_number }} &mdash; {{ $case->title }}
        </p>
      </td>
    </tr>
    <tr>
      <td style="padding:18px 24px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
          <tr>
            <td width="50%" style="padding-bottom:12px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Client</p>
              <p style="font-size:13px;font-weight:600;color:#1A0F07;margin:0;">{{ $case->client?->full_name ?? '—' }}</p>
            </td>
            <td width="50%" style="padding-bottom:12px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Category</p>
              <p style="font-size:13px;font-weight:600;color:#1A0F07;margin:0;">{{ \App\Models\LegalCase::categoryLabel($case->category) }}</p>
            </td>
          </tr>
          <tr>
            <td width="50%" style="padding-bottom:12px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Court</p>
              <p style="font-size:13px;font-weight:600;color:#1A0F07;margin:0;">{{ $case->court_name ?? '—' }}</p>
            </td>
            <td width="50%" style="padding-bottom:12px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Division</p>
              <p style="font-size:13px;font-weight:600;color:#1A0F07;margin:0;">{{ $case->court_division ?? '—' }}</p>
            </td>
          </tr>
          @if($case->court_case_number)
          <tr>
            <td width="50%" style="vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Court File No.</p>
              <p style="font-size:13px;font-weight:600;color:#1A0F07;margin:0;font-family:'Courier New',monospace;">{{ $case->court_case_number }}</p>
            </td>
            @if($case->judge_name)
            <td width="50%" style="vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 3px;">Before</p>
              <p style="font-size:13px;font-weight:600;color:#1A0F07;margin:0;">{{ $case->judge_name }}</p>
            </td>
            @endif
          </tr>
          @endif
        </table>
      </td>
    </tr>
  </table>

  {{-- CTA --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:16px;">
    <tr>
      <td align="center">
        <a href="{{ route('admin.cases.show', $case) }}"
           style="display:inline-block;background:#5D3A1A;color:#ffffff;font-size:14px;font-weight:700;
                  letter-spacing:0.04em;padding:13px 36px;border-radius:7px;text-decoration:none;">
          Open Case &amp; Prepare
        </a>
      </td>
    </tr>
  </table>

  <p style="font-size:11px;color:#9A8A7A;line-height:1.6;margin:0;text-align:center;">
    You are receiving this reminder because you are the lead officer on this case.<br>
    Reminders are sent 7, 3, 1 days before and on the day of the hearing.
  </p>

@include('emails._footer')
