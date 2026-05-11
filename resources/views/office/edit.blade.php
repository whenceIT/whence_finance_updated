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
    <label for="withinhere_branch_id" class="control-label col-md-3">
        Withinhere Branch
    </label>
    <div class="col-md-9">
        <select name="withinhere_branch_id"
            class="form-control select2"
            id="withinhere_branch_id">
            <option value=""></option>
        </select>
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
                                <option value="{{$district->id}}" @if($office->district_id == $district->id) selected @endif>
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
                                <option value="{{$districtRegional->id}}" @if($office->district_regional_id == $districtRegional->id) selected @endif>
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

        // District and District Regional loading
        $('#province_id').change(function() {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    url: "{{ url('user/get_districts_by_province') }}/" + id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#district_id').empty();
                        $('#district_id').append('<option value=""></option>');
                        $.each(data, function (key, value) {
                            $('#district_id').append('<option value="' + value.id + '">' + value.name + ' (' + (value.province ? value.province.name : 'N/A') + ')' + '</option>');
                        });
                        $('#district_regional_id').empty();
                        $('#district_regional_id').append('<option value=""></option>');
                    }
                });
            } else {
                $('#district_id').empty();
                $('#district_id').append('<option value=""></option>');
                $('#district_regional_id').empty();
                $('#district_regional_id').append('<option value=""></option>');
            }
        });

        $('#district_id').change(function() {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    url: "{{ url('user/get_district_regionals_by_district') }}/" + id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#district_regional_id').empty();
                        $('#district_regional_id').append('<option value=""></option>');
                        $.each(data, function (key, value) {
                            $('#district_regional_id').append('<option value="' + value.id + '">' + value.name + ' (' + (value.district ? value.district.name : 'N/A') + ' - ' + (value.province ? value.province.name : 'N/A') + ')' + '</option>');
                        });
                    }
                });
            } else {
                $('#district_regional_id').empty();
                $('#district_regional_id').append('<option value=""></option>');
            }
        });

        // Initialize loading on page load
        $('#province_id').trigger('change');
        $('#district_id').trigger('change');

        $(document).ready(function () {

    $.ajax({
        url: "https://withinheremobileapi.com/api/v1/businesses/entities/company/8ea1213f-fa3b-44c7-b0e3-404a39be73e4/branches",
        type: "GET",
        dataType: "json",
        success: function (response) {

            $('#withinhere_branch_id').empty();
            $('#withinhere_branch_id').append('<option value=""></option>');

            if (response && response.success && response.data) {

                $.each(response.data, function (key, value) {

                    $('#withinhere_branch_id').append(
                        `<option value="${value.branch_id}">${value.branch_name}</option>`
                    );

                });

                // 🔥 IMPORTANT: set existing value AFTER options load
                let selected = "{{ $office->withinhere_branch_id ?? '' }}";
                if (selected) {
                    $('#withinhere_branch_id').val(selected).trigger('change');
                }

                $('#withinhere_branch_id').select2({ width: '100%' });
            }
        },
        error: function (xhr) {
            console.log("Failed to load branches", xhr.responseText);
        }
    });

});
    </script>
@endsection