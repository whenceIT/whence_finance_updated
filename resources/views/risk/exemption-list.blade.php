@extends('layouts.master')

@section('title')
    Exemption List
@endsection

@section('content')
@include('components.kilo-alert')
@include('risk.partials.exemption-list-table')
@endsection