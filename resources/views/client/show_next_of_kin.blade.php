<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span></button>
    <h4 class="modal-title">{{trans_choice('general.next_of_kin',1)}}</h4>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tr>
            <td>{{trans_choice('general.name',1)}}</td>
            <td>{{$next_of_kin->first_name}} {{$next_of_kin->middle_name}} {{$next_of_kin->last_name}}</td>
        </tr>
        <tr>
            <td>{{trans_choice('general.mobile',1)}}</td>
            <td>{{$next_of_kin->mobile}} </td>
        </tr>
        <tr>
            <td>{{trans_choice('general.relationship',1)}}</td>
            <td>
                @if(!empty($next_of_kin->relationship))
                    {{$next_of_kin->relationship->name}}
                @endif
            </td>
        </tr>
        <tr>
            <td>{{trans_choice('general.gender',1)}}</td>
            <td>
                @if($next_of_kin->gender=="male")
                    {{trans_choice('general.male',1)}}
                @endif
                @if($next_of_kin->gender=="female")
                    {{trans_choice('general.female',1)}}
                @endif
                @if($next_of_kin->gender=="other")
                    {{trans_choice('general.other',1)}}
                @endif
                @if($next_of_kin->gender=="unspecified")
                    {{trans_choice('general.unspecified',1)}}
                @endif
            </td>
        </tr>

        <tr>
            <td>{{trans_choice('general.address',1)}}</td>
            <td>{{$next_of_kin->address}} </td>
        </tr>
        <tr>
            <td>{{trans_choice('general.note',2)}}</td>
            <td>{{$next_of_kin->notes}} </td>
        </tr>
        <tr>
            <td>{{trans_choice('general.picture',1)}}</td>
            <td>
                @if(!empty($next_of_kin->picture))
                    <img class=""
                         src="{{asset('uploads/'.$next_of_kin->picture)}}"
                         height="150">

                @endif
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal">Close</button>
</div>
