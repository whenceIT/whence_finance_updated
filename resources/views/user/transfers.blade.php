@extends('layouts.master')
@section('title')
Client Transfers
@endsection

@section('content')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Transfer Clients</h3>
    </div>

    <form action="{{url('user/transfer_clients') }}" method="POST">
        @csrf

        <div class="box-body">

            {{-- Instructions --}}
            <div class="alert alert-info">
                <strong>Instructions:</strong><br>
                1. Select the <strong>Loan Consultant</strong> who will <u>receive</u> the selected clients.<br>
                2. Select one or more <strong>Clients</strong> that you want to move to the chosen Loan Consultant.<br>
                3. Click <strong>"Transfer Selected Clients"</strong> to complete the transfer.<br>
                <br>
                <strong>Note:</strong> All loans belonging to the selected clients will also be reassigned to the chosen Loan Consultant.
            </div>

            {{-- Loan Consultant --}}
            <div class="form-group">
                <label class="control-label col-md-2">Loan Consultant</label>
                <div class="col-md-4">
                    <select name="loan_consultant_id" class="form-control select2" required>
                        <option></option>
                        @foreach(\App\Models\User::where('office_id',$userBranch)->get() as $key)
                            @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                <option value="{{$key->id}}">
                                    {{$key->first_name}} {{$key->last_name}}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <small class="text-muted">
                        Select the Loan Consultant who will receive the clients.
                    </small>
                </div>
            </div>

            <div class="clearfix"></div>
            <br>

            {{-- Multiple Clients Selection --}}
            <div class="form-group">
                <label class="control-label col-md-2">Select Clients</label>
                <div class="col-md-6">
                    <select name="clients[]" class="form-control select2" multiple required>
                        @foreach(\App\Models\Client::where('status', 'active')
                            ->where('office_id',$userBranch)
                            ->where('blacklisted', 0)
                            ->get() as $key)

                            <option value="{{$key->id}}">
                                @if($key->client_type=="individual")
                                    {{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                    ({{$key->account_no}}) ({{$key->nrc_number}})
                                @else
                                    {{$key->full_name}} ({{$key->account_no}})
                                @endif
                            </option>

                        @endforeach
                    </select>
                    <small class="text-muted">
                        Select the clients you want to move. Hold Ctrl (or Cmd on Mac) to select multiple clients.
                    </small>
                </div>
            </div>

        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">
                Transfer Selected Clients
            </button>
        </div>
    </form>
</div>

@endsection

