<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">Assign Permissions to {{ $user->email }}</h6>
            <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">Back to User</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('admin.users.assign_permissions', $user) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @foreach($permissions as $permission)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <label class="ml-3 block text-sm font-medium text-gray-700">
                                    {{ $permission->name }}
                                    <span class="text-gray-500 text-xs ml-1">
                                        ({{ $permission->action }} on {{ $permission->subject }})
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                @if($groupPermissionIds->contains($permission->id))
                                    <input type="checkbox" name="negative_permission_ids[]" value="{{ $permission->id }}" 
                                           @if($deniedPermissions->contains($permission->id)) checked @endif
                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    <label class="ml-2 text-sm text-red-600">Deny</label>
                                @else
                                    <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" 
                                           @if($assignedPermissions->contains($permission->id)) checked @endif
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 text-sm text-indigo-600">Assign</label>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.users.show', $user) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>