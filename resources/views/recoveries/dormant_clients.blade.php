@extends('layouts.master')
@section('title')
    {{ trans_choice('general.client', 2) }} - Recovery Clients
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-users"></i> Recovery Clients
            </h3>
            <div class="box-tools pull-right">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="javascript:void(0)" id="dormant-tab"><i class="fa fa-users"></i> Dormant Clients</a></li>
                    <li><a href="javascript:void(0)" id="recovered-tab"><i class="fa fa-check-circle"></i> Recovered Clients</a></li>
                </ul>
            </div>
        </div>
        <div class="box-body" id="clients-container">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> 
                <strong>Dormant clients</strong> are active clients who either:
                <ul style="margin-bottom: 0; margin-top: 5px;">
                    <li>Have never taken a loan, OR</li>
                    <li>Haven't taken a loan in the last 3 months</li>
                </ul>
            </div>

            <div id="loading-indicator" style="display:none;text-align:center;padding:20px;">
                <i class="fa fa-spinner fa-spin fa-2x"></i> Loading...
            </div>
            <div id="clients-table-container">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> 
                <strong>Click on Dormant or Recovered tabs to load data.</strong>
            </div>
        </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function() {
        $('#data-table').DataTable({
            dom: 'Bfrtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 10,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "order": [[6, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [8]}
            ],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                }
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "Showing _START_ to _END_ of _TOTAL_ dormant clients",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "(filtered from _MAX_ total clients)",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: true
        });
    });

    $('#dormant-tab').on('click', function(e) {
        e.preventDefault();
        loadClients('dormant');
    });

    $('#recovered-tab').on('click', function(e) {
        e.preventDefault();
        loadClients('recovered');
    });

    function loadClients(type, append) {
        var start = append ? $('#clients-table-container tbody tr').length : 0;
        var length = 10;
        
        $('#loading-indicator').show();
        if (!append) {
            $('#clients-table-container').hide();
        }
        
        $.ajax({
            url: '{{ route('client.fetch_dormant_clients') }}',
            data: {type: type, start: start, length: length},
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.length === 0) {
                        if (!append) {
                            $('#clients-table-container').html('<div class="alert alert-success text-center" style="padding: 40px;"><i class="fa fa-check-circle" style="font-size: 48px; color: #5cb85c; margin-bottom: 15px;"></i><h4>No ' + (type === 'recovered' ? 'Recovered' : 'Dormant') + ' Clients Found</h4><p class="text-muted">All active clients have had recent loan activity within the last 3 months.</p></div>');
                        }
                    } else {
                        var html = '';
                        if (!append) {
                            html = '<table class="table table-bordered table-hover table-striped" id="data-table"><thead><tr><th>ID</th><th>Client Name</th><th>Mobile</th><th>Office</th><th>Loan Officer</th><th>Total Shared Loans</th><th>Action</th></tr></thead><tbody>';
                        }
                        $.each(response.data, function(i, client) {
                            html += '<tr>';
                            html += '<td>' + client.id + '</td>';
                            html += '<td><strong>' + client.first_name + ' ' + client.last_name + '</strong></td>';
                            html += '<td>' + client.mobile + '</td>';
                            html += '<td>' + client.office + '</td>';
                            html += '<td>' + client.loan_officer + '</td>';
                            html += '<td><span class="badge bg-gray">' + client.total_loans + '</span></td>';
                            html += '<td><div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-cog"></i> Actions <span class="caret"></span></button><ul class="dropdown-menu" role="menu"><li><a href="javascript:void(0)" onclick="markRecovered(' + client.id + ')"><i class="fa fa-check"></i> Mark Recovered</a></li></ul></div></td>';
                            html += '</tr>';
                        });
                        if (!append) {
                            html += '</tbody></table>';
                            $('#clients-table-container').html(html);
                        } else {
                            $('#clients-table-container tbody').append(html);
                        }
                        $('#load-more-btn').remove();
                        if (response.data.length === length) {
                            $('#clients-table-container').after('<button id="load-more-btn" class="btn btn-primary" style="margin-top: 10px;" onclick="loadClients(\'' + type + '\', true)">Load More</button>');
                        }
                    }
                } else {
                    $('#clients-table-container').html('<div class="alert alert-danger">Failed to load clients.</div>');
                }
                $('#loading-indicator').hide();
                $('#clients-table-container').show();
            },
            error: function() {
                $('#clients-table-container').html('<div class="alert alert-danger">Failed to load clients.</div>');
                $('#loading-indicator').hide();
                $('#clients-table-container').show();
            }
        });
    }

    function markRecovered(clientId) {
        if (!confirm('Mark this client as recovered?')) {
            return;
        }

        $.ajax({
            url: '{{ url('client') }}/' + clientId + '/mark-recovered',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    KiloAlert.success('Client marked as recovered!');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    KiloAlert.error('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                KiloAlert.error('An error occurred. Please try again.');
            }
        });
    }
</script>
@endsection