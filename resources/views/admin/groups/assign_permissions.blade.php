<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">@lang('group.permissions.assign_title', ['group_name' => $group->name])</h6>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('groups.assign_permissions', $group->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @foreach ($permissions as $permission)
                        <div class="flex items-center">
                            <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}"
                                @if ($assignedPermissions->contains($permission->id)) checked @endif
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="ml-3 block text-sm font-medium text-gray-700">
                                {{ $permission->name }}
                                <span class="text-gray-500 text-xs ml-1">
                                    (@lang('group.permissions.action_on_subject', ['action' => $permission->action, 'subject' => $permission->subject]))
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.groups.show', $group->id) }}"
                        class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center">
                        @lang('group.permissions.cancel_button')
                    </a>
                    <button type="submit"
                        class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center">
                        @lang('group.permissions.save_button')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
