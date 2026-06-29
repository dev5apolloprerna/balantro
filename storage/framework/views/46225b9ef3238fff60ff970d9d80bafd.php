
<?php $__env->startSection('content'); ?>
<div data-controller="confirm-delete"
    x-data="{ openUpload:false, openClient: <?php echo e(session('iPartyId') ? 'false' : 'true'); ?> }"
    x-init="openUpload = false">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-3">
            <div>
                <h6 class="font-semibold mb-0 dark:text-white"><?php echo e(__("Journal Uploads")); ?> 
                    <!-- Client Name -->
                    <?php if(session('client_name')): ?>
                    <span class="bulk-client-name text-xl font-semibold text-green-600 whitespace-nowrap truncate max-w-[140px]" style="font-variant-caps: small-caps;">
                        (<?php echo e(session('client_name')); ?>)
                    </span>
                    <?php endif; ?>
                </h6>
            </div>
        </div>

        <div class="card bg-white dark:bg-neutral-800 rounded-lg overflow-hidden">

            <!-- TOP BAR -->
            <div class="p-4 bulk-toolbar-row flex items-center justify-between w-full gap-3 flex-nowrap border-b border-gray-200 dark:border-neutral-600">

                <?php echo $__env->make('admin.transaction-processing.bulk-upload-tabs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="flex items-center gap-3">
                    <!-- Divider -->        
                    <div class="h-4 w-px bg-gray-300 dark:bg-neutral-600"></div>
                    <!-- Year Dropdown -->
                    <?php if(!empty($years) && count($years)): ?>
                    <select
                        onchange="window.location.href=this.value"
                        class="text-xs bg-transparent border-0 focus:ring-0 outline-none">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option
                            value="<?php echo e(route('sales.select.year', $year->strYear)); ?>"
                            <?php echo e(session('year') == $year->strYear ? 'selected' : ''); ?>>
                            <?php echo e($year->strYear); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php endif; ?>
                    <!-- Divider -->
                    <div class="h-4 w-px bg-gray-300 dark:bg-neutral-600"></div>

                    <!-- Select Client -->
                    <button
                        @click="openClient=true"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                        <i class="fa-solid fa-building"></i>
                        Select Client
                    </button>

                </div>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 group-block">
                <table class="min-w-[900px] w-full text-sm text-left text-gray-600 dark:text-gray-200">
                    <!-- Table Header -->
                    <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 text-xs uppercase sticky top-0 z-10">
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
                                <a href="<?php echo e(route('transaction_processing.preview_processing_journal',$upload->id)); ?>"
                                    class="<?php if($upload->status=='completed'): ?> text-green-500
                                      <?php elseif($upload->status=='processing'): ?> text-yellow-500
                                      <?php else: ?> text-blue-500 <?php endif; ?> font-semibold">
                                    <?php echo e(ucfirst($upload->status)); ?>

                                </a>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="<?php echo e(route('transaction_processing.preview_processing_journal',$upload->id)); ?>">
                                    <i class="fa-regular fa-eye action-icon text-gray-500"></i>
                                </a>
                                <div x-data="{ open:false }" class="relative inline-block">

                                    <!-- Button -->
                                    <button onclick="openDropdown(event, <?php echo e($upload->id); ?>)"
                                        class="text-gray-500 hover:text-gray-700 px-2">
                                        <i class="fa-solid fa-ellipsis-vertical action-icon"></i>
                                    </button>

                                    <!-- Dropdown -->
                                    <div id="globalDropdown"
                                        class="hidden fixed bg-white dark:bg-neutral-800 border rounded-xl shadow-xl w-48 z-[99999]">

                                        <div class="px-4 py-2 text-xs font-semibold text-gray-400 border-b">
                                            Change Status
                                        </div>

                                        <button onclick="handleStatus('Pending')" class="dropdown-item">Pending</button>
                                        <button onclick="handleStatus('Processing')" class="dropdown-item">Processing</button>
                                        <button onclick="handleStatus('Completed')" class="dropdown-item">Completed</button>

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
        data-upload-modal
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
    
    // ✅ SHOW FILE NAME
    function showJournalFileName(input) {
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
        document.getElementById('globalDropdown').classList.add('hidden');
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
    $(document).on('select2:open', function() {
        setTimeout(function() {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/transaction-processing/journal/index.blade.php ENDPATH**/ ?>