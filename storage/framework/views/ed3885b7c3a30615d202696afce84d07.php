<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-neutral-900 w-[520px] rounded-lg shadow-lg p-6">
        <h2 class="text-lg text-white mb-4">Edit Transaction</h2>
        <form id="editForm">
            
            <?php echo csrf_field(); ?>
            <input type="hidden" id="edit_id">
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Transfer Date</label>
                <input type="date" id="edit_txn_date" class="inputCell">
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Value Date</label>
                <input type="date" id="edit_value_date" class="inputCell">
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Description</label>
                <input type="text" id="edit_narration" class="inputCell">
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Type</label>
                <select id="edit_type" class="inputCell">
                    <option value="Payment">Payment</option>
                    <option value="Receipt">Receipt</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Amount</label>
                <input type="number" id="edit_amount" class="inputCell">
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Ledger</label>
                <select id="edit_ledger" class="inputCell">
                    <option value="">Select Ledger</option>
                    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ledger->name); ?>">
                        <?php echo e($ledger->name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" id="closeModal" class="px-4 py-1 bg-gray-600 text-white rounded">
                    Cancel
                </button>
                <button type="button" id="updateBtn" class="px-4 py-1 bg-blue-600 text-white rounded">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\bulkupload\bank\edit_modal.blade.php ENDPATH**/ ?>