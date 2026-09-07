{{-- OWNERS: Manager + Supervisor, with cascading supervisor list --}}
<div id="deoAssignOwners" class="fixed inset-0 z-[100] hidden">

    <!-- Overlay -->
    <div
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        onclick="closeModal('deoAssignOwners')">
    </div>

    <!-- Center Wrapper -->
    <div class="absolute inset-0 z-10 flex items-center justify-center p-4">

        <div class="relative w-full max-w-lg">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-xl
                        dark:border-neutral-700 dark:bg-neutral-900">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4
                            border-b border-slate-200 dark:border-neutral-700">

                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white">
                        Assign Manager & Supervisor
                    </h3>

                    <button
                        type="button"
                        onclick="closeModal('deoAssignOwners')"
                        class="flex h-8 w-8 items-center justify-center rounded-full
                               text-slate-400
                               hover:bg-slate-100 hover:text-slate-700
                               dark:text-neutral-400
                               dark:hover:bg-neutral-800 dark:hover:text-white">
                        ✕
                    </button>

                </div>

                <form id="deoAssignOwnersForm" method="POST" class="px-6 py-5">
                    @csrf

                    <div class="space-y-4">

                        <!-- Manager -->
                        <div>
                            <label class="mb-2 block text-sm text-slate-700 dark:text-slate-300">
                                Manager
                            </label>

                            <div class="relative w-full">

                                <select
                                    id="deo_owner_manager_id"
                                    name="manager_id"
                                    class="block h-11 w-full appearance-none rounded-lg
                                           border border-slate-300
                                           bg-white
                                           px-3 pr-10
                                           text-sm text-slate-900
                                           focus:border-blue-500
                                           focus:ring-2 focus:ring-blue-500
                                           dark:border-neutral-700
                                           dark:bg-black
                                           dark:text-white">

                                    <option value="">— None —</option>

                                    @foreach ($managers as $m)
                                        <option value="{{ $m->id }}">
                                            {{ $m->name }}
                                        </option>
                                    @endforeach

                                </select>

                                <!-- Custom centered arrow -->
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-3
                                           flex items-center justify-center
                                           text-slate-500 dark:text-neutral-300">

                                    <i class="fa-solid fa-chevron-down text-xs"></i>

                                </div>

                            </div>
                        </div>


                        <!-- Supervisor -->
                        <div>
                            <label class="mb-2 block text-sm text-slate-700 dark:text-slate-300">
                                Supervisor
                            </label>

                            <div class="relative w-full">

                                <select
                                    id="deo_owner_supervisor_id"
                                    name="supervisor_id"
                                    class="block h-11 w-full appearance-none rounded-lg
                                           border border-slate-300
                                           bg-white
                                           px-3 pr-10
                                           text-sm text-slate-900
                                           focus:border-blue-500
                                           focus:ring-2 focus:ring-blue-500
                                           dark:border-neutral-700
                                           dark:bg-black
                                           dark:text-white">

                                    <option value="">— None —</option>

                                </select>

                                <!-- Custom centered arrow -->
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-3
                                           flex items-center justify-center
                                           text-slate-500 dark:text-neutral-300">

                                    <i class="fa-solid fa-chevron-down text-xs"></i>

                                </div>

                            </div>

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Supervisors list updates when you change the manager.
                            </p>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex justify-end gap-3">

                        <button
                            type="button"
                            onclick="closeModal('deoAssignOwners')"
                            class="rounded-lg border border-slate-300
                                   px-4 py-2 text-sm
                                   text-slate-700
                                   hover:bg-slate-100
                                   dark:border-neutral-700
                                   dark:text-slate-200
                                   dark:hover:bg-neutral-800">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg bg-blue-600
                                   px-4 py-2
                                   text-sm font-semibold text-white
                                   hover:bg-blue-700">
                            Save
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<div id="deoAssignGroups" class="fixed inset-0 z-[100] hidden">

    <!-- Overlay -->
    <div
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        onclick="closeModal('deoAssignGroups')">
    </div>

    <!-- Center Wrapper -->
    <div class="absolute inset-0 z-10 flex items-center justify-center p-4">

        <div class="w-full max-w-2xl overflow-hidden rounded-2xl
                    border border-slate-200 bg-white shadow-xl
                    dark:border-neutral-700 dark:bg-neutral-900">

            <!-- Header -->
            <div class="flex items-center justify-between
                        border-b border-slate-200
                        px-6 py-4
                        dark:border-neutral-700">

                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">
                    Assign Groups
                </h3>

                <button
                    type="button"
                    onclick="closeModal('deoAssignGroups')"
                    class="flex h-8 w-8 items-center justify-center rounded-full
                           text-slate-400
                           hover:bg-slate-100 hover:text-slate-700
                           dark:bg-neutral-800
                           dark:text-neutral-300
                           dark:hover:bg-neutral-700
                           dark:hover:text-white">
                    ✕
                </button>

            </div>

            <form id="deoAssignGroupsForm" method="POST">
                @csrf

                <!-- Groups -->
                <div class="px-6 py-5">

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                        @foreach ($groups as $g)

                            <label
                                class="flex min-h-[66px] cursor-pointer items-center gap-3
                                       rounded-lg border border-slate-200
                                       bg-white px-3 py-3
                                       transition
                                       hover:bg-slate-50
                                       dark:border-neutral-700
                                       dark:bg-black
                                       dark:hover:border-neutral-600
                                       dark:hover:bg-neutral-950">

                                <input
                                    type="checkbox"
                                    name="group_ids[]"
                                    value="{{ $g->id }}"
                                    class="h-4 w-4 rounded
                                           border-slate-300
                                           bg-white
                                           text-blue-600
                                           focus:ring-blue-500
                                           dark:border-neutral-600
                                           dark:bg-neutral-800
                                           dark:text-blue-600">

                                <div class="min-w-0">

                                    <div class="font-semibold leading-5
                                                text-slate-900 dark:text-white">
                                        {{ $g->name }}
                                    </div>

                                    <div class="mt-0.5 text-xs
                                                text-slate-500
                                                dark:text-neutral-400">
                                        {{ $g->permissions_count ?? 0 }} permissions
                                    </div>

                                </div>

                            </label>

                        @endforeach

                        @if (($groups ?? collect())->isEmpty())
                            <p class="col-span-full text-sm
                                      text-slate-500 dark:text-neutral-400">
                                No groups found.
                            </p>
                        @endif

                    </div>

                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3
                            border-t border-slate-200
                            px-6 py-4
                            dark:border-neutral-700">

                    <button
                        type="button"
                        onclick="closeModal('deoAssignGroups')"
                        class="rounded-lg
                               border border-slate-300
                               bg-white
                               px-4 py-2
                               text-sm font-medium text-slate-700
                               hover:bg-slate-100
                               dark:border-neutral-600
                               dark:bg-neutral-900
                               dark:text-white
                               dark:hover:bg-neutral-800">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg
                               bg-primary-600
                               px-4 py-2
                               text-sm font-semibold text-white
                               hover:bg-primary-700">
                        Save
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- PERMISSIONS --}}
<div id="deoAssignPermissions" class="hidden fixed inset-0 z-50">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Center Wrapper -->
    <div class="absolute inset-0 z-10 flex items-center justify-center p-4">

        <!-- Modal -->
        <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl
                    border border-slate-200
                    bg-white text-slate-900 shadow-xl
                    dark:border-neutral-700
                    dark:bg-neutral-900 dark:text-white">

            <!-- Header -->
            <div class="flex items-center justify-between
                        border-b border-slate-200
                        px-6 py-4
                        dark:border-neutral-700">

                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                    Assign Permissions
                </h2>

                <button
                    type="button"
                    onclick="closeModal('deoAssignPermissions')"
                    class="flex h-8 w-8 items-center justify-center rounded-full
                           text-slate-400
                           hover:bg-slate-100 hover:text-slate-700
                           dark:text-neutral-400
                           dark:hover:bg-neutral-800 dark:hover:text-white">
                    ✕
                </button>

            </div>

            <form id="deoAssignPermissionsForm" method="POST">
                @csrf

                <!-- Permissions -->
                <div class="max-h-[60vh] overflow-y-auto p-5 space-y-2">

                    @foreach ($permissions as $p)

                        <label
                            class="flex items-center gap-3 rounded-xl
                                   border border-slate-200
                                   bg-white
                                   p-3
                                   transition
                                   hover:border-slate-300
                                   hover:bg-slate-100
                                   dark:border-neutral-700
                                   dark:bg-black
                                   dark:hover:border-neutral-600
                                   dark:hover:bg-neutral-900">

                            <input
                                type="checkbox"
                                name="permission_ids[]"
                                value="{{ $p->id }}"
                                class="h-4 w-4 rounded
                                       border-slate-300
                                       bg-white
                                       text-blue-600
                                       focus:ring-blue-500
                                       dark:border-neutral-600
                                       dark:bg-black
                                       dark:text-white
                                       dark:focus:ring-neutral-500">

                            <span class="text-sm text-slate-800 dark:text-white">
                                {{ $p->name }}

                                <span class="text-slate-400 dark:text-neutral-400">
                                    ({{ $p->action }} {{ $p->subject }})
                                </span>
                            </span>

                        </label>

                    @endforeach

                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3
                            border-t border-slate-200
                            bg-white
                            px-6 py-4
                            dark:border-neutral-700
                            dark:bg-neutral-900">

                    <button
                        type="button"
                        onclick="closeModal('deoAssignPermissions')"
                        class="rounded-md
                               border border-slate-300
                               bg-white
                               px-4 py-2
                               text-sm text-slate-700
                               hover:bg-slate-50
                               dark:border-neutral-600
                               dark:bg-neutral-900
                               dark:text-neutral-200
                               dark:hover:bg-neutral-800">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="rounded-md
                               bg-primary-600
                               px-4 py-2
                               text-sm font-medium text-white
                               hover:bg-primary-700">
                        Save
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

            </form>

        </div>

    </div>

