
<?php $__env->startSection('content'); ?>

<div x-data="{ openUpload:false, openClient: <?php echo e(session('iPartyId') ? 'false' : 'true'); ?> }"
    x-init="openUpload = false">

    <div class="container mx-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-3">
            <h6 class="font-semibold dark:text-white">Journal Uploads
                <!-- Client Name -->
                <?php if(session('client_name')): ?>
                <span class="bulk-client-name text-xl font-semibold text-green-600 whitespace-nowrap truncate max-w-[140px]" style="font-variant-caps: small-caps;">
                    (<?php echo e(session('client_name')); ?>)
                </span>
                <?php endif; ?>
            </h6>
        </div>

        <div class="card bg-white dark:bg-neutral-800 rounded-lg overflow-hidden">

            <!-- TOP BAR -->
            <div class="p-4 bulk-toolbar-row flex items-center justify-between w-full gap-3 flex-nowrap border-b border-gray-200 dark:border-neutral-600">

                <?php echo $__env->make('admin.bulkupload.bulk-upload-tabs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="bulk-toolbar-actions flex items-center justify-end gap-2 mt-0 w-full flex-nowrap">
                    <!-- Divider -->
                    <div class="h-4 w-px bg-gray-300 dark:bg-neutral-600"></div>
                    <!-- Year Dropdown -->
                    <?php if(!empty($years) && count($years)): ?>
                    <select
                        id="journalYearSelect"
                        onchange="if (this.value) window.location.href=this.value"
                        class="text-xs bg-transparent border-0 focus:ring-0 outline-none">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="">Select Year </option>
                        <option
                            value="<?php echo e(route('sales.select.year', $year->strYear)); ?>"
                            <?php echo e(session('year') == $year->strYear || (!session('year') && $key == 0) ? 'selected' : ''); ?>>
                            <?php echo e($year->strYear); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php endif; ?>
                    <div class="h-4 w-px bg-gray-300 dark:bg-neutral-600"></div>
                    <!-- Sample Dropdown -->
                    <select
                        id="journalSampleSelect"
                        class="text-xs bg-transparent border-0 focus:ring-0 outline-none">
                        <option value="">Select Sample</option>
                        <option value="<?php echo e(asset('/samples/journal-sample-file.xlsx')); ?>">
                            Download Sample
                        </option>
                    </select>

                    <!-- Select Client -->
                    <button
                        @click="openClient=true"
                        class="bulk-text-btn flex items-center gap-1 px-3 py-1.5 text-xs border border-gray-400 text-white rounded hover:bg-gray-700 whitespace-nowrap">
                        <i class="fa-solid fa-building"></i>
                        Client
                    </button>

                    <!-- Upload -->
                    <button
                        @click="<?php echo e(session('iPartyId') ? 'openUpload=true' : 'openClient=true'); ?>"
                         class="bulk-text-btn flex items-center gap-1 px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded whitespace-nowrap">
                        <i class="fa-solid fa-upload"></i>
                        Upload
                    </button>

                    <!-- Add -->
                    <button
                        onclick="openJournalModal()"
                        class="bulk-text-btn flex items-center gap-1 px-3 py-1.5 text-xs bg-blue-500 hover:bg-blue-600 text-white rounded whitespace-nowrap">
                        <i class="fa-solid fa-plus"></i>
                        Add
                    </button>

                </div>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 group-block">
                <table id="salesTable" class="min-w-[1100px] w-full text-sm text-gray-700 dark:text-gray-300 border-collapse">
                    <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-gray-900/40 text-xs text-gray-700 dark:text-gray-300 uppercase sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3"><input type="checkbox"></th>
                            <th class="px-4 py-3">Sr.No</th>
                            <th class="px-4 py-3">File Name</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Pending</th>
                            <th class="px-4 py-3">TM</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                        <?php $__currentLoopData = $uploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="group transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] [&>*]:group-hover:text-black [&_*]:group-hover:text-black">

                            <td class="px-4 py-3"><input type="checkbox"></td>

                            <td class="px-4 py-3"><?php echo e($loop->iteration); ?></td>

                            <td class="px-4 py-3 font-medium">
                                <?php echo e($upload->file_name); ?>

                            </td>

                            <td class="px-4 py-3"><?php echo e($upload->total); ?></td>
                            <td class="px-4 py-3"><?php echo e($upload->pending); ?></td>
                            <td class="px-4 py-3"><?php echo e($upload->saved); ?></td>

                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('journal.preview',$upload->id)); ?>"
                                    class="<?php if($upload->status=='completed'): ?> text-green-500
                                      <?php elseif($upload->status=='processing'): ?> text-yellow-500
                                      <?php else: ?> text-blue-500 <?php endif; ?> font-semibold">
                                    <?php echo e(ucfirst($upload->status)); ?>

                                </a>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="<?php echo e(route('journal.preview',$upload->id)); ?>">
                                    <i class="fa-regular fa-eye action-icon text-gray-500"></i>
                                </a>

                                <div class="relative inline-block">
                                    <button onclick="openDropdown(event, <?php echo e($upload->id); ?>)"
                                        class="text-gray-500 hover:text-gray-700 px-2">
                                        <i class="fa-solid fa-ellipsis-vertical action-icon"></i>
                                    </button>
                                    <div id="globalDropdown"
                                        class="hidden fixed bg-white dark:bg-neutral-800 border rounded-xl shadow-xl w-48 z-[99999]">

                                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 border-b">
                                            Change Status
                                        </div>

                                        <button onclick="handleStatus('pending')" class="dropdown-item">Pending</button>
                                        <button onclick="handleStatus('processing')" class="dropdown-item">Processing</button>
                                        <button onclick="handleStatus('completed')" class="dropdown-item">Completed</button>

                                        <div class="border-t my-1"></div>

                                        <button onclick="handleDelete()" class="dropdown-item text-red-500" style="color: indianred;">
                                            Delete
                                        </button>
                                    </div>
                                </div>

                            </td>

                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <!-- ================= UPLOAD MODAL ================= -->
    <div
        x-cloak
        x-show="openUpload"
        style="display: none;"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

        <div class="bg-white dark:bg-neutral-800 text-gray-800 dark:text-gray-200 w-[700px] rounded-lg shadow-xl">

            <!-- HEADER -->
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-neutral-600 px-5 py-3">
                <h2 class="text-lg font-semibold">Upload Journal</h2>
                <button @click="openUpload=false" class="text-gray-500 dark:text-gray-300">✖</button>
            </div>

            <!-- FORM -->
            <form id="uploadForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="p-6">

                    <!-- UPLOAD BOX -->
                    <div onclick="document.getElementById('journalFile').click()"
                        class="border-2 border-dashed border-blue-400 dark:border-blue-500 
                            bg-gray-50 dark:bg-neutral-700 
                            text-gray-600 dark:text-gray-300
                            rounded-lg p-10 text-center cursor-pointer">

                        <i class="fa-regular fa-file text-3xl text-blue-500 mb-3"></i>

                        <p>Drag & Drop or Click to upload</p>

                        <input type="file"
                            name="file"
                            id="journalFile"
                            class="hidden"
                            accept=".xlsx,.xls"
                            onchange="showJournalFileName(this)">
                    </div>

                    <!-- FILE NAME -->
                    <div id="fileNameBox"
                        class="mt-3 hidden text-sm text-gray-600 dark:text-gray-300">
                        <i class="fa-solid fa-paperclip"></i>
                        <span id="fileNameText"></span>
                    </div>

                    <!-- ✅ PROGRESS BAR -->
                    <div id="progressBar"
                        class="w-full bg-gray-200 rounded mt-3 hidden overflow-hidden">

                        <div id="progressFill"
                            class="bg-blue-600 text-xs text-white text-center p-1 rounded"
                            style="width:0%">
                            0%
                        </div>
                    </div>

                    <!-- NOTES -->
                    <div class="mt-5 text-sm text-gray-600 dark:text-gray-300">
                        <b>Notes:</b>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Format: Journal No | Date | Ledger | Dr/Cr | Amount</li>
                            <li>Date format: DD/MM/YYYY</li>
                            <li>File size max 30MB</li>
                            <li>No password protected file</li>
                        </ul>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-neutral-600 px-5 py-3">
                    <button type="button" @click="openUpload=false"
                        class="px-4 py-2 border rounded">
                        Cancel
                    </button>

                    <!-- <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Upload
                </button> -->
                    <button type="submit"
                        id="uploadBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">

                        <span id="uploadText">Upload</span>

                        <span id="uploadLoader" class="hidden">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>

                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- CLIENT DRAWER -->
    <div
        x-cloak
        style="display: none;"
        x-show="openClient"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-0 z-50 flex justify-end">
        <!-- Background -->
        <div
            class="absolute inset-0 bg-black/50"
            @click="openClient=false">
        </div>
        <!-- Drawer -->
        <div class="relative w-[420px] h-full bg-white dark:bg-neutral-800 shadow-xl flex flex-col">
            <!-- Header -->
            <div class="flex justify-between items-center px-5 py-4 border-b">
                <h2 class="text-lg font-semibold">
                    My Company
                </h2>
                <button @click="openClient=false">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>
            </div>
            <!-- Search -->
            <div class="p-4 border-b">
                <input
                    type="text"
                    id="clientSearch"
                    placeholder="Search by Name"
                    class="w-full border px-3 py-2 rounded-md">
            </div>
            <!-- Client List -->
            <div class="flex-1 min-h-0 overflow-y-auto">
                <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a
                    href="<?php echo e(route('sales.select.company',$client->id)); ?>"
                    class="flex items-center justify-between px-4 py-3 border-b hover:bg-gray-100 dark:hover:bg-neutral-700 client-item">
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-200 dark:bg-neutral-700 p-2 rounded">
                            <i class="fa-solid fa-building text-gray-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">
                                <?php echo e($client->name); ?>

                            </div>
                            <div class="text-xs text-gray-500">
                                <?php echo e($client->code ?? ''); ?>

                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- ================= JOURNAL Add MODAL ================= -->
    <div id="journalModal" class="modal" style="display: none;">
        <div class="receipt-wrapper">
            <!-- HEADER -->
            <div class="receipt-head">
                <div>
                    <div class="receipt-company">Journal Entry</div>
                    <div class="receipt-subtitle">Voucher</div>
                </div>
                <button onclick="closeJournalModal()">✕</button>
            </div>

            <!-- FORM -->
            <div class="receipt-meta-grid">
                <div class="flex gap-4">

                    <div class="flex items-center w-1/2">
                        <label class="w-32">Journal No</label>
                        <input type="text" id="journal_no" class="receipt-input">
                    </div>

                    <div class="flex items-center w-1/2">
                        <label class="w-20">Date</label>
                        <input type="date" id="journal_date" class="receipt-input">
                    </div>

                </div>
            </div>

            <!-- ITEMS -->
            <div>

                <div class="receipt-items-header">
                    <span></span>
                    <button onclick="addRow()" class="receipt-add-btn">+ Add Row</button>
                </div>

                <div class="receipt-table-wrap">
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ledger</th>
                                <th>Dr</th>
                                <th>Cr</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="itemsBody"></tbody>

                        <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td id="totalDr">0.00</td>
                                <td id="totalCr">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="receipt-meta-grid">
                    <div class="receipt-field-row">
                        <label>Narration</label>
                        <textarea id="journal_narration" class="receipt-input"></textarea>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="receipt-footer">
                <button onclick="closeJournalModal()" class="btn-cancel">Close</button>
                <button onclick="saveJournal()" class="submit-btn">Save</button>
            </div>

        </div>
    </div>

