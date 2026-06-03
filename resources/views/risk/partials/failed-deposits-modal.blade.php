<div id="failedDepositsModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Failed Deposits (< Amount)</h3>
            <button type="button" id="closeFailedDepositsModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Threshold Amount</label>
                <input type="number" id="failedDepositThreshold" class="form-control" value="1" step="0.01" min="0">
            </div>
            <button type="button" id="queryFailedDepositsBtn" class="btn btn-primary">Query</button>
            <button type="button" id="exportFailedDepositsExcelBtn" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Export to Excel</button>
            <div id="failedDepositsResult" style="margin-top:15px; max-height:400px; overflow-y:auto;">
                <div id="failedDepositsPlaceholder" style="text-align:center; color:#666; font-size:13px;"><i class="fa fa-search"></i> Click Query to search for failed deposits</div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var modal = document.getElementById('failedDepositsModal');
    var thresholdInput = document.getElementById('failedDepositThreshold');
    var queryBtn = document.getElementById('queryFailedDepositsBtn');
    var exportBtn = document.getElementById('exportFailedDepositsExcelBtn');
    var resultDiv = document.getElementById('failedDepositsResult');
    var placeholder = document.getElementById('failedDepositsPlaceholder');
    var depositsData = [];
    
    if (!modal) return;
    
    queryBtn.addEventListener('click', function() {
        var threshold = parseFloat(thresholdInput.value) || 1;
        placeholder.style.display = 'block';
        placeholder.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
        
        fetch('/risk/failed-deposits?amount=' + threshold)
            .then(r => r.json())
            .then(data => {
                placeholder.style.display = 'none';
                depositsData = data.deposits || [];
                
                if (!data || data.deposits.length === 0) {
                    resultDiv.innerHTML = '<div class="alert alert-info">No failed deposits found.</div>';
                    return;
                }
                
                var deposits = data.deposits;
                var total = data.total || 0;
                
                var summary = '<div class="well" style="background:#f8f9fa; padding:12px; margin-bottom:15px; border-radius:6px; border-left:4px solid #e74c3c;">' +
                    '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">' +
                    '<div><strong>Total Records:</strong> ' + deposits.length + '</div>' +
                    '<div><strong>Total Amount:</strong> K' + total.toLocaleString() + '</div>' +
                    '</div></div>';
                
                var table = '<table class="table table-striped table-bordered" style="font-size:12px; margin:0;">' +
                    '<thead style="background:#e74c3c; color:#fff;">' +
                    '<tr>' +
                    '<th style="padding:8px;">Deposit Type</th>' +
                    '<th style="padding:8px;">User</th>' +
                    '<th style="padding:8px;">Office</th>' +
                    '<th style="padding:8px; text-align:right;">Amount</th>' +
                    '<th style="padding:8px;">Method</th>' +
                    '<th style="padding:8px;">Reference</th>' +
                    '<th style="padding:8px;">Date</th>' +
                    '</tr></thead><tbody>';
                
                deposits.forEach(function(d) {
                    var dateVal = d.created_date || d.date || '-';
                    var typeVal = d.deposit_type_name || d.type_name || '-';
                    var userVal = d.user_name || d.User || '-';
                    var methodVal = d.deposit_method || d.method || '-';
                    var refVal = d.reference_number || d.reference || '-';
                    var amountVal = d.amount || 0;
                    
                    table += '<tr>' +
                        '<td style="padding:6px;">' + typeVal + '</td>' +
                        '<td style="padding:6px;">' + userVal + '</td>' +
                        '<td style="padding:6px;">' + d.office_name + '</td>' +
                        '<td style="padding:6px; text-align:right;">' + amountVal.toLocaleString() + '</td>' +
                        '<td style="padding:6px;">' + methodVal + '</td>' +
                        '<td style="padding:6px; font-family:monospace; font-size:11px;">' + refVal + '</td>' +
                        '<td style="padding:6px;">' + dateVal + '</td>' +
                        '</tr>';
                });
                
                table += '</tbody></table>';
                resultDiv.innerHTML = summary + table;
            })
            .catch(function(err) {
                placeholder.style.display = 'none';
                resultDiv.innerHTML = '<div class="alert alert-danger">Error fetching results.</div>';
                console.error(err);
            });
    });
    
    exportBtn.addEventListener('click', function() {
        if (depositsData.length === 0) {
            alert('No data to export. Please run a query first.');
            return;
        }
        
        var csvContent = 'data:text/csv;charset=utf-8,';
        csvContent += 'Deposit Type,User,Office,Amount,Method,Reference,Date\n';
        
        depositsData.forEach(function(d) {
            var row = [
                d.deposit_type_name || d.type_name || '-',
                d.user_name || d.User || '-',
                d.office_name || '-',
                d.amount || 0,
                d.deposit_method || d.method || '-',
                d.reference_number || d.reference || '-',
                d.created_date || d.date || '-'
            ].join(',');
            csvContent += row + '\n';
        });
        
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'failed_deposits_query_results.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
    
    // Close handlers
    document.getElementById('closeFailedDepositsModal').addEventListener('click', function() {
        modal.style.display = 'none';
    });
})();
</script>