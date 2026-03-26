@extends('layouts.master')

@section('title', 'My Leave Days')

@section('content')
    <div class="calendar-container">
        {!! $calendarHtml !!}
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <form method="GET" action="{{ url('leave/my_leave_days') }}" style="display: inline-block;">
            <div style="display: flex; gap: 10px; align-items: center; justify-content: center;">
                <label for="year"><strong>Select Year:</strong></label>
                <select name="year" id="year" class="form-control" style="width: 150px; display: inline-block;">
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    <div class="leave-summary" style="margin-top: 40px; text-align: center;">
        <div style="width: 65%; margin: 0 auto; text-align: center;">
            <h2 style="margin-bottom: 20px; border-bottom: 2px solid; padding-bottom: 1px;">
                Leave Summary - {{ $selectedYear }}
            </h2>
        </div>

        <table class="table table-bordered table-striped" style="margin: 0 auto; width: 65%;">
            <thead class="thead-dark">
                <tr>
                    <th style="text-align: center;">Reason</th>
                    <th style="text-align: center;">Days Taken</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leaveSummary as $reason => $days)
                    <tr>
                        <td style="text-align: center;">{{ $reason }}</td>
                        <td style="text-align: center;">{{ $days }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center;">No leave taken in {{ $selectedYear }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('leave.apply') }}" class="btn btn-primary btn-md">Apply for Leave</a>
    </div>
@endsection

