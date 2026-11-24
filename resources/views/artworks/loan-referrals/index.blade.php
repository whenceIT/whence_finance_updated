@extends('layouts.master')
@section('title') All Referrals @endsection
@section('content')
<section class="content">
    <div class="box">
        <div class="box-body">
            <form method="GET" action="{{ route('loan-referrals.index') }}" class="form-inline" style="margin-bottom:15px;">
                <div class="form-group" style="margin:0 6px 6px 0;">
                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Search referrals">
                </div>
                <div class="form-group" style="margin:0 6px 6px 0;">
                    <select name="province_id" id="referral-province" class="form-control">
                        <option value="">All provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ (string) $provinceId === (string) $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0 6px 6px 0;">
                    <select name="district_id" id="referral-district" class="form-control">
                        <option value="">All districts</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ (string) $districtId === (string) $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0 6px 6px 0;">
                    <select name="office_id" id="referral-office" class="form-control">
                        <option value="">All offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ (string) $officeId === (string) $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-bottom:6px;">Search / Filter</button>
                <a href="{{ route('loan-referrals.index') }}" class="btn btn-default" style="margin-bottom:6px;">Clear</a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'ref', 'direction' => $sort === 'ref' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Ref @if($sort === 'ref')<i class="fa fa-sort-{{ $direction === 'asc' ? 'asc' : 'desc' }}"></i>@endif</a></th>
                        <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'client', 'direction' => $sort === 'client' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Client @if($sort === 'client')<i class="fa fa-sort-{{ $direction === 'asc' ? 'asc' : 'desc' }}"></i>@endif</a></th>
                        <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'nrc', 'direction' => $sort === 'nrc' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">NRC @if($sort === 'nrc')<i class="fa fa-sort-{{ $direction === 'asc' ? 'asc' : 'desc' }}"></i>@endif</a></th>
                        <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'office', 'direction' => $sort === 'office' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Branch @if($sort === 'office')<i class="fa fa-sort-{{ $direction === 'asc' ? 'asc' : 'desc' }}"></i>@endif</a></th>
                        <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'department', 'direction' => $sort === 'department' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Dept @if($sort === 'department')<i class="fa fa-sort-{{ $direction === 'asc' ? 'asc' : 'desc' }}"></i>@endif</a></th>
                        <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => $sort === 'date' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Date @if($sort === 'date')<i class="fa fa-sort-{{ $direction === 'asc' ? 'asc' : 'desc' }}"></i>@endif</a></th>
                        <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => $sort === 'status' && $direction === 'asc' ? 'desc' : 'asc', 'page' => null]) }}">Status @if($sort === 'status')<i class="fa fa-sort-{{ $direction === 'asc' ? 'asc' : 'desc' }}"></i>@endif</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($referrals as $referral)
                        <tr>
                            <td>{{ $referral->referral_reference }}</td>
                            <td>{{ $referral->client_name }}</td>
                            <td>{{ $referral->nrc_number ?: 'N/A' }}</td>
                            <td>{{ $referral->branch_name ?: 'N/A' }}</td>
                            <td>{{ $referral->referring_department ?: 'N/A' }}</td>
                            <td>{{ $referral->referral_date }}</td>
                            <td>{{ $referral->loan_status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No referrals found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $referrals->links() }}
        </div>
    </div>
</section>

<script>
    $('#referral-province').on('change', function () {
        $('#referral-district, #referral-office').val('');
        this.form.submit();
    });

    $('#referral-district').on('change', function () {
        $('#referral-office').val('');
        this.form.submit();
    });

    $('#referral-office').on('change', function () {
        this.form.submit();
    });
</script>
@endsection
