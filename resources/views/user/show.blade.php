@extends('layouts.master')
@section('title')
    {{ $user->first_name }} {{ $user->last_name }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $user->first_name }} {{ $user->last_name }}</h3>
                    <h3 class="box-title">{{$user}}</h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-responsive table-hover">
                        <tr>
                            <td>{{ trans('general.gender') }}</td>
                            <td>{{ $user->gender }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.email',1) }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.phone') }}</td>
                            <td>{{ $user->phone }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.address') }}</td>
                            <td>{!!   $user->address !!}</td>
                        </tr>

                        <tr>
                            <td>{{ trans('general.created_at') }}</td>
                            <td>{{ $user->created_at }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.updated_at') }}</td>
                            <td>{{ $user->updated_at }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.last_login') }}</td>
                            <td>{{ $user->last_login }}</td>
                        </tr>
                        @foreach(\App\Models\CustomFieldMeta::where('category', 'users')->where('parent_id', $user->id)->get() as $key)
                            <tr>
                                <td>
                                    @if(!empty($key->custom_field))
                                        {{$key->custom_field->name}}
                                    @endif
                                </td>
                                <td>
                                    @if($key->custom_field->field_type=="checkbox")
                                        @foreach(unserialize($key->name) as $v=>$k)
                                            {{$k}}<br>
                                        @endforeach
                                    @else
                                        {{$key->name}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans_choice('general.note',2) }}</h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    {!!   $user->notes !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row">

    </div>

@endsection
@section('footer-scripts')

    <script>
        $('.data-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
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
                },
                "columnDefs": [
                    {"orderable": false, "targets": 0}
                ]
            },
            responsive: true,
        });
    </script>
@endsection
