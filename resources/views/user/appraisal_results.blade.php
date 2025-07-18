@extends('layouts.master')
@section('title')
    {{ trans_choice('general.user',2) }}
@endsection

@section('content')

<div class="box box-primary">
<div class="box-header with-border">
            <h3 class="box-title">
        Please select a branch
          
            </h3>
            <div class="box-tools pull-right">

            </div>
        </div>
    <div class="box-body hidden-print">
    <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
{{csrf_field()}}
   
<div class="form-group">
        <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">


                           @if($role->role_id == '1')
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0"
                                    @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif

                        @if($role->role_id == '6')
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            @foreach(\App\Models\Office::where('province_id',$userProvince)->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif


                        @if($role->role_id == '4' || $role->role_id == '3')
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            @foreach(\App\Models\Office::where('id',$userBranch)->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif



                    </div>
</div>                   




<div class="form-group">
<label for=""
class="control-label col-md-2"></label>
<div class="col-md-4"> 
<button type="submit" class="btn btn-success">Go!
</button>
</div>
</div>
     

   
</form>
    </div>

    @if($office_id != null)
    <div class="box-body table-responsive">
        <table class="table  table-bordered table-hover table-striped" id="data-table">
      <thead>
                <tr>
                    <th>{{ trans('general.name') }}</th>
                    <th>Branch</th>
                    <th>Role</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($users as $key)
                    <tr>
                        <td>{{ $key->first_name }} {{ $key->last_name }}</td>
                        @if(!empty($key->office))
                        <td>{{$key->office->name}}</td>
                        @endif
                        <td>
                            <div class="btn-group">
                              
                            <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li>
                                            @if(\App\Models\AppraisalForm::where('role',$key->role->role_id)->first() != null)
                                            <a href="{{ url('user/'.$key->id.'/'.\App\Models\AppraisalForm::where('role',$key->role->role_id)->first()->id.'/appraisal_result') }}"><i
                                                        class="fa fa-search"></i>
                                                {{\App\Models\AppraisalForm::where('role',$key->role->role_id)->first()->form_name;}}
                                            </a>
                                            @endif

                                            @if($key->dual_role != null)
                                            @if(\App\Models\AppraisalForm::where('role',$key->dual_role->role_id)->first() != null)
                                            <a href="{{ url('user/'.$key->id.'/'.\App\Models\AppraisalForm::where('role',$key->dual_role->role_id)->first()->id.'/appraisal_result') }}"><i
                                                        class="fa fa-search"></i>
                                                {{\App\Models\AppraisalForm::where('role',$key->dual_role->role_id)->first()->form_name;}}
                                            </a>
                                            @endif
                                            @endif
                                        </li>
                                            </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
        </table>
    </div>
@endif

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
    "order": [[0, "asc"]],
    "columnDefs": [
        {"orderable": false, "targets": [6]}
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
