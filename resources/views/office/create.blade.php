@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add', 1) }} {{ trans_choice('general.branch', 1) }}
@endsection
@section('content')
    <style>
        .wizard-step {
            display: none;
        }

        .wizard-step.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .wizard-progress {
            margin-bottom: 20px;
        }

        .wizard-progress .progress-bar {
            transition: width 0.3s;
        }
    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add', 1) }} {{ trans_choice('general.branch', 1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel', 1) }}
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="wizard-progress">
                <div class="progress">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar"
                        id="wizard-progress-bar" style="width: 50%;">
                        Step 1 of 2
                    </div>
                </div>
            </div>
            <form method="post" action="{{url('office/store')}}" class="form-horizontal" id="wizard-form"
                enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="box-body">
                    <div class="wizard-step active" id="step-1">
                        <div class="form-group">
                            <label for="name" class="control-label col-md-3">{{trans_choice('general.name', 1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="name" class="form-control" value="{{old('name')}}" required
                                    id="name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="external_id"
                                class="control-label col-md-3">{{trans_choice('general.external_id', 1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="external_id" class="form-control" value="{{old('external_id')}}"
                                    required id="external_id">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="branch_capacity" class="control-label col-md-3">Branch Capacity</label>
                            <div class="col-md-9">
                                <input type="number" name="branch_capacity" class="form-control"
                                    value="{{old('branch_capacity')}}" id="branch_capacity">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="province_id" class="control-label col-md-3">Province</label>
                            <div class="col-md-9">
                                <select name="province_id" class="form-control select2" id="province_id">
                                    <option></option>
                                    @foreach(\App\Models\Province::all() as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="button" class="btn btn-info pull-right next-step">Next</button>
                        </div>
                    </div>

                    <div class="wizard-step" id="step-2">
                        <div class="form-group">
                            <label for="parent_id" class="control-label col-md-3">{{trans_choice('general.parent', 1)}}
                                {{trans_choice('general.branch', 1)}}</label>
                            <div class="col-md-9">
                                <select name="parent_id" class="form-control select2" id="parent_id" required>
                                    <option></option>
                                    @foreach(\App\Models\Office::all() as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="button" class="btn btn-default pull-left previous-step">Previous</button>
                            <button type="submit"
                                class="btn btn-primary pull-right">{{trans_choice('general.save', 1)}}</button>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="heading-elements">
                        <button type="submit"
                            class="btn btn-primary pull-right">{{trans_choice('general.save', 1)}}</button>
                    </div>
                </div>
            </form>
        </div>
@endsection
    @section('footer-scripts')
        <script>
            $(".form-horizontal").validate({
                rules: {
                    name: {
                        required: true
                    },
                    external_id: {
                        required: true
                    },
                    parent_id: {
                        required: true
                    }
                },
                highlight: function (element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass('has-error');
                },
                errorElement: 'span',
                errorClass: 'help-block',
                errorPlacement: function (error, element) {
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });

            // Wizard Navigation Logic
            var currentStep = 1;
            var totalSteps = 2;

            function updateProgressBar() {
                var percentage = (currentStep / totalSteps) * 100;
                $('#wizard-progress-bar').css('width', percentage + '%').text('Step ' + currentStep + ' of ' + totalSteps);
            }

            $('.next-step').click(function() {
                var form = $(".form-horizontal");
                
                // Validate only fields in the current step
                var currentStepFields = $('#step-' + currentStep).find('input, select, textarea');
                var isValid = true;
                currentStepFields.each(function() {
                    if (!$(this).valid()) {
                        isValid = false;
                    }
                });

                if (isValid) {
                    $('#step-' + currentStep).removeClass('active');
                    currentStep++;
                    $('#step-' + currentStep).addClass('active');
                    updateProgressBar();
                    
                    // Fix for select2 width issues when showing hidden elements
                    if ($('.select2').length > 0) {
                        $('.select2').select2({
                            width: '100%'
                        });
                    }
                }
            });

            $('.previous-step').click(function() {
                $('#step-' + currentStep).removeClass('active');
                currentStep--;
                $('#step-' + currentStep).addClass('active');
                updateProgressBar();
            });
        </script>
    @endsection