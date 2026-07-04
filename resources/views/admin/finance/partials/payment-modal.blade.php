{{-- Payment Modal — partial included per expense --}}
<div class="modal fade fin-modal" id="paymentModal{{ $expense->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" enctype="multipart/form-data"
              action="{{ route('admin.finance.expenses.payments.store', $expense) }}">
            @csrf
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3a5f,#2563eb);">
                <h5><i class="fas fa-money-bill-wave mr-2"></i> Record Payment — {{ Str::limit($expense->title, 40) }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                {{-- Balance Summary --}}
                <div class="balance-alert">
                    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                        <span><i class="fas fa-file-invoice mr-1"></i> Net Payable:
                            <strong>Rs {{ number_format((float)($expense->net_amount ?: $expense->planned_amount), 2) }}</strong>
                        </span>
                        <span><i class="fas fa-check-circle mr-1" style="color:#059669;"></i> Paid:
                            <strong style="color:#059669;">Rs {{ number_format((float)$expense->paid_amount, 2) }}</strong>
                        </span>
                        <span><i class="fas fa-hourglass-half mr-1" style="color:#dc2626;"></i> Remaining:
                            <strong style="color:#dc2626;">Rs {{ number_format((float)$expense->remaining_amount, 2) }}</strong>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Pay From (Bank Account) *</label>
                        <select name="bank_account_id" class="form-control pmt-bank-select" required
                                data-remaining="{{ $expense->remaining_amount }}"
                                data-modal="{{ $expense->id }}">
                            @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}"
                                    data-balance="{{ $account->current_balance }}"
                                    class="{{ $account->current_balance < $expense->remaining_amount ? 'text-danger' : '' }}">
                                {{ $account->name }} · Rs {{ number_format((float)$account->current_balance, 2) }}
                                {{ $account->current_balance < $expense->remaining_amount ? '(Insufficient for full)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pmt-balance-info-{{ $expense->id }}" style="font-size:.75rem;margin-top:4px;display:none;"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Payment Date *</label>
                        <input name="payment_date" type="date" class="form-control"
                               value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Amount *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="font-size:.8rem;">Rs</span></div>
                            <input name="amount" type="number" min="1"
                                   max="{{ $expense->remaining_amount }}"
                                   step="0.01" class="form-control pmt-amount-input"
                                   value="{{ $expense->remaining_amount }}" required>
                        </div>
                        <small class="text-muted" style="font-size:.72rem;">
                            Max: Rs {{ number_format((float)$expense->remaining_amount, 2) }}
                            (partial payment allowed)
                        </small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Reference No. / UTR</label>
                        <input name="reference_no" class="form-control" placeholder="Cheque / NEFT / UPI ref">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Attachment <span style="color:#94a3b8;font-weight:400;text-transform:none;">(receipt / screenshot)</span></label>
                        <input name="attachment" type="file" class="form-control attachment-input" accept=".jpg,.jpeg,.png,.pdf,.webp">
                        <div class="attachment-preview mt-2"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any remarks about this payment"></textarea>
                    </div>
                </div>
                <input type="hidden" name="status" value="submitted">

                <div class="net-calc-box pmt-preview-box-{{ $expense->id }}" style="display:none;">
                    <span class="net-label"><i class="fas fa-money-bill-wave mr-2"></i>Submitting Payment</span>
                    <span class="net-value pmt-preview-val-{{ $expense->id }}">Rs 0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius:8px;font-weight:600;">
                    <i class="fas fa-paper-plane mr-1"></i> Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>

@once
@push('scripts')
<script>
// ── Payment Modal: Bank balance checker + amount preview ───────
document.querySelectorAll('.pmt-bank-select').forEach(sel => {
    const expId = sel.dataset.modal;

    function updateBalance() {
        const opt     = sel.selectedOptions[0];
        const balance = parseFloat(opt?.dataset.balance || 0);
        const remaining = parseFloat(sel.dataset.remaining || 0);
        const infoDiv = document.querySelector(`.pmt-balance-info-${expId}`);
        if (!infoDiv) return;

        infoDiv.style.display = 'block';
        if (balance >= remaining) {
            infoDiv.innerHTML = `<span style="color:#059669;"><i class="fas fa-check-circle mr-1"></i>Available: Rs ${balance.toLocaleString('en-IN', {minimumFractionDigits:2})} — sufficient for full payment</span>`;
        } else {
            infoDiv.innerHTML = `<span style="color:#d97706;"><i class="fas fa-exclamation-triangle mr-1"></i>Available: Rs ${balance.toLocaleString('en-IN', {minimumFractionDigits:2})} — only partial payment possible</span>`;
        }
    }

    sel.addEventListener('change', updateBalance);
    updateBalance();
});

document.querySelectorAll('.pmt-amount-input').forEach(input => {
    input.addEventListener('input', () => {
        const form    = input.closest('form');
        const expId   = form.querySelector('.pmt-bank-select')?.dataset.modal;
        const preview = document.querySelector(`.pmt-preview-val-${expId}`);
        const box     = document.querySelector(`.pmt-preview-box-${expId}`);
        if (preview) preview.textContent = 'Rs ' + parseFloat(input.value || 0).toLocaleString('en-IN', {minimumFractionDigits:2});
        if (box)     box.style.display = parseFloat(input.value) > 0 ? 'flex' : 'none';
    });
});
</script>
@endpush
@endonce
