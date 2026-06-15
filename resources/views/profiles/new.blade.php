@extends('layouts.app')

@section('content')
    @include('profile._form', ['profile' => $profile, 'isEdit' => false])
@endsection