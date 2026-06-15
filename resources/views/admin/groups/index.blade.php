@extends('layouts.super_admin')

@section('content')
    @include('admin.groups.group_list', ['groups' => $groups])
@endsection
