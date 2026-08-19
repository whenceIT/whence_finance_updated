@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>
        Cash Parameters
        <small>Manage cash module parameters</small>
    </h1>
</section>


<section class="content">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa fa-check"></i>
            {{ session('success') }}
        </div>
    @endif


    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-bottom: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="box box-primary">

        <div class="box-header with-border">
            <h3 class="box-title">
                Cash Module Parameters
            </h3>
        </div>


        <div class="box-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Parameter Name</th>
                            <th>Value</th>
                            <th>Type</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($parameters as $parameter)

                            <tr>

                                <td>
                                    {{ $parameter->id }}
                                </td>

                                <td>
                                    {{ $parameter->parameter_name }}
                                </td>

                                <td>
                                    {{ $parameter->value }}
                                </td>

                                <td>
                                    {{ $parameter->type }}
                                </td>

                                <td>
                                    <a href="{{ route('cash.parameters.edit', $parameter->id) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                        Edit
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center">
                                    No cash parameters found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</section>

@endsection