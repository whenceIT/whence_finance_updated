<style>
.od-dialog.modal-fullscreen { width: 98%; max-width: 1200px; }
.od-form-row { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 12px; }
.od-form-group { flex: 1 1 160px; display: flex; flex-direction: column; }
.od-form-group label { font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #555; }
.od-form-group input, .od-form-group select { padding: 6px 8px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; }
.od-btn { padding: 6px 12px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer; }
.od-btn-save { background: #3c8dbc; color: #fff; }
.od-btn-cancel { background: #95a5a6; color: #fff; }
.od-status-pill { display: inline-block; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
.od-status-pill.owing { background: #fdecea; color: #c0392b; }
.od-status-pill.partial { background: #fff8e1; color: #f39c12; }
.od-status-pill.paid { background: #eafaf1; color: #27ae60; }
</style>

<div class="modal fade p-3" id="odModal" tabindex="-1" role="dialog" aria-labelledby="odModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog od-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="odModalLabel"><i class="fa fa-balance-scale"></i> Edit Office Debt</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow-y:auto;padding:20px 24px;">
                <div id="odFormBar" style="display:none;margin-bottom:16px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; padding:0 4px;">
                        <span id="odFormTitle" style="font-weight:700; color:#2c3e50; font-size:14px;">Add / Edit Debt Record</span>
                        <a href="#" id="odBackToList" style="font-size:12px; color:#667eea; text-decoration:none; display:flex; align-items:center; gap:4px; font-weight:600;"><i class="fa fa-arrow-left"></i> Back to list</a>
                    </div>
                    <div class="od-form-row">
                        <div class="od-form-group"><label>Branch</label><select id="odInputOffice"><option value="">Select a branch…</option><?php $offices = \App\Models\Office::orderBy('name')->get(); foreach ($offices as $o) { echo '<option value="' . $o->id . '">' . htmlspecialchars($o->name) . '</option>'; } ?></select></div>
                        <div class="od-form-group"><label>Deposit Type</label><select id="odInputDepositType"><option value="">Optional…</option><?php $depositTypes = \App\Models\DepositType::orderBy('sort_order')->orderBy('name')->get(); foreach ($depositTypes as $dt) { echo '<option value="' . $dt->id . '">' . htmlspecialchars($dt->name) . '</option>'; } ?></select></div>
                        <div class="od-form-group"><label>Month</label><select id="odInputMonth"><?php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; foreach ($months as $i => $m) { echo '<option value="' . ($i + 1) . '">' . $m . '</option>'; } ?></select></div>
                        <div class="od-form-group"><label>Year</label><select id="odInputYear"><?php $thisYear = (int) date('Y'); for ($y = $thisYear; $y >= $thisYear - 5; $y--) { echo '<option value="' . $y . '">' . $y . '</option>'; } ?></select></div>
                    </div>
                    <div class="od-form-row">
                        <div class="od-form-group"><label>Status</label><select id="odInputStatus"><option value="owing">Owing</option><option value="partial">Partially Paid</option><option value="paid">Cleared</option></select></div>
                        <div class="od-form-group"><label>Original Debt</label><input type="number" id="odInputOriginal" min="0" step="0.01"></div>
                        <div class="od-form-group"><label>Outstanding</label><input type="number" id="odInputOutstanding" min="0" step="0.01"></div>
                        <div class="od-form-group" style="flex:1 1 100%;"><label>Notes</label><input type="text" id="odInputNotes" placeholder="Optional notes…"></div>
                        <div class="od-form-group" style="justify-content:flex-end;flex-direction:row;gap:6px;"><button class="od-btn od-btn-save" id="odBtnSaveForm">Save</button><button class="od-btn od-btn-cancel" id="odBtnCancelForm">Cancel</button></div>
                    </div>
                    <input type="hidden" id="odEditId" value="">
                </div>
                <div id="odListHeader" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; gap:12px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:240px;"><h4 style="margin:0; font-size:14px; font-weight:700; color:#333;">Branch Debt Records</h4><input type="text" id="odSearchInput" placeholder="Search office, type or notes…" style="padding:5px 10px; border:1px solid #ccc; border-radius:4px; font-size:12px; width:240px; max-width:100%;"></div>
                    <div style="display:flex; gap:8px; align-items:center;"><button type="button" id="odBtnExport" class="od-btn od-btn-save" style="padding:6px 12px; font-size:12px; font-weight:600;"><i class="fa fa-file-excel-o"></i> Export Excel</button><button type="button" id="odBtnNewRow" class="od-btn od-btn-save" style="padding:6px 14px; font-size:13px; font-weight:600;"><i class="fa fa-plus"></i> New Record</button></div>
                </div>
                <div class="table-responsive"><table class="table table-sm table-hover" id="odTable"><thead><tr><th>Branch</th><th>Deposit Type</th><th>Month / Year</th><th>Status</th><th>Original (ZMW)</th><th>Outstanding (ZMW)</th><th>Notes</th><th style="width:140px;">Actions</th></tr></thead><tbody id="odTableBody"></tbody></table></div>
                <p id="odEmpty" style="display:none;text-align:center;padding:30px;color:#bbb;"><i class="fa fa-check-circle" style="font-size:28px;color:#27ae60;margin-bottom:10px;"></i><br>No branches currently carry an outstanding debt.</p>
                <div id="odShimmer" style="display:none;padding:20px 24px;">
                    @php $shimmer = 4; @endphp
                    @for($i = 0; $i < $shimmer; $i++)
                    <div style="display:flex;gap:10px;margin-bottom:8px;"><div style="width:22%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div><div style="width:18%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div><div style="width:14%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div><div style="width:10%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div><div style="width:10%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div><div style="width:16%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/kilo-alert.js"></script>
<script>
(function() {
    var editId = function() { return $('#odEditId').val(); };
    var odAllRows = [];

    $(document).on('click', '#openOfficeDebtModal', function(e) {
        e.preventDefault();
        var $liveModal = $('#odModal');
        if (!$liveModal.length) { console.error('odModal not found in DOM'); return; }
        odResetForm();
        odLoadTable();
        $liveModal.modal('show');
    });

    function odResetForm() {
        $('#odInputOffice').val('').prop('disabled', false);
        $('#odInputDepositType').val('').prop('disabled', false);
        $('#odInputMonth').val('').prop('disabled', false);
        $('#odInputYear').val('').prop('disabled', false);
        $('#odInputStatus').val('owing').prop('disabled', false);
        $('#odInputOriginal').val('').prop('disabled', false);
        $('#odInputOutstanding').val('').prop('disabled', false);
        $('#odInputNotes').val('');
        $('#odEditId').val('');
        $('#odFormBar').hide();
    }

    function odShowList() {
        $('#odFormBar').hide();
        $('#odListHeader').show();
        $('.table-responsive').show();
    }

    function odShowForm(isEdit) {
        $('#odListHeader').hide();
        $('.table-responsive').hide();
        $('#odEmpty').hide();
        var $fb = $('#odFormBar');
        $('#odFormTitle').text(isEdit ? 'Edit Debt Record' : 'Add New Debt Record');
        if (!isEdit) {
            var now = new Date();
            $('#odInputMonth').val(now.getMonth() + 1);
            $('#odInputYear').val(now.getFullYear());
            if (!$('#odInputStatus').val()) $('#odInputStatus').val('owing');
        }
        var disableIdentifiers = !!isEdit;
        $('#odInputOffice, #odInputDepositType, #odInputMonth, #odInputYear, #odInputStatus, #odInputOriginal').prop('disabled', disableIdentifiers);
        $fb.show();
        $fb[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function odLoadTable() {
        var $tableBody = $('#odTableBody');
        var $empty     = $('#odEmpty');
        var $shimmer   = $('#odShimmer');
        $tableBody.empty();
        $empty.hide();
        $shimmer.show();
        $.get('{{ route("risk.office-debts.list") }}', function(resp) {
            $shimmer.hide();
            odAllRows = resp || [];
            $('#odSearchInput').val('');
            if (odAllRows.length === 0) { $empty.show(); return; }
            odRenderDebtTable(odAllRows);
        }).fail(function() {
            $shimmer.hide();
            $tableBody.html('<tr><td colspan="8" style="padding:20px;text-align:center;color:#c0392b;">Error loading debt records. Try again.</td></tr>');
        });
    }

    function odRowHtml(row) {
        var cls = row.outstanding_amount <= 0 ? 'paid' : row.outstanding_amount < row.original_amount ? 'partial' : 'owing';
        var balance = row.outstanding_amount <= 0 ? '—' : parseFloat(row.outstanding_amount).toLocaleString('en-US', { style: 'currency', currency: 'ZMW' });
        var original = parseFloat(row.original_amount).toLocaleString('en-US', { style: 'currency', currency: 'ZMW' });
        var monthLabel = row.debt_month && row.debt_year ? (['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][row.debt_month - 1] || row.debt_month) + ' ' + row.debt_year : '—';
        return '<tr class="od-debt-row" data-id="' + row.id + '">'
             + '<td>' + (row.office_name || '—') + '</td>'
             + '<td>' + (row.deposit_type_name || '—') + '</td>'
             + '<td>' + monthLabel + '</td>'
             + '<td><span class="od-status-pill ' + cls + '">' + row.debt_status + '</span></td>'
             + '<td>' + original + '</td>'
             + '<td style="font-weight:700;color:' + (cls === 'owing' ? '#c0392b' : (cls === 'partial' ? '#f39c12' : '#27ae60')) + ';">' + balance + '</td>'
             + '<td style="color:#777;font-size:12px;">' + (row.notes || '') + '</td>'
             + '<td class="od-actions"><button class="od-btn od-btn-edit" title="Edit" onclick="odEdit(' + row.id + ')"><i class="fa fa-pencil"></i></button></td>'
             + '</tr>';
    }

    function odRenderDebtTable(rows) {
        var $tableBody = $('#odTableBody');
        $tableBody.empty();
        if (!rows || rows.length === 0) { $tableBody.html('<tr><td colspan="8" style="padding:20px;text-align:center;color:#888;">No matching records</td></tr>'); return; }
        rows.forEach(function(row) { $tableBody.append(odRowHtml(row)); });
    }

    function odExportToCSV(rows) {
        if (!rows || rows.length === 0) return;
        var headers = ['Branch','Deposit Type','Month/Year','Status','Original','Outstanding','Notes'];
        var csvRows = [headers.join(',')];
        rows.forEach(function(row) {
            var monthLabel = row.debt_month && row.debt_year ? (['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][row.debt_month - 1] || row.debt_month) + ' ' + row.debt_year : '';
            var line = ['"' + (row.office_name || '').replace(/"/g, '""') + '"', '"' + (row.deposit_type_name || '').replace(/"/g, '""') + '"', '"' + monthLabel + '"', '"' + (row.debt_status || '') + '"', row.original_amount || 0, row.outstanding_amount || 0, '"' + (row.notes || '').replace(/"/g, '""') + '"'];
            csvRows.push(line.join(','));
        });
        var csvContent = csvRows.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.href = url;
        link.download = 'office_debts_' + new Date().toISOString().slice(0,10) + '.csv';
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    window.odEdit = function(id) {
        $.get('{{ route("risk.office-debts.list") }}', function(resp) {
            var row = resp.find(function(r) { return r.id === id; });
            if (!row) return;
            $('#odEditId').val(id);
            $('#odInputOffice').val(row.office_id);
            $('#odInputDepositType').val(row.deposit_type_id || '');
            if (row.debt_month) { $('#odInputMonth').val(row.debt_month); } else if (row.created_at) { $('#odInputMonth').val(new Date(row.created_at).getMonth() + 1); }
            if (row.debt_year) { $('#odInputYear').val(row.debt_year); } else if (row.created_at) { $('#odInputYear').val(new Date(row.created_at).getFullYear()); }
            $('#odInputStatus').val(row.debt_status);
            $('#odInputOriginal').val(row.original_amount);
            $('#odInputOutstanding').val(row.outstanding_amount);
            $('#odInputNotes').val(row.notes);
            odShowForm(true);
            var $tBody = $('#odTableBody'); $tBody.find('tr[data-id="' + id + '"]')[0] && $tBody.find('tr[data-id="' + id + '"]')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    };

    window.odDel = function(id) {
        if (!confirm('Remove this debt record? The branch will no longer appear as carrying debt.')) return;
        $.ajax({ url: '{{ route("risk.office-debts.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id), type: 'DELETE', data: { _token: '{{ csrf_token() }}' } })
            .done(function(r) { if (r.success) odLoadTable(); else KiloAlert.error(r.message || 'Unable to delete record.'); })
            .fail(function() { KiloAlert.error('Network error. Try again.'); });
    };

    $(document).on('click', '#odBtnSaveForm', function() {
        var id = editId();
        var url = id ? '{{ route("risk.office-debts.update", ["id" => "__ID__"]) }}'.replace('__ID__', id) : '{{ route("risk.office-debts.store") }}';
        var type = id ? 'PUT' : 'POST';
        $.ajax({ url: url, type: type, data: { _token: '{{ csrf_token() }}', office_id: $('#odInputOffice').val(), deposit_type_id: $('#odInputDepositType').val() || null, debt_month: $('#odInputMonth').val() ? parseInt($('#odInputMonth').val()) : null, debt_year: $('#odInputYear').val() ? parseInt($('#odInputYear').val()) : null, debt_status: $('#odInputStatus').val(), original_amount: $('#odInputOriginal').val(), outstanding_amount: $('#odInputOutstanding').val(), notes: $('#odInputNotes').val() } })
            .done(function(r) { if (r.success) { odResetForm(); odShowList(); odLoadTable(); } else { KiloAlert.error(r.message || 'Save failed.'); } })
            .fail(function(xhr) { var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Save failed.'; KiloAlert.error(msg); });
    });

    $(document).on('click', '#odBtnNewRow', function() { odResetForm(); odShowForm(false); });
    $(document).on('input', '#odSearchInput', function() {
        var term = $(this).val().toLowerCase().trim();
        if (!odAllRows || odAllRows.length === 0) return;
        var filtered = odAllRows.filter(function(row) {
            return (row.office_name || '').toLowerCase().includes(term) || (row.deposit_type_name || '').toLowerCase().includes(term) || (row.debt_status || '').toLowerCase().includes(term) || (row.notes || '').toLowerCase().includes(term);
        });
        odRenderDebtTable(filtered);
    });
    $(document).on('click', '#odBtnExport', function() {
        if (!odAllRows || odAllRows.length === 0) { KiloAlert.info('No data to export.'); return; }
        var searchTerm = ($('#odSearchInput').val() || '').toLowerCase().trim();
        var dataToExport = searchTerm ? odAllRows.filter(function(row) {
            return (row.office_name || '').toLowerCase().includes(searchTerm) || (row.deposit_type_name || '').toLowerCase().includes(searchTerm) || (row.debt_status || '').toLowerCase().includes(searchTerm) || (row.notes || '').toLowerCase().includes(searchTerm);
        }) : odAllRows;
        odExportToCSV(dataToExport);
    });
    $(document).on('click', '#odBtnCancelForm, #odBackToList', function(e) { e.preventDefault(); odResetForm(); odShowList(); });
})();
</script>