{{-- resources/views/admin/supervisors/assign_manager_modal.blade.php --}}

<div id="assignManagerModal" class="fixed inset-0 z-[100] hidden">

    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        onclick="closeAssignManagerModal()">
    </div>

    {{-- Center Wrapper --}}
    <div class="absolute inset-0 z-10 flex items-center justify-center p-4">

        <div class="relative w-full max-w-lg">

            <div class="overflow-hidden rounded-2xl
                        border border-slate-200
                        bg-white shadow-xl
                        dark:border-neutral-700
                        dark:bg-neutral-900">

                {{-- Header --}}
                <div class="flex items-center justify-between
                            border-b border-slate-200
                            px-6 py-4
                            dark:border-neutral-700">

                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white">
                        Assign Manager
                    </h3>

                    <button
                        type="button"
                        onclick="closeAssignManagerModal()"
                        class="flex h-8 w-8 items-center justify-center rounded-full
                               text-slate-400
                               hover:bg-slate-100 hover:text-slate-700
                               dark:text-neutral-400
                               dark:hover:bg-neutral-800 dark:hover:text-white">
                        ✕
                    </button>

                </div>

                <form id="assignManagersForm" method="POST" class="px-6 py-5">
                    @csrf

                    <input
                        type="hidden"
                        name="page"
                        value="{{ request('page', 1) }}">

                  <div>
    <label
        class="mb-2 block text-sm font-medium
               text-slate-700 dark:text-slate-300">
        Managers
    </label>

    <div class="relative w-full">

        <select
            id="manager_id"
            name="manager_id"
            class="block h-11 w-full appearance-none rounded-lg
                   border border-slate-300
                   bg-white
                   py-2.5 pl-3 pr-10
                   text-sm text-slate-900
                   focus:outline-none
                   focus:ring-2 focus:ring-blue-500
                   dark:border-neutral-700
                   dark:bg-black
                   dark:text-white">

            @foreach ($managers ?? [] as $m)
                <option value="{{ $m->id }}">
                    {{ $m->name }}
                </option>
            @endforeach

        </select>

        <div
            class="pointer-events-none absolute inset-y-0 right-3
                   flex items-center justify-center
                   text-slate-500 dark:text-neutral-300">
            <i class="fa-solid fa-chevron-down text-xs"></i>
        </div>

    </div>
</div>

                    {{-- Footer --}}
                    <div class="mt-6 flex justify-end gap-3">

                        <button
                            type="button"
                            onclick="closeAssignManagerModal()"
                            class="rounded-lg
                                   border border-slate-300
                                   px-4 py-2
                                   text-sm font-medium text-slate-700
                                   hover:bg-slate-100
                                   dark:border-neutral-700
                                   dark:text-slate-200
                                   dark:hover:bg-neutral-800">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg
                                   bg-blue-600
                                   px-4 py-2
                                   text-sm font-semibold text-white
                                   hover:bg-blue-700
                                   focus:outline-none
                                   focus:ring-2 focus:ring-blue-500">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            // open modal and preselect current supervisor's managers
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-assign-managers]');
                if (!btn) return;

                const supervisorId = btn.getAttribute('data-supervisor-id');
                const assigned = btn.getAttribute('data-assigned'); // JSON array from row
                let selectedIds = [];
                try {
                    selectedIds = JSON.parse(assigned || '[]')
                } catch (_) {}

                // set form action
                const form = document.getElementById('assignManagersForm');
                form.action = "{{ url('admin/supervisors') }}/" + supervisorId + "/assign_managers";

                // clear & reselect
                const sel = document.getElementById('manager_ids');
                for (const opt of sel.options) {
                    opt.selected = selectedIds.includes(parseInt(opt.value));
                }

                // show modal
                const m = new bootstrap.Modal(document.getElementById('assignManagerModal'));
                m.show();
            });
        })();
    </script>
    <script>
        const modalEl = document.getElementById('assignManagerModal');
        const formEl = document.getElementById('assignManagersForm');
        const selEl = document.getElementById('manager_id'); // 👈 id changed

        const ASSIGN_URL = "{{ url('admin/supervisors') }}/__ID__/assign_managers";

        function openAssignManagerModal(btn) {
            const id = btn.getAttribute('data-supervisor-id');
            const assigned = btn.getAttribute('data-assigned'); // string or ''

            formEl.action = ASSIGN_URL.replace('__ID__', id);

            // set selected value (or clear)
            selEl.value = assigned || '';

            modalEl.classList.remove('hidden');
            document.documentElement.classList.add('overflow-hidden');
        }

        function closeAssignManagerModal() {
            modalEl.classList.add('hidden');
            document.documentElement.classList.remove('overflow-hidden');
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !modalEl.classList.contains('hidden')) closeAssignManagerModal();
        });
    </script>
@endpush
