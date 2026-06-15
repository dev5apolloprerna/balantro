<div id="editModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative mx-auto mt-24 w-[720px] max-w-[95%] rounded-2xl bg-neutral-900 text-white shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-700">
            <h2 class="text-xl font-semibold">Edit Group</h2> <button type="button" onclick="closeEditModal()"
                class="text-neutral-400 hover:text-white">✕</button>
        </div>
        <form id="editForm" method="POST" class="px-6 py-5 space-y-5"> @csrf @method('PUT') <div> <label
                    class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label> <input
                    id="edit_name" name="name" type="text"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="flex justify-end gap-3 pt-2 pb-5"> <button type="button" onclick="closeEditModal()"
                    class="rounded-md border border-neutral-600 px-4 py-2 text-sm text-neutral-200 hover:bg-neutral-800">Cancel</button>
                <button type="submit"
                    class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Update</button>
            </div>
        </form>
    </div>
</div>
