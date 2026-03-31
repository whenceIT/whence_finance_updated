@extends('layouts.master')
@section('title')
    Edit District Regional
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Edit District Regional</h3>
        </div>
        <form method="POST" action="{{ url('district-regionals/'.$districtRegional->id) }}">
            @csrf
            @method('PUT')
            <div class="box-body">
                <div class="form-group">
                    <label for="province_id">Province</label>
                    <select class="form-control" id="province_id" name="province_id" required>
                        <option value="">Select Province</option>
                        @foreach(\App\Models\Province::all() as $province)
                            <option value="{{ $province->id }}" {{ $districtRegional->province_id == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="district_id">District</label>
                    <select class="form-control" id="district_id" name="district_id" required>
                        <option value="">Select District</option>
                        @foreach(\App\Models\District::with('province')->get() as $district)
                            <option value="{{ $district->id }}" data-province="{{ $district->province_id }}" {{ $districtRegional->district_id == $district->id ? 'selected' : '' }}>
                                {{ $district->name }} ({{ $district->province->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">District Regional Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $districtRegional->name }}" required>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update District Regional</button>
                <a href="{{ url('district-regionals') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
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
            // If current selected district's province doesn't match, clear selection
            var selectedDistrictProvince = districtSelect.find('option:selected').data('province');
            if (selectedDistrictProvince && selectedDistrictProvince != selectedProvince) {
                districtSelect.val('');
            }
        } else {
            districtSelect.find('option').show();
        }
    });

    // Initialize on page load
    $('#province_id').trigger('change');
});
</script>
@endsection