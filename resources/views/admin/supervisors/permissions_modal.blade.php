<div id="permissionsModal"
    class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/60 backdrop-blur-sm">

    <!-- Overlay -->
    <div class="absolute inset-0"
        onclick="closePermissionsModal()">
    </div>

    <!-- Modal -->
    <div class="balantro-modal-panel relative z-10 w-full max-w-2xl
                rounded-2xl
                border border-slate-200
                bg-white
                text-slate-900
                shadow-xl
                dark:border-neutral-700
                dark:bg-neutral-900 
                dark:text-white">

        <!-- Header -->
        <div class="flex items-center justify-between
                    px-6 py-4
                    border-b border-slate-200
                    dark:border-neutral-700">

            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                Assign Permissions
            </h2>

            <button type="button"
                onclick="closePermissionsModal()"
                class="flex h-8 w-8 items-center justify-center rounded-full
                               text-slate-400 
                               hover:bg-gray-200 hover:text-slate-700
                               dark:text-neutral-400
                               dark:hover:bg-neutral-800 dark:hover:text-white">
                ✕
            </button>

        </div>

        <form id="permissionsForm" method="POST" class="px-6 py-5">
            @csrf

            <!-- Permissions List -->
            <div id="permissionsList"
                class="max-h-[55vh] overflow-y-auto space-y-3 pr-1">
                <!-- filled by JS -->
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 pt-5 pb-1">

                <button type="button"
                    onclick="closePermissionsModal()"
                    class="rounded-md
                           border border-slate-300
                           bg-white
                           px-4 py-2
                           text-sm text-slate-700
                           hover:bg-slate-100
                           dark:border-neutral-600
                           dark:bg-neutral-900
                           dark:text-neutral-200
                           dark:hover:bg-neutral-800">
                    Cancel
                </button>

                <button type="submit"
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