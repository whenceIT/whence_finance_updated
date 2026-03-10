@extends('layouts.master')

@section('title')
    Performance Information
@endsection

@section('content')

<section class="content">

    {{-- FILTER BOX --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Loan Consultant Performance</h3>
        </div>

        <form id="filterForm" class="form-horizontal">
            <div class="box-body">

                {{-- Start Date --}}
                <div class="form-group">
                    <label class="col-md-2 control-label">Start Date</label>
                    <div class="col-md-3">
                        <input type="date" name="start_date" id="start_date" class="form-control">
                    </div>
                </div>

                {{-- End Date --}}
                <div class="form-group">
                    <label class="col-md-2 control-label">End Date</label>
                    <div class="col-md-3">
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                </div>
{{-- Office --}}
<div class="form-group">
    <label for="office_id"
           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
    <div class="col-md-3">
        <select name="office_id" class="form-control select2" id="office_id" required>
         

            @if($role == 4)
                @foreach(\App\Models\Office::where('id', Sentinel::getUser()->office->id)->get() as $key)
                    <option value="{{$key->id}}"  @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                @endforeach
            @elseif($role == 6)
                @foreach(\App\Models\Office::where('province_id', Sentinel::getUser()->office->province_id)->get() as $key)
                    <option value="{{$key->id}}"  @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                @endforeach
            @elseif($role == 1)
               <option value="0" @if($office_id == "0") selected @endif>{{trans_choice('general.all',1)}}</option>
                @foreach(\App\Models\Office::all() as $key)
                    <option value="{{$key->id}}"  @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>

            </div>

            <div class="box-footer">
                <div class="col-md-offset-2 col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE BOX --}}
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Performance Results</h3>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Office</th>
                        <th class="text-right">Total Given Out</th>
                        <th class="text-right">Cycle Opening Uncollected (COUA)</th>
                        <th class="text-right">Cycle Opening Uncollected (without charges)</th>
                        <th class="text-right">Total Cycle Collected (TCC)</th>
                        <th class="text-right">Still Uncollected Today (SUT)</th>
                        <th class="text-right">Carry Over</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Please select filters and click Filter
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</section>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('tableBody');
    const filterForm = document.getElementById('filterForm');

    async function fetchData() {
        const start_date = document.getElementById('start_date').value;
        const end_date = document.getElementById('end_date').value;
        const office_id = document.getElementById('office_id').value;

        if (!start_date || !end_date) {
            alert('Please select both start and end dates.');
            return;
        }

        let url = `https://lms2backend.whencefinancesystem.com/loan-consultant-performance-new?start_date=${start_date}&end_date=${end_date}`;
        if (office_id) url += `&office_id=${office_id}`;

        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> Loading...
                </td>
            </tr>
        `;

        try {
            const res = await fetch(url);
            const data = await res.json();

            tableBody.innerHTML = '';

            if (!data.length) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted">No records found</td>
                    </tr>
                `;
                return;
            }

            data.forEach(row => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${row.name}</td>
                        <td>${row.office}</td>
                        <td class="text-right">${row.given_out}</td>
                        <td class="text-right">${row.total_uncollected}</td>
                         <td class="text-right">${row.uncollected_without_charges}</td>
                        <td class="text-right text-success">${row.total_collected}</td>
                        <td class="text-right text-danger">${row.still_uncollected}</td>
                        <td class="text-right">${row.carry_over}</td>
                    </tr>
                `;
            });

        } catch (error) {
            console.error(error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Error loading data
                    </td>
                </tr>
            `;
        }
    }

    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchData();
    });
});
</script>

@endsection

