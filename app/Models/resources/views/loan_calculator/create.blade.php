@extends('layouts.master')
@section('title')
    {{ trans_choice('general.loan',1) }} {{ trans_choice('general.calculator',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.loan',1) }} {{ trans_choice('general.calculator',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>

        <div class="box-body form-horizontal">
            <div class="form-group" id="">
                <label for="loan_product_id"
                       class="control-label col-md-3">{{trans_choice('general.product',1)}}</label>
                <div class="col-md-5">
                    <select name="loan_product_id" class="form-control select2" id="loan_product_id">
                        <option></option>
                        @foreach(\App\Models\LoanProduct::get() as $key)
                            <option value="{{$key->id}}">
                                {{$key->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for=""
                       class="control-label col-md-3"></label>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary" id="next">{{trans_choice('general.next',1)}}</button>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('footer-scripts')
    <script>
        $("#next").click(function (e) {

            var loan_product_id = $("#loan_product_id").val();
            if (loan_product_id == "") {
                alert("Please select type");
            } else {
                document.location = "{{url('loan/calculator/')}}/create/" + loan_product_id;

            }

        })
    </script>
@endsection