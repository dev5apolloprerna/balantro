@extends('layouts.super_admin')
@section('content')
    @include('admin.managers.manager_list', ['managers' => $managers])
@endsection
