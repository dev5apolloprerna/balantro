<div id="suspenseModal" class="fixed inset-0 hidden z-50 flex items-center justify-center">
    <!-- BACKDROP -->
    <div class="absolute inset-0 bg-black/60"></div>

    <!-- MODAL -->
    <div class="relative w-[400px] bg-white dark:bg-gray-900 rounded-lg p-5 shadow-lg">

        <h3 class="text-lg font-semibold mb-3">⚠ Mark as Suspense</h3>

        <input type="hidden" id="suspense_id">

        <div>
            <label class="text-sm">Reason (Required)</label>
            <textarea id="suspense_remark" 
                class="w-full mt-1 border p-2 rounded"
                rows="3"
                placeholder="Enter reason..."></textarea>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button onclick="closeSuspenseModal()" 
                class="px-3 py-1 bg-gray-400 text-white rounded">
                Cancel
            </button>
            <button id="submitSuspense" 
                class="px-3 py-1 bg-yellow-500 text-white rounded">
                Mark Suspense
            </button>
        </div>
    </div>
</div>

<div id="remarkModal" class="fixed inset-0 hidden z-50 flex items-center justify-center">

    <!-- BACKDROP -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <!-- MODAL -->
    <div class="relative w-[500px] bg-gradient-to-br from-gray-900 to-gray-800 
                rounded-xl shadow-2xl border border-gray-700 animate-fadeIn">

        <!-- HEADER -->
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-700">
            <h3 class="text-white text-lg font-semibold">📌 Remark</h3>
            <button onclick="closeRemarkModal()" class="text-gray-400 hover:text-white text-lg">
                ✕
            </button>
        </div>

        <!-- BODY -->
        <div class="p-5">
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 text-gray-200 text-sm leading-relaxed">
                <span id="remarkText"></span>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="flex justify-end px-5 py-3 border-t border-gray-700">
            <button onclick="closeRemarkModal()" 
                class="px-4 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                Close
            </button>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\bulkupload\bank\suspense_modal.blade.php ENDPATH**/ ?>