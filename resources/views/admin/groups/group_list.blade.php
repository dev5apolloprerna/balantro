{{-- resources/views/admin/managers/manager_list.blade.php --}}
<div class="container mx-auto px-2">
    <div class="flex justify-between items-center mb-3">
        <h6 class="text-lg font-semibold text-gray-800 dark:text-white">

            {{ __('Groups') }}
            {{-- @lang('admin.managers.table.title') --}}
        </h6>

        {{--  @can('create', App\Models\Group::class)  --}}
        {{--  <button type="button"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition cursor-pointer"
            onclick="openManagerModal()">
            New Group
        </button>  --}}
        {{--  @endcan  --}}
        <button type="button" onclick="openManagerModal()"
            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                stroke="currentColor">
                <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            New Group
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="col-span-12">
            <div class="shadow rounded-2xl overflow-hidden">
                <div class="">
                    <div id="manager-table" class="overflow-x-auto">
                        @include('admin.groups.group_table', [
                            'groups' => $groups,
                        ])
                    </div>
                    @include('admin.managers.assign_groups_modal') {{-- no per-row props --}}
                    @include('admin.groups.edit_modal')
                    @include('admin.managers.permissions_modal')
                    {{-- Include the modal with id="addManagerModal" --}}
                    @include('admin.groups.create_modal')

                    @if ($groups->count())
                        <div class="mt-4">
                            {{ $groups->links('pagination::tailwind') }}
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
            setAction(form, "{{ route('groups.update', ':id') }}", id);
            document.getElementById('edit_name').value = name || '';
            show('editModal');
        }

        function closeGroupsModal() {
            hide('assignGroupsModal');
        }

        // PERMISSIONS
        function openPermissionsModal(id) {
            const form = document.getElementById('permissionsForm');
            setAction(form, "{{ route('groups.assignPermissions', ':id') }}", id);

            const list = document.getElementById('permissionsList');
            list.innerHTML = '<div class="text-sm text-neutral-300">Loading…</div>';

            fetch("{{ route('groups.getPermissions', ':id') }}".replace(':id', id), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
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
      <div class="space-y-2">
        <h3 class="text-base font-semibold">Deny (override group permission)</h3>
        ${denies || '<div class="text-sm text-neutral-400">No group permissions to override.</div>'}
      </div>
    `;
                })
                .catch(() => list.innerHTML = '<div class="text-sm text-rose-400">Failed to load permissions.</div>');

            show('permissionsModal');
        }

        function closePermissionsModal() {
            hide('permissionsModal');
        }

        function openManagerModal() {
            const modal = document.getElementById('addManagerModal');
            if (modal) {
                modal.classList.remove('hidden'); // Remove the 'hidden' class to show the modal
            }
        }

        function closeManagerModal() {
            const modal = document.getElementById('addManagerModal');
            if (modal) {
                modal.classList.add('hidden'); // Add the 'hidden' class to hide the modal
            }
        }

        const routes = {
            destroy: "{{ url('admin/groups') }}/__ID__",
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
@endpush
