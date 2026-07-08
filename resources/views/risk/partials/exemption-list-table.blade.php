<div class="card" style="margin: 20px 0;">
    <div class="card-header" style="background: #667eea; color: #fff; padding: 12px 20px; font-weight: 600;">
        <i class="fa fa-list"></i> Deposit Month Exemptions
    </div>
    <div class="card-body" style="padding: 0;">
        @php
            $exemptions = \App\Models\DepositMonthExemption::with(['office', 'depositType'])->get();
            $grouped = $exemptions->groupBy('office_id');
        @endphp
        
        @if($exemptions->isEmpty())
            <p style="padding: 20px; color: #666; font-style: italic;">No exemptions configured.</p>
        @else
            <table class="table" style="margin: 0; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f7f8fc;">
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Office</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Deposit Types</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Exemptions</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Amount Exempted</th>
                        <!-- <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Amount Exempted</th> -->
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Months</th>
                        <th style="padding: 12px 15px; font-weight: 600; color: #333; border-bottom: 1px solid #e0e4ed;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grouped as $officeId => $officeExemptions)
                        @php
                            $office = $officeExemptions->first()->office;
                            $depositTypes = $officeExemptions->pluck('depositType.name')->map(function($name) {
                                return $name ?? 'All Types';
                            })->unique()->values()->toArray();
                            $totalExemptions = $officeExemptions->count();
                            $totalMonthsExcluded = $officeExemptions->sum('no_months_exclude');
                            $totalAmountExcluded = $officeExemptions->sum(function($ex) {
                                return ($ex->depositType->monthly_amount ?? 0) * $ex->no_months_exclude;
                            });
                        @endphp
                        <tr>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7; font-weight: 500;">
                                {{ $office->name ?? 'Unknown Office' }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                             
                                @foreach($depositTypes as $type)
                                    <span style="display: inline-block; background: #667eea; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 12px; margin: 2px; margin-right: 4px;">
                                        {{ $type }} 
                                    </span>
                                @endforeach
                            </td>
                            <!-- <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                {{ $totalExemptions }}
                            </td> -->
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                {{ $totalMonthsExcluded }}
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                K{{ number_format($totalAmountExcluded, 2) }}
                            </td>
                      
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                @php
                                    $allMonths = [];
                                    foreach($officeExemptions as $ex) {
                                        if(!empty($ex->months)) {
                                            $allMonths = array_merge($allMonths, $ex->months);
                                        }
                                    }
                                    $uniqueMonths = array_unique($allMonths);
                                @endphp
                                @if(!empty($uniqueMonths))
                                    @foreach($uniqueMonths as $m)
                                        <span style="display: inline-block; background: #667eea; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin: 2px; margin-right: 4px;">
                                            {{ ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][$m-1] ?? $m }}
                                        </span>
                                    @endforeach
                                @else
                                    <span style="color: #999;">—</span>
                                @endif
                            </td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #eef0f7;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="openEditExemptModal({{ $officeId }})">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<script>
var depositExemptModalInitialized = false;

function openEditExemptModal(officeId) {
    var exemptions = @json($grouped);
    if (exemptions[officeId]) {
        var firstExemption = exemptions[officeId][0];
        var months = firstExemption.months || [];
        var depositTypeId = firstExemption.deposit_type_id || null;
        
        $('#pltMonths').val(months).trigger('change');
        $('#pltYear').val('{{ date("Y") }}');
        if (depositTypeId) {
            $('#pltDepositType').val([depositTypeId]).trigger('change');
        } else {
            $('#pltDepositType').val(null).trigger('change');
        }
        $('#pltOffices').val([officeId]).trigger('change');
    }
    $('#depositExemptModal').modal('show');
}

$(document).ready(function() {
    if (!depositExemptModalInitialized) {
        $('#depositExemptModal').on('shown.bs.modal', function() {
            if (typeof $.fn.select2 !== 'undefined' && !$(this).hasClass('select2-modal-initialized')) {
                $(this).addClass('select2-modal-initialized');
                
                if ($('#pltMonths').hasClass('select2-hidden-accessible')) {
                    $('#pltMonths').select2('destroy');
                }
                $('#pltMonths').select2({
                    placeholder: 'Select months to exempt',
                    allowClear: true,
                    width: 'resolve',
                    dropdownAutoWidth: true,
                    closeOnSelect: false,
                    dropdownParent: $('#depositExemptModal')
                }).on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', 'Type to search months...');
                });
                
                if ($('#pltOffices').hasClass('select2-hidden-accessible')) {
                    $('#pltOffices').select2('destroy');
                }
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
                
                if ($('#pltDepositType').hasClass('select2-hidden-accessible')) {
                    $('#pltDepositType').select2('destroy');
                }
                $('#pltDepositType').select2({
                    placeholder: 'Select deposit types (optional)',
                    allowClear: true,
                    width: 'resolve',
                    dropdownAutoWidth: true,
                    closeOnSelect: false,
                    dropdownParent: $('#depositExemptModal')
                }).on('select2:open', function() {
                    $('.select2-search__field').attr('placeholder', 'Type to search deposit types...');
                });
            }
        });
        
        $('#depositExemptModal').on('hidden.bs.modal', function() {
            $(this).removeClass('select2-modal-initialized');
            if (typeof $.fn.select2 !== 'undefined') {
                if ($('#pltMonths').hasClass('select2-hidden-accessible')) {
                    $('#pltMonths').select2('destroy');
                }
                if ($('#pltOffices').hasClass('select2-hidden-accessible')) {
                    $('#pltOffices').select2('destroy');
                }
                if ($('#pltDepositType').hasClass('select2-hidden-accessible')) {
                    $('#pltDepositType').select2('destroy');
                }
            }
        });
        
        $('#pltSaveExempt').off('click').on('click', function() {
            var months = $('#pltMonths').val();
            var year = parseInt($('#pltYear').val()) || {{ date('Y') }};
            var depositTypeIds = $('#pltDepositType').val();
            var depositTypeIdStr = depositTypeIds 
                ? (Array.isArray(depositTypeIds) ? depositTypeIds.join(',') : depositTypeIds) 
                : null;
            var offices = $('#pltOffices').val();
            var officesStr = offices && offices.length ? (Array.isArray(offices) ? offices.join(',') : offices) : null;
            
            if (!months || !months.length) {
                KiloAlert.error('Please select at least one month.');
                return;
            }
            
            $.ajax({
                url: '{{ route("settings.platform.block-skip.update-months") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    months: months,
                    year: year,
                    deposit_type_id: depositTypeIdStr,
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
        
        depositExemptModalInitialized = true;
    }
});
</script>

<div class="modal fade" id="depositExemptModal" tabindex="-1" role="dialog" aria-labelledby="depositExemptModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="depositExemptModalLabel">Edit Exemption Months</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="pltMonths">Select Months to Exempt</label>
                    <select id="pltMonths" class="form-control" multiple style="height: 150px;">
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
                <div class="form-group">
                    <label for="pltDepositType">Select Deposit Type (optional)</label>
                    <select id="pltDepositType" class="form-control">
                        <option value="">All deposit types</option>
                        @foreach(\App\Models\DepositType::orderBy('name')->get() as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="pltOffices">Select Offices</label>
                    <select id="pltOffices" class="form-control" multiple style="height: 120px;">
                        @foreach(\App\Models\Office::orderBy('name')->get() as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="pltSaveExempt">Save Exemption</button>
            </div>
        </div>
    </div>
</div>