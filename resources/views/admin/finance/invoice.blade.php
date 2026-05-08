<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $expense->invoice_no }} - Invoice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <style>
        body { background:#f8fafc; color:#111827; font-family: Arial, sans-serif; }
        .sheet { max-width: 920px; margin: 30px auto; background:white; padding: 36px; border:1px solid #e5e7eb; }
        .brand { font-size: 24px; font-weight: 700; }
        .muted { color:#64748b; }
        @media print { body { background:white; } .sheet { margin:0; border:0; max-width:none; } .no-print { display:none; } }
    </style>
</head>
<body>
@php $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2); @endphp
<div class="sheet">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="brand">{{ config('app.name', 'Expense System') }}</div>
            <div class="muted">Expense voucher / invoice approval document</div>
        </div>
        <div class="text-right">
            <h2 class="mb-1">INVOICE</h2>
            <div>{{ $expense->invoice_no }}</div>
            <div class="muted">Status: {{ strtoupper($expense->status) }}</div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6>Payable To</h6>
            <strong>{{ $expense->vendor_name ?: $expense->ledger?->name }}</strong>
            <div class="muted">{{ $expense->vendor_gstin ? 'GSTIN: '.$expense->vendor_gstin : 'Ledger: '.$expense->ledger?->name }}</div>
            <div class="muted">{{ ucfirst($expense->ledger?->type ?? 'expense') }}</div>
        </div>
        <div class="col-6 text-right">
            <h6>Schedule</h6>
            <div>Month: {{ $expense->expense_month ?: '-' }}</div>
            <div>Due Date: {{ $expense->due_date?->format('d M Y') ?: '-' }}</div>
            <div>Terms: {{ $expense->payment_terms ?: '-' }}</div>
        </div>
    </div>

    <table class="table table-bordered">
        <thead><tr><th>Description</th><th>Category</th><th class="text-right">Amount</th></tr></thead>
        <tbody>
            <tr><td>{{ $expense->title }}<div class="muted small">{{ $expense->notes }}</div></td><td>{{ $expense->category ?: '-' }}</td><td class="text-right">{{ $money($expense->planned_amount) }}</td></tr>
            <tr><td colspan="2" class="text-right">Tax</td><td class="text-right">{{ $money($expense->tax_amount) }}</td></tr>
            <tr><td colspan="2" class="text-right">Discount</td><td class="text-right">- {{ $money($expense->discount_amount) }}</td></tr>
            <tr><th colspan="2" class="text-right">Net Payable</th><th class="text-right">{{ $money($expense->net_amount ?: $expense->planned_amount) }}</th></tr>
            <tr><td colspan="2" class="text-right">Paid</td><td class="text-right">{{ $money($expense->paid_amount) }}</td></tr>
            <tr><th colspan="2" class="text-right">Balance</th><th class="text-right">{{ $money($expense->remaining_amount) }}</th></tr>
        </tbody>
    </table>

    <h6 class="mt-4">Payment History</h6>
    <table class="table table-sm">
        <thead><tr><th>Date</th><th>Bank</th><th>Reference</th><th>Status</th><th class="text-right">Amount</th></tr></thead>
        <tbody>
        @forelse($expense->payments as $payment)
            <tr><td>{{ $payment->payment_date?->format('d M Y') }}</td><td>{{ $payment->bankAccount?->name }}</td><td>{{ $payment->reference_no ?: '-' }}</td><td>{{ ucfirst($payment->status) }}</td><td class="text-right">{{ $money($payment->amount) }}</td></tr>
        @empty
            <tr><td colspan="5" class="text-center muted">No payments recorded.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="row mt-5">
        <div class="col-6 muted">Generated on {{ now()->format('d M Y, h:i A') }}</div>
        <div class="col-6 text-right">Approved by: {{ $expense->approver?->name ?? '-' }}</div>
    </div>

    <div class="text-right mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print mr-1"></i> Print / Save PDF</button>
    </div>
</div>
</body>
</html>
