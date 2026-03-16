<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.collateral',1)}}</h4>
</div>
<form method="post" action="{{url('loan/collateral/'.$collateral->id.'/update')}}"
      class="form-horizontal "
      enctype="multipart/form-data" id="edit_collateral">
    {{csrf_field()}}
    <div class="modal-body">
        <div class="form-group">
            <label for="collateral_type_id"
                   class="control-label col-md-3">{{trans_choice('general.type',1)}}</label>
            <div class="col-md-9">
                <select name="collateral_type_id" class="select2 form-control"
                        id="collateral_type_id" required>
                    <option></option>
                    @foreach(\App\Models\CollateralType::all() as $key)
                        <option value="{{$key->id}}"
                                @if($collateral->collateral_type_id==$key->id) selected @endif>{{$key->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="value"
                   class="control-label col-md-3">{{trans_choice('general.value',1)}}</label>
            <div class="col-md-9">
                <input type="number" name="value" class="form-control"
                       value="{{$collateral->value}}"
                       required id="value">
            </div>
        </div>
        <div class="form-group">
            <label for="description"
                   class="control-label col-md-3">{{trans_choice('general.description',1)}}</label>
            <div class="col-md-9">
                <input type="text" name="description" class="form-control"
                       value="{{$collateral->description}}"
                       required id="description">
            </div>
        </div>
        <div class="form-group">
            <label for="serial"
                   class="control-label col-md-3">{{trans_choice('general.serial',1)}}</label>
            <div class="col-md-9">
                <input type="text" name="serial" class="form-control"
                       value="{{$collateral->serial}}"
                       required id="serial">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left"
                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
    </div>
</form>