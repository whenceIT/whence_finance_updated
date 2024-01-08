<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.guarantor',1)}}</h4>
</div>
<form method="post" action="{{url('loan/guarantor/'.$guarantor->id.'/update')}}"
      class="form-horizontal "
      enctype="multipart/form-data" id="edit_guarantor_form">
    {{csrf_field()}}
    <div class="modal-body">
        <div class="form-group">
            <label for="guarantor_amount"
                   class="control-label col-md-3">{{trans_choice('general.amount',1)}}</label>
            <div class="col-md-9">
                <input type="number" name="amount" class="form-control"
                       value="{{$guarantor->amount}}"
                       required id="guarantor_amount">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left"
                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
    </div>
</form>