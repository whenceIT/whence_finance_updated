@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Ownership Verification</h1>
</section>

<section class="content">

    @if(isset($records) && count($records) > 0)
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Existing Records</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Owner Name</th>
                        <th>Seller Name</th>
                        <th>Company Name</th>
                        <th>Authorized Rep.</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td>{{ ucfirst($record->ownership_type) }}</td>
                        <td>{{ $record->registered_owner_name }}</td>
                        <td>{{ $record->seller_name }}</td>
                        <td>{{ $record->company_name ?? '-' }}</td>
                        <td>{{ $record->authorized_representative_name ?? '-' }}</td>
                        <td>
                            <a href="{{ url('vehicles/'.$vehicle->id.'/ownership-verification/'.$record->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Add Ownership Record</h3>
            <div class="box-tools pull-right">
                <a href="{{ url('vehicles/'.$vehicle->id) }}" class="btn btn-info btn-sm">
                    Back
                </a>
            </div>
        </div>
        <form method="post" action="{{ route('vehicles.store-ownership-verification', $vehicle->id) }}" class="form-horizontal" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="ownership_type" class="control-label col-md-2">Ownership Type</label>
                    <div class="col-md-3">
                        <select name="ownership_type" class="form-control" id="ownership_type" required>
                            <option value="individual">Individual</option>
                            <option value="letter_of_sale">Letter of Sale</option>
                            <option value="company">Company</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="registered_owner" class="control-label col-md-2">Registered Owner Name</label>
                    <div class="col-md-3">
                        <input type="text" name="registered_owner" class="form-control" value="{{old('registered_owner', $vehicle->registered_owner ?? '')}}" id="registered_owner">
                    </div>
                </div>

                <div class="form-group">
                    <label for="seller_name" class="control-label col-md-2">Seller Name</label>
                    <div class="col-md-3">
                        <input type="text" name="seller_name" class="form-control" value="{{old('seller_name')}}" id="seller_name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="seller_nrc" class="control-label col-md-2">Seller NRC</label>
                    <div class="col-md-3">
                        <input type="text" name="seller_nrc" class="form-control" value="{{old('seller_nrc')}}" id="seller_nrc">
                    </div>
                </div>

                <div class="form-group">
                    <label for="seller_phone" class="control-label col-md-2">Seller Phone</label>
                    <div class="col-md-3">
                        <input type="text" name="seller_phone" class="form-control" value="{{old('seller_phone')}}" id="seller_phone">
                    </div>
                </div>

                <div class="form-group">
                    <label for="witness_1_name" class="control-label col-md-2">Witness 1 Name</label>
                    <div class="col-md-3">
                        <input type="text" name="witness_1_name" class="form-control" value="{{old('witness_1_name')}}" id="witness_1_name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="witness_1_nrc" class="control-label col-md-2">Witness 1 NRC</label>
                    <div class="col-md-3">
                        <input type="text" name="witness_1_nrc" class="form-control" value="{{old('witness_1_nrc')}}" id="witness_1_nrc">
                    </div>
                </div>

                <div class="form-group">
                    <label for="witness_2_name" class="control-label col-md-2">Witness 2 Name</label>
                    <div class="col-md-3">
                        <input type="text" name="witness_2_name" class="form-control" value="{{old('witness_2_name')}}" id="witness_2_name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="witness_2_nrc" class="control-label col-md-2">Witness 2 NRC</label>
                    <div class="col-md-3">
                        <input type="text" name="witness_2_nrc" class="form-control" value="{{old('witness_2_nrc')}}" id="witness_2_nrc">
                    </div>
                </div>

                <div class="form-group">
                    <label for="company_name" class="control-label col-md-2">Company Name</label>
                    <div class="col-md-3">
                        <input type="text" name="company_name" class="form-control" value="{{old('company_name')}}" id="company_name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="company_registration" class="control-label col-md-2">Company Registration Number</label>
                    <div class="col-md-3">
                        <input type="text" name="company_registration" class="form-control" value="{{old('company_registration')}}" id="company_registration">
                    </div>
                </div>

                <div class="form-group">
                    <label for="directors" class="control-label col-md-2">Directors</label>
                    <div class="col-md-6">
                        <textarea name="directors" class="form-control" id="directors" rows="3">{{old('directors')}}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="authorized_representative" class="control-label col-md-2">Authorized Representative Name</label>
                    <div class="col-md-3">
                        <input type="text" name="authorized_representative" class="form-control" value="{{old('authorized_representative')}}" id="authorized_representative">
                    </div>
                </div>

                <div class="form-group">
                    <label for="authorized_representative_nrc" class="control-label col-md-2">Authorized Representative NRC</label>
                    <div class="col-md-3">
                        <input type="text" name="authorized_representative_nrc" class="form-control" value="{{old('authorized_representative_nrc')}}" id="authorized_representative_nrc">
                    </div>
                </div>

                <div class="form-group">
                    <label for="ownership_documents" class="control-label col-md-2">Ownership Documents</label>
                    <div class="col-md-3">
                        <input type="file" name="ownership_documents" class="form-control" id="ownership_documents">
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">Save</button>
            </div>
        </form>
    </div>

</section>

@endsection
