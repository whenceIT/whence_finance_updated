@extends('layouts.master')

@section('title')
    Branch Performance Information
@endsection

@section('content')

<section class="content">

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Branch Performance</h3>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Office</th>
                        <th class="text-right">Cycle Opening Uncollected (COUA)</th>
                        <th class="text-right">Given Out</th>
                        <th class="text-right">Total Cycle Collected (TCC)</th>
                        <th class="text-right">Still Uncollected Today (SUT)</th>
                        <th class="text-right">PDUA%</th>
                        <th class="text-right">Staff</th>
                        <th class="text-right">Efficiency</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Loading...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('tableBody');

    async function fetchData() {
        let url = `https://lms2backend.whencefinancesystem.com/branch-performance-all`;

        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> Loading...
                </td>
            </tr>
        `;

        try {
           const res = await fetch(url);
const result = await res.json();
const data = result.data || [];

tableBody.innerHTML = '';


            if (!data.length) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">No records found</td>
                    </tr>
                `;
                return;
            }

            data.forEach(row => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${row.office ?? ''}</td>
                        <td class="text-right">${row.total_uncollected ?? 0}</td>
                        <td class="text-right">${row.given_out ?? 0}</td>
                        <td class="text-right text-success">${row.total_collected ?? 0}</td>
                        <td class="text-right text-danger">${row.still_uncollected ?? 0}</td>
                        <td class="text-right">${((row.pdua ?? 0) * 100).toFixed(2)}%</td>
                        <td class="text-right">${row.consultants_count ?? 0}</td>
                       <td class="text-right">${((row.efficiency ?? 0) * 100).toFixed(2)}%</td>
                    </tr>
                `;
            });

        } catch (error) {
            console.error(error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        Error loading data
                    </td>
                </tr>
            `;
        }
    }

    fetchData();
});
</script>

@endsection