</div>


{{-- EDIT --}}
<!-- <div id="deoEdit" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60" onclick="closeModal('deoEdit')"></div>
    <div class="relative mx-auto mt-24 w-full max-w-lg px-4">
        <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-800">
            <form id="deoEditForm" method="POST">@csrf @method('PATCH')
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Edit Data Entry Operator</h3>
                <input type="hidden" name="fcm_token" id="e_fcm_token">
                <input type="hidden" name="device_type" id="e_device_type">
                <input type="hidden" name="browser_name" id="e_browser_name">
                <input type="hidden" name="os_name" id="e_os_name">
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm text-slate-700 dark:text-slate-300">Name</label>
                        <input id="deo_edit_name" name="name" type="text"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-700 dark:text-slate-300">Email</label>
                        <input id="deo_edit_email" name="email" type="email"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-700"
                        onclick="closeModal('deoEdit')">Cancel</button>
                    <button
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

@include('admin.shared.user_form_modal', [
    'modalId' => 'deoEdit',
    'formId' => 'deoEditForm',
    'title' => 'Edit Data Entry Operator',
    'closeAction' => "closeModal('deoEdit')",
    'nameId' => 'deo_edit_name',
    'emailId' => 'deo_edit_email',
    'mode' => 'edit',
    'method' => 'PATCH',
])

