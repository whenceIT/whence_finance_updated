@extends('layouts.master')
@section('title')
    Districts
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Districts</h3>
            <div class="box-tools pull-right">
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#createDistrictModal">
                        Add District
                    </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Province</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($districts as $district)
                        <tr>
                            <td>{{ $district->name }}</td>
                            <td>{{ $district->province->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-navicon"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li>
                                            <a href="{{ url('districts/'.$district->id) }}">
                                                <i class="fa fa-search"></i>
                                                View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('districts/'.$district->id.'/edit') }}">
                                                <i class="fa fa-edit"></i>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ url('districts/'.$district->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger" style="width: 100%; text-align: left; background: none; border: none; padding: 8px 16px;" onclick="return confirm('Are you sure you want to delete this district?')">
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

    <!-- Create District Modal -->
    <div class="modal fade" id="createDistrictModal" tabindex="-1" role="dialog" aria-labelledby="createDistrictModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="createDistrictModalLabel">Create Districts</h4>
                </div>
                <form method="POST" action="{{ url('districts') }}" id="createDistrictForm">
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
                            <label>District Names</label>
                            <div id="districts-container">
                                <div class="input-group district-input-group" style="margin-bottom: 10px;">
                                    <input type="text" class="form-control district-name" name="district_names[]" placeholder="Enter district name" required>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-danger remove-district" style="display: none;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-district">
                                <i class="fa fa-plus"></i> Add Another District
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Districts</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    // Add new district input
    $('#add-district').click(function() {
        var districtInput = `
            <div class="input-group district-input-group" style="margin-bottom: 10px;">
                <input type="text" class="form-control district-name" name="district_names[]" placeholder="Enter district name" required>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-danger remove-district">
                        <i class="fa fa-trash"></i>
                    </button>
                </span>
            </div>
        `;
        $('#districts-container').append(districtInput);
        updateRemoveButtons();
    });

    // Remove district input
    $(document).on('click', '.remove-district', function() {
        $(this).closest('.district-input-group').remove();
        updateRemoveButtons();
    });

    // Update remove button visibility
    function updateRemoveButtons() {
        var inputGroups = $('.district-input-group');
        if (inputGroups.length > 1) {
            $('.remove-district').show();
        } else {
            $('.remove-district').hide();
        }
    }

    // Initialize remove buttons
    updateRemoveButtons();
});
</script>
@endsection