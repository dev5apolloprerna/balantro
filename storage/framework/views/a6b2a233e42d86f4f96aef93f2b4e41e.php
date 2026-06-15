<div id="permissionsModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative mx-auto mt-20 w-[920px] max-w-[95%] rounded-2xl bg-neutral-900 text-white shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-700">
            <h2 class="text-xl font-semibold">Assign Permissions</h2>
            <button type="button" onclick="closePermissionsModal()" class="text-neutral-400 hover:text-white">✕</button>
        </div>
        <form id="permissionsForm" method="POST" class="px-6 py-5">
            <?php echo csrf_field(); ?>
            <div id="permissionsList" class="max-h-[60vh] overflow-y-auto space-y-4 pr-1">
                <!-- filled by JS -->
            </div>
            <div class="flex justify-end gap-3 pt-5 pb-6">
                <button type="button" onclick="closePermissionsModal()"
                    class="rounded-md border border-neutral-600 px-4 py-2 text-sm text-neutral-200 hover:bg-neutral-800">Cancel</button>
                <button type="submit"
                    class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Save</button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\supervisors\permissions_modal.blade.php ENDPATH**/ ?>