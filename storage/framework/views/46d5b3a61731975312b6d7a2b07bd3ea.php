<dialog id="modal-assign-permissions"
    class="rounded-2xl backdrop:bg-black/50 dark:backdrop:bg-white/30 w-full max-w-3xl">

    <form method="dialog" class="w-full">
        <div class="rounded-2xl bg-white dark:bg-neutral-800 shadow-xl flex flex-col max-h-[85vh]">

            <!-- Header -->
            <div class="flex items-center justify-between border-b px-5 py-3 dark:border-neutral-700 shrink-0">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Assign Permissions
                </h3>

                <button type="button"
                    class="h-8 w-8 grid place-items-center rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-700"
                    data-modal-close>
                    ✕
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-5">
                <input type="hidden" id="ap-client-id">

                <div id="ap-allowed" class="space-y-2">
                    
                </div>

                
                
            </div>

            <!-- Fixed Footer -->
            <div
                class="flex items-center justify-end gap-3 border-t px-5 py-3 dark:border-neutral-700 shrink-0">

                <button type="button"
                    class="rounded-lg border border-rose-600 px-4 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-600/20"
                    data-modal-close>
                    Close
                </button>

                <button type="button" id="ap-save"
                    class="rounded-lg bg-primary-600 px-4 py-2 font-semibold text-white hover:bg-primary-700">
                    Save
                </button>
            </div>

        </div>
    </form>
</dialog><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\clients\modals\assign_permissions_plain.blade.php ENDPATH**/ ?>