{{--
    Finance Modals Partial
    ─ Ledger · Bank Account · Cashflow · Expense
    ─ Manual Bank Entry
--}}

{{-- ══════════════════════════════════════════════════════════════
     1. LEDGER MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade fin-modal" id="ledgerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.finance.ledgers.store') }}">@csrf
            <div class="modal-header">
                <h5><i class="fas fa-book mr-2"></i> Create Ledger</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Ledger Name *</label>
                        <input name="name" class="form-control" required placeholder="e.g. Raju Kumar, Rent - Office, GST Payable">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Short Code</label>
                        <input name="code" class="form-control" placeholder="e.g. SAL001">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Type *</label>
                        <select name="type" class="form-control" required>
                            <option value="salary">Salary (Employee)</option>
                            <option value="expense">Expense</option>
                            <option value="vendor">Vendor / Supplier</option>
                            <option value="income">Income</option>
                            <option value="customer">Customer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Default / Monthly Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="font-size:.8rem;">Rs</span></div>
                            <input name="default_amount" type="number" step="0.01" min="0" class="form-control" value="0" placeholder="0.00">
                        </div>
                        <small class="text-muted" style="font-size:.72rem;">Auto-fill when selected in expense form</small>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Phone</label>
                        <input name="phone" class="form-control" placeholder="Contact number">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12 form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Optional notes about this ledger"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius:8px;font-weight:600;"><i class="fas fa-save mr-1"></i> Save Ledger</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     2. BANK ACCOUNT MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade fin-modal" id="bankModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.finance.bank-accounts.store') }}">@csrf
            <div class="modal-header">
                <h5><i class="fas fa-building-columns mr-2"></i> Add Bank / Cash Account</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Account Name *</label>
                        <input name="name" class="form-control" required placeholder="e.g. HDFC Current, Office Cash">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Account Type *</label>
                        <select name="type" class="form-control" required>
                            <option value="bank">Bank Account</option>
                            <option value="cash">Cash / Petty Cash</option>
                            <option value="wallet">Digital Wallet</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Bank Name</label>
                        <input name="bank_name" class="form-control" placeholder="e.g. HDFC Bank, SBI">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Account Number</label>
                        <input name="account_number" class="form-control" placeholder="Last 4 digits or full number">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Opening Balance *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="font-size:.8rem;">Rs</span></div>
                            <input name="opening_balance" type="number" step="0.01" min="0" class="form-control live-bank-balance" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Opening Balance Date</label>
                        <input name="opening_balance_date" type="date" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Notes</label>
                        <input name="notes" class="form-control" placeholder="Optional">
                    </div>
                </div>
                <div class="net-calc-box">
                    <span class="net-label"><i class="fas fa-wallet mr-2"></i>Opening Balance</span>
                    <span class="net-value bank-preview-amount">Rs 0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" style="border-radius:8px;font-weight:600;"><i class="fas fa-save mr-1"></i> Save Account</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     3. CASHFLOW (INFLOW) PLAN MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade fin-modal" id="cashflowModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.finance.cashflows.store') }}">@csrf
            <div class="modal-header" style="background:linear-gradient(135deg,#064e3b,#059669);">
                <h5><i class="fas fa-arrow-trend-up mr-2"></i> Plan Cash Inflow</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Source Ledger</label>
                        <select name="ledger_id" class="form-control">
                            <option value="">— Direct / Other —</option>
                            @foreach(($ledgers ?? $incomeLedgers ?? []) as $ledger)
                            <option value="{{ $ledger->id }}">{{ $ledger->name }} ({{ ucfirst($ledger->type) }})</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-toggle="modal" data-target="#ledgerModal">
                            <i class="fas fa-plus"></i> New Ledger
                        </button>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Title / Description *</label>
                        <input name="title" class="form-control" required placeholder="e.g. Client payment - Project Alpha Invoice #12">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Payer Name</label>
                        <input name="payer_name" class="form-control" placeholder="Who is paying?">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Destination Bank Account *</label>
                        <select name="bank_account_id" class="form-control" required>
                            <option value="">— Select Bank —</option>
                            @foreach($bankAccounts ?? [] as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} · Rs {{ number_format((float)$account->current_balance, 0) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Expected Amount *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="font-size:.8rem;">Rs</span></div>
                            <input name="expected_amount" type="number" min="1" step="0.01" class="form-control cf-amount-input" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Expected Date *</label>
                        <input name="expected_date" type="date" class="form-control" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Reference No.</label>
                        <input name="reference_no" class="form-control" placeholder="Invoice / cheque no.">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="submitted">Submit for Approval</option>
                            <option value="draft">Save as Draft</option>
                        </select>
                    </div>
                    <div class="col-md-8 form-group">
                        <label>Attachment <span style="color:#94a3b8;font-weight:400;text-transform:none;">(jpg, png, pdf)</span></label>
                        <input name="attachment" type="file" class="form-control attachment-input" accept=".jpg,.jpeg,.png,.pdf,.webp">
                        <div class="attachment-preview mt-2"></div>
                    </div>
                    <div class="col-12 form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional remarks"></textarea>
                    </div>
                </div>
                <div class="net-calc-box" id="cfPreview" style="display:none;">
                    <span class="net-label"><i class="fas fa-arrow-trend-up mr-2"></i>Inflow Amount</span>
                    <span class="net-value cf-amount-preview">Rs 0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" style="border-radius:8px;font-weight:600;"><i class="fas fa-paper-plane mr-1"></i> Save Cashflow Plan</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     4. EXPENSE PLAN MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade fin-modal" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.finance.expenses.store') }}">@csrf
            <div class="modal-header" style="background:linear-gradient(135deg,#78350f,#d97706);">
                <h5><i class="fas fa-receipt mr-2"></i> Create Expense / Salary Plan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                {{-- Section: Basic Info --}}
                <div class="section-divider">Basic Information</div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Ledger / Employee *</label>
                        <select name="ledger_id" class="form-control ledger-amount-source" required>
                            <option value="">— Select Ledger —</option>
                            @foreach($expenseLedgers ?? [] as $ledger)
                            <option value="{{ $ledger->id }}" data-amount="{{ $ledger->default_amount }}">
                                {{ $ledger->name }} · {{ ucfirst($ledger->type) }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-toggle="modal" data-target="#ledgerModal">
                            <i class="fas fa-plus"></i> New Ledger
                        </button>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Title / Description *</label>
                        <input name="title" class="form-control" required placeholder="e.g. April Salary - Raju Kumar, Office Rent May">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Vendor / Employee Name</label>
                        <input name="vendor_name" class="form-control" placeholder="Party name (if different from ledger)">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Priority *</label>
                        <select name="priority" class="form-control" required>
                            <option value="urgent">🔴 Urgent</option>
                            <option value="high">🟠 High</option>
                            <option value="normal" selected>🔵 Normal</option>
                            <option value="low">⚪ Low</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="submitted">Submit for Approval</option>
                            <option value="draft">Save as Draft</option>
                        </select>
                    </div>
                </div>

                {{-- Section: Amount Calculation --}}
                <div class="section-divider">Amount</div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Base / Salary Amount *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="font-size:.8rem;">Rs</span></div>
                            <input name="planned_amount" type="number" min="1" step="0.01" class="form-control planned-amount calc-net" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Tax / GST Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="font-size:.8rem;">Rs</span></div>
                            <input name="tax_amount" type="number" min="0" step="0.01" class="form-control calc-net" value="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Discount</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="font-size:.8rem;">Rs</span></div>
                            <input name="discount_amount" type="number" min="0" step="0.01" class="form-control calc-net" value="0" placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="net-calc-box mb-3">
                    <div>
                        <div class="net-label">Net Payable = Base + Tax − Discount</div>
                    </div>
                    <span class="net-value net-preview" id="expNetPreview">Rs 0.00</span>
                </div>

                {{-- Section: Schedule --}}
                <div class="section-divider">Schedule &amp; Bank</div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Expense Date</label>
                        <input name="expense_month" type="date" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Due Date</label>
                        <input name="due_date" type="date" class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Preferred Bank Account</label>
                        <select name="bank_account_id" class="form-control">
                            <option value="">— Decide while paying —</option>
                            @foreach($bankAccounts ?? [] as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} · Rs {{ number_format((float)$account->current_balance, 0) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Section: Attachment & Notes --}}
                <div class="section-divider">Attachment &amp; Notes</div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Attachment <span style="color:#94a3b8;font-weight:400;text-transform:none;">(invoice, receipt, etc.)</span></label>
                        <input name="attachment" type="file" class="form-control attachment-input" accept=".jpg,.jpeg,.png,.pdf,.webp">
                        <div class="attachment-preview mt-2"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Payment terms, remarks, breakdown..."></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning" style="border-radius:8px;font-weight:600;color:#fff;"><i class="fas fa-save mr-1"></i> Save Expense Plan</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     5. MANUAL BANK ENTRY MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade fin-modal" id="manualEntryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.finance.bank-accounts.manual-entry') }}">@csrf
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3a5f,#2563eb);">
                <h5><i class="fas fa-pen-to-square mr-2"></i> Manual Bank Entry</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="balance-alert">
                    <i class="fas fa-info-circle mr-1"></i>
                    Use this for <strong>bank charges, corrections, interest credits</strong>, or any entry not tied to a cashflow or expense plan.
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Bank Account *</label>
                        <select name="bank_account_id" class="form-control" required id="manualBankSelect">
                            <option value="">— Select Account —</option>
                            @foreach($bankAccounts ?? [] as $account)
                            <option value="{{ $account->id }}" data-balance="{{ $account->current_balance }}">
                                {{ $account->name }} · Rs {{ number_format((float)$account->current_balance, 2) }}
                            </option>
                            @endforeach
                        </select>
                        <div id="manualBankBalance" style="font-size:.75rem;color:var(--fc-success);margin-top:4px;display:none;">
                            Current balance: <strong id="manualBankBalVal"></strong>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Entry Type *</label>
                        <select name="direction" class="form-control" required>
                            <option value="debit">Debit (Money going out)</option>
                            <option value="credit">Credit (Money coming in)</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Amount *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text" style="font-size:.8rem;">Rs</span></div>
                            <input name="amount" type="number" min="0.01" step="0.01" class="form-control" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Transaction Date *</label>
                        <input name="transaction_date" type="date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Category</label>
                        <input name="category" class="form-control" placeholder="Bank charges, Interest...">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Party Name</label>
                        <input name="party_name" class="form-control" placeholder="Bank or counterparty name">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Reference No.</label>
                        <input name="reference_no" class="form-control" placeholder="Cheque / UTR / TXN no.">
                    </div>
                    <div class="col-12 form-group">
                        <label>Description *</label>
                        <input name="description" class="form-control" required placeholder="e.g. Bank service charge Q1, Inward interest credit">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius:8px;font-weight:600;"><i class="fas fa-paper-plane mr-1"></i> Post Entry</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     SHARED JAVASCRIPT
══════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
// ── Utility ────────────────────────────────────────────────────
function fmtMoney(v) {
    return 'Rs ' + Number(v || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

$(document).on('show.bs.modal', '.fin-modal', function() {
    const zIndex = 1050 + (10 * $('.modal.show').length);
    $(this).css('z-index', zIndex);
    setTimeout(() => {
        $('.modal-backdrop').not('.modal-stack').last().css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$(document).on('hidden.bs.modal', '.fin-modal', function() {
    if ($('.modal.show').length) {
        $('body').addClass('modal-open');
    }
});

// ── Expense Net Calculator ─────────────────────────────────────
document.querySelectorAll('.calc-net').forEach(input => {
    input.addEventListener('input', () => {
        const form = input.closest('form');
        const base     = parseFloat(form.querySelector('[name="planned_amount"]')?.value || 0) || 0;
        const tax      = parseFloat(form.querySelector('[name="tax_amount"]')?.value  || 0) || 0;
        const discount = parseFloat(form.querySelector('[name="discount_amount"]')?.value || 0) || 0;
        const net      = Math.max(0, base + tax - discount);
        const preview  = form.querySelector('#expNetPreview, .net-preview');
        if (preview) preview.textContent = fmtMoney(net);
    });
});

// ── Ledger Default Amount Auto-Fill ───────────────────────────
document.querySelectorAll('.ledger-amount-source').forEach(select => {
    select.addEventListener('change', () => {
        const amt    = select.selectedOptions[0]?.dataset.amount;
        const target = select.closest('form').querySelector('.planned-amount');
        if (amt && parseFloat(amt) > 0 && target && !target.value) {
            target.value = parseFloat(amt).toFixed(2);
            target.dispatchEvent(new Event('input'));
        }
    });
});

// ── Cashflow Amount Preview ────────────────────────────────────
document.querySelectorAll('.cf-amount-input').forEach(input => {
    input.addEventListener('input', () => {
        const form    = input.closest('form');
        const preview = form.querySelector('.cf-amount-preview');
        const box     = form.querySelector('#cfPreview');
        if (preview) preview.textContent = fmtMoney(input.value);
        if (box)     box.style.display = input.value > 0 ? 'flex' : 'none';
    });
});

// ── Bank Account Opening Balance Preview ───────────────────────
document.querySelectorAll('.live-bank-balance').forEach(input => {
    input.addEventListener('input', () => {
        const form    = input.closest('form');
        const preview = form.querySelector('.bank-preview-amount');
        if (preview) preview.textContent = fmtMoney(input.value);
    });
});

// ── Manual Entry Bank Balance Display ─────────────────────────
const manualBankSel = document.getElementById('manualBankSelect');
if (manualBankSel) {
    manualBankSel.addEventListener('change', function() {
        const opt     = this.selectedOptions[0];
        const balDiv  = document.getElementById('manualBankBalance');
        const balVal  = document.getElementById('manualBankBalVal');
        if (opt && opt.dataset.balance) {
            balDiv.style.display = 'block';
            balVal.textContent   = fmtMoney(opt.dataset.balance);
        } else {
            balDiv.style.display = 'none';
        }
    });
}

// ── Attachment Preview ─────────────────────────────────────────
document.querySelectorAll('.attachment-input').forEach(input => {
    input.addEventListener('change', () => {
        const preview = input.closest('.form-group').querySelector('.attachment-preview');
        if (!preview) return;
        preview.innerHTML = '';
        const file = input.files[0];
        if (!file) return;

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'attachment-thumb';
            img.style.maxHeight = '80px';
            preview.appendChild(img);
        } else {
            const size = file.size > 1024 * 1024
                ? (file.size / 1024 / 1024).toFixed(1) + ' MB'
                : Math.round(file.size / 1024) + ' KB';
            preview.innerHTML = `<span class="file-badge"><i class="far fa-file-pdf"></i>${file.name} <span style="color:#94a3b8;margin-left:4px;">${size}</span></span>`;
        }
    });
});
</script>
@endpush

