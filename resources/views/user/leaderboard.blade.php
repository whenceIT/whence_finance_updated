@extends('layouts.master')
@section('title')
Leaderboard
@endsection
@section('content')
@php
    $userInfo = \App\Helpers\GeneralHelper::get_user_info();
    $user = $userInfo->user;
    $role = $userInfo->role;
    $office = $userInfo->office;

@endphp
<div class="box-body hidden-print">

<form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
    {{csrf_field()}}
    <div class="form-group">
    <label for="time_period"
        class="control-label col-md-2">Start date
    </label>
    <div class="col-md-3">
        <input type="text" name="start_date" class="form-control date-picker" required id="start_date" value="{{ old('start_date', $startDate ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label for="time_period"
        class="control-label col-md-2">End date
    </label>
    <div class="col-md-3">
            <input type="text" name="end_date" class="form-control date-picker" required id="end_date" value="{{ old('end_date', $endDate ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label for="leaderboard_type" class="control-label col-md-2">Leaderboard Type</label>
    <div class="col-md-3">
        <select name="leaderboard_type" class="form-control" id="leaderboard_type" required>
        <option value="officer" @if(($leaderboard_type ?? 'officer') == 'officer') selected @endif>Loan Officer Leaderboard</option>
        <option value="office" @if(($leaderboard_type ?? 'officer') == 'office') selected @endif>Office by Office Leaderboard</option>
        </select>
    </div>
</div>

@if ($role == 6)
<div class="form-group">
    <label for="office_id" class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
    <div class="col-md-3">
        <select name="office" class="form-control select2" id="office" required>
        <option value="0" @if($office=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
            @foreach(\App\Models\Office::where('province_id',$user->province_id)->get() as $key)
                <option value="{{$key->id}}"  @if($office==$key->id) selected @endif>{{$key->name}}</option>
            @endforeach
        </select>
    </div>
</div>
@elseif($role == 1)
    <div class="form-group">
        <label for="office_id" class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
        <div class="col-md-3">
            <select name="office" class="form-control select2" id="office" required>
            <option value="0" @if($office=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                @foreach(\App\Models\Office::all() as $key)
                    <option value="{{$key->id}}"  @if($office==$key->id) selected @endif>{{$key->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif


<div class="form-group">
<label for=""
class="control-label col-md-2"></label>
<div class="col-md-4">
<button type="submit" class="btn btn-success">Go!
                        </button>
</div>
</div>  
</form>
@php
$number = 0;
@endphp

@if(!empty($startDate))
<div class="box box-primary">
<div  class="box-header with-border">
<h2 class="box-title" style="font-weight: bold;">{{ ($leaderboard_type ?? 'officer') == 'officer' ? 'LOAN CONSULTANT PERFORMANCE LEADERBOARD' : 'OFFICE PERFORMANCE LEADERBOARD' }} between {{date("jS M, Y", strtotime($startDate))}} and {{date("jS M, Y", strtotime($endDate))}}</h2>
</div>
<div class="box-body table-responsive">
<table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Rank</th>
    @if(($leaderboard_type ?? 'officer') == 'officer')
    <th>First Name</th>
    <th>Branch</th>
    @else
    <th>Office</th>
    @endif
    <th>Cash Collections</th>
    </tr>
</thead>
<tfoot>
    <tr style="background-color: #f0f0f0; font-weight: bold;">
        <td colspan="{{ ($leaderboard_type ?? 'officer') == 'officer' ? 3 : 2 }}" style="text-align: right;">Total Collections</td>
        <td>{{ number_format($apiData['total_collected'] ?? 0, 2) }}</td>
    </tr>
    <tr style="background-color: #f0f0f0; font-weight: bold;">
        <td colspan="{{ ($leaderboard_type ?? 'officer') == 'officer' ? 3 : 2 }}" style="text-align: right;">Uncollected</td>
        <td>{{ number_format($apiData['total_uncollected'] ?? 0, 2) }}</td>
    </tr>
    <tr style="background-color: #f0f0f0; font-weight: bold;">
        <td colspan="{{ ($leaderboard_type ?? 'officer') == 'officer' ? 3 : 2 }}" style="text-align: right;">Given Out</td>
        <td>{{ number_format($apiData['given_out'] ?? 0, 2) }}</td>
    </tr>
    <tr style="background-color: #f0f0f0; font-weight: bold;">
        <td colspan="{{ ($leaderboard_type ?? 'officer') == 'officer' ? 3 : 2 }}" style="text-align: right;">Carry Over</td>
        <td>{{ number_format($apiData['carry_over'] ?? 0, 2) }}</td>
    </tr>
</tfoot>
<tbody>
@foreach($data as $information)
    <tr>
        <td style="font-weight: bold;">
            {{++$number}}
            @if($number == 1)
            <i class="fa fa-trophy" aria-hidden="true" style="color: gold;"></i>
            @endif
        </td>
        @if(($leaderboard_type ?? 'officer') == 'officer')
        <td>{{$information->first_name}} {{$information->last_name}}</td>
        <td>{{$information->office}}</td>
        @else
        <td>{{$information->office}}</td>
        @endif
        <td>{{ number_format($information->amount, 2) }}</td>
    </tr>
@endforeach
</tbody>
</table>
</div>
</div>

@endif
</div>
@endsection
@section('footer-scripts')
<script>
    $('#leaderboard_type').on('change', function() {
        if ($(this).val() == 'office') {
            $('#office').val('0').trigger('change').prop('disabled', true);
        } else {
            $('#office').prop('disabled', false);
        }
    });

    // Initial check
    if ($('#leaderboard_type').val() == 'office') {
        $('#office').val('0').trigger('change').prop('disabled', true);
    }

    $('#data-table').DataTable({
            dom: 'frtip',
            "paging": false,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[{{ ($leaderboard_type ?? 'officer') == 'officer' ? 3 : 1 }}, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });

</script>
@endsection
