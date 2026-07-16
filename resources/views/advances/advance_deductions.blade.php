@extends('layouts.master')

@section('title')
    Advance Deductions
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Advance Deductions</h3>
        </div>
        <div class="box-body table-responsive">
            @if ($advances->isEmpty())
                <p>No advance deductions found.</p>
            @else
                <table class="table table-bordered table-hover table-striped" id="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Amount</th>
                            <th>Installments</th>
                            <th>Installment Amount</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>For</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($advances as $advance)
                            <tr>
                                <td>{{ $advance->id }}</td>
                                <td>{{ $advance->first_name }} {{ $advance->last_name }}</td>
                                <td>{{ $advance->office->name ?? 'N/A' }}</td>
                                <td>{{ number_format($advance->amount, 2) }}</td>
                                <td>{{ $advance->installments }}</td>
                                <td>{{ number_format($advance->installment_amount, 2) }}</td>
                                <td>{{ number_format($advance->amount_paid ?? 0, 2) }}</td>
                                <td>{{ number_format($advance->remaining_amount, 2) }}</td>
                                <td>
                                    <span class="label label-{{ $advance->status == 'pending' ? 'warning' : ($advance->status == 'approved' ? 'success' : 'default') }}">
                                        {{ ucfirst($advance->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $advance->created_at->format('Y-m-d') }}
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('advances.deduction', $advance->id) }}" id="deduct-form-{{ $advance->id }}" style="display: inline;">
                                        @csrf
                                        <button type="button" class="btn btn-success btn-xs deduct-btn" 
                                                data-form-id="{{ $advance->id }}" 
                                                data-advance-name="{{ $advance->first_name }} {{ $advance->last_name }}"
                                                data-installment-amount="{{ number_format($advance->installment_amount, 2) }}"
                                                data-remaining-amount="{{ number_format($advance->remaining_amount, 2) }}">
                                            <i class="fa fa-minus"></i> Deduct
                                        </button>
                                    </form>
                                    <a href="{{ route('advances.show', $advance->id) }}" class="btn btn-info btn-xs">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="modal fade" id="deductConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deductConfirmModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm" role="document" style="margin-top:15vh;">
            <div class="modal-content">
                <div class="modal-body text-center" style="padding:24px 24px 16px;">
                    <div style="width:52px;height:52px;border-radius:50%;background:#fdf3f2;
                                margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-minus" style="font-size:22px;color:#28a745;"></i>
                    </div>
                    <h4 id="deductConfirmModalLabel" style="margin:0 0 6px;font-size:15px;font-weight:600;color:#333;">
                        Confirm Deduction
                    </h4>
                    <p style="margin:0;color:#999;font-size:13px;">This will process the deduction for this advance.</p>
                </div>
                <div class="modal-footer" style="padding:8px 16px 16px;border-top:none;justify-content:center;gap:8px;">
                    <button type="button" class="btn btn-default btn-flat btn-xs"
                            style="min-width:74px;" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeductBtn"
                            class="btn btn-success btn-flat btn-xs"
                            style="min-width:74px;">Deduct</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true, 
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[0, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [10]}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });

        var currentFormId = null;

        function openDeductModal(formId, advanceName, installmentAmount, remainingAmount) {
            currentFormId = formId;
            $('#deductConfirmModal').find('h4').text('Deduct from ' + advanceName + '?');
            $('#deductConfirmModal').find('p').last().html('Installment Amount: <strong>' + installmentAmount + '</strong><br>Current Remaining Balance: <strong>' + remainingAmount + '</strong>');
            $('#deductConfirmModal').modal('show');
        }

        function submitDeduction() {
            if (currentFormId) {
                document.getElementById('deduct-form-' + currentFormId).submit();
            }
        }

        $(document).on('click', '.deduct-btn', function() {
            var formId = $(this).data('form-id');
            var advanceName = $(this).data('advance-name');
            var installmentAmount = $(this).data('installment-amount');
            var remainingAmount = $(this).data('remaining-amount');

            openDeductModal(formId, advanceName, installmentAmount, remainingAmount);
        });

        $('#confirmDeductBtn').off('click').on('click', function(e) {
            e.preventDefault();
            submitDeduction();
        });
    });
</script>
@endsection