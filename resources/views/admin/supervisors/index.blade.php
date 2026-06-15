@extends('layouts.super_admin')

@section('content')
    @include('admin.supervisors.supervisor_list', [
        'supervisors' => $supervisors, // 👈 use supervisors (not managers)
        'managers' => $managers,
        'groups' => $groups, // 👈 pass groups
    ])
@endsection
