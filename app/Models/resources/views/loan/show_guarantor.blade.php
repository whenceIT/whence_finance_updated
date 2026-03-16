<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">{{trans_choice('general.guarantor',1)}} {{trans_choice('general.detail',2)}}</h4>
</div>

    <div class="modal-body">
        <table class="table table-striped">
            <tr>
                <td>{{ trans_choice('general.type',1) }}</td>
                <td>
                    @if(!empty($guarantor->relationship))
                        {{$guarantor->relationship->name}}
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ trans('general.amount') }}</td>
                <td>{{$guarantor->amount}}</td>
            </tr>
            <tr>
                <td>{{ trans('general.name') }}</td>
                <td>{!!$guarantor->first_name !!} {{$guarantor->middle_name}} {{$guarantor->last_name}}</td>
            </tr>
            <tr>
                <td>{{ trans('general.mobile') }}</td>
                <td>{{$guarantor->mobile}}</td>
            </tr>
        </table>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left"
                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
    </div>