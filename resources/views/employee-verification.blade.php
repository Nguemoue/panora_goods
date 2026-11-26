@extends('layouts.app')

@section('title', __('frontend.nav.employee_verification'))

@section('content')
    <div class="min-h-screen bg-slate-50 pb-12">
        @livewire('employee-verification')
    </div>

@endsection
