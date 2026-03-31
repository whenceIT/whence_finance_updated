@extends('layouts.master')
@section('title')
    District Regionals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">District Regionals</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#createDistrictRegionalModal">
                    Add District Regional
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($districtRegionals as $districtRegional)
                        <tr>
                            <td>{{ $districtRegional->name }}</td>
                            <td>{{ $districtRegional->district->name ?? 'N/A' }}</td>
                            <td>{{ $districtRegional->province->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-navicon"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li>
                                                <a href="{{ url('district-regionals/'.$districtRegional->id) }}">
                                                    <i class="fa fa-search"></i>
                                                    View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('district-regionals/'.$districtRegional->id.'/edit') }}">
                                                    <i class="fa fa-edit"></i>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ url('district-regionals/'.$districtRegional->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger" style="width: 100%; text-align: left; background: none; border: none; padding: 8px 16px;" onclick="return confirm('Are you sure you want to delete this district regional?')">
                                                        <i class="fa fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create District Regional Modal -->
    <div class="modal fade" id="createDistrictRegionalModal" tabindex="-1" role="dialog" aria-labelledby="createDistrictRegionalModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="createDistrictRegionalModalLabel">Create District Regionals</h4>
                </div>
                <form method="POST" action="{{ url('district-regionals') }}" id="createDistrictRegionalForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="province_id">Province</label>
                            <select class="form-control" id="province_id" name="province_id" required>
                                <option value="">Select Province</option>
                                @foreach(\App\Models\Province::all() as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="district_id">District</label>
                            <select class="form-control" id="district_id" name="district_id" required>
                                <option value="">Select District</option>
                                @foreach(\App\Models\District::with('province')->get() as $district)
                                    <option value="{{ $district->id }}" data-province="{{ $district->province_id }}">{{ $district->name }} ({{ $district->province->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>District Regional Names</label>
                            <div id="district-regionals-container">
                                <div class="input-group district-regional-input-group" style="margin-bottom: 10px;">
                                    <input type="text" class="form-control district-regional-name" name="district_regional_names[]" placeholder="Enter district regional name" required>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-danger remove-district-regional" style="display: none;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-district-regional">
                                <i class="fa fa-plus"></i> Add Another District Regional
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create District Regionals</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    // Filter districts based on selected province
    $('#province_id').change(function() {
        var selectedProvince = $(this).val();
        var districtSelect = $('#district_id');

        if (selectedProvince) {
            districtSelect.find('option').each(function() {
                var provinceId = $(this).data('province');
                if (provinceId == selectedProvince || $(this).val() == '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            districtSelect.val('');
        } else {
            districtSelect.find('option').show();
        }
    });

    // Add new district regional input
    $('#add-district-regional').click(function() {
        var districtRegionalInput = `
            <div class="input-group district-regional-input-group" style="margin-bottom: 10px;">
                <input type="text" class="form-control district-regional-name" name="district_regional_names[]" placeholder="Enter district regional name" required>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-danger remove-district-regional">
                        <i class="fa fa-trash"></i>
                    </button>
                </span>
            </div>
        `;
        $('#district-regionals-container').append(districtRegionalInput);
        updateRemoveButtons();
    });

    // Remove district regional input
    $(document).on('click', '.remove-district-regional', function() {
        $(this).closest('.district-regional-input-group').remove();
        updateRemoveButtons();
    });

    // Update remove button visibility
    function updateRemoveButtons() {
        var inputGroups = $('.district-regional-input-group');
        if (inputGroups.length > 1) {
            $('.remove-district-regional').show();
        } else {
            $('.remove-district-regional').hide();
        }
    }

    // Initialize remove buttons
    updateRemoveButtons();
});
</script>
@endsection