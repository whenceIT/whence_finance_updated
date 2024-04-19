@extends('layouts.auth')
@section('title')
   Create an account
@endsection
@section('content')
<div class="row">
    <div class="col-lg-8">
    <img class="landing-img"src="{{asset('assets/landing_page/img/whence.jpg') }}"/>
    </div>
    <div class="col-lg-4">
        <div class="login-box">
            <div class="login-logo">
            <img src="{{asset('assets/landing_page/img/whence-logo-2.png') }}" style="width: 100%;height: 200px;margin-bottom: 10px;"/>
            <br>
            <br>
            </div>
            <div class="login-box-body">
            @if(Session::has('flash_notification.message'))
                    <script>toastr.{{ Session::get('flash_notification.level') }}('{{ Session::get("flash_notification.message") }}', 'Response Status')</script>
                @endif
            <h3 class="login-box-msg"style="
        margin-top: -90px;
        text-align: center;
        font-size: 22px;
        color: #000;
        font-family: apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';letter-spacing: 0.0312rem;
        font-weight: 700;"><strong>Create a new account</strong></h3>
        <form method="post" class="form" action="{{url('create_client_user')}}">
        {{csrf_field()}}
        <label for="first_name"
                           class="">{{trans_choice('general.first_name',1)}}</label>
                    <input type="text" name="first_name" class="form-control"
                           placeholder="{{trans_choice('general.first_name',1)}}"
                           required id="first_name">

                           <label for="last_name"
                           class="">{{trans_choice('general.last_name',1)}}</label>
                    <input type="text" name="last_name" class="form-control"
                           placeholder="{{trans_choice('general.last_name',1)}}"
                           required id="last_name">

                           <label for="gender"
                           class="">{{trans_choice('general.gender',1)}}</label>
                    <select name="gender" class="form-control" id="gender">
                        <option value="male">{{trans('general.male')}}</option>
                        <option value="female">{{trans('general.female')}}</option>
                    </select>

                    <label for="office_id">{{trans_choice('general.office',1)}}</label>
                           <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>

                    <label for="phone"
                           class="">{{trans_choice('general.phone',1)}}</label>
                    <input type="text" name="phone" class="form-control"
                           placeholder="{{trans_choice('general.phone',1)}}"
                           id="phone">

                           <label for="email"
                           class="">{{trans_choice('general.email',1)}}</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="{{trans_choice('general.email',1)}}"
                           required
                           id="email">

                           <label for="password"
                           class="">{{trans_choice('general.password',1)}}</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="{{trans_choice('general.password',1)}}"
                           value="" required
                           id="password">

                           <label for="repeat_password"
                           class="">{{trans_choice('general.repeat_password',1)}}</label>
                    <input type="password" name="repeat_password" class="form-control"
                           placeholder="{{trans_choice('general.repeat_password',1)}}"
                           value="" required
                           id="repeat_password">
                             
                          
                   
                       <button type="submit"
                                    class="btn btn-block btn-flat" style="width:80%; color:#ffffff; background-color:#00a04a; margin-top:10px">Create</button>
                        </form>
                           </div>       
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(".form").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                }, password: {
                    required: true,
                    minlength: 6,
                },
                repeat_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                }
            }, highlight: function (element) {
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
    </script>
@endsection