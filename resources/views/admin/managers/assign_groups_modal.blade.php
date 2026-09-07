<div id="assignGroupsModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
    <div class="absolute inset-0" onclick="closeGroupsModal()"></div>
    <div class="balantro-modal-panel relative w-full max-w-2xl rounded-2xl bg-white text-slate-900 shadow-xl dark:bg-neutral-900 dark:text-white">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-xl font-semibold">Assign Groups</h2>
            <button type="button" onclick="closeGroupsModal()" class="flex h-8 w-8 items-center justify-center rounded-full
                               text-slate-400 
                               hover:bg-gray-200 hover:text-slate-700
                               dark:text-neutral-400
                               dark:hover:bg-neutral-800 dark:hover:text-white">✕</button>
        </div>
        <form id="assignGroupsForm" method="POST" class="px-6 py-5">
            @csrf
            <div id="groupsList" class=" grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[60vh] overflow-y-auto pr-1">
                <!-- filled by JS -->
            </div>
            <div class="flex justify-end gap-3 pt-5 pb-6">
                <button type="button" onclick="closeGroupsModal()"
                    class="rounded-md border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-neutral-800">Cancel</button>
                <button type="submit"
                    class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Save</button>
            </div>
        </form>
    </div>
</div>   
