@extends('layouts.master')
@section('title') Defaulters List @endsection
@section('content')
<section class="content-header"><h1>Defaulters List</h1></section>
<section class="content"><div class="box"><div class="box-body">

<div class="callout callout-info" style="margin-bottom:20px;">
    <h4><i class="fa fa-info-circle"></i> Overdue Loans Drilldown</h4>
    <p>Click a province to expand its districts, then a district to expand its offices, then an office to view its overdue loans.</p>
</div>

<div id="defaulters-accordion" style="margin-bottom:20px;"></div>

<div id="loan-results" style="display:none; margin-top:20px;">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title" id="loan-results-title">Overdue Loans</h3>
        </div>
        <div class="box-body">
            <div class="alert alert-info" id="loan-results-loading">Loading loans...</div>
            <table class="table table-bordered table-striped" id="loan-results-table" style="display:none;">
                <thead>
                <tr>
                    <th>Account Number</th>
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
    var $loanResults = $('#loan-results');
    var $loanTable = $('#loan-results-table');
    var $loanBody = $('#loan-results-body');
    var $loanLoading = $('#loan-results-loading');
    var currentOfficeId = null;
    var currentOfficeName = null;

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
                            $loanResults.hide();
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
                            $loanResults.hide();
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
                        '<button type="button" class="btn btn-xs btn-default print-office-btn" style="margin-left:10px;" title="Print overdue loans"><i class="fa fa-print"></i> Print</button>' +
                        '<span class="toggle-icon">+</span>' +
                        '</div>' +
                        '<div class="accordion-body" style="display:none;">' +
                        '<div class="inner-list" id="loans-placeholder-' + o.id + '">' +
                        '<div class="sub-item" style="color:#999; font-style:italic;">Click to view overdue loans</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>'
                    );
                    item.find('.accordion-header .print-office-btn').on('click', function(e) {
                        e.stopPropagation();
                        printOfficeLoans(o.id, o.name, o.loan_count);
                    });
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
                            $loanResults.hide();
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
        $loanResults.show();
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
                        '<td>' + (l.account_number || '-') + '</td>' +
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

    // ---------- Print Office Loans ----------
    function printOfficeLoans(officeId, officeName, loanCount) {
        var win = window.open('', '_blank', 'width=800,height=600');
        win.document.write('<!DOCTYPE html>');
        win.document.write('<html>');
        win.document.write('<head>');
        win.document.write('<title>Overdue Loans - ' + officeName + '</title>');
        win.document.write('<link rel="stylesheet" href="{{ asset("vendor/adminlte/dist/css/AdminLTE.min.css") }}">');
        win.document.write('<style>');
        win.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
        win.document.write('h1 { font-size: 18px; margin-bottom: 10px; }');
        win.document.write('table { width: 100%; border-collapse: collapse; margin-top: 15px; }');
        win.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
        win.document.write('th { background: #f4f4f4; }');
        win.document.write('.header-info { margin-bottom: 20px; }');
        win.document.write('.print-btn { display: none; }');
        win.document.write('@media print { .no-print { display: none; } }');
        win.document.write('</style>');
        win.document.write('</head>');
        win.document.write('<body>');
        win.document.write('<div class="header-info">');
        win.document.write('<h1>Overdue Loans Report</h1>');
        win.document.write('<p><strong>Office:</strong> ' + officeName + '</p>');
        win.document.write('<p><strong>Total Overdue Loans:</strong> ' + loanCount + '</p>');
        win.document.write('<p><strong>Generated:</strong> ' + new Date().toLocaleString() + '</p>');
        win.document.write('</div>');
        win.document.write('<table>');
        win.document.write('<thead><tr><th>Account Number</th><th>Client Name</th><th>NRC</th><th>Phone</th><th>Principal</th><th>First Repayment Date</th><th>Status</th></tr></thead>');
        win.document.write('<tbody id="print-tbody"></tbody>');
        win.document.write('</table>');
        win.document.write('<button class="btn btn-primary no-print" onclick="window.print();"><i class="fa fa-print"></i> Print</button>');
        win.document.write('<script>');
        win.document.write('$(document).ready(function() {');
        win.document.write('  $.ajax({');
        win.document.write('    url: "' + "{{ route('recovery.defaulters.loans', ':id') }}".replace(':id', officeId) + '",');
        win.document.write('    method: "GET",');
        win.document.write('    dataType: "json",');
        win.document.write('    success: function(data) {');
        win.document.write('      var $tbody = $("#print-tbody");');
        win.document.write('      if (!data || data.length === 0) {');
        win.document.write('        $tbody.html("<tr><td colspan=\"7\" class=\"text-center\">No overdue loans found.</td></tr>");');
        win.document.write('        return;');
        win.document.write('      }');
        win.document.write('      $.each(data, function(i, l) {');
        win.document.write('        $tbody.append("<tr>" +');
        win.document.write('          "<td>" + (l.account_number || "-") + "</td>" +');
        win.document.write('          "<td>" + (l.first_name || "") + " " + (l.last_name || "") + "</td>" +');
        win.document.write('          "<td>" + (l.nrc_number || "-") + "</td>" +');
        win.document.write('          "<td>" + (l.phone_primary || "-") + "</td>" +');
        win.document.write('          "<td>" + (l.principal ? parseFloat(l.principal).toFixed(2) : "0.00") + "</td>" +');
        win.document.write('          "<td>" + (l.first_repayment_date || "-") + "</td>" +');
        win.document.write('          "<td>" + (l.status || "Unknown") + "</td>" +');
        win.document.write('        "</tr>");');
        win.document.write('      });');
        win.document.write('      window.print();');
        win.document.write('    },');
        win.document.write('    error: function() {');
        win.document.write('      $("#print-tbody").html("<tr><td colspan=\"7\" class=\"text-center\">Failed to load loans.</td></tr>");');
        win.document.write('    }');
        win.document.write('  });');
        win.document.write('});');
        win.document.write('</script>');
        win.document.write('</body>');
        win.document.write('</html>');
        win.document.close();
    }

    // ---------- Initial load ----------
    loadProvinces();
});
</script>
@endsection
