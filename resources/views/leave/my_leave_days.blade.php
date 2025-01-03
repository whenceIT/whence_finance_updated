@extends('layouts.master')

<!---MAIN VIEW--->

@section('title', 'My Leave Days')

@section('content')
    <div class="calendar-container">
        {!! $calendarHtml !!}
    </div>

    <div class="leave-summary" style="margin-top: 60px; text-align: center;">
    <div style="width: 65%; margin: 0 auto; text-align: center;">
        <h2 style="margin-bottom: 20px; text-decoration-skip-ink: none; border-bottom: 2px solid; padding-bottom: 1px;">Leave Summary</h2>
    </div>
    <table class="table table-bordered table-striped" style="margin: 0 auto; width: 65%;">
        <thead class="thead-dark">
            <tr>
                <th style="text-align: center;">Reason</th>
                <th style="text-align: center;">Days Taken</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leaveSummary as $reason => $days)
                <tr>
                    <td style="text-align: center;">{{ $reason }}</td>
                    <td style="text-align: center;">{{ $days }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('leave.apply') }}" class="btn btn-primary btn-md">Apply for Leave</a>
    </div>
@endsection

