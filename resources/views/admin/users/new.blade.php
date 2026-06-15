<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">New User</h6>
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Users</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            @include('admin.users.form', ['user' => $user])
        </div>
    </div>
</div>