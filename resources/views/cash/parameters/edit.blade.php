@extends('layouts.master')

@section('content')

<section class="content-header">

    <h1>
        Edit Cash Parameter
        <small>Update parameter settings</small>
    </h1>

</section>


<section class="content">

    <div class="row">

        <div class="col-md-8">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        Edit Parameter
                    </h3>

                </div>


                <form method="POST"
                      action="{{ route('cash.parameters.update', $parameter->id) }}">

                    @csrf
                    @method('PUT')


                    <div class="box-body">

                        <div class="form-group">

                            <label for="parameter_name">
                                Parameter Name
                            </label>

                            <input type="text"
                                   name="parameter_name"
                                   id="parameter_name"
                                   class="form-control"
                                   value="{{ old('parameter_name', $parameter->parameter_name) }}"
                                   required>

                        </div>


                        <div class="form-group">

                            <label for="value">
                                Value
                            </label>

                            <input type="text"
                                   name="value"
                                   id="value"
                                   class="form-control"
                                   value="{{ old('value', $parameter->value) }}">

                        </div>


                        <div class="form-group">

                            <label for="type">
                                Type
                            </label>

                            <input type="text"
                                   name="type"
                                   id="type"
                                   class="form-control"
                                   value="{{ old('type', $parameter->type) }}">

                        </div>

                    </div>


                    <div class="box-footer">

                        <a href="{{ route('cash.parameters.index') }}"
                           class="btn btn-default">
                            <i class="fa fa-arrow-left"></i>
                            Back
                        </a>

                        <button type="submit"
                                class="btn btn-primary pull-right">
                            <i class="fa fa-save"></i>
                            Save Changes
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>

@endsection