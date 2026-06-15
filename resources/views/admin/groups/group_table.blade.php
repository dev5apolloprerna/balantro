<div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">

        {{-- <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-neutral-100">Groups</h2>
        </div> --}}

        <div
            class="mt-5 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden group-block">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] dark:bg-gray-900/40 sticky top-0 z-10">
                        <tr class="text-black-900 dark:text-gray-300">
                            <th scope="col" class="px-2 py-1">Name</th>
                            <th scope="col" class="px-2 py-1 text-center">Users Count</th>
                            <!-- <th scope="col" class="px-2 py-1 text-center">Permissions Count</th> -->
                            <th scope="col" class="px-2 py-1 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                        @foreach ($groups as $group)
                            <tr class="hover:bg-neutral-900/40 transition">
                                <td class="px-2 py-1 text-neutral-100 font-medium">
                                    {{ $group->name }}
                                </td>

                                <td class="px-2 py-1 text-center">
                                    <span
                                        class="inline-flex items-center rounded-full bg-neutral-800/80 px-2.5 py-1 text-xs font-medium text-neutral-200">
                                        {{ $group->users_count }}
                                    </span>
                                </td>

                                <!-- <td class="px-2 py-1 text-center">
                                    <span
                                        class="inline-flex items-center rounded-full bg-violet-900/40 px-2.5 py-1 text-xs font-medium text-violet-300">
                                        {{ $group->permissions_count }}
                                    </span>
                                </td> -->

                                <td class="px-2 py-1 text-center">
                                    <div class="flex items-center justify-center gap-2.5">
                                        <!-- <button type="button"
                                            class="rounded-full bg-purple-100 p-2 text-purple-700 ring-1 ring-inset ring-purple-200 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:ring-purple-800"
                                            title="Permissions" onclick="openPermissionsModal({{ $group->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5zm0 2.18l7 3.12v4.92c0 1.7-.5 3.43-1.35 4.95C16 14.94 13.26 14.5 12 14.5s-4 .44-5.65 1.67C5.5 14.65 5 12.92 5 11.22V6.3zM12 6a3.5 3.5 0 0 0-3.5 3.5A3.5 3.5 0 0 0 12 13a3.5 3.5 0 0 0 3.5-3.5A3.5 3.5 0 0 0 12 6m0 2a1.5 1.5 0 0 1 1.5 1.5A1.5 1.5 0 0 1 12 11a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 12 8m0 8.5c1.57 0 3.64.61 4.53 1.34C15.29 19.38 13.7 20.55 12 21c-1.7-.45-3.29-1.62-4.53-3.16c.9-.73 2.96-1.34 4.53-1.34">
                                                </path>
                                            </svg>
                                        </button> -->
                                        {{-- Edit --}}
                                        <button type="button"
                                            onclick="openEditModal({{ $group->id }}, '{{ e($group->name) }}')"
                                            class="rounded-full bg-emerald-100 p-2 text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 24 24">
                                                <g fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2">
                                                    <path
                                                        d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                    </path>
                                                    <path
                                                        d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z">
                                                    </path>
                                                </g>
                                            </svg>
                                        </button>

                                        {{-- Delete --}}
                                        {{-- <form action="{{ route('groups.destroy', $group->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this group?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="rounded-full bg-rose-100 p-2 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800"
                                                title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M10 5h4a2 2 0 1 0-4 0M8.5 5a3.5 3.5 0 1 1 7 0h5.75a.75.75 0 0 1 0 1.5h-1.32l-1.17 12.111A3.75 3.75 0 0 1 15.026 22H8.974a3.75 3.75 0 0 1-3.733-3.389L4.07 6.5H2.75a.75.75 0 0 1 0-1.5zm2 4.75a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0zM14.25 9a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-1.5 0v-7.5a.75.75 0 0 1 .75-.75m-7.516 9.467a2.25 2.25 0 0 0 2.24 2.033h6.052a2.25 2.25 0 0 0 2.24-2.033L18.424 6.5H5.576z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form> --}}

                                        {{-- Permissions --}}


                                        <button type="button" title="Delete"
                                            class="rounded-full bg-rose-100 p-2 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800"
                                            data-open-delete data-id="{{ $group->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 24 24">
                                                <g fill="none">
                                                    <path
                                                        d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z">
                                                    </path>
                                                    <path fill="currentColor"
                                                        d="M14.28 2a2 2 0 0 1 1.897 1.368L16.72 5H20a1 1 0 1 1 0 2l-.003.071l-.867 12.143A3 3 0 0 1 16.138 22H7.862a3 3 0 0 1-2.992-2.786L4.003 7.07L4 7a1 1 0 0 1 0-2h3.28l.543-1.632A2 2 0 0 1 9.721 2zm3.717 5H6.003l.862 12.071a1 1 0 0 0 .997.929h8.276a1 1 0 0 0 .997-.929zM10 10a1 1 0 0 1 .993.883L11 11v5a1 1 0 0 1-1.993.117L9 16v-5a1 1 0 0 1 1-1m4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1m.28-6H9.72l-.333 1h5.226z">
                                                    </path>
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="">
                    {{ $groups->links() }}
                </div>
            </div>

            @include('shared.confirm_delete_modal', [
                'resource_name' => __('group.resource_name'),
            ])
        </div>
    </div>
</div>
