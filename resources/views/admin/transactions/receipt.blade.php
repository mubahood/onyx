<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt {{ $transaction->receipt_number }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; background: #F7F2EC; display: flex; justify-content: center; padding: 40px 20px; }
    .receipt { background: #fff; width: 420px; border: 1px solid #E8DDD4; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 20px rgba(93,58,26,0.1); }
    .receipt-header { background: linear-gradient(135deg, #1C120A, #3A2010); padding: 28px 28px 24px; text-align: center; }
    .logo-mark { width: 48px; height: 48px; background: linear-gradient(135deg, #5D3A1A, #C4956A); border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-family: Georgia, serif; font-size: 1.25rem; font-weight: 900; color: #fff; margin-bottom: 12px; }
    .receipt-header h1 { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: 0.05em; }
    .receipt-header p { font-size: 0.7rem; color: rgba(255,243,230,0.55); margin-top: 3px; letter-spacing: 0.08em; text-transform: uppercase; }
    .receipt-tag { background: rgba(196,149,106,0.15); border: 1px solid rgba(196,149,106,0.3); border-radius: 20px; padding: 4px 16px; display: inline-block; font-size: 0.75rem; font-weight: 700; color: #C4956A; letter-spacing: 0.08em; margin-top: 12px; }
    .receipt-number { font-size: 1rem; font-weight: 700; color: #fff; margin-top: 8px; font-family: monospace; }
    .receipt-body { padding: 24px 28px; }
    .receipt-amount { text-align: center; padding: 20px 0; border-bottom: 1px dashed #E8DDD4; margin-bottom: 18px; }
    .receipt-amount .label { font-size: 0.7rem; color: #7A6555; text-transform: uppercase; letter-spacing: 0.08em; }
    .receipt-amount .amount { font-size: 2rem; font-weight: 700; color: #15803D; margin-top: 4px; }
    .row { display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; border-bottom: 1px solid #F7F2EC; font-size: 0.8125rem; gap: 10px; }
    .row .key { color: #7A6555; flex-shrink: 0; }
    .row .val { font-weight: 600; color: #1A0F07; text-align: right; }
    .receipt-footer { padding: 18px 28px; text-align: center; border-top: 1px dashed #E8DDD4; background: #F7F2EC; }
    .receipt-footer p { font-size: 0.7rem; color: #7A6555; line-height: 1.5; }
    .print-btn { display: block; margin: 20px auto 0; padding: 10px 28px; background: #5D3A1A; color: #fff; border: none; border-radius: 4px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; font-weight: 600; cursor: pointer; }
    @media print { .print-btn { display: none; } body { background: white; padding: 0; } }
  </style>
</head>
<body>

<div class="receipt">
  <div class="receipt-header">
    <div class="logo-mark">OL</div>
    <h1>ONYX Legal</h1>
    <p>Official Payment Receipt</p>
    <div class="receipt-tag">RECEIPT</div>
    <div class="receipt-number">{{ $transaction->receipt_number }}</div>
  </div>

  <div class="receipt-body">
    <div class="receipt-amount">
      <div class="label">Amount Received</div>
      <div class="amount">UGX {{ number_format($transaction->amount, 0) }}</div>
    </div>

    <div class="row"><span class="key">Date</span><span class="val">{{ $transaction->transaction_date->format('d F Y') }}</span></div>
    <div class="row"><span class="key">Received From</span><span class="val">{{ $transaction->client?->full_name ?? 'N/A' }}</span></div>
    @if($transaction->case)
    <div class="row"><span class="key">Case #</span><span class="val">{{ $transaction->case->case_number }}</span></div>
    @endif
    <div class="row"><span class="key">Description</span><span class="val">{{ $transaction->description }}</span></div>
    <div class="row"><span class="key">Payment Method</span><span class="val">{{ \App\Models\Transaction::methodLabel($transaction->payment_method) }}</span></div>
    @if($transaction->reference_number)
    <div class="row"><span class="key">Reference</span><span class="val">{{ $transaction->reference_number }}</span></div>
    @endif
    <div class="row"><span class="key">Account</span><span class="val">{{ $transaction->account?->name }}</span></div>
    <div class="row"><span class="key">Received By</span><span class="val">{{ $transaction->createdBy?->name }}</span></div>
    <div class="row"><span class="key">TXN #</span><span class="val" style="font-family:monospace;font-size:0.7rem;">{{ $transaction->transaction_number }}</span></div>
  </div>

  <div class="receipt-footer">
    <p>Thank you for your payment.<br>This is an official receipt from ONYX Legal.<br>Please retain for your records.</p>
  </div>
</div>

<button onclick="window.print()" class="print-btn">
  🖨️ Print / Save as PDF
</button>

</body>
</html>
