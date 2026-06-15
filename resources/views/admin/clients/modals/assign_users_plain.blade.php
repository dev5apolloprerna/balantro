{{-- resources/views/admin/clients/modals/assign_users_plain.blade.php --}}
<dialog id="modal-assign-users" class="rounded-2xl backdrop:bg-black/50 dark:backdrop:bg-white/30 w-full max-w-xl">
    <form method="dialog" class="w-full">
        <div class="rounded-2xl bg-white dark:bg-neutral-800 shadow-xl">
            <div class="flex items-center justify-between border-b px-5 py-3 dark:border-neutral-700">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Assign Users') }}</h3>
                <button type="button"
                    class="h-8 w-8 grid place-items-center rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-700"
                    data-modal-close>
                    ✕
                </button>
            </div>

            <div class="space-y-4 p-5">
                <input type="hidden" id="au-client-id">

                <div>
                    <label class="mb-1 block text-sm font-medium">{{ __('Manager') }}</label>
                    <select id="au-manager"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">{{ __('Select Manager') }}</option>
                        @foreach ($managers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">{{ __('Supervisor') }}</label>
                    <select id="au-supervisor"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"
                        disabled>
                        <option value="">{{ __('Select Supervisor (choose manager first)') }}</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">{{ __('Data Entry Operator') }}</label>
                    <select id="au-deo"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"
                        disabled>
                        <option value="">{{ __('Select Data Entry Operator (choose supervisor first)') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t px-5 py-3 dark:border-neutral-700">
                <button type="button"
                    class="rounded-lg border border-rose-600 px-4 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-600/20"
                    data-modal-close>
                    {{ __('Close') }}
                </button>
                <button type="button" id="au-save"
                    class="rounded-lg bg-primary-600 px-4 py-2 font-semibold text-white hover:bg-primary-700">
                    {{ __('Assign') }}
                </button>
            </div>
        </div>
    </form>
</dialog>
