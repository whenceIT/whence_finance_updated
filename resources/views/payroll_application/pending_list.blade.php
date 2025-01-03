@extends('layouts.master')
@section('title')
   Pending payroll loan applications
@endsection
@section('content')
<div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> Pending List </h3>

        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Employer</th>
                    <th>Amount</th>
		    <th>Loan Term</th>
                     <th>Monthly Repayment</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td><a href="{{ url('loan/payroll_loan/'.$key->id.'/payroll_applicant') }}" data-toggle="tooltip" title="Click to view">{{ $key->id }}</a></td>
                        <td>
                       {{ $key->client_name }}
                        </td>
                        <td>
                        {{ $key->employer_name }}
                        </td>
			<td>{{number_format($key->amount) }}</td>
			<td>{{ $key->loan_term}}</td>
 <th>{{$key->deduction_amount}}</th>
                        <td>{{date("jS M, Y", strtotime($key->created_at))}}</td>
                        <td>
                               <div ass="btn-group">
                    <a href="{{ url('loan/payroll_loan/'.$key->id.'/payroll_applicant') }}" class="btn btn-primary">
                    Details</a>
                    </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>

        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[7, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
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
    </script>
@endsection