{{-- DELETE --}}
<div id="deoDelete" class="fixed inset-0 z-[100] hidden">
    <!-- Overlay -->
    <div
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        onclick="closeModal('deoDelete')">
    </div>
    <!-- Center -->
    <div class="absolute inset-0 z-10 flex items-center justify-center p-4">

        <div class="w-full max-w-md">

            <div class="overflow-hidden rounded-2xl
                        border border-slate-200
                        bg-white shadow-xl
                        dark:border-neutral-700
                        dark:bg-neutral-900">

                <!-- Header -->
                <div class="flex items-center justify-between
                            border-b border-slate-200
                            px-6 py-4
                            dark:border-neutral-700">

                    <h3 class="text-lg font-semibold
                               text-slate-900 dark:text-white">
                        Delete operator?
                    </h3>

                    <!-- Close Icon -->
                    <button
                        type="button"
                        onclick="closeModal('deoDelete')"
                        class="flex h-8 w-8 items-center justify-center rounded-full
                               text-slate-400 
                               hover:bg-gray-200 hover:text-slate-700
                               dark:text-neutral-400
                               dark:hover:bg-neutral-800 dark:hover:text-white">
                        ✕
                    </button>

                </div>

                <!-- Body -->
                <form id="deoDeleteForm" method="POST" class="px-6 py-5">
                    @csrf
                    @method('DELETE')

                    <p class="text-sm
                              text-slate-600
                              dark:text-neutral-400">
                        This action cannot be undone.
                    </p>

                    <!-- Footer -->
                    <div class="mt-6 flex justify-end gap-3">

                        <button
                            type="button"
                            onclick="closeModal('deoDelete')"
                            class="rounded-md border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-neutral-800">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg
                                   bg-rose-600
                                   px-4 py-2
                                   text-sm font-semibold text-white
                                   hover:bg-rose-700">
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ADD (Create) --}}
<!-- <div id="deoAdd" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60" onclick="closeModal('deoAdd')"></div>
    <div class="relative mx-auto mt-24 w-full max-w-lg px-4">
        <div class="rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-800">
            <form id="deoAddForm" method="POST">
                @csrf
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Add Data Entry Operator</h3>
                <input type="hidden" name="fcm_token" id="c_fcm_token">
                <input type="hidden" name="device_type" id="c_device_type">
                <input type="hidden" name="browser_name" id="c_browser_name">
                <input type="hidden" name="os_name" id="c_os_name">
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm text-slate-700 dark:text-slate-300">Name</label>
                        <input name="name" type="text" required
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm text-slate-700 dark:text-slate-300">Email</label>
                        <input name="email" type="email"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                    </div>
                    {{-- <div>
                        <label class="mb-1 block text-sm text-slate-700 dark:text-slate-300">Password</label>
                        <input name="password" type="password"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                    </div> --}}
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-700"
                        onclick="closeModal('deoAdd')">Cancel</button>
                    <button
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

