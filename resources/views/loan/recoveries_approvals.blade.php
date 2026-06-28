@extends('layouts.master')
@section('title')
    Pending Debt Recovery Transaction Approvals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Recovery Payment Transaction Approvals</h3>
            <div class="box-tools pull-right">
                <span class="label label-info">{{ count($data) }} Pending</span>
            </div>
        <div class="box-body table-responsive">
            @if(count($data) > 0)
            <table class="table table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Branch</th>
                    <th>Loan Officer</th>
                    <th>Client</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Receipt #</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                @if($key->recoveryCase)
                <?php
                    $client = $key?->recoveryCase?->client;
                ?>
                    <tr>
                        <td>
                            @if($key->recoveryCase->loan)
                                <a href="{{ url('loan/'.$key->recoveryCase->loan->id.'/show') }}" data-toggle="tooltip" title="Click to view loan">
                                    {{ $key->recoveryCase->loan->id }}
                                </a>
                            @else
                                <span class="text-muted">Loan N/A</span>
                            @endif
                            <br>
                            <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#caseModal{{$key->id}}" title="View Case Details">
                                <i class="fa fa-eye"></i> Case Details
                            </button>
                        </td>
                        <td>
                            @if($key->recoveryCase->loan && !empty($key->recoveryCase->loan->office))
                                {{$key->recoveryCase->loan->office->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->recordedBy))
                                {{$key->recordedBy->first_name}} {{$key->recordedBy->last_name}}
                            @endif
                        </td>
                        @if(!empty($client))
                        <td>{{$client->first_name}} {{$client->last_name}}</td>
                        @else
                        <td>-</td>
                        @endif
                        <td>{{number_format($key->amount,2)}}</td>
                        <td>{{$key->payment_date}}</td>
                        <td>
                            @if(!empty($key->payment_method))
                                <span class="label label-primary">
                                    {{ ucfirst(str_replace('_', ' ', $key->payment_method)) }}
                                </span>
                            @else
                                <span class="label label-default">Not Set</span>
                            @endif
                        </td>
                        <td>{{ $key->receipt_number ?? '-' }}</td>
                        <td>
                            <a href="{{ url('loan/recoveries_approve/'.$key->id) }}"
                               onclick="return confirm('Are you sure you want to approve this recovery payment?')">
                                <span class="label label-success">Approve</span>
                            </a>
                            <a href="{{ url('loan/recoveries_decline/'.$key->id) }}"
                               onclick="return confirm('Are you sure you want to decline this recovery payment?')">
                                <span class="label label-danger">Decline</span>
                            </a>
                        </td>
                    </tr>
                @endif
                @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No pending debt recovery transactions to approve.
            </div>
            @endif
        </div>
    </div>

    <!-- Recovery Case Details Modals -->
    @foreach($data as $key)
    @if($key->recoveryCase)
    <div class="modal fade" id="caseModal{{$key->id}}" tabindex="-1" role="dialog" aria-labelledby="caseModalLabel{{$key->id}}">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="caseModalLabel{{$key->id}}">
                        Recovery Case Details - {{ $key->recoveryCase->case_number ?? 'N/A' }}
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><strong>Case Information</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Case Number:</strong></td>
                                    <td>{{ $key->recoveryCase->case_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>{{ $key->recoveryCase->category_label ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="label label-{{ $key->recoveryCase->status_color ?? 'default' }}">
                                            {{ ucfirst(str_replace('_', ' ', $key->recoveryCase->status ?? 'N/A')) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created Date:</strong></td>
                                    <td>{{ $key->recoveryCase->created_at ? $key->recoveryCase->created_at->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Approved Date:</strong></td>
                                    <td>{{ $key->recoveryCase->approved_date ? $key->recoveryCase->approved_date->format('d M Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><strong>Financial Details</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Loan Outstanding:</strong></td>
                                    <td>K{{ number_format($key->recoveryCase->loan_outstanding_amount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount Recovered:</strong></td>
                                    <td>K{{ number_format($key->recoveryCase->amount_recovered ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Recovery Costs:</strong></td>
                                    <td>K{{ number_format($key->recoveryCase->recovery_costs ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Net Recovery:</strong></td>
                                    <td>K{{ number_format($key->recoveryCase->net_recovery ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Recovery Rate:</strong></td>
                                    <td>{{ $key->recoveryCase->recovery_rate ?? 0 }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5><strong>Branch Information</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Origin Branch:</strong></td>
                                    <td>{{ $key->recoveryCase->originBranch->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Supporting Branch:</strong></td>
                                    <td>{{ $key->recoveryCase->supportingBranch->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Assigned Specialist:</strong></td>
                                    <td>{{ $key->recoveryCase->assignedSpecialist ? $key->recoveryCase->assignedSpecialist->first_name . ' ' . $key->recoveryCase->assignedSpecialist->last_name : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><strong>Attribution Percentages</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Recoveries Dept:</strong></td>
                                    <td>{{ $key->recoveryCase->recoveries_dept_attribution_pct ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Origin Branch:</strong></td>
                                    <td>{{ $key->recoveryCase->origin_branch_attribution_pct ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Supporting Branch:</strong></td>
                                    <td>{{ $key->recoveryCase->supporting_branch_attribution_pct ?? 0 }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($key->recoveryCase->notes)
                    <div class="row">
                        <div class="col-md-12">
                            <h5><strong>Notes</strong></h5>
                            <p>{{ $key->recoveryCase->notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if($key->recoveryCase->category == 'legal')
                    <div class="row">
                        <div class="col-md-12">
                            <h5><strong>Legal Information</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Lawyer Firm:</strong></td>
                                    <td>{{ $key->recoveryCase->lawyer_firm ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Legal Reference:</strong></td>
                                    <td>{{ $key->recoveryCase->legal_reference_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Filed Date:</strong></td>
                                    <td>{{ $key->recoveryCase->legal_filed_date ? $key->recoveryCase->legal_filed_date->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Court Date:</strong></td>
                                    <td>{{ $key->recoveryCase->court_date ? $key->recoveryCase->court_date->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Legal Costs:</strong></td>
                                    <td>K{{ number_format($key->recoveryCase->legal_costs_incurred ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($key->recoveryCase->category == 'skip_trace')
                    <div class="row">
                        <div class="col-md-12">
                            <h5><strong>Skip Trace Information</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Tracking Code:</strong></td>
                                    <td>{{ $key->recoveryCase->skip_trace_tracking_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Client Located:</strong></td>
                                    <td>{{ $key->recoveryCase->client_located ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Located Date:</strong></td>
                                    <td>{{ $key->recoveryCase->located_date ? $key->recoveryCase->located_date->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Skip Trace Costs:</strong></td>
                                    <td>K{{ number_format($key->recoveryCase->skip_trace_costs ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <a href="{{ url('recovery/case/'.$key->recoveryCase->id.'/show') }}" class="btn btn-primary" target="_blank">
                        View Full Case Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
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
            "order": [[5, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [8]}
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
