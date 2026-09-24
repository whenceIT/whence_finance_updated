@extends('layouts.master')

@section('title')
    Exemption List
@endsection

@section('content')
@include('components.kilo-alert')

<button type="button" id="btnOpenDepositExemptModal" class="btn btn-outline-primary btn-sm" style="border-radius:6px; margin-top:4px;">
    <i class="fa fa-calendar-times-o"></i> Exempt Months
</button>
@include('risk.partials.exemption-list-table')


@include('risk.partials.deposit-exempt-modal', ['depositTypes' => $depositTypes ?? [], 'offices' => $offices ?? []])

<script>
document.getElementById('btnOpenDepositExemptModal').addEventListener('click', function() {
    if (typeof window.openEditExemptModal === 'function') {
        window.openEditExemptModal();
    }
});
</script>
@endsection