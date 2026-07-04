@php
    $status = $status ?? 'draft';
    $type = $type ?? 'expense';

    $steps = $type === 'cashflow'
        ? [
            'draft' => ['Draft saved', 'Submit the cash-in plan for approval.'],
            'submitted' => ['Next: Approval', 'Finance approval is pending.'],
            'approved' => ['Next: Receipt', 'Confirm receipt when money arrives.'],
            'received' => ['Process Done', 'Cash received and balance updated.'],
        ]
        : [
            'draft' => ['Draft saved', 'Submit the expense for approval.'],
            'submitted' => ['Next: Approval', 'Approve this expense first.'],
            'approved' => ['Next: Payment', 'Record payment from selected account.'],
            'partial' => ['Next: Payment', 'Pay the remaining balance.'],
            'paid' => ['Process Done', 'Payment posted and balance updated.'],
        ];

    [$title, $hint] = $steps[$status] ?? [ucfirst($status), 'Review this plan status.'];
    $tone = in_array($status, ['paid', 'received'], true) ? 'done' : (in_array($status, ['approved', 'partial'], true) ? 'ready' : 'wait');
@endphp

<span class="process-bot process-bot-{{ $tone }}" title="{{ $hint }}">
    <i class="fas fa-route"></i>
    <span>{{ $title }}</span>
</span>
