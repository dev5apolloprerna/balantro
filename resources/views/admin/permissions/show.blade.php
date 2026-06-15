<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">Permission Details</h6>
            <div class="space-x-3">
                <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                <a href="{{ route('admin.permissions.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Permissions</a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $permission->name }}</dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Action</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $permission->action }}</dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Subject</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $permission->subject }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Conditions</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <pre class="bg-gray-50 p-4 rounded-md overflow-x-auto">
                            @if($permission->conditions)
                                {{ json_encode($permission->conditions, JSON_PRETTY_PRINT) }}
                            @endif
                        </pre>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>