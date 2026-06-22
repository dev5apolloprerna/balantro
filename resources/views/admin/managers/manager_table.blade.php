{{-- resources/views/admin/managers/manager_table.blade.php --}}
<div class="space-y-6">
    {{-- <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Managers</h1>

        <button type="button" onclick="openManagerModal()"
            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                stroke="currentColor">
                <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            New Manager
        </button>
    </div> --}}

    <div
        class="rounded-xl border border-neutral-200 shadow-sm dark:border-neutral-700">
        <div class="table-scroll-safe overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50/80 dark:bg-neutral-800/60">
                    <tr class="text-left text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                        <th class="px-2 py-1">Name</th>
                        <th class="px-2 py-1">Email</th>
                        <th class="px-2 py-1">Groups</th>
                        <th class="px-2 py-1 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                    @if ($managers->count())
                        @foreach ($managers as $manager)
                            @include('admin.managers.manager_row', ['manager' => $manager])
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-neutral-500 dark:text-neutral-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-10 w-10" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path d="M21 21l-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="text-base font-medium">No managers found</p>
                                    <p class="text-sm">Click “New Manager” to create one.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
