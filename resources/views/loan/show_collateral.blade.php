<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">{{trans_choice('general.collateral',1)}} {{trans_choice('general.detail',2)}}</h4>
</div>

    <div class="modal-body">
        <table class="table table-striped">
            <tr>
                <td>{{ trans_choice('general.type',1) }}</td>
                <td>
                    @if(!empty($collateral->type))
                        {{$collateral->type->name}}
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ trans('general.value') }}</td>
                <td>{{$collateral->value}}</td>
            </tr>
            <tr>
                <td>{{ trans('general.description') }}</td>
                <td>{!!$collateral->description !!}</td>
            </tr>
            <tr>
                <td>{{ trans('general.serial') }}</td>
                <td>{{$collateral->serial}}</td>
            </tr>
        </table>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left"
                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
    </div>