@include('admin.shared.user_form_modal', [
    'modalId' => 'deoAdd',
    'formId' => 'deoAddForm',
    'title' => 'Add Data Entry Operator',
    'closeAction' => "closeModal('deoAdd')",
    'nameId' => 'deo_create_name',
    'emailId' => 'deo_create_email',
])

<script>
    const routes = {
        assignOwners: "{{ url('admin/data_entry_operators') }}/__ID__/assign_users",
        assignGroups: "{{ url('admin/data_entry_operators') }}/__ID__/assign_groups",
        assignPermissions: "{{ url('admin/data_entry_operators') }}/__ID__/assign_permissions",
        getPermissions: "{{ url('admin/data_entry_operators') }}/__ID__/get_permissions", // used below
        //getPermissions: "{{ url('admin/data_entry_operators') }}/__ID__/permissions",
        update: "{{ url('admin/data_entry_operators') }}/__ID__",
        destroy: "{{ url('admin/data_entry_operators') }}/__ID__",
        store: "{{ url('admin/data_entry_operators') }}", // NEW
    };

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.documentElement.classList.add('overflow-hidden');
    }

    // function closeModal(id) {
    //     document.getElementById(id).classList.add('hidden');
    //     document.documentElement.classList.remove('overflow-hidden');
    // }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.add('hidden');
        document.documentElement.classList.remove('overflow-hidden');
    }

    // --- Wire up buttons (event delegation) ---
    document.addEventListener('click', (e) => {
        let btn;

        // Owners (manager + supervisor)
        btn = e.target.closest('[data-open-assign-owners]');
        if (btn) {
            const id = btn.dataset.id;
            const mId = btn.dataset.managerId || '';
            const sId = btn.dataset.supervisorId || '';

            const form = document.getElementById('deoAssignOwnersForm');
            form.action = routes.assignOwners.replace('__ID__', id);

            const mSel = document.getElementById('deo_owner_manager_id');
            const sSel = document.getElementById('deo_owner_supervisor_id');

            // populate supervisors for selected manager
            function renderSupervisors(mid, selected) {
                sSel.innerHTML = '<option value="">— None —</option>';
                const list = (window.MGR_SUPS || {})[mid] || [];
                for (const sup of list) {
                    const opt = document.createElement('option');
                    opt.value = sup.id;
                    opt.textContent = sup.name;
                    if (String(selected) === String(sup.id)) opt.selected = true;
                    sSel.appendChild(opt);
                }
            }

            mSel.value = mId || '';
            renderSupervisors(mId || 0, sId || '');

            // cascade on change
            //mSel.onchange = () => renderSupervisors(mSel.value || 0, '');
            //mSel.onchange = () => renderSupervisors(mSel.value || 0, sSel.value || '');
            mSel.onchange = () => renderSupervisors(mSel.value || 0, sSel.value || '');
            openModal('deoAssignOwners');
            return;
        }

        // Groups
        btn = e.target.closest('[data-open-assign-groups]');
        if (btn) {
            const id = btn.dataset.id;
            const ids = JSON.parse(btn.dataset.groupIds || '[]').map(Number);
            const form = document.getElementById('deoAssignGroupsForm');
            form.action = routes.assignGroups.replace('__ID__', id);
            document.querySelectorAll('#deoAssignGroupsForm input[name="group_ids[]"]').forEach(cb => {
                cb.checked = ids.includes(parseInt(cb.value));
            });
            openModal('deoAssignGroups');
            return;
        }

        // Permissions
        // btn = e.target.closest('[data-open-assign-permissions]');
        // if (btn) {
        //     const id = btn.dataset.id;
        //     const form = document.getElementById('deoAssignPermissionsForm');
        //     form.action = routes.assignPermissions.replace('__ID__', id);

        //     // reset
        //     document.querySelectorAll('#deoAssignPermissionsForm input[name="permission_ids[]"]').forEach(
        //         cb => {
        //             cb.checked = false;
        //             cb.disabled = false;
        //             cb.dataset.group = '0';
        //             cb.closest('label')?.classList.remove('bg-slate-50', 'dark:bg-slate-800/60');
        //         });

        //     fetch(routes.getPermissions.replace('__ID__', id), {
        //             headers: {
        //                 'Accept': 'application/json'
        //             }
        //         })
        //         .then(r => r.json())
        //         .then(data => {
        //             // ⬇️ Coerce to numbers to avoid type mismatch
        //             const assigned = new Set((data.assigned_permissions || []).map(Number));
        //             const groupIds = new Set((data.group_permission_ids || []).map(Number));
        //             const denied = new Set((data.denied_permissions || []).map(Number));

        //             document.querySelectorAll('#deoAssignPermissionsForm input[name="permission_ids[]"]')
        //                 .forEach(cb => {
        //                     const pid = Number(cb.value);
        //                     if (assigned.has(pid)) cb.checked = true;
        //                     if (groupIds.has(pid)) {
        //                         cb.dataset.group = '1';
        //                         cb.closest('label')?.classList.add('bg-slate-50',
        //                             'dark:bg-slate-800/60');
        //                     }
        //                 });

        //             openModal('deoAssignPermissions');
        //         })
        //         .catch(() => openModal('deoAssignPermissions'));

        //     return;
        // }

        // Edit
        btn = e.target.closest('[data-open-edit]');
        if (btn) {
            const id = btn.dataset.id;
            const form = document.getElementById('deoEditForm');
            form.action = routes.update.replace('__ID__', id);
            document.getElementById('deo_edit_name').value = btn.dataset.name || '';
            document.getElementById('deo_edit_email').value = btn.dataset.email || '';
            openModal('deoEdit');
            return;
        }

        // Delete
        btn = e.target.closest('[data-open-delete]');
        if (btn) {
            const id = btn.dataset.id;
            const form = document.getElementById('deoDeleteForm');
            form.action = routes.destroy.replace('__ID__', id);
            openModal('deoDelete');
            return;
        }

        btn = e.target.closest('[data-open-add-user]');
        if (btn) {
            const form = document.getElementById('deoAddForm');
            form.action = routes.store; // POST /admin/data_entry_operators
            // reset fields
            form.reset();
            openModal('deoAdd');
            return;
        }
    });

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-open-assign-permissions]');
        if (!btn) return;

        const id = btn.dataset.id;
        const form = document.getElementById('deoAssignPermissionsForm');
        form.action = routes.assignPermissions.replace('__ID__', id);

        // reset all first
        document.querySelectorAll('#deoAssignPermissionsForm input[name="permission_ids[]"]')
            .forEach(cb => {
                cb.checked = false;
                cb.disabled = false;
                cb.dataset.group = '0';
            });

        fetch(routes.getPermissions.replace('__ID__', id), {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                const assigned = new Set(data.assigned_permissions || []);
                const groupIds = new Set(data.group_permission_ids || []);

                document.querySelectorAll('#deoAssignPermissionsForm input[name="permission_ids[]"]')
                    .forEach(cb => {
                        const pid = Number(cb.value);
                        if (assigned.has(pid)) cb.checked = true;
                        if (groupIds.has(pid)) {
                            cb.dataset.group = '1';
                            cb.closest('label')?.classList.add('bg-slate-50', 'dark:bg-slate-800/60');
                        }
                    });

                openModal('deoAssignPermissions');
            })
            .catch(() => openModal('deoAssignPermissions'));
    });
    // ESC to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            ['deoAssignOwners', 'deoAssignGroups', 'deoAssignPermissions', 'deoEdit', 'deoDelete'].forEach(
                id => {
                    const el = document.getElementById(id);
                    if (el && !el.classList.contains('hidden')) closeModal(id);
                });
        }
    });
