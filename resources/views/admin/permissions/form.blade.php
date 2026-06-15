<form action="{{ isset($permission->id) ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}" method="POST" class="space-y-6">
    @csrf
    @if(isset($permission->id))
        @method('PUT')
    @endif

    @if($errors->any())
        <div class="bg-red-50 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        {{ trans_choice('There is :count error|There are :count errors', $errors->count()) }} prohibited this permission from being saved:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $permission->name ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    </div>

    <div>
        <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
        <input type="text" name="action" id="action" value="{{ old('action', $permission->action ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    </div>

    <div>
        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
        <input type="text" name="subject" id="subject" value="{{ old('subject', $permission->subject ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    </div>

    <div>
        <label for="conditions" class="block text-sm font-medium text-gray-700">Conditions</label>
        <textarea name="conditions" id="conditions" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('conditions', $permission->conditions ? json_encode($permission->conditions) : '') }}</textarea>
        <p class="mt-1 text-sm text-gray-500">Enter conditions as JSON (e.g., {"user_id": 1})</p>
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('admin.permissions.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
        </a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ isset($permission->id) ? 'Update' : 'Create' }}
        </button>
    </div>
</form>