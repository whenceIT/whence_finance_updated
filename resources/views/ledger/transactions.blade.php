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
                                    <td class="text-center">
                                    @if($details['walletWarning'])
    <span style="color:red;">
        {{ $details['walletWarning'] }}
    </span>
@else
    {{ number_format($details['cashBalance'], 2) }}
@endif
</td>

                                    <td class="text-center">
                                          @if($details['walletWarning'])
                                          <span style="color:red;">
        {{ $details['walletWarning'] }}
    </span>
    @else
                                     <a href="{{ route('ledger.show', ['officeName' => $officeName]) }}"
   class="btn btn-primary"
   style="margin-right:8px;">
   View Ledger
</a>

<a href="{{ route('ledger.show_new', ['officeName' => $officeName]) }}"
   class="btn btn-primary"
   style="
        background: linear-gradient(135deg, #ff6a00, #ff2d55);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(255, 45, 85, 0.35);
        position: relative;
        transition: all 0.2s ease-in-out;
   "
   onmouseover="this.style.transform='scale(1.05)'"
   onmouseout="this.style.transform='scale(1)'">
   ✨ View New Ledger
</a>
                                        @endif
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

