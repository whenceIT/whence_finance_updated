<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.next_of_kin',1)}}</h4>
</div>
<form method="post" action="{{url('client/next_of_kin/'.$next_of_kin->id.'/update')}}"
      class="form-horizontal "
      enctype="multipart/form-data" id="edit_next_of_kin_form">
    {{csrf_field()}}
    <div class="modal-body">
        <div class="form-group">
            <label for="next_of_kin_first_name"
                   class="control-label col-md-3">{{trans_choice('general.first_name',1)}}</label>
            <div class="col-md-9">
                <input type="text" name="first_name" class="form-control"
                       value="{{$next_of_kin->first_name}}"
                       required id="next_of_kin_first_name">
            </div>
        </div>
        <div class="form-group">
            <label for="next_of_kin_middle_name"
                   class="control-label col-md-3">{{trans_choice('general.middle_name',1)}}</label>
            <div class="col-md-9">
                <input type="text" name="middle_name" class="form-control"
                       value="{{$next_of_kin->middle_name}}"
                       id="next_of_kin_middle_name">
            </div>
        </div>
        <div class="form-group">
            <label for="next_of_kin_last_name"
                   class="control-label col-md-3">{{trans_choice('general.last_name',1)}}</label>
            <div class="col-md-9">
                <input type="text" name="last_name" class="form-control"
                       value="{{$next_of_kin->last_name}}"
                       required id="next_of_kin_last_name">
            </div>
        </div>
        <div class="form-group">
            <label for="next_of_kin_mobile"
                   class="control-label col-md-3">{{trans_choice('general.mobile',1)}}</label>
            <div class="col-md-9">
                <input type="text" name="mobile" class="form-control"
                       value="{{$next_of_kin->mobile}}"
                       id="next_of_kin_mobile">
            </div>
        </div>
        <div class="form-group">
            <label for="client_relationship_id"
                   class="control-label col-md-3">{{trans_choice('general.relationship',1)}}</label>
            <div class="col-md-9">
                <select name="client_relationship_id" class="select2 form-control"
                        id="client_relationship_id" required>
                    <option></option>
                    @foreach(\App\Models\ClientRelationship::all() as $key)
                        <option value="{{$key->id}}"
                                @if($next_of_kin->client_relationship_id==$key->id) selected @endif>{{$key->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="next_of_kin_gender"
                   class="control-label col-md-3">{{trans_choice('general.gender',1)}}</label>
            <div class="col-md-9">
                <select name="gender" class="form-control" id="next_of_kin_gender">
                    <option value="male"  @if($next_of_kin->gender=="male") selected @endif>{{trans('general.male')}}</option>
                    <option value="female"  @if($next_of_kin->gender=="female") selected @endif>{{trans('general.female')}}</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="address"
                   class="control-label col-md-3">{{trans_choice('general.address',1)}}</label>
            <div class="col-md-9">
                <input type="text" name="address" class="form-control"
                       value="{{$next_of_kin->address}}"
                       id="address">
            </div>
        </div>
        <div class="form-group">
            <label for="next_of_kin_notes"
                   class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
            <div class="col-md-9">
                        <textarea name="notes" class="form-control"
                                  id="next_of_kin_notes" rows="3">{{$next_of_kin->notes}}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="next_of_kin_picture"
                   class="control-label col-md-3">{{trans_choice('general.picture',1)}}</label>
            <div class="col-md-9">
                <input type="file" name="picture" class="form-control"
                       id="next_of_kin_picture">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left"
                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
    </div>
</form>