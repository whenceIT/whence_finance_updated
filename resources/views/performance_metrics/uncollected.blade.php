@extends('layouts.master')

@section('title')
    Uncollected
@endsection

<style>
    .fixed-width-name {
        width: 150px;
    }
    .fixed-width-branch {
        width: 150px;
    }
    .fixed-width-target {
        width: 100px;
    }
    .table-container {
        overflow-x: auto;
    }
    .container {
        max-width: 1200px; 
        margin: 0 auto; 
    }

    .table-responsive {
        overflow-x: auto; 
    }

    #data-table {
        width: 100%; 
    }
    .faint-color {
    color: red; 
}
</style>

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
    
    <div style="display: flex; align-items: center; justify-content: center; padding-bottom: 10px; ">
                <a href="{{ route('performance_metrics.targets') }}" style="margin: 10px;">
                <span class="label label-success" style="font-size: 15px;">Targets</span>
                </a>

                <a href="{{ route('performance_metrics.uncollected')}}" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Staff Uncollected amounts</span>
                </a>

                <a href="{{ route('performance_metrics.low_performance')}}" style="margin: 10px;">
                <span class="label label-danger" style="font-size: 15px;">Low Performance</span>
                </a>

                <a href="{{ route('performance_metrics.defaulted')}}" style="margin: 10px;">
                <span class="label label-warning" style="font-size: 15px;">Staff Defaulted Loans</span>
                </a>
            </div>
            <p style="text-align: center;">Uncollected amounts in the last 6 months.
                Highlighting those failing to reduce their uncollected to below 56, 000.
            </p>
            <div class="d-flex justify-content-center align-items-center mb-3" style="margin-bottom: -50px">
                <form method="GET" action="{{ route('performance_metrics.uncollected') }}" class="form-inline">
                    <div class="form-group">
                        <label for="office_id">Filter by Branch:</label>
                        <select name="office_id" id="office_id" class="form-control">
                            <option value="">All Branches</option>
                            @if($user_role == 1)
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif($user_role == 4)
                                @foreach($offices->where('id', $user_branch) as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif($user_role == 6)
                                @foreach($offices->where('province_id', $user_province) as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>


            <div class="box-body table-responsive">
                <table id="data-table" class="table table-bordered table-striped table-hover no-footer">
                    <thead>
                        <tr>
                            <th rowspan="2" class="fixed-width-name">Name</th>
                            <th rowspan="2" class="fixed-width-branch">Branch</th>
                           
                        </tr>
                        <tr> <!---5-->
                            @for ($i = 1; $i >= 0; $i--)
                                <th class="fixed-width-target">
                                    {{ \Carbon\Carbon::parse($compare_dates[$i])->format('M') }} - {{ \Carbon\Carbon::parse($target_dates[$i])->format('M') }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                <tbody> <!----6--->
                @forelse($loanConsultants as $user)
                <?php
                $uncollectedAmounts = array_fill(0, 2, 0);
                $MoneyGivenOut = array_fill(0, 2, 0);
                $MoneyCollected = array_fill(0, 2, 0);
                $FullPayment = array_fill(0, 2, 0);
                $PartPayment = array_fill(0, 2, 0);
                $ReloanPayment = array_fill(0, 2, 0);

                foreach ($user->loan as $loan) {
                    foreach ($loan->transactions as $transaction) {
                        $transactionDate = \Carbon\Carbon::parse($transaction->date);
                        //6
                        for ($i = 0; $i < 2; $i++) {
                            if ($transactionDate->lt($compare_dates[$i])) {
                                $MoneyGivenOut[$i] += $transaction->debit;
                                $MoneyCollected[$i] += $transaction->credit;
                            }
        
                            if ($transactionDate->between($compare_dates[$i], $target_dates[$i])) {
                                if ($transaction->payment_apply_to == 'full_payment') {
                                    $FullPayment[$i] += $transaction->credit;
                                } elseif ($transaction->payment_apply_to == 'part_payment') {
                                    $PartPayment[$i] += $transaction->credit;
                                } elseif ($transaction->payment_apply_to == 'reloan_payment') {
                                    $ReloanPayment[$i] += $transaction->credit;
                                }
                            }
                        }
                    }
                }
                //6
                for ($i = 0; $i < 2; $i++) {
                    $uncollectedAmounts[$i] = ($MoneyGivenOut[$i] - $MoneyCollected[$i]) - ($FullPayment[$i] + $PartPayment[$i] + $ReloanPayment[$i]);
                }
                ?>
                <tr>
                    <td class="fixed-width-name">{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td class="fixed-width-branch">{{ $user->office ? $user->office->name : 'N/A' }}</td>
        <!---5----> @for ($i = 1; $i >= 0; $i--)
                    <td class="{{ $uncollectedAmounts[$i] > 56000 ? 'faint-color' : '' }}">
                        {{ number_format($uncollectedAmounts[$i], 2) }}
                    </td>
                    @endfor
                </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                dom: 'frtip',
                "paging": true,
                "lengthChange": false,
                "displayLength": 30,
                "searching": true,
                "ordering": false,
                "autoWidth": false,
                
                "language": {
                    "search": "{{ trans('general.search') }}",
                    "paginate": {
                        "first": "{{ trans('general.first') }}",
                        "last": "{{ trans('general.last') }}",
                        "next": "{{ trans('general.next') }}",
                        "previous": "{{ trans('general.previous') }}"
                    }
                },
                responsive: false
            });
        });
    </script>

@endsection
<!---◄- ▲  -->

