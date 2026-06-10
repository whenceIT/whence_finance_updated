<div class="box-body">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-3">
            <div class="info-box" style="background: #f8f9fa;">
                <div class="info-box-content">
                    <span class="info-box-text">Total Deposits</span>
                    <span class="info-box-number">K{{ number_format($totalDeposits ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box" style="background: #f8f9fa;">
                <div class="info-box-content">
                    <span class="info-box-text">Total Expenses</span>
                    <span class="info-box-number">K{{ number_format($totalExpenses ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box" style="background: #e8f5e9;">
                <div class="info-box-content">
                    <span class="info-box-text">Available Balance</span>
                    <span class="info-box-number">K{{ number_format($availableBalance ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 text-right" style="margin-top: 20px;">
            <a href="{{ route('administration_expenses.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Create New Expense
            </a>
        </div>
    </div>

    <form method="get" action="{{ route('administration_expenses.index') }}" class="form-horizontal">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="start_date" class="control-label col-md-2">Start Date</label>
            <div class="col-md-3">
                <input type="text" name="start_date" class="form-control date-picker" value="{{ $start_date ?? '' }}">
            </div>
        </div>

        <div class="form-group">
            <label for="end_date" class="control-label col-md-2">End Date</label>
            <div class="col-md-3">
                <input type="text" name="end_date" class="form-control date-picker" value="{{ $end_date ?? '' }}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-md-3 text-center">
                <button type="submit" class="btn btn-success">Search</button>
                <a href="{{ route('administration_expenses.index') }}" class="btn btn-danger">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="box-body">
    <div class="table-responsive">
        <table id="admin-data-table" class="table table-bordered table-condensed table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Bank Charge Type</th>
                    <th>Entered By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses ?? [] as $expense)
                <tr>
                    <td>{{ $expense->expense_date }}</td>
                    <td>{{ $expense->category->name ?? '-' }}</td>
                    <td>{{ $expense->reference_number ?? '-' }}</td>
                    <td>K{{ number_format($expense->amount, 2) }}</td>
                    <td>{{ $expense->comments ?? '-' }}</td>
                    <td>{{ $expense->bank_charge_type ?? '-' }}</td>
                    <td>{{ $expense->enteredByName ?? '-' }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="{{ route('administration_expenses.edit', $expense->id) }}"><i class="fa fa-edit"></i> Edit</a></li>
                                <li><a href="{{ route('administration_expenses.destroy', $expense->id) }}" class="delete"><i class="fa fa-trash"></i> Delete</a></li>
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
    $('#admin-data-table').DataTable({
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