</script>
<script>
    window.MGR_SUPS = @json($mgrSupMap ?? []);
    // Example check: console.log(window.MGR_SUPS);
</script>
<script>
    const permForm = document.getElementById('deoAssignPermissionsForm');
    if (permForm) {
        permForm.addEventListener('submit', (e) => {
            const vals = Array.from(
                permForm.querySelectorAll('input[name="permission_ids[]"]:checked')
            ).map(cb => cb.value);
            console.log('Submitting permission_ids:', vals);
            // If you see [] here, the issue is client-side selection / form wiring.
        });
    }
</script>
<script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js"></script>
<script src="{{ asset('assets/firebase/firebase.js') }}"></script>
<script>
    

    document.getElementById("deoAddForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        // await fillDeviceInfo();

        // Wait for next event loop cycle
        await new Promise(resolve => setTimeout(resolve, 50));

        console.log("Submitting with:", {
            fcm: document.getElementById("c_fcm_token").value,
            device: document.getElementById("c_device_type").value,
            browser: document.getElementById("c_browser_name").value,
            os: document.getElementById("c_os_name").value,
        });

        this.submit();
    });
</script>

<script>
    // async function fillEditDeviceInfo() {
    //     // Device type
    //     let deviceType = /Mobi|Android/i.test(navigator.userAgent) ?
    //         "mobile" :
    //         "pc";

    //     // Browser name (basic detection)
    //     let browserName = "Unknown";
    //     if (/Chrome/i.test(navigator.userAgent)) browserName = "Chrome";
    //     else if (/Firefox/i.test(navigator.userAgent)) browserName = "Firefox";
    //     else if (
    //         /Safari/i.test(navigator.userAgent) &&
    //         !/Chrome/i.test(navigator.userAgent)
    //     )
    //         browserName = "Safari";
    //     else if (/Edge/i.test(navigator.userAgent)) browserName = "Edge";

    //     // OS name
    //     let osName = "Unknown";
    //     if (/Win/i.test(navigator.userAgent)) osName = "Windows";
    //     else if (/Mac/i.test(navigator.userAgent)) osName = "MacOS";
    //     else if (/Linux/i.test(navigator.userAgent)) osName = "Linux";
    //     else if (/Android/i.test(navigator.userAgent)) osName = "Android";
    //     else if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) osName = "iOS";

    //     document.getElementById("e_device_type").value = deviceType;
    //     document.getElementById("e_browser_name").value = browserName;
    //     document.getElementById("e_os_name").value = osName;

    //     // Try to fetch FCM token
    //     // try {
    //     const perm = await Notification.requestPermission();

    //     if (perm === "granted") {
    //         const token = await messaging.getToken({
    //             vapidKey: VAPID_PUBLIC_KEY,
    //         });
    //         if (token) {
    //             document.getElementById("e_fcm_token").value = token;
    //         }
    //     }
    //     // } catch (e) {
    //     //     console.warn("FCM token skipped:", e);
    //     // }
    // }

    document.getElementById("deoEditForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        // await fillEditDeviceInfo();

        // Wait for next event loop cycle
        await new Promise(resolve => setTimeout(resolve, 50));

        console.log("Submitting with:", {
            fcm: document.getElementById("e_fcm_token").value,
            device: document.getElementById("e_device_type").value,
            browser: document.getElementById("e_browser_name").value,
            os: document.getElementById("e_os_name").value,
        });

        this.submit();
    });
</script>