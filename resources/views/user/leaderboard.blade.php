@extends('layouts.master')
@section('title')
Leaderboard
@endsection
@section('content')
<div>

<form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
{{csrf_field()}}
    <div class="form-group">

    <label for="time_period"
        class="control-label col-md-2">Time period
    </label>
    <div class="col-md-3">
    <select name="time_period" class="form-control select2" id="time_period" required>
        <option value="Monthly" selected>This Month</option>
            <option value="Daily" @if($time_period == 'Daily') selected @endif>Today</option>
            <option value="Weekly"@if($time_period == 'Weekly') selected @endif>This Week</option>
        <option value="Yearly" @if($time_period == 'Yearly') selected @endif>This Year</option>
    </select>
    </div>

  
    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                        <select name="office" class="form-control select2" id="office" required>
                        <option value="0" @if($office=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                        @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}"  @if($office==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>


               
        <button type="submit" class="btn btn-success">Go!
                        </button>
              

    </div>
   
</form>
<?php

function compare($a,$b){
    return $a->amount < $b->amount;
}
usort($data,"compare");

$use = date('Y-');
$use.'24';
$number = 0;

$branches = [];

?>
<div class="box box-primary">
<div  class="box-header with-border">
<h2 class="box-title" style="font-weight: bold;">LOAN CONSULTANT PERFORMANCE LEADERBOARD between {{date("jS M, Y", strtotime($startDate))}} and {{date("jS M, Y", strtotime($endDate))}}</h2>
</div>
<div class="box-body table-responsive">
<table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Rank</th>
    <th>First Name</th>
    <th>Branch</th>
    <th>Cash Collectionns</th>
    </tr>
</thead>
<tbody>
@foreach($data as $information)
<?php 
$isBranch = 1;
if(in_array($information->office,$branches)){
    $isBranch = 2;
}

if($isBranch == 1){
    array_push(
        $branches,$information->office
    );
}

?>

@if(!empty($information->role->role_id))
@if($information->role->role_id == '3' || $information->role->role_id == '4')
@if($isBranch == 1)
    <tr style="background-color: #B2D3C2;">
        @if(($number + 1) == 1)
        <td style="font-weight: bold;">
            {{$number = $number + 1}}
            <i class="fa fa-trophy" aria-hidden="true" style="color: gold;"></i>
        </td>
        @else
        <td style="font-weight: bold;">
            {{$number = $number + 1}}
        </td>
        @endif
        <td>{{$information->first_name}} {{$information->last_name}}</td>
        @if($isBranch == 1)
        <td>{{$information->office}} {{$isBranch}}</td>
        @else
        <td>{{$information->office}} {{$isBranch}}</td>
        @endif
        @if($time_period == 'Yearly')
        <td>-</td>
        @else
        <td>{{$information->amount}}</td>
        @endif
    </tr>
    @else
    <tr>
        @if(($number + 1) == 1)
        <td style="font-weight: bold;">
            {{$number = $number + 1}}
            <i class="fa fa-trophy" aria-hidden="true" style="color: gold;"></i>
        </td>
        @else
        <td style="font-weight: bold;">
            {{$number = $number + 1}}
        </td>
        @endif
        <td>{{$information->first_name}} {{$information->last_name}}</td>
        @if($isBranch == 1)
        <td>{{$information->office}} {{$isBranch}}</td>
        @else
        <td>{{$information->office}} {{$isBranch}}</td>
        @endif
        @if($time_period == 'Yearly')
        <td>-</td>
        @else
        <td>{{$information->amount}}</td>
        @endif
    </tr>
    @endif
@endif
@endif
@endforeach                    
</table>
</div>
</div>
</div>
@endsection
@section('footer-scripts')
<script>
    $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[5, "desc"]],
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