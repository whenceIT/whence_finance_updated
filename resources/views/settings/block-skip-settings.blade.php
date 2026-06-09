@extends('layouts.master')

@section('title')
    Block Skip Settings
@endsection

@section('content')
<div class="container-fluid" style="padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('risk.branch-deposit-audit') }}" class="btn btn-secondary btn-sm" style="border-radius: 6px;">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <div>
                    <h2 style="margin: 0; font-size: 24px; font-weight: 600; color: #1a1a2e;">
                        <i class="fa fa-unlock" style="margin-right: 8px; color: #007bff;"></i>
                        Block Skip Settings
                    </h2>
                    <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">
                        Configure which deposit types are exempt from mandatory payment requirements for each office
                    </p>
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" id="initializeAllBtn" class="btn btn-success btn-sm" style="border-radius: 6px;">
                    <i class="fa fa-check-circle"></i> Activate for All Offices
                </button>
                <button type="button" id="deactivateAllBtn" class="btn btn-warning btn-sm" style="border-radius: 6px;">
                    <i class="fa fa-ban"></i> Remove for All Offices
                </button>
            </div>
        </div>

        <div style="background: #fff; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #374151;">Office Settings</h3>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="position: relative;">
                        <i class="fa fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" id="searchOffice" placeholder="Search office..." style="padding: 6px 12px 6px 32px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px; width: 200px;">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" style="margin: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Office</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Code</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Admin</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Building</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Statutory</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Set up debt</th>
                            <th style="padding: 12px 16px; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;"></th>
                        </tr>
                    </thead>
                    <tbody id="settingsTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editSettingsModal" tabindex="-1" role="dialog" aria-labelledby="editSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editSettingsModalLabel">
                    <i class="fa fa-unlock"></i> Block Skip Settings
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="blockSkipForm">
                    <input type="hidden" id="blockSkipId" value="">
                    <div class="form-group">
                        <label>Office</label>
                        <input type="text" id="blockSkipOfficeName" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Admin</label>
                        <div>
                            <label class="radio-inline"><input type="radio" name="admin" value="1" id="bs_admin_1"> Disable</label>
                            <label class="radio-inline"><input type="radio" name="admin" value="0" id="bs_admin_0"> Enable</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Building</label>
                        <div>
                            <label class="radio-inline"><input type="radio" name="building" value="1" id="bs_building_1"> Disable</label>
                            <label class="radio-inline"><input type="radio" name="building" value="0" id="bs_building_0"> Enable</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Statutory</label>
                        <div>
                            <label class="radio-inline"><input type="radio" name="statutory" value="1" id="bs_statutory_1"> Disable</label>
                            <label class="radio-inline"><input type="radio" name="statutory" value="0" id="bs_statutory_0"> Enable</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Set up debt</label>
                        <div>
                            <label class="radio-inline"><input type="radio" name="set_up_debt" value="1" id="bs_set_up_debt_1"> Disable</label>
                            <label class="radio-inline"><input type="radio" name="set_up_debt" value="0" id="bs_set_up_debt_0"> Enable</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let settingsData = @json($officeSettings);
    
    function renderTable(data) {
        var html = '';
        data.forEach(function(o) {
            var adminStatus = o.admin ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>';
            var buildingStatus = o.building ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>';
            var statutoryStatus = o.statutory ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>';
            var setUpDebtStatus = o.set_up_debt ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>';
            
            html += '<tr>' +
                '<td style="padding: 12px 16px;">' + o.office_name + '</td>' +
                '<td style="padding: 12px 16px; color: #6b7280;">' + o.office_code + '</td>' +
                '<td style="padding: 12px 16px;">' + adminStatus + '</td>' +
                '<td style="padding: 12px 16px;">' + buildingStatus + '</td>' +
                '<td style="padding: 12px 16px;">' + statutoryStatus + '</td>' +
                '<td style="padding: 12px 16px;">' + setUpDebtStatus + '</td>' +
                '<td style="padding: 12px 16px;"><button class="btn btn-sm btn-primary edit-btn" data-id="' + o.office_id + '" style="border-radius: 4px;">Edit</button></td>' +
                '</tr>';
        });
        $('#settingsTableBody').html(html);
    }
    
    renderTable(settingsData);
    
    $('#searchOffice').on('input', function() {
        var term = $(this).val().toLowerCase();
        var filtered = settingsData.filter(function(o) {
            return o.office_name.toLowerCase().includes(term) || o.office_code.toLowerCase().includes(term);
        });
        renderTable(filtered);
    });
    
    $(document).on('click', '.edit-btn', function() {
        var officeId = $(this).data('id');
        var data = settingsData.find(function(o) { return o.office_id === officeId; });
        if (data) {
            $('#blockSkipId').val(data.id || '');
            $('#blockSkipOfficeName').val(data.office_name);
            $('input[name="admin"][value="' + (data.admin ? '0' : '1') + '"]').prop('checked', true);
            $('input[name="building"][value="' + (data.building ? '0' : '1') + '"]').prop('checked', true);
            $('input[name="statutory"][value="' + (data.statutory ? '0' : '1') + '"]').prop('checked', true);
            $('input[name="set_up_debt"][value="' + (data.set_up_debt ? '0' : '1') + '"]').prop('checked', true);
            $('#editSettingsModal').modal('show');
        }
    });
    
    $('#blockSkipForm').on('submit', function(e) {
        e.preventDefault();
        var data = {
            _token: '{{ csrf_token() }}',
            id: $('#blockSkipId').val(),
            office_id: settingsData.find(function(o) { return o.office_name === $('#blockSkipOfficeName').val(); })?.office_id,
            admin: $('input[name="admin"]:checked').val() || 0,
            building: $('input[name="building"]:checked').val() || 0,
            statutory: $('input[name="statutory"]:checked').val() || 0,
            set_up_debt: $('input[name="set_up_debt"]:checked').val() || 0,
        };
        $.post('/settings/platform/block-skip/save', data, function(res) {
            alert(res.message || 'Saved');
            location.reload();
        }).fail(function() {
            alert('Save failed.');
        });
    });
    
    $('#initializeAllBtn').on('click', function() {
        if (!confirm('Initialize block skip settings for all offices with default values (enabled)?')) return;
        $.post('/settings/platform/block-skip/initialize-all', {
            _token: '{{ csrf_token() }}'
        }, function(res) {
            alert(res.message || 'Initialized');
            location.reload();
        }).fail(function() {
            alert('Failed to initialize.');
        });
    });
    
    $('#deactivateAllBtn').on('click', function() {
        if (!confirm('Deactivate block skip settings for all offices? This will remove all custom settings.')) return;
        $.post('/settings/platform/block-skip/deactivate-all', {
            _token: '{{ csrf_token() }}'
        }, function(res) {
            alert(res.message || 'Deactivated');
            location.reload();
        }).fail(function() {
            alert('Failed to deactivate.');
        });
    });
});
</script>

@endsection