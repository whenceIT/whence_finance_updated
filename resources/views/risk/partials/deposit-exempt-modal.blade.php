<style>
.plt-modal { padding: 20px; }
.plt-modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
.plt-modal-title { margin: 0; font-size: 16px; font-weight: 600; color: #333; }
.plt-form-row { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
.plt-form-group { flex: 1 1 200px; display: flex; flex-direction: column; }
.plt-form-group label { font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #555; }
.plt-form-group input, .plt-form-group select, .plt-form-group textarea { padding: 8px 10px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; width: 100%; }
.plt-form-group textarea { min-height: 80px; resize: vertical; }
.plt-form-full { flex: 1 1 100%; }
.plt-modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 12px; border-top: 1px solid #eee; }
.plt-btn { padding: 8px 16px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer; }
.plt-btn-primary { background: #3c8dbc; color: #fff; }
.plt-btn-secondary { background: #95a5a6; color: #fff; }
.plt-office-select { width: 100%; }
</style>

@include('components.kilo-alert')
<div class="modal fade" id="depositExemptModal" tabindex="-1" role="dialog" aria-labelledby="depositExemptModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="depositExemptModalLabel">Exempt Deposit Months</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body plt-modal">
                <input type="hidden" id="pltEditId" value="">
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltMonths">Number of Months to Exempt</label>
                        <input type="number" id="pltMonths" min="0" max="12" value="0" placeholder="Enter months">
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltDepositType">Select Deposit Type (optional)</label>
                        <select id="pltDepositType" class="plt-office-select" name="deposit_type_id">
                            <option value="">All deposit types</option>
                            @foreach($depositTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltOffices">Select Offices (leave empty for all)</label>
                        <select id="pltOffices" class="plt-office-select" name="offices[]" multiple>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="plt-btn plt-btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="plt-btn plt-btn-primary" id="pltSaveExempt">Save Exemption</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function initOfficeSelect() {
        if (typeof $.fn.select2 === 'undefined') return;
        $('#pltOffices').select2({
            placeholder: 'Select offices (leave empty for all)',
            allowClear: true,
            width: 'resolve',
            dropdownAutoWidth: true,
            closeOnSelect: false,
            dropdownParent: $('#depositExemptModal')
        }).on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Type to search offices...');
        });
    }

    function initDepositTypeSelect() {
        if (typeof $.fn.select2 === 'undefined') return;
        $('#pltDepositType').select2({
            placeholder: 'Select deposit type (optional)',
            allowClear: true,
            width: 'resolve',
            dropdownAutoWidth: true,
            closeOnSelect: true,
            dropdownParent: $('#depositExemptModal')
        }).on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Type to search deposit types...');
        });
    }

    function destroyOfficeSelect() {
        if (typeof $.fn.select2 !== 'undefined') {
            if ($('#pltOffices').hasClass('select2-hidden-accessible')) {
                $('#pltOffices').select2('destroy');
            }
            if ($('#pltDepositType').hasClass('select2-hidden-accessible')) {
                $('#pltDepositType').select2('destroy');
            }
        }
    }

    window.openDepositExemptModal = function() {
        destroyOfficeSelect();
        $('#pltMonths').val('0');
        $('#pltDepositType').val(null);
        $('#pltOffices').val([]);
        $('#depositExemptModal').modal('show');
        initOfficeSelect();
        initDepositTypeSelect();
    };

    $('#depositExemptModal').on('hidden.bs.modal', function() {
        destroyOfficeSelect();
    });

    $('#pltSaveExempt').on('click', function() {
        var months = parseInt($('#pltMonths').val()) || 0;
        var depositTypeId = $('#pltDepositType').val() || null;
        var offices = $('#pltOffices').val();
        var officesStr = offices && offices.length ? offices.join(',') : null;

        $.ajax({
            url: '{{ route("settings.platform.block-skip.update-months") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                deposit_months_exempted: months,
                deposit_type_id: depositTypeId,
                offices: officesStr
            },
            success: function(response) {
                if (response.success) {
                    KiloAlert.success('Exemption saved successfully.');
                    $('#depositExemptModal').modal('hide');
                } else {
                    KiloAlert.error(response.message || 'Failed to save exemption.');
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred.';
                KiloAlert.error(msg);
            }
        });
    });
})();
</script>
