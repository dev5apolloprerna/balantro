
<?php $__env->startSection('content'); ?>
<div data-controller="confirm-delete"
    x-data="{ openUpload:false, openClient: <?php echo e(session('iPartyId') ? 'false' : 'true'); ?> }"
    x-init="openUpload = false">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h6 class="font-semibold mb-0 dark:text-white"><?php echo e(__("Purchase")); ?></h6>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="col-span-12">
                <div class="card !border-0 rounded-lg overflow-hidden bg-white dark:bg-neutral-800">
                    <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-2 sm:p-3">
                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col sm:flex-row gap-3 w-full">
                                <div class="bulk-toolbar-row flex items-center justify-between w-full gap-3 flex-nowrap">
                                    <!-- Left Side Tabs -->
                                    <?php echo $__env->make('admin.transaction-processing.bulk-upload-tabs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <!-- Right Side Actions -->
                                     <div class="bulk-toolbar-actions flex items-center justify-end gap-2 mt-0 w-full flex-nowrap">
                                        <?php if(session('client_name')): ?>
                                        <div class="bulk-client-name text-sm text-green-600 font-semibold whitespace-nowrap truncate max-w-[140px]" style="font-variant-caps: small-caps;">
                                            <?php echo e(session('client_name')); ?>

                                        </div>
                                        <?php endif; ?>
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
                                            class="bulk-text-btn bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md flex items-center gap-2 shadow-sm whitespace-nowrap">
                                            <i class="fa-solid fa-building"></i>
                                            Select Client
                                        </button>

                                        <?php if(session('guid')): ?>
                                            <a href="<?php echo e(route('clients.Gstindex', session('guid'))); ?>" class="bulk-settings-btn rounded-full bg-cyan-100 p-2 text-cyan-700 ring-1 ring-inset ring-cyan-200 hover:bg-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-300 dark:ring-cyan-800 shrink-0" 
                                                title="GST Settings">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    width="20"
                                                    height="20"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round">

                                                    <circle cx="12" cy="12" r="3"></circle>

                                                    <path
                                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2
                                                        2 0 1 1-2.83 2.83l-.06-.06a1.65
                                                        1.65 0 0 0-1.82-.33 1.65
                                                        1.65 0 0 0-1 1.51V21a2
                                                        2 0 1 1-4 0v-.09a1.65
                                                        1.65 0 0 0-1-1.51 1.65
                                                        1.65 0 0 0-1.82.33l-.06.06a2
                                                        2 0 1 1-2.83-2.83l.06-.06a1.65
                                                        1.65 0 0 0 .33-1.82 1.65
                                                        1.65 0 0 0-1.51-1H3a2
                                                        2 0 1 1 0-4h.09a1.65
                                                        1.65 0 0 0 1.51-1 1.65
                                                        1.65 0 0 0-.33-1.82l-.06-.06a2
                                                        2 0 1 1 2.83-2.83l.06.06a1.65
                                                        1.65 0 0 0 1.82.33h.01a1.65
                                                        1.65 0 0 0 1-1.51V3a2
                                                        2 0 1 1 4 0v.09a1.65
                                                        1.65 0 0 0 1 1.51h.01a1.65
                                                        1.65 0 0 0 1.82-.33l.06-.06a2
                                                        2 0 1 1 2.83 2.83l-.06.06a1.65
                                                        1.65 0 0 0-.33 1.82v.01a1.65
                                                        1.65 0 0 0 1.51 1H21a2
                                                        2 0 1 1 0 4h-.09a1.65
                                                        1.65 0 0 0-1.51 1z">
                                                    </path>

                                                </svg>
                                            </a>
                                            <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="bankTable" class="min-w-full text-sm text-left text-gray-600 dark:text-gray-200">
                            <!-- Table Header -->
                            <thead class="bg-gray-200 dark:bg-neutral-700 text-gray-600 dark:text-gray-200 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3">
                                        <input type="checkbox">
                                    </th>
                                    <th class="px-4 py-3 ">Sr.No.</th>
                                    <th class="px-4 py-3 w-[250px]">File Name</th>
                                    <!-- <th class="px-4 py-3">Type</th> -->
                                    <th class="px-4 py-3">Statement Date</th>
                                    <th class="px-4 py-3">Synced Date</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3">Pending</th>
                                    <th class="px-4 py-3">Saved</th>
                                    <th class="px-4 py-3">Synced</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right w-[120px]">Action</th>
                                </tr>
                            </thead>
                            <!-- Table Body -->
                            <tbody class="divide-y">
                                <?php $__currentLoopData = $uploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                    <td class="px-4 py-3">
                                        <input type="checkbox">
                                    </td>
                                    <td class="px-4 py-3"><?php echo e($loop->iteration); ?></td>
                                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200 w-[250px] truncate">
                                        <?php echo e($upload->file_name); ?>

                                    </td>
                                    <!-- <td class="px-4 py-3">
                                        <?php echo e(ucfirst(str_replace('_',' ',$upload->type))); ?>

                                    </td> -->
                                    <td class="px-4 py-3">
                                        <?php echo e($upload->statement_date ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php echo e($upload->synced_date ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php echo e($upload->total ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php echo e($upload->pending ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php echo e($upload->saved ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php echo e($upload->synced ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php if($upload->status == 'Completed'): ?>
                                        <a href="<?php echo e(route('transaction_processing.preview_processing_bank',$upload->id)); ?>"
                                            class="text-green-500 font-semibold hover:underline">
                                            Completed
                                        </a>
                                        <?php elseif($upload->status == 'Processing'): ?>
                                        <a href="<?php echo e(route('transaction_processing.preview_processing_bank',$upload->id)); ?>"
                                            class="text-yellow-500 font-semibold hover:underline">
                                            Processing
                                        </a>
                                        <?php elseif($upload->status == 'Pending'): ?>
                                        <a href="<?php echo e(route('transaction_processing.preview_processing_bank',$upload->id)); ?>"
                                            class="text-yellow-500 font-semibold hover:underline">
                                            Pending
                                        </a>
                                        <?php else: ?>
                                        <a href="<?php echo e(route('transaction_processing.preview_processing_bank',$upload->id)); ?>"
                                            class="text-red-500 font-semibold hover:underline">
                                            Failed
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-right flex justify-end gap-4">
                                        <a href="<?php echo e(route('transaction_processing.preview_processing_bank',$upload->id)); ?>">
                                            <i class="fa-regular fa-eye action-icon text-gray-500 cursor-pointer"></i>
                                        </a>
                                        <!-- <i class="fa-regular fa-file-lines text-gray-500 cursor-pointer"></i>
                                        <i class="fa-solid fa-ellipsis-vertical text-gray-500 cursor-pointer"></i> -->
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
        </div>
    </div>

    <!-- Upload Purchase Modal -->
    <div
        x-cloak
        x-show="openUpload"
        x-transition
        style="display: none;"
        data-upload-modal
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
        <div class="bg-white dark:bg-neutral-800 w-[650px] rounded-lg shadow-xl">
            <!-- HEADER -->
            <div class="flex justify-between items-center border-b px-5 py-4">
                <h2 class="text-lg font-semibold">
                    Upload Banking
                </h2>
                <button @click="openUpload=false">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>
            </div>
            <form method="POST" action="<?php echo e(route('bank.upload')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="p-6 space-y-5">
                    <!-- SELECT BANK -->
                    <div>
                        <label class="text-sm font-medium text-gray-600">
                            Select Bank
                        </label>
                        <select
                            name="bank_name"
                            class="w-full mt-1 border rounded-md px-3 py-2 text-sm">
                            <option value="">Search to Select</option>
                            <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($bank->name); ?>">
                                <?php echo e($bank->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <!-- DRAG DROP AREA -->
                    <div
                        class="border-2 border-dashed border-blue-400 rounded-lg p-10 text-center bg-gray-50 dark:bg-neutral-900">
                        <p class="text-gray-600 mb-3">
                            Drag and drop a file here or
                        </p>
                        <input
                            type="file"
                            name="bank_file"
                            id="bankFileUpload"
                            class="hidden"
                            accept=".xlsx,.xls,.csv"
                            onchange="showFileName(this)">
                        <button
                            type="button"
                            onclick="document.getElementById('bankFileUpload').click()"
                            class="border border-blue-500 text-blue-600 px-4 py-2 rounded-md text-sm">
                            Click to upload
                        </button>
                    </div>
                    <!-- FILE NAME -->
                    <div id="uploadedFileName" class="text-sm text-gray-500 hidden">
                        <i class="fa-solid fa-paperclip"></i>
                        <span id="fileNameText"></span>
                    </div>
                    <!-- NOTES -->
                    <div class="mt-6 text-sm">
                        <p class="font-semibold mb-2 text-gray-300">
                            Notes:
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="border border-neutral-600 rounded-md p-3 bg-gray-100 text-gray-700 dark:bg-neutral-700 dark:text-gray-200">
                                ❌ Please don't upload Passbook, Share Market statement and Loan statement.
                            </div>
                            <div class="border border-neutral-600 rounded-md p-3 bg-gray-100 text-gray-700 dark:bg-neutral-700 dark:text-gray-200">
                                ❌ Please don't upload password protected, RTP or TEXT format files.
                            </div>
                        </div>
                    </div>
                    <!-- FILE TYPES -->
                    <div class="grid grid-cols-3 gap-3 text-center text-sm">
                        <div class="border rounded-md p-3">
                            📊 Excel
                            <br>
                            <span class="text-xs text-gray-500">Upto 30 min</span>
                        </div>
                        <!-- <div class="border rounded-md p-3">
                            📄 Original PDF
                            <br>
                            <span class="text-xs text-gray-500">Upto 1 Hour</span>
                        </div>
                        <div class="border rounded-md p-3">
                            🖨 Scanned PDF
                            <br>
                            <span class="text-xs text-gray-500">Upto 12 Hours</span>
                        </div> -->
                    </div>
                </div>
                <!-- FOOTER -->
                <div class="flex justify-end gap-3 border-t px-5 py-4">
                    <button
                        type="button"
                        @click="openUpload=false"
                        class="px-4 py-2 border rounded-md">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md">
                        Upload
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

    <style>
        #bankTable {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* 🔥 IMPORTANT */
            font-size: 11px;
        }
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
        function showFileName(event) {
            const file = event.target.files[0];
            if (file) {
                document.getElementById("fileNameText").innerText = file.name;
                document.getElementById("uploadedFileName").classList.remove("hidden");
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#sampleDownload').change(function() {
                let type = $(this).val();
                if (type == 'with-item') {
                    window.location.href = "/samples/Purchase-with-item-sample-file.xlsx";
                }
                if (type == 'without-item') {
                    window.location.href = "/samples/Purchase-without-item-sample-file.xlsx";
                }
                // reset dropdown
                $(this).val('');
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let input = document.getElementById('clientSearch');
            if (input) {
                input.addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    document.querySelectorAll('.client-item').forEach(function(item) {
                        item.style.display =
                            item.innerText.toLowerCase().includes(filter) ?
                            '' :
                            'none';
                    });
                });
            }
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

        // STATUS CHANGE
        function handleStatus(status) {
            $.post("<?php echo e(route('bank.upload.status')); ?>", {
                _token: "<?php echo e(csrf_token()); ?>",
                id: currentId,
                status: status
            }, function(res) {
                showToast(res.message,'success');
                location.reload();
            });
        }

        // DELETE
        function handleDelete() {
            if (!confirm('Delete full upload?')) return;
            $.post("<?php echo e(route('bank.bulk.delete')); ?>", {
                _token: "<?php echo e(csrf_token()); ?>",
                ids: [currentId]
            }, function(res) {
                showToast(res.message,'success');
                location.reload();
            });
        }
    </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/transaction-processing/bank/index.blade.php ENDPATH**/ ?>