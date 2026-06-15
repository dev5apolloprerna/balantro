{{-- resources/views/admin/managers/manager_list.blade.php --}}
@php($groups = $groups ?? collect())
@php($supervisors = $supervisors ?? collect())
<div class="container mx-auto px-2">
    <div class="flex justify-between items-center mb-6">
        <h6 class="text-lg font-semibold text-gray-800 dark:text-white">

            {{ __('Supervisors') }}
        </h6>

        {{-- @can('create', App\Models\Supervisor::class) --}}
        {{-- <button type="button"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition cursor-pointer"
                onclick="openManagerModal()">
                @lang('admin.common.supervisors.Supervisors')
            </button> --}}
        @if(auth()->user()->can('supervisors.create') || $user->role === \App\Models\User::ROLES['super_admin'])
                <button type="button" onclick="openManagerModal()"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-2 py-1 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor">
                        <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    New Supervisor
                </button>
            @endif
        
        {{-- @endcan --}}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="col-span-12">
            <div class="shadow rounded-2xl overflow-hidden">
                <div class="">
                    <div id="supervisor-table" class="overflow-x-auto">
                        @include('admin.supervisors.supervisor_table', [
                            'supervisors' => $supervisors,
                            'groups' => $groups,
                        ])
                    </div>
                    @include('admin.supervisors.assign_groups_modal') {{-- no per-row props --}}
                    @include('admin.supervisors.edit_modal')
                    @include('admin.supervisors.permissions_modal')
                    {{-- Include the modal with id="addManagerModal" --}}
                    @include('admin.supervisors.create_modal')
                    @include('admin.supervisors.assign_manager_modal', ['managers' => $managers])
                    @if ($supervisors->count())
                        <div class="mt-4">
                            {{ $supervisors->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div id="deoDelete" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60" onclick="closeModal('deoDelete')"></div>
    <div class="relative mx-auto mt-32 w-full max-w-md px-4">
        <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-800">
            <form id="deoDeleteForm" method="POST">@csrf @method('DELETE')
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Delete operator?</h3>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">This action cannot be undone.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-700"
                        onclick="closeModal('deoDelete')">Cancel</button>
                    <button
                        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function openManagerModal() {
            const modal = document.getElementById('addManagerModal');
            if (!modal) return console.warn("Modal not found");
            modal.classList.remove('hidden');
        }

        function closeManagerModal() {
            const modal = document.getElementById('addManagerModal');
            if (!modal) return;
            modal.classList.add('hidden');
        }

        function setAction(form, urlTemplate, id) {
            form.action = urlTemplate.replace(':id', id);
        }

        function show(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('hidden');
        }

        function hide(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        }

        function openEditModal(id, name, email) {
            const form = document.getElementById('editForm');
            setAction(form, "{{ route('supervisors.update', ':id') }}", id);
            document.getElementById('edit_name').value = name || '';
            document.getElementById('edit_email').value = email || '';
            show('editModal');
        }

        function closeEditModal() {
            hide('editModal');
        }

        // GROUPS
        function openGroupsModal(id) {
            const form = document.getElementById('assignGroupsForm');
            setAction(form, "{{ route('supervisors.assignGroups', ':id') }}", id);

            const list = document.getElementById('groupsList');
            list.innerHTML = '<div class="text-sm text-neutral-300">Loading…</div>';

            fetch("{{ route('supervisors.getGroups', ':id') }}".replace(':id', id), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    // Convert everything to numbers to avoid "string vs number" misses
                    const assigned = new Set((data.assigned_group_ids || []).map(Number));

                    list.innerHTML = (data.groups || []).map(g => {
                        const gid = Number(g.id);
                        const checked = assigned.has(gid) ? 'checked' : '';
                        return `
        <label class="flex items-center gap-3 rounded-lg border border-neutral-700 p-3 hover:bg-neutral-800">
          <input type="checkbox" name="group_ids[]" value="${gid}" ${checked}
                 class="h-4 w-4 rounded border-neutral-500 text-blue-500 focus:ring-blue-500">
          <div>
            <div class="font-medium">${g.name}</div>
            <div class="text-xs text-neutral-400">${g.permissions_count} permissions</div>
          </div>
        </label>
      `;
                    }).join('');
                })
                .catch(() => list.innerHTML = '<div class="text-sm text-rose-400">Failed to load groups.</div>');

            show('assignGroupsModal');
        }

        function closeGroupsModal() {
            hide('assignGroupsModal');
        }

        // PERMISSIONS
        function openPermissionsModal(id) {
            const form = document.getElementById('permissionsForm');
            setAction(form, "{{ route('supervisors.assignPermissions', ':id') }}", id);

            const list = document.getElementById('permissionsList');
            list.innerHTML = '<div class="text-sm text-neutral-300">Loading…</div>';

            fetch("{{ route('supervisors.getPermissions', ':id') }}".replace(':id', id), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    console.log(data);
                    // Coerce all IDs to Number to avoid "string vs number" misses
                    const groupSet = new Set((data.group_permission_ids || []).map(Number));
                    const allowSet = new Set((data.assigned_permissions || []).map(Number));
                    const denySet = new Set((data.denied_permissions || []).map(Number));

                    const rows = (data.permissions || []).map(p => {
                        const pid = Number(p.id);
                        const allowChecked = allowSet.has(pid) ? 'checked' : '';
                        const denyChecked = (groupSet.has(pid) && denySet.has(pid)) ? 'checked' : '';

                        return `
                                <div class="space-y-2">
                                <label class="flex items-center gap-3 rounded-lg border border-neutral-700 p-3 hover:bg-neutral-800">
                                    <input type="checkbox" name="permission_ids[]" value="${pid}" ${allowChecked}
                                        class="h-4 w-4 rounded border-neutral-500 text-purple-500 focus:ring-purple-500">
                                    <span class="text-sm">${p.name} <span class="text-neutral-400">(${p.action} ${p.subject})</span></span>
                                </label>
                                </div>
                            `;
                    }).join('');

                    const denies = (data.permissions || [])
                        .filter(p => new Set((data.group_permission_ids || []).map(Number)).has(Number(p.id)))
                        .map(p => {
                            const pid = Number(p.id);
                            const denyChecked = denySet.has(pid) ? 'checked' : '';
                            return `
                                <label class="flex items-center gap-3 rounded-lg border border-neutral-700 p-3 hover:bg-neutral-800">
                                    <input type="checkbox" name="denied_permission_ids[]" value="${pid}" ${denyChecked}
                                        class="h-4 w-4 rounded border-neutral-500 text-rose-500 focus:ring-rose-500">
                                    <span class="text-sm">${p.name} <span class="text-neutral-400">(${p.action} ${p.subject})</span></span>
                                </label>
                                `;
                        }).join('');

                    list.innerHTML = `
                        <div class="space-y-2">
                            <h3 class="text-base font-semibold">Allow (explicitly grant)</h3>
                            ${rows}
                        </div>
                        <hr class="my-4 border-neutral-700"/>
                        
                        `;
                        // <div class="space-y-2">
                        //     <h3 class="text-base font-semibold">Deny (override group permission)</h3>
                        //     ${denies || '<div class="text-sm text-neutral-400">No group permissions to override.</div>'}
                        // </div>
                }).catch(() => list.innerHTML = '<div class="text-sm text-rose-400">Failed to load permissions.</div>');

            show('permissionsModal');
        }

        function closePermissionsModal() {
            hide('permissionsModal');
        }

        const routes = {
            destroy: "{{ url('admin/supervisors') }}/__ID__",
        };

        document.addEventListener('click', (e) => {
            let btn;

            btn = e.target.closest('[data-open-delete]');
            if (btn) {
                const id = btn.dataset.id;
                const form = document.getElementById('deoDeleteForm');
                form.action = routes.destroy.replace('__ID__', id);
                openModal('deoDelete');
                return;
            }
        });

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.documentElement.classList.add('overflow-hidden');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hidden');
            document.documentElement.classList.remove('overflow-hidden');
        }
    </script>
    <script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js"></script>
    <script src="{{ asset('assets/firebase/firebase.js') }}"></script>
@endpush
