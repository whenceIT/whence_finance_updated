@extends('layouts.master')

@section('title', 'Create Ticket')

@section('content')
@include('partials.fullscreen_loader')
<?php
    $user = Sentinel::getUser();
    if ($user) {
        $role = $user->role->role_id;
        $office = $user->office->id;
        $isAdmin = $role;
    } else {
        $role = null;
        $office = null;
        $isAdmin = $role;
    }
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Issue New Ticket</h3>
    </div>
    <div class="box-body">
        @if($openCount >= 5)
            <div class="alert alert-warning">You already have 5 open tickets. Resolve or close an existing ticket before creating a new one. <a href="{{ url('ticket') }}" class="btn btn-sm btn-default">Back to Tickets</a></div>
        @endif
            <form method="post" action="{{ url('ticket/store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Title</label>
                    <input type="text" class="form-control" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control" required>
                        <option value="low">Low</option>
                        <option value="normal" selected>Normal</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input disabled type="text" class="form-control" name="department" id="department" value="Administration">
                </div>

                <div class="form-group">
                    <label for="issue_category_id">Issue Category</label>
                    <select name="issue_category_id" id="issue_category_id" class="form-control">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" data-priority="{{ $c->priority_default }}" data-sla="{{ $c->sla_days }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="sla_days">SLA (Days)</label>
                    <input readonly type="number" min="0" class="form-control" name="sla_days" id="sla_days" value="">
                </div>
                @if($isAdmin == 1)
                <div class="form-group">
                    <label for="due_date">Due Date (optional)</label>
                    <input type="date" class="form-control" name="due_date" id="due_date" value="">
                </div>
                @endif
                <div class="form-group">
                    <label for="assigned_office">Office</label>
                    <select name="assigned_office" id="assigned_office" class="form-control text-dark">
                        <option value="" selected>-- Select Office --</option>
                        @foreach($offices as $o)
                            @if($o->id == 2)
                                <option value="{{ $o->id }}" >{{ $o->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="assigned_role">User Role</label>
                    <select name="assigned_role" id="assigned_role" class="form-control" disabled>
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $r)
                            @if($r->id == 1)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <script>
                    (function($){
                        var categories = @json($categories->toArray());
                        $('#issue_category_id').on('change', function(){
                            var id = $(this).val();
                            if(!id){
                                $('#sla_days').val('');
                                $('#due_date').val('');
                                return;
                            }
                            var cat = categories.find(function(c){ return c.id == id; });
                            if(!cat) return;
                            // set priority if user has not overridden
                            if(!$('#priority').val()){
                                $('#priority').val((cat.priority_default || '').toLowerCase());
                            }
                            if(cat.sla_days){
                                $('#sla_days').val(cat.sla_days);
                                // compute default due date if none selected
                                if(!$('#due_date').val()){
                                    var d = new Date();
                                    d.setDate(d.getDate() + parseInt(cat.sla_days));
                                    var yyyy = d.getFullYear();
                                    var mm = ('0'+(d.getMonth()+1)).slice(-2);
                                    var dd = ('0'+d.getDate()).slice(-2);
                                    $('#due_date').val(yyyy+'-'+mm+'-'+dd);
                                }
                            }
                        });

                        function refreshUsers(){
                            var office = $('#assigned_office').val();
                            var role = $('#assigned_role').val();
                            console.log('refreshUsers called', { office: office, role: role });
                            $('#assigned_to').attr('disabled', true).html('<option value="">Loading...</option>');
                            if(!office || !role){
                                $('#assigned_to').attr('disabled', true).html('<option value="">-- Select User --</option>');
                                return;
                            }
                            $.get('{{ url("ticket/users") }}', { office_id: office, role_id: role, type: 'new' })
                             .done(function(resp){
                                console.log('users response', resp);
                                if(resp.success){
                                    var opts = '<option value="">-- Unassigned --</option>';
                                    resp.users.forEach(function(u){
                                        opts += '<option value="'+u.id+'">'+u.display+'</option>';
                                    });
                                    $('#assigned_to').html(opts).attr('disabled', false);
                                } else {
                                    var msg = resp.message ? ' ('+resp.message+')' : '';
                                    $('#assigned_to').html('<option value="">-- No users'+msg+' --</option>').attr('disabled', true);
                                }
                             })
                             .fail(function(xhr, status, err){
                                console.error('Failed to fetch users', status, err, xhr.responseText);
                                $('#assigned_to').html('<option value="">-- Error loading users --</option>').attr('disabled', true);
                             });
                        }

                        $('#assigned_office').on('change', function(){
                            // enable role select when office picked
                            if($(this).val()){
                                $('#assigned_role').attr('disabled', false);
                                // if a role is already selected, refresh users
                                if($('#assigned_role').val()){
                                    refreshUsers();
                                }
                            } else {
                                $('#assigned_role').val('').attr('disabled', true);
                                $('#assigned_to').val('').attr('disabled', true);
                            }
                        });

                        $('#assigned_role').on('change', function(){
                            refreshUsers();
                        });

                        // if there are preselected values (old input), attempt to refresh
                        $(document).ready(function(){
                            if($('#assigned_office').val() && $('#assigned_role').val()){
                                refreshUsers();
                            }

                            // if old category set, trigger change to pre-fill fields
                                if($('#issue_category_id').val()){
                                    $('#issue_category_id').trigger('change');
                                }
                            });
   
                            // show fullscreen loader on form submit if all required fields are filled
                            $('form').on('submit', function(e){
                                var name = $('#name').val().trim();
                                var priority = $('#priority').val();
                                var office = $('#assigned_office').val();
                                var role = $('#assigned_role').val();
                                var assignedTo = $('#assigned_to').val();
                                var slaDays = $('#sla_days').val();

                                if(name && priority && office && role && assignedTo && slaDays){
                                    $('#fullscreen-loader').show();
                                } else {
                                    e.preventDefault();
                                    alert('Please fill in all required fields, including selecting an issue category.');
                                    return false;
                                }
                            });
                        })(jQuery);
                    </script>
                <div class="form-group">
                    <label for="assigned_to">Assign To</label>
                    <select name="assigned_to" id="assigned_to" class="form-control" disabled>
                        <option value="">-- Select User --</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                    <a href="{{ url('ticket') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
    </div>
</div>
@endsection