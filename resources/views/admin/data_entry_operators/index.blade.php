@extends('layouts.super_admin')

@section('content')
    @include('admin.data_entry_operators.data_entry_operator_list', [
        'data_entry_operators' => $dataEntryOperators,
        'managers' => $managers,
        'supervisors' => $supervisors,
        'groups' => $groups,
        'permissions' => $permissions,
        'mgrSupMap' => $mgrSupMap,
    ])
@endsection
