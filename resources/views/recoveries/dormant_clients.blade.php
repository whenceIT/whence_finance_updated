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
                    <li><a href="javascript:void(0)" id="overdue-tab"><i class="fa fa-exclamation-triangle"></i> Overdue Loans</a></li>
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
<style>
.office-section {
    margin-bottom: 30px;
}
.office-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 6px 6px 0 0;
    margin-bottom: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.office-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
}
.office-count {
    font-size: 14px;
    opacity: 0.95;
}
.nav-tabs > li > a {
    cursor: pointer !important;
}
.nav-tabs > li.active > a {
    background-color: #667eea !important;
    color: white !important;
    border-color: #667eea !important;
}
</style>
<script>
    $(document).ready(function() {
        // Load dormant clients by default
        loadClients('dormant');
    });

    $('#dormant-tab').on('click', function(e) {
        e.preventDefault();
        $('.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        loadClients('dormant');
    });

    $('#recovered-tab').on('click', function(e) {
        e.preventDefault();
        $('.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        loadClients('recovered');
    });

    $('#overdue-tab').on('click', function(e) {
        e.preventDefault();
        $('.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        loadClients('overdue');
    });

    function loadClients(type) {
        $('#loading-indicator').show();
        $('#clients-table-container').hide();
        
        $.ajax({
            url: '{{ route('client.fetch_dormant_clients') }}',
            data: {type: type},
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.length === 0) {
                        var message = '';
                        if (type === 'recovered') {
                            message = 'No Recovered Clients Found';
                        } else if (type === 'overdue') {
                            message = 'No Clients with Overdue Loans Found';
                        } else {
                            message = 'No Dormant Clients Found';
                        }
                        $('#clients-table-container').html(
                            '<div class="alert alert-success text-center" style="padding: 40px;">' +
                            '<i class="fa fa-check-circle" style="font-size: 48px; color: #5cb85c; margin-bottom: 15px;"></i>' +
                            '<h4>' + message + '</h4>' +
                            '<p class="text-muted">All clients are up to date.</p>' +
                            '</div>'
                        );
                    } else {
                        renderGroupedTables(response.grouped_by_office, type);
                    }
                } else {
                    $('#clients-table-container').html('<div class="alert alert-danger">Failed to load clients.</div>');
                }
                $('#loading-indicator').hide();
                $('#clients-table-container').show();
            },
            error: function() {
                $('#clients-table-container').html('<div class="alert alert-danger">Failed to load clients. Please try again.</div>');
                $('#loading-indicator').hide();
                $('#clients-table-container').show();
            }
        });
    }

    function renderGroupedTables(groupedData, type) {
        var html = '';
        
        $.each(groupedData, function(index, officeGroup) {
            html += '<div class="office-section">';
            html += '<div class="office-header">';
            html += '<h3 class="office-title"><i class="fa fa-building"></i> ' + officeGroup.office_name + '</h3>';
            html += '<div class="office-count"><strong>' + officeGroup.count + '</strong> Clients</div>';
            html += '</div>';
            html += '<div class="box box-primary" style="margin-top: 0; border-radius: 0 0 6px 6px;">';
            html += '<div class="box-body table-responsive" style="padding: 0;">';
            html += '<table class="table table-bordered table-hover table-striped office-table" data-office="' + officeGroup.office_name + '">';
            html += '<thead><tr>';
            html += '<th>ID</th>';
            html += '<th>Client Name</th>';
            html += '<th>Mobile</th>';
            html += '<th>Loan Officer</th>';
            
            if (type === 'overdue') {
                html += '<th>Loan ID</th>';
                html += '<th>Loan Amount</th>';
                html += '<th>First Repayment Date</th>';
                html += '<th>Days Overdue</th>';
            } else {
                html += '<th>Last Loan Date</th>';
                html += '<th>Days Since Last Loan</th>';
            }
            
            html += '<th>Total Shared Loans</th>';
            html += '<th>Action</th>';
            html += '</tr></thead><tbody>';
            
            $.each(officeGroup.clients, function(i, client) {
                html += '<tr>';
                html += '<td>' + client.id + '</td>';
                html += '<td><strong>' + client.first_name + ' ' + client.last_name + '</strong></td>';
                html += '<td>' + client.mobile + '</td>';
                html += '<td>' + client.loan_officer + '</td>';
                
                if (type === 'overdue') {
                    html += '<td><a href="{{ url('loan') }}/' + client.loan_id + '/show">' + client.loan_id + '</a></td>';
                    html += '<td>K' + parseFloat(client.loan_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) + '</td>';
                    html += '<td>' + client.first_repayment_date + '</td>';
                    html += '<td><span class="badge bg-red">' + client.days_overdue + ' days</span></td>';
                } else {
                    html += '<td>' + client.last_loan_date + '</td>';
                    html += '<td>' + (client.days_since_last_loan !== null ? client.days_since_last_loan + ' days' : 'N/A') + '</td>';
                }
                
                html += '<td><span class="badge bg-gray">' + client.total_loans + '</span></td>';
                html += '<td>';
                html += '<a href="{{ url('client') }}/' + client.id + '/show" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a> ';
                
                if (type === 'dormant') {
                    html += '<button class="btn btn-xs btn-success" onclick="markRecovered(' + client.id + ')"><i class="fa fa-check"></i> Mark Recovered</button>';
                }
                
                html += '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            html += '</div></div></div>';
        });
        
        $('#clients-table-container').html(html);
        
        // Initialize DataTables for each office table
        $('.office-table').each(function() {
            $(this).DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        className: 'btn btn-success btn-xs',
                        title: $(this).data('office') + ' - ' + type.charAt(0).toUpperCase() + type.slice(1) + ' Clients'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i> PDF',
                        className: 'btn btn-danger btn-xs',
                        orientation: 'landscape',
                        title: $(this).data('office') + ' - ' + type.charAt(0).toUpperCase() + type.slice(1) + ' Clients'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        className: 'btn btn-info btn-xs',
                        title: $(this).data('office') + ' - ' + type.charAt(0).toUpperCase() + type.slice(1) + ' Clients'
                    }
                ],
                "paging": true,
                "lengthChange": true,
                "displayLength": 10,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[0, "asc"]],
                "language": {
                    "lengthMenu": "{{ trans('general.lengthMenu') }}",
                    "zeroRecords": "{{ trans('general.zeroRecords') }}",
                    "info": "Showing _START_ to _END_ of _TOTAL_ clients",
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
                    alert('Client marked as recovered!');
                    loadClients('dormant');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please try again.');
            }
        });
    }
</script>
@endsection