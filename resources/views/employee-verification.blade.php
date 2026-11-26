@extends('layouts.app')

@section('title', __('frontend.nav.employee_verification'))

@section('content')
    <div class="py-2 sm:py-4">
        @livewire('employee-verification')
    </div>
@endsection
