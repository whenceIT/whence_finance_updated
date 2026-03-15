@extends('layouts.master')
@section('title') Custom Loan Schedule @endsection
@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Custom Payment Plan</h3>
        <div class="box-tools pull-right">
            <button onclick="window.history.back()" class="btn btn-sm btn-info">
                {{ trans_choice('general.cancel',1) }}
            </button>
        </div>
    </div>
    <form method="POST" action="{{ url('loan/'.$loan->id.'/store_custom_schedule') }}">
        @csrf

        
        <div class="box-body">
        <div class="form-group">
    <label for="start_date">First Installment Date</label>
    <input type="date" name="start_date" id="start_date" class="form-control" required>
</div>
            <div class="form-group">
                <label for="term">New Loan Term (months)</label>
                <input type="number" name="term" id="term" min="1" class="form-control" required value="{{ old('term', $loan->loan_term) }}">
            </div>
           

            <div id="schedule-container"></div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right">Save Schedule</button>
        </div>
    </form>
</div>
@endsection

@section('footer-scripts')

<script>
    const outstandingPrincipal = parseFloat({{ floatval($loan->principal_outstanding) }});
    const outstandingInterest = parseFloat({{ floatval($loan->interest_outstanding) }});

    document.addEventListener('DOMContentLoaded', function () {
    const termInput = document.getElementById('term');
    const startDateInput = document.getElementById('start_date');
    const container = document.getElementById('schedule-container');

    function generateScheduleFields(term) {
        const startDateValue = startDateInput.value;
        if (!term || term < 1 || !startDateValue) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = '';

        const basePrincipal = (outstandingPrincipal / term);
        const baseInterest = (outstandingInterest / term);
        const startDate = new Date(startDateValue);

        let runningPrincipal = 0;
        let runningInterest = 0;

        for (let i = 1; i <= term; i++) {
            const dueDate = new Date(startDate);
            dueDate.setMonth(dueDate.getMonth() + (i - 1));
            const formattedDueDate = dueDate.toISOString().split('T')[0];

            const principal = (i === term) ? (outstandingPrincipal - runningPrincipal).toFixed(2) : basePrincipal.toFixed(2);
            const interest = (i === term) ? (outstandingInterest - runningInterest).toFixed(2) : baseInterest.toFixed(2);

            runningPrincipal += parseFloat(principal);
            runningInterest += parseFloat(interest);

            const row = `
            <div class="box box-solid box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">Installment ${i}</h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Due Date</label>
                            <input type="date" name="schedule[${i}][due_date]" class="form-control" required value="${formattedDueDate}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Principal</label>
                            <input type="number" step="0.01" name="schedule[${i}][principal]" class="form-control" required value="${principal}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Interest</label>
                            <input type="number" step="0.01" name="schedule[${i}][interest]" class="form-control" required value="${interest}">
                        </div>
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', row);
        }
    }

    // Auto-generate if both fields are pre-filled
    const initialTerm = parseInt(termInput.value);
    const initialStartDate = startDateInput.value;
    if (initialTerm && initialStartDate) {
        generateScheduleFields(initialTerm);
    }

    termInput.addEventListener('input', () => {
        const term = parseInt(termInput.value);
        generateScheduleFields(term);
    });

    startDateInput.addEventListener('change', () => {
        const term = parseInt(termInput.value);
        generateScheduleFields(term);
    });
});

</script>

<script>
    console.log("Outstanding Principal:", outstandingPrincipal);
    console.log("Outstanding Interest:", outstandingInterest);
</script>


@endsection
