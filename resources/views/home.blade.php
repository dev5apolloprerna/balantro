@extends('layouts.super_admin')

@section('content')
    <!-- Main Content -->

    <!-- Header -->


    <!-- Page Content -->
    <!-- <div class="mb-8">
        <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Welcome
            {{ auth()->user()->name }}!</h1>
    </div> -->

    <!-- Documents Section -->
    @if (auth()->user()->role == \App\Models\User::ROLES['super_admin'])
        @include('super_admin_dashboard.index')
    @elseif (auth()->user()->role == \App\Models\User::ROLES['supervisor'])
        @include('supervisor_dashboard.index')
    @elseif (auth()->user()->role == \App\Models\User::ROLES['manager'])
        @include('manager_dashboard.index')
    @elseif (auth()->user()->role == \App\Models\User::ROLES['data_entry_operator'])
        @include('data_entry_operator_dashboard.index')
    @elseif (auth()->user()->role == \App\Models\User::ROLES['client'])
        @include('client_dashboard.index')
    @endif
@endsection
