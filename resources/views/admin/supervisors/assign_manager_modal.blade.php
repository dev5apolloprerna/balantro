{{-- resources/views/admin/supervisors/assign_manager_modal.blade.php --}}
{{-- resources/views/admin/supervisors/assign_manager_modal.blade.php --}}
<div id="assignManagerModal" class="fixed inset-0 z-[100] hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/60" onclick="closeAssignManagerModal()"></div>

    {{-- Dialog --}}
    <div class="relative mx-auto mt-24 w-full max-w-lg px-4">
        <div class="rounded-2xl bg-white shadow-xl dark:bg-slate-800">
            <form id="assignManagersForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="flex items-start justify-between">
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Assign Manager</h3>
                    <button type="button"
                        class="text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white"
                        onclick="closeAssignManagerModal()">×</button>
                </div>

                <div class="mt-5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Managers</label>

                    {{-- Multi-select; remove "multiple" if only one manager is allowed --}}
                    <select id="manager_id" name="manager_id"
                        class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-3 pr-10 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                        @foreach ($managers ?? [] as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>

                    {{-- Helper text (optional) --}}
                    {{-- <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Hold Ctrl/Cmd to select multiple.</p> --}}
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                        class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700"
                        onclick="closeAssignManagerModal()">Cancel</button>
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Save</button>
                </div>
            </form>
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
