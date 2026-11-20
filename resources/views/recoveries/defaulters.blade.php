@extends('layouts.master')
@section('title') Defaulters List @endsection
@section('content')
<section class="content"><div class="box"><div class="box-body">

<div class="callout callout-info" style="margin-bottom:20px;">
    <h4><i class="fa fa-info-circle"></i> Overdue Loans Drilldown</h4>
    <p>Click a province to expand its districts, then a district to expand its offices, then an office to view its overdue loans.</p>
</div>

<div id="defaulters-accordion" style="margin-bottom:20px;"></div>

<div class="modal fade" id="loan-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="loan-results-title">Overdue Loans</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="loan-results-loading">Loading loans...</div>
                <table class="table table-bordered table-striped" id="loan-results-table" style="display:none;">
                    <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>Client Name</th>
                        <th>NRC</th>
                        <th>Phone</th>
                        <th>Principal</th>
                        <th>First Repayment Date</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody id="loan-results-body"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-loan-results"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
    </div>
</div>

</div></div></section>

<style>
.accordion-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
    background: #fff;
    overflow: hidden;
}
.accordion-header {
    background: #f4f4f4;
    padding: 12px 16px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    border-bottom: 1px solid #ddd;
    transition: background 0.2s;
}
.accordion-header:hover {
    background: #e8e8e8;
}
.accordion-header.expanded {
    background: #e8f5e9;
    border-bottom-color: #00a04a;
}
.accordion-header .count-badge {
    background: #00a04a;
    color: #fff;
    border-radius: 12px;
    padding: 2px 10px;
    font-size: 12px;
    font-weight: bold;
}
.accordion-header .toggle-icon {
    font-size: 12px;
}
.accordion-body {
    padding: 0;
    border-top: 1px solid #ddd;
}
.accordion-body .inner-list {
    padding: 12px 16px;
}
.accordion-body .inner-list:empty {
    display: none;
}
.sub-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 16px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background 0.15s;
}
.sub-item:hover {
    background: #f9f9f9;
}
.sub-item.expanded {
    background: #e8f5e9;
    font-weight: 600;
    border-bottom-color: #00a04a;
}
.sub-item:last-child {
    border-bottom: none;
}
.sub-item .count-badge {
    background: #00a04a;
    color: #fff;
    border-radius: 12px;
    padding: 2px 8px;
    font-size: 11px;
}
.print-office-btn {
    background: #f4f4f4;
    border: 1px solid #ddd;
    color: #333;
}
.print-office-btn:hover {
    background: #e8e8e8;
    border-color: #bbb;
}
.empty-state {
    text-align: center;
    color: #999;
    padding: 20px;
    font-style: italic;
}
</style>

