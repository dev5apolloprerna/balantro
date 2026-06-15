<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">User Details</h6>
            <div class="space-x-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Users</a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->role }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Groups</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->groups->pluck('name')->implode(', ') }}
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Direct Permissions</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($user->permissions->count())
                            <ul>
                                @foreach($user->permissions as $permission)
                                    <li>
                                        {{ $permission->name }} ({{ $permission->action }} on {{ $permission->subject }})
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-400">No direct permissions</span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Group Permissions</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($user->groups->count())
                            @foreach($user->groups as $group)
                                <div class="mb-2">
                                    <span class="font-semibold">{{ $group->name }}:</span>
                                    @if($group->permissions->count())
                                        <ul class="ml-4">
                                            @foreach($group->permissions as $permission)
                                                <li>
                                                    {{ $permission->name }} ({{ $permission->action }} on {{ $permission->subject }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400">No permissions</span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <span class="text-gray-400">No group permissions</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>