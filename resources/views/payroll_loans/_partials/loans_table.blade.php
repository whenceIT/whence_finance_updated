{{-- Reusable loans table for Payroll Loan Manager list pages --}}
@if($loans->isEmpty())
    <div class="callout callout-info">
        <p>No loans found.</p>
    </div>
@else
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover" id="payroll-loans-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Loan ID</th>
                <th>Client</th>
                <th>Branch</th>
                <th>Loan Officer</th>
                <th class="text-right">Principal</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $i => $loan)
            <?php
                $statusColors = [
                    'disbursed' => 'success',
                    'pending'   => 'warning',
                    'approved'  => 'primary',
                    'closed'    => 'default',
                ];
                $badgeColor = $statusColors[$loan->status] ?? 'info';
            ?>
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <a href="{{ url('loan/'.$loan->id.'/show') }}" title="View loan">
                        {{ $loan->id }}
                    </a>
                </td>
                <td>
                    @if($loan->client)
                        {{ $loan->client->first_name }} {{ $loan->client->last_name }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ $loan->office->name ?? '—' }}</td>
                <td>
                    @if($loan->loan_officer)
                        {{ $loan->loan_officer->first_name }} {{ $loan->loan_officer->last_name }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($loan->principal, 2) }}</td>
                <td>
                    <span class="label label-{{ $badgeColor }}">
                        {{ ucfirst($loan->status) }}
                    </span>
                </td>
                <td>{{ $loan->created_at ? $loan->created_at->format('Y-m-d') : '—' }}</td>
                <td>
                    <a href="{{ url('loan/'.$loan->id.'/show') }}" class="btn btn-xs btn-info">
                        <i class="fa fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if ($.fn.DataTable) {
        $('#payroll-loans-table').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
        });
    }
});
</script>
@endif
