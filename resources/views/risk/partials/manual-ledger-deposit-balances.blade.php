<!-- Ledger summary table + fetch script moved here -->

<div id="ledgerTableSection" style="display: none; margin-top: 40px;">
    <div class="deposit-header-box">
        <h3 style="margin-top: 0;">Ledger Summary</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
            <thead>
                    <tr>
                        <th>Office</th>
                        <th>Building Paid</th>
                        <th>Building Outstanding (System Outstanding)</th>
                        <th>Building Ledger Balance (Bright's Ledger)</th>
                        <th></th>
                        <th>Statutory Paid</th>
                        <th>Statutory Outstanding (System Outstanding)</th>
                        <th>Statutory Ledger Balance (Bright's Ledger)</th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody">
                    <tr><td colspan="7" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function fetchLedgerTable() {
        $.get('/api/ledger-summary', function(res) {
            var $tbody = $('#ledgerTableBody').empty();
            var data = res.data || [];
            if (data.length === 0) {
                $tbody.append('<tr><td colspan="7" class="text-center">No data available</td></tr>');
            } else {
                data.forEach(function(d) {
                    var buildingPaid = parseFloat(d.building_paid) || 0;
                    var buildingOutstanding = parseFloat(d.building_outstanding) || 0;
                    var buildingLedger = d.ledger_balance_building ? (parseFloat(d.ledger_balance_building.balance) || 0) : 0;
                    var statutoryPaid = parseFloat(d.statutory_paid) || 0;
                    var statutoryOutstanding = parseFloat(d.statutory_outstanding) || 0;
                    var statutoryLedger = d.ledger_balance_statutory ? (parseFloat(d.ledger_balance_statutory.balance) || 0) : 0;

                    // Consider equal if difference is very small (tolerance for floating math)
                    var buildingMatch = Math.abs(buildingOutstanding - buildingLedger) < 0.005;
                    var statutoryMatch = Math.abs(statutoryOutstanding - statutoryLedger) < 0.005;

                    var outTdStyle = buildingMatch ? ' style="background:#27ae60;color:#fff;font-weight:700;"' : '';
                    var ledTdStyle = buildingMatch ? ' style="background:#27ae60;color:#fff;font-weight:700;"' : '';
                    var statOutTdStyle = statutoryMatch ? ' style="background:#27ae60;color:#fff;font-weight:700;"' : '';
                    var statLedTdStyle = statutoryMatch ? ' style="background:#27ae60;color:#fff;font-weight:700;"' : '';

                    $tbody.append('<tr>' +
                        '<td>' + (d.office_name || '-') + '</td>' +
                        '<td>K' + buildingPaid.toLocaleString() + '</td>' +
                        '<td' + outTdStyle + '>K' + buildingOutstanding.toLocaleString() + '</td>' +
                        '<td' + ledTdStyle + '>K' + buildingLedger.toLocaleString() + '</td>' +
                        '<td style="background:#f4f5f7;padding:0;margin:0;border:none;width:14px;"></td>' +
                        '<td>K' + statutoryPaid.toLocaleString() + '</td>' +
                        '<td' + statOutTdStyle + '>K' + statutoryOutstanding.toLocaleString() + '</td>' +
                        '<td' + statLedTdStyle + '>K' + statutoryLedger.toLocaleString() + '</td>' +
                        '</tr>');
                });
            }
            $('#ledgerTableSection').show();
        });
    }
    fetchLedgerTable();
</script>
