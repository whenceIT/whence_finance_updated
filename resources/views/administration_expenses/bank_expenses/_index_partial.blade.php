<div class="box-body" style="margin-bottom: 15px;">
    <div class="text-right">
        <a href="{{ route('bank_account_expenses.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Create New Expense
        </a>
    </div>
</div>

<div class="box-body">
    <div class="table-responsive">
        <table id="bank-data-table" class="table table-bordered table-condensed table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Bank Account</th>
                    <th>Category</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Entered By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bankExpenses ?? [] as $expense)
                <tr>
                    <td>{{ $expense->transaction_date }}</td>
                    <td>{{ $expense->bankAccount->name ?? '-' }}</td>
                    <td>{{ $expense->category->name ?? '-' }}</td>
                    <td>{{ $expense->reference_number ?? '-' }}</td>
                    <td>K{{ number_format($expense->amount, 2) }}</td>
                    <td>{{ $expense->comments ?? '-' }}</td>
                    <td>{{ $expense->enteredByName ?? '-' }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="{{ route('bank_account_expenses.edit', $expense->id) }}"><i class="fa fa-edit"></i> Edit</a></li>
                                <li><a href="{{ route('bank_account_expenses.destroy', $expense->id) }}" class="delete"><i class="fa fa-trash"></i> Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#bank-data-table').DataTable({
        dom: 'frtip',
        "paging": true,
        "lengthChange": true,
        "displayLength": 15,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "order": [[0, "desc"]]
    });
</script>