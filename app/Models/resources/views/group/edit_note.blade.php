<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.note',1)}}</h4>
</div>
<form method="post" action="{{url('client/note/'.$note->id.'/update')}}"
      class="form-horizontal "
      enctype="multipart/form-data" id="edit_note">
    {{csrf_field()}}
    <div class="modal-body">
        <div class="form-group">
            <label for="note_notes"
                   class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
            <div class="col-md-9">
                        <textarea name="notes" class="form-control"
                                  id="note_notes" rows="3">{{$note->notes}}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left"
                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
    </div>
</form>