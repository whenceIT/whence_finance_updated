<div id="depositQueryModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Query Deposit Statements</h3>
            <button type="button" id="closeDepositQueryModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="depositQueryForm" class="form-inline">
                <div class="form-group">
                    <label>Office</label>
                    <select name="office_id" class="form-control">
                        <!-- <option value="">All Offices</option> -->
                        <?php if (isset($offices) && $offices): ?>
                            <?php foreach ($offices as $o): ?>
                                <?php $oid = $o->id; $oname = $o->name; ?>
                                <option value="<?= $oid ?>"><?= e($oname) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Deposit Type</label>
                    <select name="deposit_type" class="form-control">
                        <option value="">All Types</option>
                        <?php 
                        $depositTypes = \App\Models\DepositType::orderBy('sort_order')->orderBy('name')->get();
                        foreach ($depositTypes as $t): ?>
                            <option value="<?= $t->id ?>"><?= e($t->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <select name="year" class="form-control">
                        <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                            <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Query</button>
            </form>
            <div style="margin-top:10px; margin-bottom:10px;">
                <button type="button" id="exportDepositExcelBtn" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Export to Excel</button>
            </div>
            <div id="depositQueryResult" style="margin-top:15px; max-height:400px; overflow-y:auto;">
                <div id="depositQueryPlaceholder" style="text-align:center; color:#666; font-size:13px;"><i class="fa fa-search"></i> Enter search criteria and click Query</div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('depositQueryForm');
    var resultDiv = document.getElementById('depositQueryResult');
    var placeholder = document.getElementById('depositQueryPlaceholder');
    var exportBtn = document.getElementById('exportDepositExcelBtn');
    var depositsData = [];
    
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        placeholder.style.display = 'block';
        placeholder.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
        
        var formData = new FormData(form);
        var params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        fetch('/risk/deposits/query?' + params.toString())
            .then(r => r.json())
            .then(data => {
                placeholder.style.display = 'none';
                depositsData = data.deposits || [];
                
                if (!data || data.deposits.length === 0) {
                    resultDiv.innerHTML = '<div class="alert alert-info">No deposit records found.</div>';
                    return;
                }
                
                var deposits = data.deposits;
                var total = data.total || 0;
                
                var summary = '<div class="well" style="background:#f8f9fa; padding:12px; margin-bottom:15px; border-radius:6px; border-left:4px solid #3c8dbc;">' +
                    '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">' +
                    '<div><strong>Total Records:</strong> ' + deposits.length + '</div>' +
                    '<div><strong>Total Amount:</strong> K' + total.toLocaleString() + '</div>' +
                    '</div></div>';
                
                var table = '<table class="table table-striped table-bordered" style="font-size:12px; margin:0;">' +
                    '<thead style="background:#3c8dbc; color:#fff;">' +
                    '<tr>' +
                    '<th style="padding:8px;">Deposit Type</th>' +
           
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
                        '<td style="padding:6px;">' + d.office_name + '</td>' +
                        '<td style="padding:6px; text-align:right;"><span class="editable-amount" data-id="' + d.id + '" contenteditable="true" style="cursor:text; padding:4px; border:1px solid transparent; border-radius:3px;">' + amountVal.toLocaleString() + '</span></td>' +
                        '<td style="padding:6px;">' + methodVal + '</td>' +
                        '<td style="padding:6px; font-family:monospace; font-size:11px;">' + refVal + '</td>' +
                        '<td style="padding:6px;">' + dateVal + '</td>' +
                        '</tr>';
                });
                
                table += '</tbody></table>';
                resultDiv.innerHTML = summary + table;
                
                resultDiv.querySelectorAll('.editable-amount').forEach(function(el) {
                    el.addEventListener('blur', function() {
                        var id = this.getAttribute('data-id');
                        var newAmount = parseFloat(this.textContent.replace(/[^\d.-]/g, '')) || 0;
                        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        fetch('/risk/deposits/update-amount', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ id: id, amount: newAmount })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                this.style.border = '1px solid #d4edda';
                                setTimeout(() => { this.style.border = '1px solid transparent'; }, 1000);
                            }
                        })
                        .catch(err => console.error('Save error:', err));
                    });
                });
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
        link.setAttribute('download', 'deposits_query_results.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
})();
</script>