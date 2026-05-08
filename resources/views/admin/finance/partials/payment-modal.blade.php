<div class="modal fade" id="paymentModal{{ $expense->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('admin.finance.expenses.payments.store', $expense) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Pay {{ $expense->title }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">Remaining balance: <strong>{{ $money($expense->remaining_amount) }}</strong></div>
                <div class="row">
                    <div class="col-md-4 form-group"><label>Bank Account</label><select name="bank_account_id" class="form-control" required>@foreach($bankAccounts as $account)<option value="{{ $account->id }}">{{ $account->name }} ({{ $money($account->current_balance) }})</option>@endforeach</select></div>
                    <div class="col-md-4 form-group"><label>Amount</label><input name="amount" type="number" min="1" max="{{ $expense->remaining_amount }}" step="0.01" class="form-control live-money" value="{{ $expense->remaining_amount }}" required></div>
                    <div class="col-md-4 form-group"><label>Payment Date</label><input name="payment_date" type="date" class="form-control" value="{{ now()->toDateString() }}" required></div>
                    <div class="col-md-4 form-group"><label>Reference No.</label><input name="reference_no" class="form-control"></div>
                    <div class="col-md-8 form-group"><label>Attachment</label><input name="attachment" type="file" class="form-control attachment-input" accept=".jpg,.jpeg,.png,.pdf,.webp"><div class="attachment-preview mt-2"></div></div>
                    <div class="col-12 form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                </div>
                <input type="hidden" name="status" value="submitted">
            </div>
            <div class="modal-footer"><span class="mr-auto text-muted live-preview"></span><button class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Submit Payment</button></div>
        </form>
    </div>
</div>
