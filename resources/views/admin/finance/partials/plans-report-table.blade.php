@php $money = fn($amount) => 'Rs ' . number_format((float) $amount, 2); @endphp
<table class="table table-striped datatable">
    <thead>
        <tr>
            <th>Type</th>
            <th>Title</th>
            <th>Ledger</th>
            <th>User / Role</th>
            <th>Bank</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Step</th>
            <th>Last Edit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $item)
        @php
            $row = $item['row'];
            $isExpense = $item['type'] === 'Expense';
            $amount = $isExpense ? ($row->net_amount ?: $row->planned_amount) : $row->expected_amount;
            $date = $isExpense ? $row->expense_month : $row->expected_date;
        @endphp
        <tr>
            <td><span class="badge badge-{{ $isExpense ? 'warning' : 'success' }}">{{ $item['type'] }}</span></td>
            <td><strong>{{ $row->title }}</strong><div class="text-muted small">{{ $isExpense ? $row->invoice_no : $row->receipt_no }}</div></td>
            <td>{{ $row->ledger?->name ?? '-' }}</td>
            <td>{{ $row->creator?->name ?? '-' }}<div class="text-muted small">{{ $row->creator?->roles?->pluck('name')->implode(', ') ?: 'No role' }}</div></td>
            <td>{{ $row->bankAccount?->name ?? '-' }}</td>
            <td>{{ $date?->format('d M Y') ?: '-' }}</td>
            <td>{{ $money($amount) }}</td>
            <td><span class="badge badge-light">{{ ucfirst($row->status) }}</span></td>
            <td>@include('admin.finance.partials.process-bot', ['status' => $row->status, 'type' => $isExpense ? 'expense' : 'cashflow'])</td>
            <td>{{ $row->updated_at?->format('d M Y H:i') }}<div class="text-muted small">{{ $row->editor?->name ?: 'System' }}</div></td>
        </tr>
        @endforeach
    </tbody>
</table>
