<style>
.plt-modal { padding: 20px; }
.plt-modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
.plt-modal-title { margin: 0; font-size: 16px; font-weight: 600; color: #333; }
.plt-form-row { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
.plt-form-group { flex: 1 1 200px; display: flex; flex-direction: column; min-width: 200px; }
.plt-form-group label { font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #555; }
.plt-form-group input, .plt-form-group select, .plt-form-group textarea { 
    padding: 8px 10px; 
    font-size: 13px; 
    border: 1px solid #ccc; 
    border-radius: 4px; 
    width: 100%; 
    box-sizing: border-box;
}
.plt-form-group textarea { min-height: 80px; resize: vertical; }
.plt-form-full { flex: 1 1 100%; }
.plt-modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 12px; border-top: 1px solid #eee; }
.plt-btn { padding: 8px 16px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer; }
.plt-btn-primary { background: #3c8dbc; color: #fff; }
.plt-btn-secondary { background: #95a5a6; color: #fff; }
.plt-office-select { width: 100% !important; max-width: 100%; }
</style>

@include('components.kilo-alert')
<div class="modal fade" id="depositExemptModal" tabindex="-1" role="dialog" aria-labelledby="depositExemptModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="depositExemptModalLabel">Edit Exemption Months</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body plt-modal">
                <input type="hidden" id="pltEditId" value="">
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltMonths">Select Months to Exempt</label>
                        <select id="pltMonths" class="plt-office-select" name="months[]" multiple>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltYear">Year</label>
                        <input type="number" id="pltYear" min="{{ date('Y') }}" max="{{ date('Y') + 10 }}" value="{{ date('Y') }}">
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltDepositType">Select Deposit Type (optional)</label>
                        <select id="pltDepositType" class="plt-office-select" name="deposit_type_id[]" multiple>
                            <option value="">All deposit types</option>
                            @foreach($depositTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltOffices">Select Offices</label>
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
    function initSelect2() {
        if (typeof $.fn.select2 === 'undefined') return;
        
        $('#pltMonths').select2({
            placeholder: 'Select months to exempt',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#depositExemptModal .modal-body')
        }).on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Type to search months...');
        });

        $('#pltOffices').select2({
            placeholder: 'Select offices',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#depositExemptModal .modal-body')
        }).on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Type to search offices...');
        });

        $('#pltDepositType').select2({
            placeholder: 'Select deposit types (optional)',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#depositExemptModal .modal-body')
        }).on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Type to search deposit types...');
        });
    }

    function destroySelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            if ($('#pltMonths').hasClass('select2-hidden-accessible')) $('#pltMonths').select2('destroy');
            if ($('#pltOffices').hasClass('select2-hidden-accessible')) $('#pltOffices').select2('destroy');
            if ($('#pltDepositType').hasClass('select2-hidden-accessible')) $('#pltDepositType').select2('destroy');
        }
    }

    window.openEditExemptModal = function(exemptionData) {
        var exemption = {};
        if (exemptionData) {
            if (typeof exemptionData === 'string') {
                try { exemption = JSON.parse(exemptionData); } catch(e) { exemption = {}; }
            } else if (typeof exemptionData === 'object') {
                exemption = exemptionData;
            }
        }
        
        destroySelect2();
        $('#pltEditId').val(exemption.id || '');
        $('#pltMonths').val(exemption.months || []).trigger('change');
        $('#pltYear').val(exemption.year || {{ date('Y') }});
        
        if (exemption.deposit_type_id) {
            $('#pltDepositType').val([exemption.deposit_type_id]).trigger('change');
        } else {
            $('#pltDepositType').val(null).trigger('change');
        }
        
        if (exemption.office_id) {
            $('#pltOffices').val([exemption.office_id]).trigger('change');
        } else {
            $('#pltOffices').val(null).trigger('change');
        }
        
        $('#depositExemptModal').modal('show');
    };

    $('#depositExemptModal').on('shown.bs.modal', function() {
        if (!$(this).hasClass('select2-modal-initialized')) {
            $(this).addClass('select2-modal-initialized');
            initSelect2();
        }
    });

    $('#depositExemptModal').on('hidden.bs.modal', function() {
        destroySelect2();
        $(this).removeClass('select2-modal-initialized');
        $('#pltEditId').val('');
    });

    $('#pltSaveExempt').on('click', function() {
        var exemptionId = $('#pltEditId').val();
        var months = $('#pltMonths').val() || [];
        var year = parseInt($('#pltYear').val()) || {{ date('Y') }};
        var depositTypeIds = $('#pltDepositType').val() || [];
        var offices = $('#pltOffices').val() || [];

        if (!months.length) {
            KiloAlert.error('Please select at least one month.');
            return;
        }

        var url = exemptionId
            ? '{{ route("deposit-month-exemptions.update", ":id") }}'.replace(':id', exemptionId)
            : '{{ route("settings.platform.block-skip.update-months") }}';
        var method = exemptionId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: {
                _token: '{{ csrf_token() }}',
                months: months,
                year: year,
                deposit_type_id: depositTypeIds.length ? depositTypeIds.join(',') : null,
                offices: offices.length ? offices.join(',') : null
            },
            success: function(response) {
                if (response.success) {
                    KiloAlert.success('Exemption saved successfully.');
                    $('#depositExemptModal').modal('hide');
                    window.location.reload();
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