</div>
<style>
    .dropdown-item {
        width: 100%;
        text-align: left;
        padding: 10px 16px;
        font-size: 14px;
        display: block;
        border: none;
        background: transparent;
    }

    .dropdown-item:hover {
        background: #374151;
        color: white;
    }

    /* DARK MODE FIX */
    .dark .dropdown-item {
        color: #e5e7eb;
        /* light text */
    }

    .dark .dropdown-item:hover {
        background: #374151;
        /* dark hover bg */
        color: #ffffff;
        /* white text */
    }

    #globalDropdown {
        border-radius: 10px;
        overflow: hidden;
        /* 🔥 IMPORTANT FIX */
    }

    .card-header {
        backdrop-filter: blur(6px);
    }

    button {
        transition: all 0.2s ease;
    }

    .card-header select {
        cursor: pointer;
    }

    .card-header button {
        white-space: nowrap;
    }

    .card-header a {
        letter-spacing: 0.3px;
    }

</style>
<style>
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    /* MAIN CARD */
    .receipt-wrapper {
        width: 780px;
        max-width: 95%;
        background: #ffffff;
        color: #111;
        border-radius: 14px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    /* HEADER */
    .receipt-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 5px;
        background: #ffffff;
        /* dark blue ERP style */
        color: #000;
    }

    .receipt-company {
        font-size: 16px;
        font-weight: 600;
    }

    .receipt-subtitle {
        font-size: 12px;
        opacity: 0.7;
    }

    /* FORM */
    .receipt-meta-grid {
        padding: 3px 10px;
    }

    .receipt-field-row {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .receipt-field-row label {
        width: 130px;
        font-size: 13px;
        color: #374151;
    }

    /* INPUT */
    .receipt-input {
        flex: 1;
        padding: 4px 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #f9fafb;
        font-size: 13px;
    }

    /* ITEMS HEADER */
    .receipt-items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px 10px;
        font-weight: 500;
    }

    /* TABLE */
    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .receipt-table th {
        background: #f3f4f6;
        padding: 2px;
        text-align: left;
        color: #374151;
        font-weight: 600;
        
    }

    .receipt-table td {
        padding: 2px;
        border-top: 1px solid #e5e7eb;
    }

    /* ROW INPUT */
    .receipt-table input {
        width: 100%;
        padding: 4px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        background: #000 !important;
        color: #fff !important;
        border: 1px solid #444 !important;
    }

    .receipt-table input::placeholder {
        color: #aaa;
    }
    /* TOTAL ROW */
    tfoot td {
        font-weight: 600;
        background: #f9fafb;
    }

    /* ADD BUTTON */
    .receipt-add-btn {
        background: #2563eb;
        color: white;
        padding: 2px 5px;
        border-radius: 6px;
        font-size: 12px;
    }

    /* FOOTER */
    .receipt-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid #e5e7eb;
    }

    .submit-btn {
        background: #16a34a;
        color: white;
        padding: 7px 16px;
        border-radius: 6px;
    }

    .btn-cancel {
        background: #6b7280;
        color: white;
        padding: 7px 16px;
        border-radius: 6px;
    }

    /* 🔥 SELECT BOX */
    .select2-container--default .select2-selection--single {
        height: 34px !important;
        background-color: #f9fafb !important;
        /* border: 1px solid #d1d5db !important; */
        display: flex !important;
        align-items: center !important;

        background: #000 !important;
        color: #fff !important;
        border: 1px solid #444 !important;

    }

    .select2-container--default .select2-selection__rendered {
        color: #fff !important;
    }

    /* 🔥 SELECTED VALUE */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #000 !important;
        opacity: 1 !important;
    }

    /* 🔥 DROPDOWN OPTIONS FIX (MAIN ISSUE) */
    .select2-results__option {
        color: #000 !important;
        background: #fff !important;
    }

    /* 🔥 HOVER FIX */
    .select2-results__option--highlighted {
        background: #2563eb !important;
        color: #fff !important;
    }

    /* 🔥 DARK MODE FIX */
    .dark .select2-results__option {
        color: #fff !important;
        background: #020617 !important;
    }

    .dark .select2-results__option--highlighted {
        background: #2563eb !important;
        color: #fff !important;
    }

    /* 🔥 DROPDOWN BOX */
    .select2-dropdown {
        z-index: 99999 !important;
    }

    .auto-row input {
        background: #fef9c3;
    }
