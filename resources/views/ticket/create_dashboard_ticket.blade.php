@extends('layouts.master')

@section('title', 'Create Ticket')

@section('content')
@include('partials.fullscreen_loader')

<?php
    $user = Sentinel::getUser();
    $role = $user ? $user->role->role_id : null;
    $office = $user ? $user->office->id : null;
    $isAdmin = $role;
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Loan Consultant Dashboard Discrepancy Ticket</h3>
    </div>

    <div class="box-body">

        @if($openCount >= 10)
            <div class="alert alert-warning">
                You already have 10 open tickets. Resolve or close an existing ticket before creating a new one.
                <a href="{{ url('ticket') }}" class="btn btn-sm btn-default">Back to Tickets</a>
            </div>
        @endif

        <form method="post" action="{{ url('ticket/store_dashboard_ticket') }}" id="ticketForm">
            @csrf

            {{-- Title --}}
            <div class="form-group">
                <label>Ticket Title</label>
                <input type="text" class="form-control" name="name" value="Dashboard Summary Discrepancy" readonly>
            </div>

            <hr>

            {{-- SUMMARY SECTIONS --}}
            <div class="form-group">
                <label>1. Summary Figures with Issues <span class="text-danger">*</span></label>

                <div id="summaryWrapper">

                    <div class="summary-section box box-default" style="padding:15px; margin-bottom:20px;">

                        <div class="clearfix" style="margin-bottom:10px;">
                            <strong>Summary Section</strong>
                            <button type="button" class="btn btn-danger btn-xs pull-right removeSummary" style="display:none;">
                                Remove This Summary
                            </button>
                        </div>

                        <div class="form-group">
                            <label>Select Summary Figure</label>
                            <select class="form-control summary-select">
                                <option value="">-- Select --</option>
                                <option>Cycle Opening Uncollected</option>
                                <option>Total Cycle Collected</option>
                                <option>Cycle Given Out</option>
                                <option>Cycle Still Uncollected</option>
                                <option>Carry Over</option>
                            </select>
                        </div>

                        <hr>

                        <strong>Transactions / Loans Causing This Issue</strong>

                        <div class="entriesWrapper" style="margin-top:10px;">

                            <div class="entry box box-default" style="padding:15px; margin-bottom:15px;">

                                <div class="clearfix" style="margin-bottom:10px;">
                                    <strong>Transaction Entry</strong>
                                    <button type="button" class="btn btn-danger btn-xs pull-right removeEntry" style="display:none;">
                                        Remove Entry
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label>Loan ID</label>
                                    <input type="text" class="form-control loan-id">
                                </div>

                                <div class="form-group">
                                    <label>Client Name</label>
                                    <input type="text" class="form-control client-name">
                                </div>

                                <div class="form-group">
                                    <label>Transaction Type</label>
                                    <select class="form-control transaction-type">
                                        <option value="">-- Select --</option>
                                        <option>Part Payment</option>
                                        <option>Full Payment</option>
                                        <option>Reloan</option>
                                        <option>New Loan</option>
                                        <option>Carry Over</option>
                                        <option>Balance</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Description of Discrepancy</label>
                                    <textarea class="form-control discrepancy-desc" rows="3"
                                        placeholder="Explain why this loan or transaction is incorrect"></textarea>
                                </div>

                            </div>

                        </div>

                        <button type="button" class="btn btn-default btn-sm addEntry">
                            + Add Transaction Under This Summary
                        </button>

                    </div>

                </div>

                <button type="button" class="btn btn-primary" id="addSummary">
                    + Add Another Summary Figure
                </button>

            </div>

            {{-- HIDDEN FIELDS --}}
            <textarea name="description" id="finalDescription" class="hidden"></textarea>
            <input type="hidden" name="summary_issue" id="finalSummaryIssue">

            {{-- Priority --}}
            <div class="form-group">
                <label>Priority</label>
                <select name="priority" class="form-control" required>
                    <option value="low">Low</option>
                    <option value="normal" selected>Normal</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            {{-- Department --}}
            <div class="form-group">
                <label>Department</label>
                <input disabled class="form-control" value="Administration">
            </div>

            {{-- Issue Category --}}
            <div class="form-group">
                <label>Issue Category</label>
                <select name="issue_category_id" id="issue_category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" data-sla="{{ $c->sla_days }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- SLA --}}
            <div class="form-group">
                <label>SLA (Days)</label>
                <input readonly type="number" name="sla_days" id="sla_days" class="form-control">
            </div>

            {{-- Office --}}
            <div class="form-group">
                <label>Office</label>
                <select name="assigned_office" id="assigned_office" class="form-control" required>
                    <option value="">-- Select Office --</option>
                    @foreach($offices as $o)
                        @if($o->id == 2)
                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Create Ticket</button>
                <a href="{{ url('ticket') }}" class="btn btn-default">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
(function($){

    function refreshButtons() {

        $('.summary-section').each(function(){
            if($('.summary-section').length > 1){
                $(this).find('.removeSummary').show();
            } else {
                $(this).find('.removeSummary').hide();
            }
        });

        $('.entriesWrapper').each(function(){
            var entries = $(this).find('.entry');
            entries.each(function(){
                if(entries.length > 1){
                    $(this).find('.removeEntry').show();
                } else {
                    $(this).find('.removeEntry').hide();
                }
            });
        });
    }

    $('#addSummary').on('click', function(){
        var clone = $('.summary-section:first').clone();
        clone.find('input, textarea, select').val('');
        $('#summaryWrapper').append(clone);
        refreshButtons();
    });

    $(document).on('click', '.removeSummary', function(){
        $(this).closest('.summary-section').remove();
        refreshButtons();
    });

    $(document).on('click', '.addEntry', function(){
        var wrapper = $(this).siblings('.entriesWrapper');
        var clone = wrapper.find('.entry:first').clone();
        clone.find('input, textarea, select').val('');
        wrapper.append(clone);
        refreshButtons();
    });

    $(document).on('click', '.removeEntry', function(){
        $(this).closest('.entry').remove();
        refreshButtons();
    });

    $('#ticketForm').on('submit', function(e){

        var description = "";
        var selectedSummaries = [];

        $('.summary-section').each(function(){

            var summary = $(this).find('.summary-select').val();

            if(summary){
                selectedSummaries.push(summary);
                description += "Summary Figure with Issue: " + summary + "\n\n";

                $(this).find('.entry').each(function(index){

                    var loanId = $(this).find('.loan-id').val();
                    var client = $(this).find('.client-name').val();
                    var type = $(this).find('.transaction-type').val();
                    var desc = $(this).find('.discrepancy-desc').val();

                    if(loanId || client || type || desc){
                        description += "  Entry " + (index + 1) + ":\n";
                        description += "  Loan ID: " + loanId + "\n";
                        description += "  Client Name: " + client + "\n";
                        description += "  Transaction Type: " + type + "\n";
                        description += "  Description: " + desc + "\n\n";
                    }

                });

                description += "--------------------------------------\n\n";
            }

        });

        if(selectedSummaries.length === 0){
            alert('Please select at least one summary figure with issue.');
            e.preventDefault();
            return false;
        }

        $('#finalSummaryIssue').val(selectedSummaries.join(', '));
        $('#finalDescription').val(description);
        $('#fullscreen-loader').show();
    });

    refreshButtons();

})(jQuery);
</script>

@endsection
