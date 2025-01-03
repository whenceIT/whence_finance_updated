@extends('layouts.master')

@section('title', 'Ledger Summary')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="data-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 33%;">Branch Name</th>
                                    <th class="text-center" style="width: 33%;">Cash Balance</th>
                                    <th class="text-center" style="width: 33%;">Action</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ledgerEntriesByOffice as $officeName => $details)
                                <tr>
                                    <td class="text-center">{{ $officeName }}</td>
                                    <td class="text-center">{{ number_format($details['closingBalance'], 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('ledger.show', ['officeName' => $officeName]) }}" class="btn btn-primary">View Ledger</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function() {
        $('#data-table').DataTable({
            dom: 'lfrtip',
            "paging": false,
            "lengthChange": true,
            "searching": true, 
            "info": true,
            "autoWidth": true,
            
        });
    });
</script>
@endsection

