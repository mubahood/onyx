@php
  $subject = "Case Closed — {$case->case_number}";
  $outcomeLabel = match($case->score) { 1 => 'WON', -1 => 'LOST', default => 'NEUTRAL' };
  $outcomeColor = match($case->score) { 1 => '#16A34A', -1 => '#DC2626', default => '#9A8A7A' };
  $outcomeBg    = match($case->score) { 1 => '#F0FDF4', -1 => '#FEF2F2', default => '#F7F3EE' };
@endphp
@include('emails._header')

  <p style="font-size:15px;color:#1A0F07;font-weight:700;margin:0 0 6px;">Hello, {{ $officer->name }},</p>
  <p style="font-size:14px;color:#5A4A3A;line-height:1.7;margin:0 0 24px;">
    The following case has been <strong>closed</strong> and the outcome has been recorded.
  </p>

  {{-- Outcome banner --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
         style="background:{{ $outcomeBg }};border:2px solid {{ $outcomeColor }};border-radius:8px;margin-bottom:20px;">
    <tr>
      <td style="padding:16px 24px;text-align:center;">
        <p style="font-size:11px;font-weight:700;color:{{ $outcomeColor }};text-transform:uppercase;letter-spacing:0.2em;margin:0 0 4px;">Case Outcome</p>
        <p style="font-size:28px;font-weight:900;color:{{ $outcomeColor }};margin:0;letter-spacing:0.06em;">{{ $outcomeLabel }}</p>
      </td>
    </tr>
  </table>

  {{-- Case summary --}}
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
         style="background:#FDF9F5;border:1.5px solid #E8DDD4;border-radius:8px;margin-bottom:20px;">
    <tr>
      <td style="padding:20px 24px;">
        <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.14em;margin:0 0 4px;">
          {{ $case->case_number }}
        </p>
        <p style="font-size:15px;font-weight:700;color:#1A0F07;margin:0 0 16px;">{{ $case->title }}</p>

        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
          <tr>
            <td width="50%" style="padding-bottom:10px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;">Client</p>
              <p style="font-size:13px;color:#2A1A0A;font-weight:600;margin:0;">{{ $case->client?->full_name ?? '—' }}</p>
            </td>
            <td width="50%" style="padding-bottom:10px;vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;">Closed Date</p>
              <p style="font-size:13px;color:#2A1A0A;font-weight:600;margin:0;">{{ $case->closed_date?->format('d M Y') ?? now()->format('d M Y') }}</p>
            </td>
          </tr>
          <tr>
            <td width="50%" style="vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;">Category</p>
              <p style="font-size:13px;color:#2A1A0A;font-weight:600;margin:0;">{{ \App\Models\LegalCase::categoryLabel($case->category) }}</p>
            </td>
            <td width="50%" style="vertical-align:top;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;">Filing Date</p>
              <p style="font-size:13px;color:#2A1A0A;font-weight:600;margin:0;">{{ $case->filing_date->format('d M Y') }}</p>
            </td>
          </tr>
        </table>

        @if($case->closing_remarks)
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
               style="margin-top:14px;border-top:1px solid #E8DDD4;">
          <tr>
            <td style="padding-top:14px;">
              <p style="font-size:10px;font-weight:700;color:#9A8A7A;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 5px;">Closing Remarks</p>
              <p style="font-size:13px;color:#5A4A3A;line-height:1.6;margin:0;font-style:italic;">
                &ldquo;{{ $case->closing_remarks }}&rdquo;
              </p>
            </td>
          </tr>
        </table>
        @endif
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
          View Full Case Record
        </a>
      </td>
    </tr>
  </table>

@include('emails._footer')
