@extends('layouts.super_admin')

@section('content')
    @include('profiles._form', ['profile' => $profile, 'isEdit' => true])
@endsection