</style>
<style>
    .inputCell {
        background: white;
        border: 1px solid #d1d5db;
        color: #111827;
        padding: 4px;
        border-radius: 4px;
        width: 100%;
    }

    .dark .inputCell {
        background: #020617;
        border: 1px solid #374151;
        color: white;
    }

    .searchInput {
        width: 100%;
        background: white;
        border: 1px solid #d1d5db;
    }

    .dark .searchInput {
        background: #020617;
        color: white;
    }

    /* 🔥 MATCH PREVIEW STYLE */

    #journalModal .receipt-input {
        background: #000 !important;
        color: #fff !important;
        border: 1px solid #444 !important;
    }

    /* TABLE INPUT SAME */
    #journalModal .receipt-table input {
        background: #000 !important;
        color: #fff !important;
        border: 1px solid #444 !important;
    }

    /* HEADER COMPACT */
    #journalModal .receipt-head {
        padding: 8px 12px !important;
    }

    /* TABLE SPACING FIX */
    #journalModal .receipt-table td {
        padding: 3px !important;
    }

    /* TOTAL ROW ALIGN */
    #journalModal tfoot td {
        text-align: center;
    }

    /* SELECT2 SAME AS PREVIEW */
    #journalModal .select2-container--default .select2-selection--single {
        background: #000 !important;
        color: #fff !important;
        border: 1px solid #444 !important;
    }

    #journalModal .select2-selection__rendered {
        color: #fff !important;
    }

    /* DROPDOWN */
    .select2-dropdown {
        background: #000 !important;
        color: #fff !important;
    }

    .select2-results__option {
        background: #000 !important;
        color: #fff !important;
    }

    .select2-results__option--highlighted {
        background: #2563eb !important;
        color: #fff !important;
    }
    .receipt-footer {
        justify-content: flex-end;
        gap: 10px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    
    document.addEventListener("DOMContentLoaded", function() {
        let input = document.getElementById('clientSearch');
        if (input) {
            input.addEventListener('keyup', function() {
                let filter = this.value.toLowerCase();
                document.querySelectorAll('.client-item').forEach(function(item) {
                    item.style.display = item.innerText.toLowerCase().includes(filter) ? '' : 'none';
                });
            });
        }
    });

    let ledgers = <?php echo json_encode($ledgers, 15, 512) ?>;
</script>
<script>
    // ✅ SHOW FILE NAME
    function showJournalFileName(input) {
        if (!validateUploadFileSize(input)) {
            document.getElementById('fileNameText').innerText = '';
            document.getElementById('fileNameBox').classList.add('hidden');
            return;
        }

        let file = input.files[0];
        if (file) {
            document.getElementById('fileNameText').innerText = file.name;
            document.getElementById('fileNameBox').classList.remove('hidden');
        }
    }

    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        let fileInput = $('#journalFile')[0];
        if (!fileInput.files.length) {
            showToast('Please select file','error');
            return;
        }
        if (!validateUploadFileSize(fileInput)) {
            document.getElementById('fileNameText').innerText = '';
            document.getElementById('fileNameBox').classList.add('hidden');
            return;
        }

        let formData = new FormData(this);
        // 🔥 DISABLE BUTTON + SHOW LOADER
        $('#uploadBtn').prop('disabled', true);
        $('#uploadText').text('Uploading...');
        $('#uploadLoader').removeClass('hidden');
        $.ajax({
            url: "<?php echo e(route('journal.upload')); ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            // ✅ PROGRESS CODE HERE
            xhr: function() {
                let xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(e) {
                    if (e.lengthComputable) {
                        let percent = Math.round((e.loaded / e.total) * 100);
                        $('#progressFill')
                            .css('width', percent + '%')
                            .text(percent + '%');
                    }
                });
                return xhr;
            },
            success: function(res) {
                $('#progressFill').css('width', '100%').text('Done ✔');
                $('#uploadText').text('Uploaded ✔');
                setTimeout(() => {
                    location.reload();
                }, 800);
            },
            error: function() {
                showToast('Upload Failed','error');
                // 🔥 ENABLE AGAIN
                $('#uploadBtn').prop('disabled', false);
                $('#uploadText').text('Upload');
                $('#uploadLoader').addClass('hidden');
            }
        });
    });

    let currentId = null;

    function openDropdown(e, id) {
        e.stopPropagation();
        currentId = id;

        let dropdown = document.getElementById('globalDropdown');
        dropdown.classList.remove('hidden');

        let rect = e.target.getBoundingClientRect();
        dropdown.style.top = rect.bottom + "px";
        dropdown.style.left = (rect.left - 150) + "px";
    }

    document.addEventListener('click', function() {
        // document.getElementById('globalDropdown').classList.add('hidden');
        let dropdown = document.getElementById('globalDropdown');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    });

    // STATUS
    function handleStatus(status) {
        $.post("<?php echo e(route('journal.upload.status')); ?>", {
            _token: "<?php echo e(csrf_token()); ?>",
            id: currentId,
            status: status
        }, function(res) {
            showToast(res.message,'success');
            location.reload();
        });
    }

    // DELETE SINGLE
    function handleDelete() {

        if (!confirm('Delete full upload?')) return;

        $.post("<?php echo e(route('journal.bulk.delete')); ?>", {
            _token: "<?php echo e(csrf_token()); ?>",
            ids: [currentId]
        }, function(res) {
            showToast(res.message,'success');
            location.reload();
        });
    }

    // BULK DELETE
    function bulkDelete() {

        let ids = $('.rowCheckbox:checked').map(function() {
            return this.value;
        }).get();

        if (ids.length == 0) {
            showToast('Select at least one','error');
            return;
        }

        if (!confirm('Delete selected?')) return;

        $.post("<?php echo e(route('journal.bulk.delete')); ?>", {
            _token: "<?php echo e(csrf_token()); ?>",
            ids: ids
        }, function(res) {
            showToast(res.message,'success');
            location.reload();
        });
    }

    // document.querySelector('select').addEventListener('change', function() {
    //     if (this.value !== "Sample") {
    //         window.location.href = this.value;
    //     }
    // });

    document.getElementById('journalSampleSelect')?.addEventListener('change', function() {
        if (this.value) {
            window.location.href = this.value;
            this.value = '';
        }
    });

    function openJournalModal() {
        $('#journalModal').addClass('show');
        if ($('#itemsBody tr').length === 0) {
            addRow();
            addRow();
        }
    }

    function closeJournalModal() {
        $('#journalModal').removeClass('show');
    }

    function addRow() {

        let index = $('#itemsBody tr').length + 1;

        let options = `<option value="">Select Ledger</option>`;
        ledgers.forEach(l => {
            //options += `<option value="${l.name}">${l.name}</option>`;
            //options += `<option value="${encodeURIComponent(l.name)}">${l.name}</option>`;
            options += `<option value="${l.id}">${l.name}</option>`;
        });

        let row = `
        <tr>
            <td>${index}</td>

            <td>
                <select class="receipt-input ledger">
                    ${options}
                </select>
            </td>

            <td>
                <input type="number" class="receipt-input debit">
            </td>

            <td>
                <input type="number" class="receipt-input credit">
            </td>

            <td>
                <button onclick="$(this).closest('tr').remove(); recalc()">✕</button>
            </td>
        </tr>`;

        $('#itemsBody').append(row);

        $('.ledger').select2({
            dropdownParent: $('#journalModal'),
            width: '100%'
        });
    }

    $(document).on('input', '.debit, .credit', function() {

        let row = $(this).closest('tr');

        if ($(this).hasClass('debit')) {
            row.find('.credit').val(0);
        } else {
            row.find('.debit').val(0);
        }

        recalc();
    });

    function recalc() {

        let dr = 0,
            cr = 0;

        $('#itemsBody tr').each(function() {

            dr += parseFloat($(this).find('.debit').val()) || 0;
            cr += parseFloat($(this).find('.credit').val()) || 0;

        });

        $('#totalDr').text(dr.toFixed(2));
        $('#totalCr').text(cr.toFixed(2));
    }

    function saveJournal() {

        let totalDr = parseFloat($('#totalDr').text());
        let totalCr = parseFloat($('#totalCr').text());

        if (totalDr !== totalCr) {
            showToast('Debit & Credit must be equal','error');
            return;
        }

        let items = [];

        $('#itemsBody tr').each(function() {
            items.push({
                //ledger_name: $(this).find('.ledger').val(),
                // ledger_name: decodeURIComponent($(this).find('.ledger').val()),
                ledger_id: $(this).find('.ledger').val(),
                debit: parseFloat($(this).find('.debit').val()) || 0,
                credit: parseFloat($(this).find('.credit').val()) || 0
            });
        });

        $.post("<?php echo e(route('journal.manual.create')); ?>", {
            _token: "<?php echo e(csrf_token()); ?>",
            journal_no: $('#journal_no').val(),
            date: $('#journal_date').val(),
            narration: $('#journal_narration').val(),
            items: items
        }, function(res) {

            showToast(res.message || 'Saved','success');
            location.reload();

        });
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/bulkupload/journal/index.blade.php ENDPATH**/ ?>