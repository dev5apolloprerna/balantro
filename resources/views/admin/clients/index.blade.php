{{-- resources/views/admin/clients/index.blade.php --}}
@extends('layouts.super_admin')

@section('content')
    @include('admin.clients.client_list', [
        'clients' => $clients,
        'data_entry_operators' => $dataEntryOperators,
        'managers' => $managers,
        'supervisors' => $supervisors,
        'groups' => $groups,
        'permissions' => $permissions,
        'mgrSupMap' => $mgrSupMap,
    ])

    {{-- Modals --}}
    @include('admin.clients.modals.assign_users_plain')
    @include('admin.clients.modals.assign_groups_plain')
    @include('admin.clients.modals.assign_permissions_plain')

    {{-- </div> --}}

    @push('scripts')
        <script>
            window.CLIENT_ROUTES = {
                assignUsers: @json(route('clients.assignUsers', ['client' => '__ID__'])),
                mgrSup: @json(route('clients.managerSupervisors', ['manager' => '__ID__'])),
                supDeo: @json(route('clients.supervisorDataEntryOperators', ['supervisor' => '__ID__'])),
                assignGroups: @json(route('clients.assignGroups', ['client' => '__ID__'])),
                getGroups: @json(route('clients.getGroups', ['client' => '__ID__'])),
                assignPermissions: @json(route('clients.assignPermissions', ['client' => '__ID__'])),
                getPermissions: @json(route('clients.getPermissions', ['client' => '__ID__'])),

                clientStore: @json(route('clients.store')),
                clientUpdate: @json(route('clients.update', ['client' => '__ID__'])),
                clientEdit: @json(route('clients.edit', ['client' => '__ID__'])),
            };
        </script>
        @include('admin.clients.modals_js') {{-- <- path must match the file you edited --}}
    @endpush
@endsection
