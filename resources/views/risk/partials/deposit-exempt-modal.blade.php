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

    .plt-checkbox-toggle {
        width: 100%;
        padding: 8px 10px;
        font-size: 13px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fff;
        cursor: pointer;
        text-align: left;
        box-sizing: border-box;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 38px;
    }
    .plt-checkbox-toggle:hover { border-color: #3c8dbc; }
    .plt-checkbox-toggle:focus { outline: none; border-color: #3c8dbc; box-shadow: 0 0 0 2px rgba(60, 141, 188, 0.2); }
    .plt-checkbox-toggle.has-selection { color: #333; font-weight: 500; }
    .plt-checkbox-panel {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-height: 250px;
        overflow-y: auto;
        margin-top: 4px;
        z-index: 1070;
    }
    .plt-checkbox-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 13px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.1s ease;
    }
    .plt-checkbox-item:last-child { border-bottom: none; }
    .plt-checkbox-item:hover { background: #f5f5f5; }
    .plt-checkbox-item input[type="checkbox"] { margin-right: 10px; cursor: pointer; }
    .plt-checkbox-item input[type="checkbox"]:checked + span { color: #3c8dbc; font-weight: 600; }
    .plt-checkbox-arrow { transition: transform 0.2s ease; display: inline-block; }
    .plt-checkbox-arrow.open { transform: rotate(180deg); }
    .plt-selection-summary { margin-top: 12px; padding: 12px; background: #f9f9f9; border: 1px solid #eee; border-radius: 4px; font-size: 12px; color: #555; }
    .plt-selection-summary .plt-summary-row { margin-bottom: 4px; }
    .plt-selection-summary .plt-summary-row:last-child { margin-bottom: 0; }
    .plt-summary-label { font-weight: 600; color: #333; }
    .plt-summary-values { color: #3c8dbc; }
    .plt-summary-empty { color: #999; font-style: italic; }

    .modal-backdrop.show { z-index: 1050 !important; }
    #depositExemptModal { z-index: 1060 !important; }
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
                        <label>Select Months to Exempt</label>
                        <div id="pltMonthsDropdown" class="plt-checkbox-dropdown" style="position: relative;">
                            <button type="button" id="pltMonthsToggle" class="plt-checkbox-toggle" style="width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; background: #fff; cursor: pointer; text-align: left; box-sizing: border-box;">
                                <span id="pltMonthsDisplay">Select months to exempt</span>
                                <span style="float: right; margin-top: 2px;"><i class="fa fa-caret-down"></i></span>
                            </button>
                            <div id="pltMonthsDropdownPanel" class="plt-checkbox-panel" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1070; max-height: 250px; overflow-y: auto; margin-top: 4px;">
                                @foreach(range(1, 12) as $monthNum)
                                    <label class="plt-checkbox-item" style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'">
                                        <input type="checkbox" name="months[]" value="{{ $monthNum }}" style="margin-right: 10px;" data-month="{{ $monthNum }}">
                                        <span>{{ ['January','February','March','April','May','June','July','August','September','October','November','December'][$monthNum - 1] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" id="pltMonthsHidden" name="months_hidden" value="">
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
                        <div id="pltDepositTypeDropdown" class="plt-checkbox-dropdown" style="position: relative;">
                            <button type="button" id="pltDepositTypeToggle" class="plt-checkbox-toggle">
                                <span id="pltDepositTypeDisplay">Select deposit types (optional)</span>
                                <span style="float: right; margin-top: 2px;"><i class="fa fa-caret-down"></i></span>
                            </button>
                            <div id="pltDepositTypeDropdownPanel" class="plt-checkbox-panel">
                                <label class="plt-checkbox-item" style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;" data-value="">
                                    <input type="checkbox" name="deposit_type_id[]" value="" style="margin-right: 10px;">
                                    <span>All deposit types</span>
                                </label>
                                @foreach($depositTypes as $type)
                                    <label class="plt-checkbox-item" style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;" data-value="{{ $type->id }}">
                                        <input type="checkbox" name="deposit_type_id[]" value="{{ $type->id }}" style="margin-right: 10px;">
                                        <span>{{ $type->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltOffices">Select Offices</label>
                        <div id="pltOfficesDropdown" class="plt-checkbox-dropdown" style="position: relative;">
                            <button type="button" id="pltOfficesToggle" class="plt-checkbox-toggle">
                                <span id="pltOfficesDisplay">Select offices</span>
                                <span style="float: right; margin-top: 2px;"><i class="fa fa-caret-down"></i></span>
                            </button>
                            <div id="pltOfficesDropdownPanel" class="plt-checkbox-panel">
                                @foreach($offices as $office)
                                    <label class="plt-checkbox-item" style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f0f0f0;">
                                        <input type="checkbox" name="offices[]" value="{{ $office->id }}" style="margin-right: 10px;">
                                        <span>{{ $office->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Selection values -->
                <div class="plt-selection-summary">
                    <div class="plt-summary-row">
                        <span class="plt-summary-label">Months:</span>
                        <span class="plt-summary-values" id="pltSummaryMonths"><span class="plt-summary-empty">None selected</span></span>
                    </div>
                    <div class="plt-summary-row">
                        <span class="plt-summary-label">Deposit Types:</span>
                        <span class="plt-summary-values" id="pltSummaryDepositTypes"><span class="plt-summary-empty">None selected</span></span>
                    </div>
                    <div class="plt-summary-row">
                        <span class="plt-summary-label">Offices:</span>
                        <span class="plt-summary-values" id="pltSummaryOffices"><span class="plt-summary-empty">None selected</span></span>
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
    function setupCheckboxDropdown(dropdownId, toggleId, panelId, displayId, hiddenId) {
        var $toggle = $('#' + toggleId);
        var $panel = $('#' + panelId);
        var $display = $('#' + displayId);
        var $dropdown = $('#' + dropdownId);
        var isOpen = false;

        $toggle.on('click', function(e) {
            e.stopPropagation();
            isOpen = !isOpen;
            $panel.css('display', isOpen ? 'block' : 'none');
            $toggle.find('.plt-checkbox-arrow').toggleClass('open', isOpen);
            if (isOpen) {
                $dropdown.css('z-index', 1070);
            }
        });

        $panel.on('click', function(e) { e.stopPropagation(); });

        $(document).on('click', function() {
            if (isOpen) {
                isOpen = false;
                $panel.css('display', 'none');
                $toggle.find('.plt-checkbox-arrow').removeClass('open');
            }
        });

        $dropdown.on('change', 'input[type="checkbox"]', function() {
            var $this = $(this);
            if ($this.val() === '') {
                var $allCb = $panel.find('input[type="checkbox"]');
                $allCb.not($this).prop('checked', $this.prop('checked'));
            }
            updateDisplay();
        });

        function updateDisplay() {
            var $checked = $panel.find('input[type="checkbox"]:not([value=""]):checked');
            if ($checked.length === 0) {
                $display.text($toggle.data('placeholder') || 'Select items');
            } else {
                $display.text($checked.map(function() { return $(this).next('span').text(); }).get().join(', '));
            }
            updateSummary();
        }

        function getValues() {
            return $panel.find('input[type="checkbox"]:not([value=""]):checked').map(function() { return $(this).val(); }).get();
        }

        function setValues(values) {
            var vals = values || [];
            var strVals = vals.map(String);
            $panel.find('input[type="checkbox"]').each(function() {
                var $cb = $(this);
                $cb.prop('checked', strVals.indexOf($cb.val()) !== -1);
            });
            updateDisplay();
        }

        function clearValues() {
            $panel.find('input[type="checkbox"]').prop('checked', false);
            updateDisplay();
        }

        return {
            getValues: getValues,
            setValues: setValues,
            clearValues: clearValues,
            isOpen: function() { return isOpen; }
        };
    }

    function updateSummary() {
        var months = monthsDropdown.getValues();
        var dts = depositTypeDropdown.getValues();
        var offs = officesDropdown.getValues();

        $('#pltSummaryMonths').html(months.length
            ? months.map(function(m) { return ['January','February','March','April','May','June','July','August','September','October','November','December'][parseInt(m)-1] || m; }).join(', ')
            : '<span class="plt-summary-empty">None selected</span>');

        $('#pltSummaryDepositTypes').html(dts.length
            ? dts.join(', ')
            : '<span class="plt-summary-empty">None selected</span>');

        $('#pltSummaryOffices').html(offs.length
            ? offs.join(', ')
            : '<span class="plt-summary-empty">None selected</span>');
    }

    var monthsDropdown = setupCheckboxDropdown('pltMonthsDropdown', 'pltMonthsToggle', 'pltMonthsDropdownPanel', 'pltMonthsDisplay', 'pltMonthsHidden');
    var depositTypeDropdown = setupCheckboxDropdown('pltDepositTypeDropdown', 'pltDepositTypeToggle', 'pltDepositTypeDropdownPanel', 'pltDepositTypeDisplay', null);
    var officesDropdown = setupCheckboxDropdown('pltOfficesDropdown', 'pltOfficesToggle', 'pltOfficesDropdownPanel', 'pltOfficesDisplay', null);

    window.openEditExemptModal = function(exemptionData) {
        var exemption = {};
        if (exemptionData) {
            if (typeof exemptionData === 'string') {
                try { exemption = JSON.parse(exemptionData); } catch(e) { exemption = {}; }
            } else if (typeof exemptionData === 'object') {
                exemption = exemptionData;
            }
        }

        var months = exemption.months || [];
        if (months.length && typeof months[0] === 'string') {
            months = months.map(function(m) { return parseInt(m, 10); });
        }
        monthsDropdown.setValues(months);

        $('#pltYear').val(exemption.year || {{ date('Y') }});

        var dtIds = exemption.deposit_type_id ? (typeof exemption.deposit_type_id === 'string' ? exemption.deposit_type_id.split(',') : exemption.deposit_type_id) : [];
        depositTypeDropdown.setValues(dtIds);

        var offIds = exemption.office_id ? (typeof exemption.office_id === 'string' ? exemption.office_id.split(',') : exemption.office_id) : [];
        officesDropdown.setValues(offIds);

        $('#pltEditId').val(exemption.id || '');
        $('#depositExemptModal').modal('show');
    };

    $('#depositExemptModal').on('shown.bs.modal', function() {
        // Values are set by openEditExemptModal; no additional clearing needed
    });

    $('#depositExemptModal').on('hidden.bs.modal', function() {
        $('#pltEditId').val('');
        monthsDropdown.clearValues();
        depositTypeDropdown.clearValues();
        officesDropdown.clearValues();
        $('#pltYear').val('{{ date('Y') }}');
    });

    $('#pltSaveExempt').on('click', function() {
        var exemptionId = $('#pltEditId').val();
        var months = monthsDropdown.getValues();
        var year = parseInt($('#pltYear').val()) || {{ date('Y') }};
        var depositTypeIds = depositTypeDropdown.getValues();
        var offices = officesDropdown.getValues();

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