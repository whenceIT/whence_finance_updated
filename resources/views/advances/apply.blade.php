@extends('layouts.master')

@section('title')
    Salary Advance Application
@endsection

@section('content')

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">
            {{ trans('Apply') }} {{ trans('for') }} {{ trans('a') }} {{ trans('salary') }} {{ trans('advance') }}
        </h3>
            <div class="box-tools pull-right">
    </div>
        </div>
        <form method="post" action="{{ route('advances.submit') }}" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            <div class="box-body">
                <!--field to store the user's basic pay hidden-->
               

                <div class="form-group">
                    <label for="first_name" class="control-label col-md-2">{{ trans('general.first_name') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="first_name" class="form-control" value="{{ $firstName }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="last_name" class="control-label col-md-2">{{ trans('general.last_name') }}</label>
                    <div class="col-md-6">
                        <input type="text" name="last_name" class="form-control" value="{{ $lastName }}" readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="amount" class="control-label col-md-2">{{ trans_choice('general.amount', 1) }}</label>
                    <div class="col-md-3">                                                                                      
                        <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" required id="amount" >
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="date"
                           class="control-label col-md-2">{{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="date" class="form-control date-picker"
                               value="{{date('Y-m-d')}}"
                               id="date">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="office_id" class="control-label col-md-2">{{ trans_choice('general.office', 1) }}</label>
                <div class="col-md-3">
                    <select name="office_id" class="form-control" id="office_id" required>
                        <option value="">Office</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                    </select>
                </div>
                </div>
                <div class="form-group">
                    <label for="installments" class="control-label col-md-2">{{ __('Installments') }}</label>
                <div class="col-md-3">
                    <select name="installments" class="form-control" id="installments" required>
                        <option value="">Select Payable Installments</option>
                    @for ($i = 1; $i <= 3; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                    </select>
                </div>
                </div>
                <div class="form-group">
                    <label for="installment_amount" class="control-label col-md-2">{{ __('Monthly Installment Amount') }}</label>
                    <div class="col-md-3">
                        <input type="text" name="installment_amount" class="form-control" id="installment_amount" readonly>
                    </div>
                </div>

                

                <div class="form-group">
                    <label for="purpose" class="control-label col-md-2">{{ __('Select Purpose') }}</label>
                    <div class="col-md-3">
                        <select name="purpose" class="form-control" required>
                            <option value="General Finance">General Finance</option>
                            <option value="Medical Emergency">Medical Emergency</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mode_of_payment" class="control-label col-md-2">{{ __('Select Mode of Payment') }}</label>
                    <div class="col-md-3">
                        <select name="mode_of_payment" class="form-control" required>
                            <option value="Mobile Money">Mobile Money</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </div>
                </div>

                
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-2">{{trans_choice('general.note',2)}}</label>
                    <div class="col-md-8">
                        <textarea name="notes" class="form-control "
                                  placeholder=""
                                  id="notes" rows="3">{{old('notes')}}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-1 col-md-10">
                        <div class="checkbox">
                            <label style="padding-left: 10px;">
                                <input type="checkbox" name="acceptance" id="acceptance" required>
                                
                                    {{ __('I hereby acknowledge my agreement to pay the calculated installment amount on a monthly basis, commencing from the date of approval of my salary advancement.') }}
                                          
                            </label>
                        </div> 
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{ trans_choice('general.submit', 1) }}</button>
            </div>
        </form>
    </div>
    <!-- /.boxx -->
    
@endsection

@section('footer-scripts')
    <script>
        $(document).ready(function (e) {
            $(".form-horizontal").validate();
        })
    </script>
    <script>
        document.getElementById('installments').addEventListener('change', function() {
            var amount = parseFloat(document.getElementById('amount').value);
            var installments = parseInt(this.value);
            var installmentAmount = amount / installments;
            document.getElementById('installment_amount').value = installmentAmount.toFixed(2); // Display installment amount
        });
    </script>
@endsection