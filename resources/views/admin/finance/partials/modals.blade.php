<div class="modal fade" id="ledgerModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><form class="modal-content" method="POST" action="{{ route('admin.finance.ledgers.store') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">Create Ledger</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 form-group"><label>Name</label><input name="name" class="form-control" required></div>
                <div class="col-md-3 form-group"><label>Code</label><input name="code" class="form-control"></div>
                <div class="col-md-3 form-group"><label>Type</label><select name="type" class="form-control"><option value="salary">Salary</option><option value="expense">Expense</option><option value="income">Income</option><option value="vendor">Vendor</option><option value="customer">Customer</option><option value="other">Other</option></select></div>
                <div class="col-md-4 form-group"><label>Default Amount</label><input name="default_amount" type="number" step="0.01" class="form-control" value="0"></div>
                <div class="col-md-4 form-group"><label>Phone</label><input name="phone" class="form-control"></div>
                <div class="col-md-4 form-group"><label>Status</label><select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                <div class="col-12 form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            </div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Ledger</button></div>
    </form></div>
</div>

<div class="modal fade" id="bankModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><form class="modal-content" method="POST" action="{{ route('admin.finance.bank-accounts.store') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">Create Bank / Cash Account</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 form-group"><label>Account Name</label><input name="name" class="form-control" required></div>
                <div class="col-md-6 form-group"><label>Bank Name</label><input name="bank_name" class="form-control"></div>
                <div class="col-md-4 form-group"><label>Type</label><select name="type" class="form-control"><option value="bank">Bank</option><option value="cash">Cash</option><option value="wallet">Wallet</option></select></div>
                <div class="col-md-4 form-group"><label>Account No.</label><input name="account_number" class="form-control"></div>
                <div class="col-md-4 form-group"><label>Opening Balance</label><input name="opening_balance" type="number" step="0.01" min="0" class="form-control live-money" required></div>
                <div class="col-md-4 form-group"><label>Status</label><select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                <div class="col-md-8 form-group"><label>Notes</label><input name="notes" class="form-control"></div>
            </div>
        </div>
        <div class="modal-footer"><span class="mr-auto text-muted live-preview"></span><button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Account</button></div>
    </form></div>
</div>

<div class="modal fade" id="cashflowModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.finance.cashflows.store') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">Plan Cash Inflow</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-5 form-group"><label>Title</label><input name="title" class="form-control" required></div>
                <div class="col-md-4 form-group"><label>Source Ledger</label><select name="ledger_id" class="form-control"><option value="">Direct / Other</option>@foreach($incomeLedgers ?? [] as $ledger)<option value="{{ $ledger->id }}">{{ $ledger->name }}</option>@endforeach</select></div>
                <div class="col-md-3 form-group"><label>Payer Name</label><input name="payer_name" class="form-control"></div>
                <div class="col-md-4 form-group"><label>Bank Account</label><select name="bank_account_id" class="form-control" required>@foreach($bankAccounts ?? [] as $account)<option value="{{ $account->id }}">{{ $account->name }} ({{ $money($account->current_balance) }})</option>@endforeach</select></div>
                <div class="col-md-4 form-group"><label>Expected Amount</label><input name="expected_amount" type="number" min="1" step="0.01" class="form-control live-money" required></div>
                <div class="col-md-4 form-group"><label>Expected Date</label><input name="expected_date" type="date" class="form-control" required></div>
                <div class="col-md-4 form-group"><label>Reference No.</label><input name="reference_no" class="form-control"></div>
                <div class="col-md-4 form-group"><label>Status</label><select name="status" class="form-control"><option value="submitted">Submit for Approval</option><option value="draft">Draft</option></select></div>
                <div class="col-md-4 form-group"><label>Attachment</label><input name="attachment" type="file" class="form-control attachment-input" accept=".jpg,.jpeg,.png,.pdf,.webp"><div class="attachment-preview mt-2"></div></div>
                <div class="col-12 form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
            </div>
        </div>
        <div class="modal-footer"><span class="mr-auto text-muted live-preview"></span><button class="btn btn-success"><i class="fas fa-paper-plane mr-1"></i> Save Cashflow</button></div>
    </form></div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-xl"><form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.finance.expenses.store') }}">@csrf
        <div class="modal-header"><h5 class="modal-title">Plan Expense / Salary</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4 form-group"><label>Title</label><input name="title" class="form-control" required placeholder="May salary - Ajay"></div>
                <div class="col-md-4 form-group"><label>Ledger</label><select name="ledger_id" class="form-control ledger-amount-source" required><option value="">Select</option>@foreach($expenseLedgers ?? [] as $ledger)<option value="{{ $ledger->id }}" data-amount="{{ $ledger->default_amount }}">{{ $ledger->name }} · {{ ucfirst($ledger->type) }}</option>@endforeach</select></div>
                <div class="col-md-4 form-group"><label>Vendor / Employee</label><input name="vendor_name" class="form-control" placeholder="Party name"></div>
                <div class="col-md-3 form-group"><label>Category</label><input name="category" class="form-control" placeholder="Salary, Rent, Utility"></div>
                <div class="col-md-3 form-group"><label>Base Amount</label><input name="planned_amount" type="number" min="1" step="0.01" class="form-control live-money planned-amount calc-net" required></div>
                <div class="col-md-2 form-group"><label>Tax</label><input name="tax_amount" type="number" min="0" step="0.01" class="form-control calc-net" value="0"></div>
                <div class="col-md-2 form-group"><label>Discount</label><input name="discount_amount" type="number" min="0" step="0.01" class="form-control calc-net" value="0"></div>
                <div class="col-md-2 form-group"><label>Net</label><input class="form-control net-preview" readonly value="0.00"></div>
                <div class="col-md-3 form-group"><label>Expense Month</label><input name="expense_month" type="month" class="form-control" value="{{ now()->format('Y-m') }}"></div>
                <div class="col-md-3 form-group"><label>Due Date</label><input name="due_date" type="date" class="form-control"></div>
                <div class="col-md-3 form-group"><label>Priority</label><select name="priority" class="form-control"><option value="normal">Normal</option><option value="high">High</option><option value="urgent">Urgent</option><option value="low">Low</option></select></div>
                <div class="col-md-3 form-group"><label>Payment Terms</label><input name="payment_terms" class="form-control" placeholder="Net 7 / Immediate"></div>
                <div class="col-md-3 form-group"><label>GSTIN</label><input name="vendor_gstin" class="form-control"></div>
                <div class="col-md-4 form-group"><label>Preferred Bank</label><select name="bank_account_id" class="form-control"><option value="">Decide while paying</option>@foreach($bankAccounts ?? [] as $account)<option value="{{ $account->id }}">{{ $account->name }}</option>@endforeach</select></div>
                <div class="col-md-3 form-group"><label>Status</label><select name="status" class="form-control"><option value="submitted">Submit for Approval</option><option value="draft">Draft</option></select></div>
                <div class="col-md-5 form-group"><label>Attachment</label><input name="attachment" type="file" class="form-control attachment-input" accept=".jpg,.jpeg,.png,.pdf,.webp"><div class="attachment-preview mt-2"></div></div>
                <div class="col-12 form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
            </div>
        </div>
        <div class="modal-footer"><span class="mr-auto text-muted live-preview"></span><button class="btn btn-warning"><i class="fas fa-save mr-1"></i> Save Expense Plan</button></div>
    </form></div>
</div>

@push('scripts')
<script>
function formatMoney(value) {
    const amount = Number(value || 0);
    return 'Rs ' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
document.querySelectorAll('.live-money').forEach(input => {
    input.addEventListener('input', () => {
        const preview = input.closest('form').querySelector('.live-preview');
        if (preview) preview.textContent = 'Amount preview: ' + formatMoney(input.value);
    });
});
document.querySelectorAll('.ledger-amount-source').forEach(select => {
    select.addEventListener('change', () => {
        const amount = select.selectedOptions[0]?.dataset.amount;
        const target = select.closest('form').querySelector('.planned-amount');
        if (amount && Number(amount) > 0 && target && !target.value) {
            target.value = amount;
            target.dispatchEvent(new Event('input'));
        }
    });
});
document.querySelectorAll('.calc-net').forEach(input => {
    input.addEventListener('input', () => {
        const form = input.closest('form');
        const base = Number(form.querySelector('[name="planned_amount"]')?.value || 0);
        const tax = Number(form.querySelector('[name="tax_amount"]')?.value || 0);
        const discount = Number(form.querySelector('[name="discount_amount"]')?.value || 0);
        const net = Math.max(0, base + tax - discount);
        const target = form.querySelector('.net-preview');
        if (target) target.value = net.toFixed(2);
        const preview = form.querySelector('.live-preview');
        if (preview) preview.textContent = 'Net payable: ' + formatMoney(net);
    });
});
document.querySelectorAll('.attachment-input').forEach(input => {
    input.addEventListener('change', () => {
        const preview = input.parentElement.querySelector('.attachment-preview');
        preview.innerHTML = '';
        const file = input.files[0];
        if (!file) return;
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.maxHeight = '90px';
            img.style.borderRadius = '8px';
            img.style.border = '1px solid #e5e7eb';
            preview.appendChild(img);
        } else {
            preview.innerHTML = '<span class="badge badge-light"><i class="far fa-file-pdf mr-1"></i>' + file.name + '</span>';
        }
    });
});
</script>
@endpush