<script>
$(document).ready(function() {
    var $accordion = $('#defaulters-accordion');
    var $loanResults = $('#loan-modal');
    var $loanTable = $('#loan-results-table');
    var $loanBody = $('#loan-results-body');
    var $loanLoading = $('#loan-results-loading');
    var currentOfficeId = null;
    var currentOfficeName = null;

    $('#print-loan-results').on('click', function() {
        var printContents = document.getElementById('loan-results-table').outerHTML;
        var title = document.getElementById('loan-results-title').innerText;
        var w = window.open('', '', 'height=600,width=900');
        w.document.write('<html><head><title>' + title + '</title>');
        w.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">');
        w.document.write('</head><body>');
        w.document.write('<h3>' + title + '</h3>');
        w.document.write(printContents);
        w.document.write('</body></html>');
        w.document.close();
        w.focus();
        w.print();
        w.close();
    });

    // ---------- Province Level ----------
    function loadProvinces() {
        $accordion.html('<div class="alert alert-info">Loading provinces...</div>');

        $.ajax({
            url: '{{ route('recovery.defaulters.provinces') }}',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $accordion.empty();
                if (!data || data.length === 0) {
                    $accordion.html('<div class="empty-state">No provinces with overdue loans found.</div>');
                    return;
                }
                $.each(data, function(i, p) {
                    var item = $(
                        '<div class="accordion-item province-item" data-id="' + p.id + '">' +
                        '<div class="accordion-header">' +
                        '<span>' + p.name + '</span>' +
                        '<span class="count-badge">' + p.loan_count + ' overdue</span>' +
                        '<span class="toggle-icon">+</span>' +
                        '</div>' +
                        '<div class="accordion-body" style="display:none;">' +
                        '<div class="inner-list" id="districts-' + p.id + '"></div>' +
                        '</div>' +
                        '</div>'
                    );
                    item.find('.accordion-header').on('click', function() {
                        var $header = $(this);
                        var $body = $header.next();
                        var isExpanded = $header.hasClass('expanded');

                        // Collapse other open province items
                        $accordion.find('.accordion-item.province-item .accordion-header.expanded').each(function() {
                            var $h = $(this);
                            $h.removeClass('expanded').find('.toggle-icon').text('+');
                            $h.next().slideUp(200);
                        });

                        // Collapse loan results when opening a new province
                        if (!isExpanded) {
                            $loanResults.modal('hide');
                            $loanTable.hide();
                            $loanLoading.show();
                            currentOfficeId = null;
                        }

                        if (isExpanded) {
                            $header.removeClass('expanded').find('.toggle-icon').text('+');
                            $body.slideUp(200);
                        } else {
                            $header.addClass('expanded').find('.toggle-icon').text('-');
                            $body.slideDown(200);
                            loadDistricts(p.id, $body.find('#districts-' + p.id));
                        }
                    });
                    $accordion.append(item);
                });
            },
            error: function() {
                $accordion.html('<div class="alert alert-danger">Failed to load provinces.</div>');
            }
        });
    }

    // ---------- District Level ----------
    function loadDistricts(provinceId, $container) {
        if ($container.children().length > 0) return; // already loaded

        $container.html('<div class="alert alert-info">Loading districts...</div>');

        $.ajax({
            url: '{{ route('recovery.defaulters.districts', ':id') }}'.replace(':id', provinceId),
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $container.empty();
                if (!data || data.length === 0) {
                    $container.html('<div class="empty-state">No districts with overdue loans.</div>');
                    return;
                }
                $.each(data, function(i, d) {
                    var item = $(
                        '<div class="accordion-item district-item" data-id="' + d.id + '" data-province="' + provinceId + '">' +
                        '<div class="accordion-header" style="padding-left:30px; background:#fafafa;">' +
                        '<span>' + d.name + '</span>' +
                        '<span class="count-badge">' + d.loan_count + ' overdue</span>' +
                        '<span class="toggle-icon">+</span>' +
                        '</div>' +
                        '<div class="accordion-body" style="display:none;">' +
                        '<div class="inner-list" id="offices-' + d.id + '"></div>' +
                        '</div>' +
                        '</div>'
                    );
                    item.find('.accordion-header').on('click', function(e) {
                        e.stopPropagation();
                        var $header = $(this);
                        var $body = $header.next();
                        var isExpanded = $header.hasClass('expanded');

                        // Collapse other open district items within this province
                        $container.find('.accordion-header.expanded').each(function() {
                            var $h = $(this);
                            $h.removeClass('expanded').find('.toggle-icon').text('+');
                            $h.next().slideUp(200);
                        });

                        if (!isExpanded) {
                            $loanResults.modal('hide');
                            $loanTable.hide();
                            $loanLoading.show();
                            currentOfficeId = null;
                        }

                        if (isExpanded) {
                            $header.removeClass('expanded').find('.toggle-icon').text('+');
                            $body.slideUp(200);
                        } else {
                            $header.addClass('expanded').find('.toggle-icon').text('-');
                            $body.slideDown(200);
                            loadOffices(d.id, $body.find('#offices-' + d.id));
                        }
                    });
                    $container.append(item);
                });
            },
            error: function() {
                $container.html('<div class="alert alert-danger">Failed to load districts.</div>');
            }
        });
    }

    // ---------- Office Level ----------
    function loadOffices(districtId, $container) {
        if ($container.children().length > 0) return; // already loaded

        $container.html('<div class="alert alert-info">Loading offices...</div>');

        $.ajax({
            url: '{{ route('recovery.defaulters.offices', ':id') }}'.replace(':id', districtId),
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $container.empty();
                if (!data || data.length === 0) {
                    $container.html('<div class="empty-state">No offices with overdue loans.</div>');
                    return;
                }
                $.each(data, function(i, o) {
                    var item = $(
                        '<div class="accordion-item office-item" data-id="' + o.id + '" data-name="' + o.name + '" data-district="' + districtId + '">' +
                        '<div class="accordion-header" style="padding-left:50px; background:#fafafa;">' +
                        '<span>' + o.name + '</span>' +
                        '<span class="count-badge">' + o.loan_count + ' overdue</span>' +
                        '<span class="toggle-icon">+</span>' +
                        '</div>' +
                        '<div class="accordion-body" style="display:none;">' +
                        '<div class="inner-list" id="loans-placeholder-' + o.id + '">' +
                        '<div class="sub-item" style="color:#999; font-style:italic;">Click to view overdue loans</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>'
                    );
                    item.find('.accordion-header').on('click', function(e) {
                        e.stopPropagation();
                        var $header = $(this);
                        var $body = $header.next();
                        var isExpanded = $header.hasClass('expanded');

                        // Collapse other open office items within this district
                        $container.find('.accordion-header.expanded').each(function() {
                            var $h = $(this);
                            $h.removeClass('expanded').find('.toggle-icon').text('+');
                            $h.next().slideUp(200);
                        });

                        if (isExpanded) {
                            $header.removeClass('expanded').find('.toggle-icon').text('+');
                            $body.slideUp(200);
                            $loanResults.modal('hide');
                            $loanTable.hide();
                            $loanLoading.show();
                            currentOfficeId = null;
                        } else {
                            $header.addClass('expanded').find('.toggle-icon').text('-');
                            $body.slideDown(200);
                            currentOfficeId = o.id;
                            currentOfficeName = o.name;
                            loadOfficeLoans(o.id);
                        }
                    });
                    $container.append(item);
                });
            },
            error: function() {
                $container.html('<div class="alert alert-danger">Failed to load offices.</div>');
            }
        });
    }

    // ---------- Loan Results ----------
    function loadOfficeLoans(officeId) {
        $loanResults.modal('show');
        $loanTable.hide();
        $loanLoading.show();
        $('#loan-results-title').text('Overdue Loans — ' + currentOfficeName);

        $.ajax({
            url: '{{ route('recovery.defaulters.loans', ':id') }}'.replace(':id', officeId),
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $loanLoading.hide();
                if (!data || data.length === 0) {
                    $loanTable.hide();
                    $loanLoading.html('<div class="empty-state">No overdue loans found for this office.</div>').show();
                    return;
                }
                $loanBody.empty();
                $.each(data, function(i, l) {
                    var row = $(
                        '<tr>' +
                        '<td>' + (l.id || '-') + '</td>' +
                        '<td>' + (l.first_name || '') + ' ' + (l.last_name || '') + '</td>' +
                        '<td>' + (l.nrc_number || '-') + '</td>' +
                        '<td>' + (l.phone_primary || '-') + '</td>' +
                        '<td>' + (l.principal ? parseFloat(l.principal).toFixed(2) : '0.00') + '</td>' +
                        '<td>' + (l.first_repayment_date || '-') + '</td>' +
                        '<td><span class="label bg-red">' + (l.status || 'Unknown') + '</span></td>' +
                        '</tr>'
                    );
                    $loanBody.append(row);
                });
                $loanTable.show();
            },
            error: function() {
                $loanLoading.html('<div class="alert alert-danger">Failed to load loans.</div>').show();
            }
        });
    }


    // ---------- Initial load ----------
    loadProvinces();
});
</script>
@endsection
