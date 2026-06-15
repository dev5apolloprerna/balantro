<div class="container mx-auto px-4 py-8" data-controller="client-modal">
    <div class="flex justify-between items-center mb-6">
        <h6 class="font-semibold mb-0 dark:text-white">Users</h6>
        <div class="flex space-x-1">
            <button data-action="client-modal#open" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Client</button>
        </div>
    </div>

    @include('admin.users.client_modal')
    
    <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="col-span-12">
            <div class="card !border-0 overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Groups</th>
                                    <th class="!text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->role }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $user->groups->pluck('name')->implode(', ') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium !text-center">
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Show</a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                                            <a href="{{ route('admin.users.assign_groups', $user) }}" class="text-green-600 hover:text-green-900 mr-3">Assign Groups</a>
                                            <a href="{{ route('admin.users.assign_permissions', $user) }}" class="text-blue-600 hover:text-blue-900 mr-3">Assign Permissions</a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>      
        </div>
    </div>
    {{ $users->links() }}
</div>