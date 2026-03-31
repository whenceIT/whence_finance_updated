@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit', 1) }} {{ trans_choice('general.branch', 1) }}
@endsection
@section('content')
    @php
    $breadcrumb = [
        ['label' => trans_choice('general.branch', 2), 'url' => url('office')],
        ['label' => trans_choice('general.edit', 1) . ' ' . trans_choice('general.branch', 1), 'url' => '']
    ];
    @endphp
    @include('partials.breadcrumb')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.edit', 1) }} {{ trans_choice('general.branch', 1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel', 1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('office/' . $office->id . '/update')}}" class="form-horizontal"
            enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name" class="control-label col-md-3">{{trans_choice('general.name', 1)}}</label>
                    <div class="col-md-9">
                        <input type="text" name="name" class="form-control" value="{{$office->name}}" required id="name">
                    </div>

                </div>
                <div class="form-group">
                    <label for="external_id"
                        class="control-label col-md-3">{{trans_choice('general.external_id', 1)}}</label>
                    <div class="col-md-9">
                        <input type="text" name="external_id" class="form-control" value="{{$office->external_id}}" required
                            id="external_id">
                    </div>

                </div>
                <div class="form-group">
                    <label for="branch_capacity" class="control-label col-md-3">Branch Capacity</label>
                    <div class="col-md-9">
                        <input type="number" name="branch_capacity" class="form-control"
                            value="{{$office->branch_capacity}}" id="branch_capacity">
                    </div>
                </div>
                <div class="form-group">
                    <label for="province_id" class="control-label col-md-3">Province</label>
                    <div class="col-md-9">
                        <select name="province_id" class="form-control select2" id="province_id">
                            <option></option>
                            @foreach(\App\Models\Province::all() as $key)
                                <option value="{{$key->id}}" @if($office->province_id == $key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="district_id" class="control-label col-md-3">District</label>
                    <div class="col-md-9">
                        <select name="district_id" class="form-control select2" id="district_id">
                            <option></option>
                            @foreach(\App\Models\District::with('province')->get() as $district)
                                <option value="{{$district->id}}" data-province="{{$district->province_id}}" @if($office->district_id == $district->id) selected @endif>
                                    {{$district->name}} ({{$district->province->name ?? 'N/A'}})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="district_regional_id" class="control-label col-md-3">District Regional</label>
                    <div class="col-md-9">
                        <select name="district_regional_id" class="form-control select2" id="district_regional_id">
                            <option></option>
                            @foreach(\App\Models\DistrictRegional::with(['district', 'province'])->get() as $districtRegional)
                                <option value="{{$districtRegional->id}}" data-district="{{$districtRegional->district_id}}" data-province="{{$districtRegional->province_id}}" @if($office->district_regional_id == $districtRegional->id) selected @endif>
                                    {{$districtRegional->name}} ({{$districtRegional->district->name ?? 'N/A'}} - {{$districtRegional->province->name ?? 'N/A'}})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="parent_id" class="control-label col-md-3">{{trans_choice('general.parent', 1)}}
                        {{trans_choice('general.branch', 1)}}</label>
                    <div class="col-md-9">
                        <select name="parent_id" class="form-control select2" id="parent_id" @if($office->default_office != 1)
                        required @endif>
                            <option></option>
                            @foreach(\App\Models\Office::where('id', '!=', $office->id)->get() as $key)
                                <option value="{{$key->id}}" @if($office->parent_id == $key->id) selected @endif>{{$key->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save', 1)}}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $(".form-horizontal").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                }
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

        // District and District Regional filtering
        $('#province_id').change(function() {
            var selectedProvince = $(this).val();
            var districtSelect = $('#district_id');
            var districtRegionalSelect = $('#district_regional_id');

            if (selectedProvince) {
                // Filter districts by province
                districtSelect.find('option').each(function() {
                    var provinceId = $(this).data('province');
                    if (provinceId == selectedProvince || $(this).val() == '') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                // Filter district regionals by province
                districtRegionalSelect.find('option').each(function() {
                    var provinceId = $(this).data('province');
                    if (provinceId == selectedProvince || $(this).val() == '') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                // Clear selections if they don't match the new province
                var selectedDistrictProvince = districtSelect.find('option:selected').data('province');
                if (selectedDistrictProvince && selectedDistrictProvince != selectedProvince) {
                    districtSelect.val('');
                }

                var selectedRegionalProvince = districtRegionalSelect.find('option:selected').data('province');
                if (selectedRegionalProvince && selectedRegionalProvince != selectedProvince) {
                    districtRegionalSelect.val('');
                }
            } else {
                districtSelect.find('option').show();
                districtRegionalSelect.find('option').show();
            }

            // Trigger select2 update
            districtSelect.trigger('change.select2');
            districtRegionalSelect.trigger('change.select2');
        });

        $('#district_id').change(function() {
            var selectedDistrict = $(this).val();
            var districtRegionalSelect = $('#district_regional_id');

            if (selectedDistrict) {
                // Filter district regionals by district
                districtRegionalSelect.find('option').each(function() {
                    var districtId = $(this).data('district');
                    if (districtId == selectedDistrict || $(this).val() == '') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                // Clear selection if it doesn't match the new district
                var selectedRegionalDistrict = districtRegionalSelect.find('option:selected').data('district');
                if (selectedRegionalDistrict && selectedRegionalDistrict != selectedDistrict) {
                    districtRegionalSelect.val('');
                }
            } else {
                // If no district selected, show all district regionals for the selected province
                var selectedProvince = $('#province_id').val();
                if (selectedProvince) {
                    districtRegionalSelect.find('option').each(function() {
                        var provinceId = $(this).data('province');
                        if (provinceId == selectedProvince || $(this).val() == '') {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                } else {
                    districtRegionalSelect.find('option').show();
                }
            }

            // Trigger select2 update
            districtRegionalSelect.trigger('change.select2');
        });

        // Initialize filtering on page load
        $('#province_id').trigger('change');
        $('#district_id').trigger('change');
    </script>
@endsection