
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
                                <div class="flex flex-col sm:flex-row justify-between items-center w-full">
                                    <!-- Left Side Tabs -->
                                    <?php echo $__env->make('admin.bulkupload.bulk-upload-tabs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <!-- Right Side Actions -->
                                    <div class="flex items-center justify-end gap-3 mt-3 sm:mt-0 w-full">

                                        <!-- LEFT GROUP (Client + Dropdown) -->
                                        <div class="flex items-center gap-2 bg-gray-100 dark:bg-neutral-700 px-3 py-2 rounded-md">

                                            <!-- Client Name -->
                                            <?php if(session('client_name')): ?>
                                            <span class="text-xs font-semibold text-green-600 whitespace-nowrap" style="font-size: 1.0rem;font-variant-caps: small-caps;">
                                                <?php echo e(session('client_name')); ?>

                                            </span>
                                            <?php endif; ?>

                                            <!-- Divider -->
                                            <div class="h-4 w-px bg-gray-300 dark:bg-neutral-600"></div>
                                            <!-- Year Dropdown -->
                                            <?php if(!empty($years) && count($years)): ?>
                                            <select
                                                onchange="window.location.href=this.value"
                                                class="text-xs bg-transparent border-0 focus:ring-0 outline-none">
                                                <option value="">Select Year</option>
                                                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

                                            <!-- Sample Dropdown -->
                                            <select id="sampleDownload"
                                                class="text-xs bg-transparent border-0 focus:ring-0 outline-none">
                                                <option value="">Select Sample</option>
                                                <option value="with-item">With Item</option>
                                                <option value="without-item">Without Item</option>
                                            </select>

                                        </div>

                                        <!-- RIGHT GROUP (Actions) -->
                                        <div class="flex items-center gap-2">

                                            <!-- Client -->
                                            <button
                                                @click="openClient=true"
                                                class="px-3 py-2 text-xs border rounded-md hover:bg-gray-100 dark:hover:bg-neutral-700 flex items-center gap-1">
                                                <i class="fa-solid fa-building text-xs"></i>
                                                Client
                                            </button>

                                            <!-- Upload -->
                                            <button
                                                @click="<?php echo e(session('iPartyId') ? 'openUpload = true' : 'openClient = true'); ?>"
                                                class="px-3 py-2 text-xs border border-blue-500 text-blue-600 rounded-md hover:bg-blue-50 flex items-center gap-1">
                                                <i class="fa-solid fa-upload text-xs"></i>
                                                Upload
                                            </button>

                                            <!-- Primary Action -->
                                            <button id="addEntryBtn" onclick="openPurchaseModal()"
                                                class="px-4 py-2 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-1 shadow-sm">
                                                <i class="fa-solid fa-plus text-xs"></i>
                                                Add
                                            </button>

                                            <?php if(session('guid')): ?>
                                            <a href="<?php echo e(route('clients.Gstindex', session('guid'))); ?>" class="rounded-full bg-cyan-100 p-2 text-cyan-700 ring-1 ring-inset ring-cyan-200 hover:bg-cyan-200 dark:bg-cyan-900/30 dark:text-cyan-300 dark:ring-cyan-800"
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
                    </div>
                    <div class="overflow-y-auto h-[calc(100%-120px)]">
                        <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-200">
                            <!-- Table Header -->
                            <thead class="bg-gray-200 dark:bg-neutral-700 text-gray-600 dark:text-gray-200 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3">
                                        <input type="checkbox">
                                    </th>
                                    <th class="px-4 py-3 ">Sr.No.</th>
                                    <th class="px-4 py-3">File Name</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Statement Date</th>
                                    <th class="px-4 py-3">Synced Date</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3">Pending</th>
                                    <th class="px-4 py-3">Saved</th>
                                    <th class="px-4 py-3">Synced</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Action</th>
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
                                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-200">
                                        <?php echo e($upload->file_name); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <?php echo e(ucfirst(str_replace('_',' ',$upload->type))); ?>

                                    </td>
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
                                        <?php if($upload->status == 'Complete'): ?>
                                        <a href="<?php echo e(route('purchase.preview',$upload->id)); ?>"
                                            class="text-green-500 font-semibold hover:underline">
                                            Complete
                                        </a>
                                        <?php elseif($upload->status == 'Processing'): ?>
                                        <a href="<?php echo e(route('purchase.preview',$upload->id)); ?>"
                                            class="text-yellow-500 font-semibold hover:underline">
                                            Processing
                                        </a>
                                        <?php elseif($upload->status == 'Pending'): ?>
                                        <a href="<?php echo e(route('purchase.preview',$upload->id)); ?>"
                                            class="text-yellow-500 font-semibold hover:underline">
                                            Pending
                                        </a>
                                        <?php else: ?>
                                        <a href="<?php echo e(route('purchase.preview',$upload->id)); ?>"
                                            class="text-red-500 font-semibold hover:underline">
                                            Failed
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-right flex justify-end gap-4">
                                        <a href="<?php echo e(route('purchase.preview',$upload->id)); ?>">
                                            <i class="fa-regular fa-eye action-icon text-gray-500 cursor-pointer"></i>
                                        </a>
                                        <!-- <i class="fa-regular fa-file-lines text-gray-500 cursor-pointer"></i> -->
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

        <div class="bg-white dark:bg-neutral-800 w-[720px] rounded-lg shadow-xl">

            <!-- Header -->
            <div class="flex justify-between items-center border-b px-5 py-3">
                <h2 class="text-lg font-semibold">Upload Purchase</h2>

                <button @click="openUpload=false" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="<?php echo e(route('purchase.upload')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <!-- Body -->
                <div class="p-6">

                    <!-- Upload Area -->
                    <div class="border-2 border-dashed border-blue-400 dark:border-blue-500 rounded-lg p-10 text-center">

                        <i class="fa-regular fa-file text-3xl text-blue-500 mb-3"></i>

                        <p class="text-gray-600 mb-3">
                            Drag and drop a file here or
                        </p>

                        <div class="text-center">

                            <!-- Hidden File Input -->
                            <input
                                type="file"
                                name="purchase_file"
                                id="purchaseFileUpload"
                                class="hidden"
                                accept=".xlsx,.xls"
                                onchange="showFileName(this)">

                            <!-- Styled Button -->
                            <button
                                type="button"
                                onclick="document.getElementById('purchaseFileUpload').click()"
                                class="border border-blue-500 text-blue-600 px-4 py-2 rounded-md text-sm flex items-center gap-2 mx-auto">

                                <i class="fa-solid fa-upload"></i>
                                Click to upload

                            </button>

                        </div>

                    </div>

                    <!-- File name -->
                    <div id="uploadedFileName" class="text-sm text-gray-500 dark:text-gray-300 mt-3">
                        <i class="fa-solid fa-paperclip"></i>
                        <span id="fileNameText"></span>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6 text-sm text-gray-600 dark:text-gray-300">

                        <p class="font-semibold mb-2">Notes:</p>

                        <ul class="list-disc ml-5 space-y-1">

                            <li>Please make sure the uploaded excel file does not contain the dot(.) and dollar($) symbol in the column header and other then purchase/purchase field do not add anything above header.</li>

                            <li>Please make sure the file size must not exceed 30MB.</li>

                            <li>Sync the ledger before uploading the file.</li>

                            <li>Please don't upload password protected excel files.</li>

                            <li>Date format should be DD/MM/YYYY.</li>

                        </ul>

                    </div>

                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 border-t px-5 py-3">

                    <button
                        @click="openUpload=false"
                        class="px-4 py-2 border rounded-md text-gray-600">
                        Cancel
                    </button>

                    <button
                        class="px-4 py-2 bg-blue-600 text-white rounded-md">
                        Upload
                    </button>

                    <!-- <button type="submit"
                        class="px-4 py-2 bg-blue-800 text-white rounded-md">
                        Upload & Preview
                    </button> -->

                </div>
            </form>
        </div>
    </div>

    <!-- CLIENT DRAWER -->
    <div
        x-cloak
        x-show="openClient"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        style="display: none;"
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


    
    <div id="editModal" class="modal light-only">
        <div class="receipt-wrapper">
            <input type="hidden" id="edit_id">

            
            <div class="receipt-head">
                <div class="receipt-head-left">
                    <div class="receipt-company">Purchase Bill</div>
                    <div class="receipt-subtitle">Tax Invoice</div>
                </div>
                <div class="receipt-head-right">
                    <button type="button" class="receipt-close-btn" onclick="closeEditModal()">✕</button>
                </div>
            </div>
            <div class="receipt-body"> <!-- 🔥 ADD THIS -->

                
                <div class="receipt-meta-grid">
                    
                    <div class="receipt-meta-block">
                        <div class="receipt-block-title"><i class="fa-solid fa-building text-blue-400 mr-1"></i> Supplier Details</div>
                        <div class="receipt-field-row">
                            <label>Party Name</label>
                            <div style="display:flex; gap:6px; width:100%;">
                                <select id="edit_party" class="receipt-input party-select ledgerSelect" style="flex:1;">
                                    <option value=""></option>
                                    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->name); ?>"><?php echo e($ledger->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <button type="button"
                                    onclick="openLedgerModal()"
                                    style="padding:4px 8px; font-size:12px; background:#2563eb; color:white; border:none; border-radius:4px;">
                                    +
                                </button>
                            </div>
                        </div>
                        <div class="receipt-field-row">
                            <label>GSTIN / UIN</label>
                            <input type="text" id="edit_gst" class="receipt-input" placeholder="GST Number">
                        </div>
                        <div class="receipt-field-row">
                            <label>Address</label>
                            <textarea id="edit_address" class="receipt-input" placeholder="Address"></textarea>
                        </div>
                        <div class="receipt-field-row">
                            <label>Pincode</label>
                            <input type="text" id="edit_pincode" class="receipt-input" placeholder="Pincode">
                        </div>
                        <div class="receipt-field-row">
                            <label>City</label>
                            <input type="text" id="edit_city" class="receipt-input" placeholder="City">
                        </div>
                    </div>
                    
                    <div class="receipt-meta-block">
                        <div class="receipt-block-title"><i class="fa-solid fa-file-invoice text-blue-400 mr-1"></i> Invoice Details</div>
                        <div id="invoice_purcashe_ledger_wrap" class="receipt-field-row">
                            <label>Purcashe Ledger</label>
                            <select id="noitem_purcashe_ledger" class="receipt-input ledgerSelect">
                                <option value="">Select Ledger</option>
                                <?php $__currentLoopData = $purcasheLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ledger->name); ?>" data-ledger-id="<?php echo e($ledger->id); ?>" data-without-item="<?php echo e($ledger->is_without_item ?? 0); ?>"><?php echo e($ledger->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="receipt-field-row">
                            <label>Invoice No.</label>
                            <input type="text" id="edit_invoice" class="receipt-input" placeholder="Invoice Number">
                        </div>
                        <div class="receipt-field-row">
                            <label>Date</label>
                            <input type="date" id="edit_date" class="receipt-input" min="<?php echo e(session('year_from')); ?>" max="<?php echo e(session('year_to')); ?>">
                        </div>
                        <div class="receipt-field-row">
                            <label>Voucher Type</label>
                            <select id="edit_voucher_type" class="receipt-input">
                                <?php $__currentLoopData = $vchTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vchType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vchType); ?>"><?php echo e($vchType); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="receipt-field-row">
                            <label>Place Of Supply</label>
                            <select id="edit_place" class="receipt-input">
                                <option value="">Select State</option>
                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($state); ?>"><?php echo e($state); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                    </div>
                </div>

                
                <div class="">
                    <!-- <div class="receipt-items-header style=" display:flex; gap:10px; align-items:center;">

                        <div style="display:flex; gap:10px; align-items:center;">

                            <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                                <input type="radio" name="entry_mode" value="item" checked>
                                <span>With Item</span>
                            </label>

                            <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                                <input type="radio" name="entry_mode" value="noitem">
                                <span>Without Item</span>
                            </label>

                        </div>

                        <div class="receipt-field-row" style="margin:0;">
                            <label style="width:auto;padding-right:4px;">GST Mode</label>
                            <select id="gst_calc_mode" class="receipt-input" style="width:180px;">
                                <option value="standard">Standard (Auto Calculate)</option>
                                <option value="custom">Custom (Manual Slots)</option>
                            </select>
                        </div>

                        <div id="igst_toggle_wrap" class="receipt-field-row" style="margin:0;">
                            <label style="width:auto;padding-right:4px;">Use IGST</label>
                            <input type="checkbox" id="edit_is_igst">
                        </div>

                        <button type="button" id="addItemRow" class="receipt-add-btn">
                            + Add Row
                        </button>

                    </div> -->
                    <div class="receipt-items-header" style="display:flex; gap:10px; align-items:center;">

                        <div style="display:flex; gap:10px; align-items:center;">

                            <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                                <input type="radio" name="entry_mode" value="item" checked>
                                <span>With Item</span>
                            </label>

                            <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                                <input type="radio" name="entry_mode" value="noitem">
                                <span>Without Item</span>
                            </label>

                        </div>

                        <!-- Existing GST Mode -->
                        <div id="gst_mode_wrap" class="receipt-field-row" style="margin:0;">
                            <label style="width:auto;padding-right:4px;">GST Mode</label>
                            <select id="gst_calc_mode" class="receipt-input" style="width:180px;">
                                <option value="standard">Standard (Auto Calculate)</option>
                                <option value="custom">Custom (Manual Slots)</option>
                            </select>
                        </div>

                        <!-- IGST -->
                        <div id="igst_toggle_wrap" class="receipt-field-row" style="margin:0;">
                            <label style="width:auto;padding-right:4px;">Use IGST</label>
                            <input type="checkbox" id="edit_is_igst">
                        </div>

                        <!-- Add Row -->
                        <button type="button" id="addItemRow" class="receipt-add-btn">
                            + Add Row
                        </button>
                        <button type="button" id="addNoItemRow" class="receipt-add-btn" style="display:none;">
                            + Add More
                        </button>
                    </div>
                    
                    <div id="standard_items_section">
                        <div class="receipt-table-wrap">
                            <table class="receipt-table" id="editItemsTable">
                                <thead>
                                    <tr>
                                        <th class="col-sr">#</th>
                                        <th class="col-item">Item / Particulars</th>
                                        <th class="col-num">HSN</th>
                                        <th class="col-num">GST %</th>
                                        <th class="col-num">Qty</th>
                                        <th class="col-num">Unit</th>
                                        <th class="col-num">Rate</th>
                                        <th class="col-num">Amount</th>
                                        <th class="col-action"></th>
                                    </tr>
                                </thead>
                                <tbody id="editItemsBody"></tbody>
                                <tfoot>
                                    <tr class="receipt-subtotal-row">
                                        <td colspan="6" class="text-right text-xs pr-2">Sub Total</td>
                                        <td class="text-right font-semibold" id="foot_amount">0.00</td>
                                        <td class="text-right font-semibold" id="foot_total">0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div id="no_item_section" style="display:none;">
                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th>Purchase Ledger</th>
                                    <th>GST %</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="noItemBody"></tbody>
                        </table>
                    </div>

                    
                    <div id="custom_slots_section" style="display:none;">
                        <div style="padding:8px 10px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                            <div style="font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
                                GST Rate-wise Breakup (auto-populated from item GST%)
                            </div>
                            <table class="custom-slots-table" id="customSlotsTable">
                                <thead>
                                    <tr>
                                        <th>GST Rate</th>
                                        <th>Taxable Amt</th>
                                        <th>IGST Ledger</th>
                                        <th>IGST Amt</th>
                                        <th>CGST Ledger</th>
                                        <th>CGST Amt</th>
                                        <th>SGST Ledger</th>
                                        <th>SGST Amt</th>
                                    </tr>
                                </thead>
                                <tbody id="customSlotsBody">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="receipt-tax-summary">
                    <div class="tax-summary-left">
                        <div class="receipt-field-row">
                            <label style="font-size:11px;color:#000;width:115px;flex-shrink:0;text-align:right;padding-right:6px;">Remarks</label>
                            <textarea id="edit_remarks" class="receipt-input" placeholder="Remarks" rows="2"></textarea>
                        </div>
                        <div class="tax-note">* GST is calculated from items</div>
                    </div>
                    <div class="tax-summary-right">
                        <div class="tax-row">
                            <span class="tax-label">Taxable Amount</span>
                            <span class="tax-value" id="sum_amount">0.00</span>
                        </div>

                        
                        <div id="standard_tax_rows">

                            <!-- IGST -->
                            <div class="tax-row" style="display:flex; gap:5px;">
                                <span class="tax-label">IGST</span>
                                <select id="igst_ledger" class="receipt-input" style="width:140px;">
                                    <option value="">Select Ledger</option>
                                    <?php $__currentLoopData = $iGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->id); ?>"><?php echo e($ledger->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <!-- <span class="tax-value" id="sum_igst">0.00</span> -->
                                <input type="number" id="manual_igst" class="receipt-input" style="width:80px;" placeholder="Amt">
                            </div>

                            <!-- CGST -->
                            <div class="tax-row" style="display:flex; gap:5px;">
                                <span class="tax-label">CGST</span>
                                <select id="cgst_ledger" class="receipt-input" style="width:140px;">
                                    <option value="">Select Ledger</option>
                                    <?php $__currentLoopData = $cGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->id); ?>"><?php echo e($ledger->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <!-- <span class="tax-value" id="sum_cgst">0.00</span> -->
                                <input type="number" id="manual_cgst" class="receipt-input" style="width:80px;" placeholder="Amt">
                            </div>

                            <!-- SGST -->
                            <div class="tax-row" style="display:flex; gap:5px;">
                                <span class="tax-label">SGST</span>
                                <select id="sgst_ledger" class="receipt-input" style="width:140px;">
                                    <option value="">Select Ledger</option>
                                    <?php $__currentLoopData = $sGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->id); ?>"><?php echo e($ledger->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <!-- <span class="tax-value" id="sum_sgst">0.00</span> -->
                                <input type="number" id="manual_sgst" class="receipt-input" style="width:80px;" placeholder="Amt">
                            </div>

                        </div>

                        
                        <div id="custom_tax_rows" style="display:none;">
                            
                        </div>

                        <div class="tax-row">
                            <span class="tax-label">Round Off</span>
                            <!-- <span class="tax-value" id="sum_roundoff">0.00</span> -->
                            <input type="number" step="0.01" id="sum_roundoff" class="receipt-input tax-value" style="width:90px;text-align:right;" value="0.00">
                        </div>

                        <div class="tax-row grand-total-row">
                            <span class="tax-label">GRAND TOTAL</span>
                            <span class="tax-value" id="sum_grand_total">0.00</span>
                        </div>
                    </div>
                </div>

                
                <input type="hidden" id="edit_amount">
                <input type="hidden" id="edit_sgst">
                <input type="hidden" id="edit_cgst">
                <input type="hidden" id="edit_igst">
                <input type="hidden" id="edit_roundoff">
                <input type="hidden" id="edit_total_amount">
            </div>
            
            <div class="receipt-footer">
                <div class="receipt-footer-note">This is a computer-generated Purchase record.</div>
                <div class="receipt-footer-actions">
                    <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                    <button type="button" onclick="savePurchase()" id="saveRow" class="submit-btn">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="ledgerModal" class="modal" style="display: none;">
        <div class="modal-content">
            <!-- HEADER -->
            <div class="modal-header">
                <h3>Create Ledger</h3>
                <button type="button" class="close-btn" onclick="closeLedgerModal()">✕</button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <form id="ledgerForm">
                    <?php echo csrf_field(); ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="Name">
                        </div>
                        <div class="form-group">
                            <label>Parent</label>
                            <select name="Parent">
                                <option>Select Parent</option>
                                <?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($parent->strParents); ?>"><?php echo e($parent->strParents); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mailing Name</label>
                            <input type="text" name="MailingName">
                        </div>
                        <div class="form-group">
                            <label>Address Line 1</label>
                            <input type="text" name="AddressLine1">
                        </div>
                        <div class="form-group">
                            <label>Address Line 2</label>
                            <input type="text" name="AddressLine2">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="City">
                        </div>
                        <div class="form-group">
                            <label>Pincode</label>
                            <input type="text" name="Pincode">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <select id="State" class="inputCell">
                                <option value="">Select State</option>
                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($state); ?>"><?php echo e($state); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="Country">
                        </div>
                        <div class="form-group">
                            <label>GST No</label>
                            <input type="text" name="GstNo">
                        </div>
                        <div class="form-group">
                            <label>GST Registration Type</label>
                            <select name="GstRegistrationType">
                                <option value="">Select</option>
                                <option value="Regular">Regular</option>
                                <option value="Composition">Composition</option>
                                <option value="Unregistered">Unregistered</option>
                                <option value="Casual Taxable">Casual Taxable</option>
                                <option value="Non-resident Taxable">Non-resident Taxable</option>
                                <option value="Input Service Distributor">Input Service Distributor</option>
                                <option value="Special Economic Zone">Special Economic Zone</option>
                                <option value="E-commerce Operators">E-commerce Operators</option>
                                <option value="Tax Deduction at Source">Tax Deduction at Source</option>
                                <option value="TCS Collector">TCS Collector</option>
                                <option value="Voluntary Registration">Voluntary Registration</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="closeLedgerModal()" class="btn-cancel">Cancel</button>
                <button type="submit" form="ledgerForm" class="submit-btn">Save Ledger</button>
            </div>
        </div>
    </div>

    <style>

        /* SELECT2 */
        .select2-container--default .select2-selection--single {
            background: #fff;
            border: 1px solid #d1d5db;
            color: #111827;
            height: 30px;
        }

        .select2-container--default .select2-selection__rendered {
            color: #111827;
        }

        .select2-container--default .select2-results__option {
            color: #111827;
            background: white;
        }

        .select2-container--default .select2-results__option--highlighted {
            background: #2563eb;
            color: white;
        }

        .select2-dropdown {
            background: white;
            border: 1px solid #d1d5db;
        }

        .dark .select2-container--default .select2-selection--single {
            background: #020617;
            border: 1px solid #374151;
            color: white;
        }

        .dark .select2-container--default .select2-results__option {
            background: #020617;
            color: white;
        }

        .dark .select2-container--default .select2-results__option--highlighted {
            background: #2563eb;
            color: white;
        }

        .dark .select2-dropdown {
            background: #020617;
            border: 1px solid #374151;
            color: white;
        }

        /* Select2 dropdown background fix */
        .select2-container--default .select2-results__option {
            background: #ffffff !important;
            color: #000000 !important;
        }

        .select2-container--default .select2-results__option--highlighted {
            background: #2563eb !important;
            /* blue highlight */
            color: #ffffff !important;
        }

        /* Selected item (top input box) */
        .select2-container--default .select2-selection--single {
            background: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #d1d5db !important;
        }

        /* Dropdown box */
        .select2-dropdown {
            background: #ffffff !important;
            color: #000000 !important;
        }
        /* MODAL BASE */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            background: rgba(0, 0, 0, .65);
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            width: 95%;
            max-width: 1100px;
            max-height: 95vh;
            background: #fff;
            color: #111827;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .dark .modal-content {
            background: #1e293b;
            color: #e2e8f0;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .modal-header {
            border-color: #334155;
        }

        .modal-header h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        .modal-body {
            padding: 16px;
            overflow-y: auto;
            flex: 1;
            max-height: calc(95vh - 120px);
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #64748b;
            border-radius: 10px;
        }

        .modal-footer {
            padding: 12px 16px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .dark .modal-header,
        .dark .modal-footer {
            border-color: #334155;
        }

        .modal-content input,
        .modal-content select {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #111827;
            font-size: 13px;
        }

        .dark .modal-content input,
        .dark .modal-content select {
            background: #020617;
            border: 1px solid #334155;
            color: #e2e8f0;
        }

        .modal-content input:focus,
        .modal-content select:focus {
            border-color: #3b82f6;
            outline: none;
        }

        /* ══ RECEIPT MODAL ══ */
        #editModal.modal.show {
            align-items: flex-start;
            padding: 16px;
            overflow-y: hidden;
        }

        .receipt-wrapper {
            width: 95%;
            max-width: 1100px;
            max-height: 95vh;
            background: #fff;
            border-radius: 8px;
            /* overflow: auto; */
            overflow: hidden;
            /* change */
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .4);
            border: 1px solid #e2e8f0;
        }

        .receipt-body {
            overflow-y: auto;
            flex: 1;
        }

        .dark #editModal input,
        .dark #editModal select,
        .dark #editModal textarea,
        .dark #editModal .receipt-input {
            background: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #d1d5db !important;
        }

        .receipt-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 4px 8px;
            background: #fff;
        }

        .receipt-company {
            font-size: 12px;
            font-weight: 700;
            color: #000;
        }

        .receipt-subtitle {
            font-size: 8px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .receipt-close-btn {
            background: rgba(0, 0, 0, .1);
            border: none;
            color: #000;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .receipt-close-btn:hover {
            background: rgba(239, 68, 68, .15);
            color: #dc2626;
        }

        .receipt-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 2px solid #e2e8f0;
        }

        .receipt-meta-block {
            padding: 4px 9px;
        }

        .receipt-meta-block:first-child {
            border-right: 1px solid #e2e8f0;
        }

        .receipt-block-title {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #000;
            border-bottom: 1px dashed #e2e8f0;
        }

        .receipt-field-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2px;
        }

        .receipt-field-row label {
            font-size: 11px;
            color: #374151 !important;
            width: 115px;
            flex-shrink: 0;
            text-align: right;
            padding-right: 6px;
        }

        .dark .receipt-field-row label {
            color: #e5e7eb !important;
        }

        input[type="radio"] {
            pointer-events: auto !important;
            position: relative;
            z-index: 10;
        }

        label {
            cursor: pointer;
        }

        .receipt-input {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 12px;
            color: #111827;
            width: 100%;
        }

        .receipt-input:focus {
            border-color: #3b82f6;
            background: #eff6ff;
            outline: none;
        }

        .receipt-items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e2e8f0;
        }

        .receipt-add-btn {
            font-size: 11px;
            background: #059669;
            color: white;
            border: none;
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .receipt-add-btn:hover {
            background: #047857;
        }

        .receipt-table-wrap {
            max-height: 160px;
            overflow-y: auto;
        }

        .receipt-table-wrap::-webkit-scrollbar {
            width: 4px;
        }

        .receipt-table-wrap::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .receipt-table thead tr {
            background: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
            position: sticky;
            top: 0;
        }

        .receipt-table th {
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #000;
            white-space: nowrap;
        }

        .receipt-table th.col-num {
            text-align: right;
        }

        .receipt-table th.col-sr {
            width: 30px;
        }

        .receipt-table th.col-item {
            min-width: 180px;
        }

        .receipt-table th.col-num {
            width: 85px;
        }

        .receipt-table th.col-action {
            width: 36px;
        }

        .receipt-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .receipt-table tbody tr:hover {
            background: #f8fafc;
        }

        .receipt-table td {
            vertical-align: middle;
        }

        .receipt-table td.td-sr {
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            padding-left: 8px;
        }

        .receipt-table td input[type="text"],
        .receipt-table td input[type="number"] {
            width: 100%;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 3px;
            padding: 3px 5px;
            font-size: 12px;
            color: #111827;
        }

        .receipt-table td input:focus {
            border-color: #3b82f6;
            background: #eff6ff;
            outline: none;
        }

        .receipt-table td input[readonly] {
            background: #f8fafc;
            color: #374151;
            border-color: #e2e8f0;
            font-weight: 600;
        }

        .receipt-table td input[type="number"] {
            text-align: right;
        }

        .receipt-table td:last-child {
            text-align: center;
        }

        .receipt-subtotal-row td {
            padding: 5px 4px;
            font-size: 12px;
            color: #000;
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }

        .receipt-del-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #ef4444;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .receipt-del-btn:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .receipt-tax-summary {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 6px 9px;
            border-top: 2px dashed #e2e8f0;
            background: #f8fafc;
        }

        .tax-note {
            font-size: 10px;
            color: #000;
            font-style: italic;
            margin-top: 4px;
        }

        .tax-summary-right {
            min-width: 320px;
        }

        .tax-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            color: #000;
            gap: 6px;
        }

        .tax-label {
            font-size: 11px;
            flex: 1;
        }

        .tax-value {
            font-weight: 600;
            font-size: 12px;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .grand-total-row {
            background: #1e40af;
            border-radius: 4px;
            padding: 2px 8px !important;
            margin-top: 4px;
            border-bottom: none !important;
        }

        .grand-total-row .tax-label,
        .grand-total-row .tax-value {
            color: #ffffff !important;
            font-size: 13px !important;
            font-weight: 700 !important;
        }

        .receipt-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 9px;
            border-top: 1px solid #e2e8f0;
            background: #fff;
            position: sticky;
            bottom: 0;
            z-index: 10;
        }

        .receipt-footer-note {
            font-size: 10px;
            color: #000;
            font-style: italic;
        }

        .receipt-footer-actions {
            display: flex;
            gap: 10px;
        }

        /* ══ CUSTOM GST SLOTS TABLE ══ */
        .custom-slots-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .custom-slots-table th {
            background: #e0e7ff;
            color: #1e40af;
            padding: 0px 8px;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            border: 1px solid #c7d2fe;
        }

        .custom-slots-table td {
            padding: 0px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .custom-slots-table .rate-badge {
            display: inline-block;
            background: #1e40af;
            color: #fff;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .custom-slots-table select {
            width: 100%;
            font-size: 11px;
            padding: 2px 4px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            background: #fff;
            color: #111827;
        }

        .custom-slots-table input[type="number"] {
            width: 100%;
            font-size: 11px;
            padding: 2px 4px;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            background: #f8fafc;
            color: #374151;
            font-weight: 600;
            text-align: right;
        }

        .custom-slots-table .zero-row {
            opacity: .4;
        }

        /* ══ VIEW MODAL STYLES ══ */
        .view-card {
            background: #1e293b;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .view-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px 20px;
        }

        .view-grid label {
            font-size: 11px;
            color: #94a3b8;
        }

        .view-grid p {
            font-size: 13px;
            font-weight: 500;
            margin: 2px 0 0;
            color: #e2e8f0;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            background: #f59e0b;
            color: white;
        }

        .view-totals {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .view-totals .box {
            background: #020617;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }

        .view-totals span {
            font-size: 11px;
            color: #94a3b8;
        }

        .view-totals strong {
            display: block;
            font-size: 14px;
            margin-top: 3px;
        }

        .view-totals .highlight {
            background: #2563eb;
            color: white;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #cbd5f5;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .view-table {
            width: 100%;
            border-collapse: collapse;
        }

        .view-table th {
            font-size: 11px;
            background: #020617;
            padding: 8px;
            color: #94a3b8;
            text-align: left;
        }

        .view-table td {
            padding: 8px;
            border-bottom: 1px solid #1e293b;
            font-size: 12px;
        }

        .view-table td:nth-child(n+4) {
            text-align: right;
        }

        .view-table tbody tr:hover {
            background: #1e293b;
        }

        #no_item_section {
            border-top: 1px dashed #e2e8f0;
            margin-top: 10px;
            padding-top: 10px;
        }

        #no_item_section .receipt-field-row {
            max-width: 400px;
        }

        button {
            position: relative;
            z-index: 5;
        }

        /* 🔥 FORCE FULL LIGHT MODE FOR Purchase MODAL */

        #editModal,
        #editModal * {
            color-scheme: light;
        }

        /* Background fix */
        #editModal .receipt-wrapper {
            background: #ffffff !important;
            color: #111827 !important;
        }

        /* Inputs */
        #editModal input,
        #editModal select,
        #editModal textarea {
            background: #ffffff !important;
            color: #111827 !important;
            border: 1px solid #d1d5db !important;
        }

        /* Labels */
        #editModal label {
            color: #374151 !important;
        }

        /* Table */
        #editModal table {
            background: #ffffff !important;
            color: #111827 !important;
        }

        #editModal th {
            background: #f1f5f9 !important;
            color: #000 !important;
        }

        #editModal td {
            color: #111827 !important;
        }

        /* Tax summary */
        #editModal .receipt-tax-summary {
            background: #f8fafc !important;
        }

        /* Footer */
        #editModal .receipt-footer {
            background: #ffffff !important;
        }

        #editModal input[type="radio"] {
            accent-color: #2563eb;
            /* optional */
        }

        .light-only {
            color-scheme: light;
        }

        #editModal {
            background: rgba(0, 0, 0, 0.5);
            /* overlay */
        }

        #editModal .receipt-wrapper {
            background: #ffffff !important;
            color: #111827 !important;
        }

        /* Inputs */
        #editModal input,
        #editModal select,
        #editModal textarea {
            background: #ffffff !important;
            color: #111827 !important;
            border: 1px solid #d1d5db !important;
        }

        /* Labels */
        #editModal label {
            color: #374151 !important;
        }

        /* Table */
        #editModal table {
            background: #ffffff !important;
        }

        #editModal th {
            background: #f1f5f9 !important;
            color: #000 !important;
        }

        /* Summary */
        #editModal .receipt-tax-summary {
            background: #f8fafc !important;
        }

        /* Footer */
        #editModal .receipt-footer {
            background: #ffffff !important;
        }

        .btn-cancel {
            background: #374151;
            color: #fff;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 12px;
        }

        .btn-cancel:hover {
            background: #1f2937;
        }

        .submit-btn {
            background: #2563eb;
            color: #fff;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 12px;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }
    </style>
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

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            background: rgba(0,0,0,.65);
        }
        #ledgerModal {
            position: fixed !important;
            inset: 0;
            z-index: 999999 !important;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,.6);
        }

        #ledgerModal.show{
            display:flex !important;
        }

        #ledgerModal .modal-content{
            width: 900px;
            max-width: 95%;
            max-height: 90vh;
            overflow-y: auto;

            background: #fff;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0,0,0,.3);

            display: flex;
            flex-direction: column;
        }

        #ledgerModal .modal-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:15px 20px;
            border-bottom:1px solid #e5e7eb;
        }

        #ledgerModal .modal-body{
            padding:20px;
            overflow-y:auto;
            flex:1;
        }

        #ledgerModal .modal-footer{
            padding:15px 20px;
            border-top:1px solid #e5e7eb;
            display:flex;
            justify-content:flex-end;
            gap:10px;
        }

        .form-grid{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:15px;
        }

        .form-group{
            display:flex;
            flex-direction:column;
        }

        .form-group label{
            margin-bottom:5px;
            font-size:13px;
            font-weight:600;
        }

        /* Ledger Modal Dark Theme */
        .dark #ledgerModal .modal-content{
            background:#1f2937;
            color:#f3f4f6;
        }

        .dark #ledgerModal .modal-header,
        .dark #ledgerModal .modal-footer{
            border-color:#374151;
        }

        .dark #ledgerModal h3,
        .dark #ledgerModal label{
            color:#f3f4f6 !important;
        }

        .dark #ledgerModal input,
        .dark #ledgerModal select,
        .dark #ledgerModal textarea{
            background:#111827 !important;
            border:1px solid #374151 !important;
            color:#f9fafb !important;
        }

        .dark #ledgerModal input::placeholder{
            color:#9ca3af;
        }

        .dark #ledgerModal select option{
            background:#111827;
            color:#f9fafb;
        }
    </style>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
    <script>
        function showFileName(input) {
            if (!validateUploadFileSize(input)) {
                document.getElementById('fileNameText').innerText = '';
                document.getElementById('uploadedFileName').style.display = 'none';
                return;
            }

            let fileName = input.files[0]?.name;

            if (fileName) {
                document.getElementById('fileNameText').innerText = fileName;
                document.getElementById('uploadedFileName').style.display = 'block';
            } else {
                document.getElementById('fileNameText').innerText = '';
                document.getElementById('uploadedFileName').style.display = 'none';
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

            $('.ledgerSelect').select2({
                width: '100%',
                placeholder: "Search Ledger...",
                allowClear: true,
                dropdownAutoWidth: true
            });

            $('.itemSelect').select2({
                width: '100%',
                placeholder: "Search Item...",
                allowClear: true,
                dropdownAutoWidth: true
            });

            $(document).on('focus', '.ledgerSelect', function() {
                $(this).select2('open');
            });
        });
    </script>
    <script>
        let IGST_LEDGERS = <?php echo json_encode($iGstLedgers, 15, 512) ?>;
        let CGST_LEDGERS = <?php echo json_encode($cGstLedgers, 15, 512) ?>;
        let SGST_LEDGERS = <?php echo json_encode($sGstLedgers, 15, 512) ?>;
        const ITEM_MASTER = <?php echo json_encode($stockItems, 15, 512) ?>;
        const PURCHASE_LEDGERS = <?php echo json_encode($purcasheLedgers ?? [], 15, 512) ?>;
        const PURCHASE_GST_MAPPINGS = <?php echo json_encode($purchaseGstMappings ?? [], 15, 512) ?>;
        const PARTY_LEDGER_DETAILS = <?php echo json_encode($ledgers ?? [], 15, 512) ?>;
        const ROUND_OFF_SIDE = <?php echo json_encode($roundOffSide ?? 'normal', 15, 512) ?>;
    </script>
    <script>
        function normalizedLedgerName(value) {
            return String(value || '').replace(/['"]/g, '').trim().toLowerCase();
        }

        function findPartyLedgerDetails(value) {
            const normalized = normalizedLedgerName(value);
            return PARTY_LEDGER_DETAILS.find(ledger =>
                String(ledger.id || '') === String(value || '') ||
                normalizedLedgerName(ledger.name) === normalized
            ) || null;
        }

        function applyPartyLedgerDetails(value) {
            const ledger = findPartyLedgerDetails(value);

            $('#edit_gst').val(ledger?.gst_no || '');
            $('#edit_address').val(ledger?.address || '');
            $('#edit_pincode').val(ledger?.pincode || '');
            $('#edit_city').val(ledger?.city || '');

            if (ledger?.state) {
                $('#edit_place').val(ledger.state).trigger('change');
            }
        }

        $(document).on('change', '#edit_party', function () {
            applyPartyLedgerDetails($(this).val());
        });
        
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

        // STATUS
        function handleStatus(status) {
            $.post("<?php echo e(route('purchase.upload.status')); ?>", {
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

            $.post("<?php echo e(route('purchase.bulk.delete')); ?>", {
                _token: "<?php echo e(csrf_token()); ?>",
                ids: [currentId]
            }, function(res) {
                showToast(res.message,'success');
                location.reload();
            });
        }

        function openPurchaseModal() {

            let fromDate = "<?php echo e(session('year_from')); ?>";
            let toDate = "<?php echo e(session('year_to')); ?>";
            let today = new Date().toISOString().split('T')[0];

            // 🔥 If today inside FY → use today
            if (today >= fromDate && today <= toDate) {
                $('#edit_date').val(today);
            } else {
                // otherwise set FY start
                $('#edit_date').val(fromDate);
            }
            $('#edit_date').val(new Date().toISOString().split('T')[0]);
            $('#edit_party').val('').trigger('change');
            document.getElementById('editModal').classList.add('show');

        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
        }

        function addPurchaseRow(data = {}) {

            let row = `
            <tr>
                <td>
                <select class="item border p-1 itemSelect">
                        ${buildItemOptions(data.item_name || '')}
                    </select></td>
                <td><input class="gst border p-1"></td>
                <td><input class="qty border p-1"></td>
                <td><input class="rate border p-1"></td>
                <td><input class="amount border p-1" readonly></td>
            </tr>`;

            $('#purchaseItems').append(row);
        }

        $(document).on('input', '.qty, .rate, .gst', function() {

            let total = 0;
            let cgst = 0;
            let sgst = 0;
            let igst = 0;

            $('#purchaseItems tr').each(function() {

                let qty = parseFloat($(this).find('.qty').val()) || 0;
                let rate = parseFloat($(this).find('.rate').val()) || 0;
                let gst = parseFloat($(this).find('.gst').val()) || 0;

                let amount = qty * rate;
                $(this).find('.amount').val(amount.toFixed(2));

                total += amount;

                let gstAmt = (amount * gst) / 100;

                cgst += gstAmt / 2;
                sgst += gstAmt / 2;
            });

            $('#purchase_cgst').val(cgst.toFixed(2));
            $('#purchase_sgst').val(sgst.toFixed(2));
            $('#purchaseTotal').text((total + cgst + sgst).toFixed(2));
        });

        function savePurchase() {
            let btn = $('#saveRow'); // ✅ correct button select

            // 🚫 Prevent multiple click
            if (btn.prop('disabled')) return;

            // 🔒 Disable button
            btn.prop('disabled', true);

            // 💡 Show loader
            btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            let party = $('#edit_party').val();
            let invoice = $('#edit_invoice').val();

            if (!party) {
                showToast('Please select Party','error');
                btn.prop('disabled', false);
                btn.html('Save');
                return;
            }
            if (!invoice) {
                showToast('Please enter Invoice Number','error');
                btn.prop('disabled', false);
                btn.html('Save');
                return;
            }

            let mode = $('input[name="entry_mode"]:checked').val();
            let amount = 0;

            // ✅ HANDLE MODE
            if (mode === 'noitem') {
                let ledgerRows = collectNoItemRows();
                amount = ledgerRows.reduce((sum, row) => sum + (parseFloat(row.amount) || 0), 0);
                if (!ledgerRows.length || amount <= 0) {
                    showToast('Please add at least one purchase ledger row','error');
                    btn.prop('disabled', false);
                    btn.html('Save');
                    return;
                }

            } else {
                amount = $('#edit_amount').val();
            }
            let items = [];

            $('#editItemsBody tr').each(function() {

                let row = $(this);

                let qty = parseFloat(row.find('.qty').val()) || 0;
                let rate = parseFloat(row.find('.rate').val()) || 0;
                let gst = parseFloat(row.find('.gst').val()) || 0;

                let amount = qty * rate;
                let gstAmt = (amount * gst) / 100;

                let isIGST = $('#edit_is_igst').is(':checked');

                let cgst = 0,
                    sgst = 0,
                    igst = 0;

                if (isIGST) {
                    igst = gstAmt;
                } else {
                    cgst = gstAmt / 2;
                    sgst = gstAmt / 2;
                }

                items.push({
                    //item: row.find('.item_name').val(),
                    item: row.find('.item_name option:selected').text(), // ✅ FULL NAME
                    //item: row.find('.item_name option:selected').text(),
                    hsn: row.find('.hsn').val(),
                    gst: gst,
                    qty: qty,
                    unit: row.find('.unit').val(),
                    rate: rate,
                    amount: amount,

                    // ✅ ADD THESE (IMPORTANT)
                    cgst: cgst,
                    sgst: sgst,
                    igst: igst,
                    total_amount: amount + cgst + sgst + igst
                });

            });

            let custom_gst = [];

            $('#customSlotsBody tr').each(function() {

                let row = $(this);

                custom_gst.push({
                    rate: row.find('.gst_rate').text().replace('%', ''),
                    purchase_ledger_id: row.find('.slot_purchase_ledger_id').val(),
                    taxable: parseFloat(row.find('td:eq(1)').text()) || 0,

                    // ✅ match backend naming
                    igst_ledger_id: row.find('.igst_ledger').val(),
                    igst_amount: row.find('.igst_amt').val(),

                    cgst_ledger_id: row.find('.cgst_ledger').val(),
                    cgst_amount: row.find('.cgst_amt').val(),

                    sgst_ledger_id: row.find('.sgst_ledger').val(),
                    sgst_amount: row.find('.sgst_amt').val()
                });

            });
            let cgstLedger = $('#cgst_ledger').val();
            let sgstLedger = $('#sgst_ledger').val();
            let igstLedger = $('#igst_ledger').val();
            let noitemRows = collectNoItemRows();

            if (mode === 'noitem') {
                $('#gst_calc_mode').val('custom');
            }

            let data = {
                _token: "<?php echo e(csrf_token()); ?>",
                party: party,
                invoice: invoice,
                date: $('#edit_date').val(),
                gst: $('#edit_gst').val(),
                place: $('#edit_place').val(),
                voucher_type: $('#edit_voucher_type').val(),
                remarks: $('#edit_remarks').val(),
                is_igst: $('#edit_is_igst').is(':checked') ? 1 : 0,
                amount: amount,
                cgst: $('#edit_cgst').val(),
                sgst: $('#edit_sgst').val(),
                igst: $('#edit_igst').val(),
                total: $('#edit_total_amount').val(),
                roundoff: $('#edit_roundoff').val(),
                city: $('#edit_city').val(),
                pincode: $('#edit_pincode').val(),
                address: $('#edit_address').val(),

                // 🔥 ADD THIS
                cgst_ledger: cgstLedger,
                sgst_ledger: sgstLedger,
                igst_ledger: igstLedger,

                entry_mode: mode,
                // purchase_ledger: $('#noitem_purcashe_ledger').val(),
                purchase_ledger: $('#noitem_purcashe_ledger option:selected').text(),
                purchase_ledger_name: $('#noitem_purcashe_ledger option:selected').text(),
                purchase_ledger_id: $('#noitem_purcashe_ledger').val(),

                items: items,
                custom_slots: custom_gst,
                gst_mode: $('#gst_calc_mode').val(),
                noitem_rows: noitemRows
            };

            $.ajax({
                url: "<?php echo e(route('purchase.manual.create')); ?>",
                type: "POST",
                data: data,

                success: function(res) {
                    console.log(res.status);
                    if (res.status) {
                        showToast(res.message || 'Inserted successfully', 'success');
                        closeEditModal();
                        location.reload();
                    } else {
                        showToast(res.message || 'Something went wrong', 'error');

                        // 🔥 Enable button again
                        btn.prop('disabled', false);
                        btn.html('Save');
                    }
                },

                error: function(xhr) {

                    let msg = 'Server error';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    showToast(msg, 'error');

                    // 🔥 Enable button again
                    btn.prop('disabled', false);
                    btn.html('Save');
                }
            });
        }

        $(document).on('change', 'input[name="entry_mode"]', function() {

            let mode = $(this).val();

            if (mode === 'item') {
                $('#standard_items_section').show();
                $('#no_item_section').hide();
                $('#addItemRow').show();
                $('#addNoItemRow').hide();
                $('#gst_mode_wrap').show();
            } else {
                $('#standard_items_section').hide();
                $('#no_item_section').show();
                $('#addItemRow').hide();
                $('#addNoItemRow').show();
                $('#gst_mode_wrap').show();
                $('#gst_calc_mode').val('custom').trigger('change');
                if ($('#noItemBody tr').length === 0) {
                    addNoItemRow();
                }
            }
        });

        $(document).on('click', '#addItemRow', function() {
            addItemRow(); // tamaro existing function
        });

        function addItemRow(data = {}) {
            let rowCount = $('#editItemsBody tr').length + 1;

            let row = `
                <tr>
                    <td class="td-sr">${rowCount}</td>
                    <td>
                    <select class="item_name itemSelect">
                        ${buildItemOptions(data.item_name || '')}
                    </select>
                    </td>
                    <td><input type="text" class="hsn" value="${data.hsn || ''}"></td>
                    <td><input type="number" class="gst" value="${data.gst || 0}"></td>
                    <td><input type="number" class="qty" value="${data.qty || 1}"></td>
                    <td><input type="text" class="unit" value="${data.unit || 'NOS'}"></td>
                    <td><input type="number" class="rate" value="${data.rate || 0}"></td>
                    <td><input type="number" class="amount" value="${data.amount || 0}" readonly></td>
                    <td>
                        <button type="button" class="receipt-del-btn">✕</button>
                    </td>
                </tr>
            `;

            $('#editItemsBody').append(row);
            // 🔥 Apply Select2 AFTER append
            $('.itemSelect').last().select2({
                width: '100%',
                placeholder: "Search Item...",
                allowClear: true
            });
            recalcTotals(); // 🔥 IMPORTANT
        }

        $('#edit_is_igst').change(function() {

            if ($(this).is(':checked')) {
                $('#cgst_ledger').closest('.tax-row').hide();
                $('#sgst_ledger').closest('.tax-row').hide();
                $('#igst_ledger').closest('.tax-row').show();
            } else {
                $('#cgst_ledger').closest('.tax-row').show();
                $('#sgst_ledger').closest('.tax-row').show();
                $('#igst_ledger').closest('.tax-row').hide();
            }

        });

        $(document).on('input', '.qty, .rate, .gst', function() {

            let row = $(this).closest('tr');

            let qty = parseFloat(row.find('.qty').val()) || 0;
            let rate = parseFloat(row.find('.rate').val()) || 0;

            let amount = qty * rate;

            row.find('.amount').val(amount.toFixed(2));

            recalcTotals(); // 🔥 important
        });

        function recalcTotals() {

            let mode = $('#gst_calc_mode').val();
            let entryMode = $('input[name="entry_mode"]:checked').val();

            let taxable = 0;
            let totalGST = 0;

            let isIGST = $('#edit_is_igst').is(':checked');
            applyGstLedgerMapping(false);
            let rateMap = {};

            // =========================
            // ✅ WITH ITEM
            // =========================
            if (entryMode === 'item') {

                $('#editItemsBody tr').each(function() {

                    let row = $(this);

                    let qty = parseFloat(row.find('.qty').val()) || 0;
                    let rate = parseFloat(row.find('.rate').val()) || 0;
                    let gst = parseFloat(row.find('.gst').val()) || 0;
                    let itemName = row.find('.item_name option:selected').text();
                    let itemMapping = findItemGstMapping(itemName);

                    let amount = qty * rate;
                    row.find('.amount').val(amount.toFixed(2));

                    taxable += amount;

                    let gstAmt = (amount * gst) / 100;
                    totalGST += gstAmt;

                    if (!rateMap[gst]) {
                        rateMap[gst] = {
                            amt: 0,
                            gst: 0,
                            rate: gst,
                            itemName: itemName,
                            itemMapping: itemMapping ? itemGstMappingObject(itemMapping) : null
                        };
                    }

                    rateMap[gst].amt += amount;
                    rateMap[gst].gst += gstAmt;
                    if (!rateMap[gst].itemMapping && itemMapping) {
                        rateMap[gst].itemName = itemName;
                        rateMap[gst].itemMapping = itemGstMappingObject(itemMapping);
                    }
                });

                if (mode === 'standard') {
                    let firstItemName = $('#editItemsBody tr:first .item_name option:selected').text();
                    applyItemGstMapping(firstItemName, false);
                }
            }

            // =========================
            // ✅ WITHOUT ITEM (🔥 FIX)
            // =========================
            if (entryMode === 'noitem') {

                $('#noItemBody tr').each(function(index) {
                    let gst = parseFloat($(this).find('.noitem-gst').val()) || 0;
                    let amount = parseFloat($(this).find('.noitem-amount').val()) || 0;
                    let ledgerSelect = $(this).find('.noitem-ledger');
                    let ledgerId = ledgerSelect.val() || '';
                    let ledgerName = ledgerSelect.find('option:selected').text() || '';
                    let gstAmt = (amount * gst) / 100;
                    let key = `row:${index}|${gst}|${ledgerId}`;

                    taxable += amount;
                    totalGST += gstAmt;

                    rateMap[key] = {
                        amt: amount,
                        gst: gstAmt,
                        rate: gst,
                        ledgerId: ledgerId,
                        ledgerName: ledgerName,
                        slotKey: key
                    };
                });
            }

            let cgst = 0,
                sgst = 0,
                igst = 0;

            // =========================
            // ✅ STANDARD MODE
            // =========================
            if (mode === 'standard') {

                if (isIGST) {
                    igst = totalGST;

                    $('#manual_igst').val(igst.toFixed(2));
                    $('#manual_cgst').val(0);
                    $('#manual_sgst').val(0);

                } else {
                    cgst = totalGST / 2;
                    sgst = totalGST / 2;

                    $('#manual_cgst').val(cgst.toFixed(2));
                    $('#manual_sgst').val(sgst.toFixed(2));
                    $('#manual_igst').val(0);
                }
            }

            // =========================
            // ✅ CUSTOM MODE
            // =========================
            if (mode === 'custom') {

                let html = '';

                let existing = {};
                $('#customSlotsBody tr').each(function() {
                    let rate = $(this).find('.gst_rate').text().replace('%', '');
                    let purchaseLedgerId = $(this).find('.slot_purchase_ledger_id').val() || '';
                    let key = $(this).data('slot-key') || `${rate}|${purchaseLedgerId}`;
                    existing[key] = {
                        igst: $(this).find('.igst_ledger').val(),
                        cgst: $(this).find('.cgst_ledger').val(),
                        sgst: $(this).find('.sgst_ledger').val()
                    };
                });

                Object.keys(rateMap).forEach(key => {

                    let data = rateMap[key];
                    let rate = data.rate ?? key;

                    let cg = data.gst / 2;
                    let sg = data.gst / 2;

                    html += `
            <tr data-slot-key="${data.slotKey || key}" data-purchase-ledger-id="${data.ledgerId || ''}">
                <td class="gst_rate">${rate}%</td>
                <td>${data.amt.toFixed(2)}<input type="hidden" class="slot_purchase_ledger_id" value="${data.ledgerId || ''}"></td>

                <td>
                    <select class="receipt-input igst_ledger">
                        ${buildLedgerOptions(IGST_LEDGERS, mappedGstLedgerId('igst', existing[key]?.igst ?? existing[`${rate}|${data.ledgerId || ''}`]?.igst, data.ledgerId || '', data.ledgerName || '', data.itemMapping || null))}
                    </select>
                </td>
                <td>
                    <input type="number" value="${isIGST ? data.gst.toFixed(2) : '0.00'}" class="receipt-input igst_amt">
                </td>

                <td>
                    <select class="receipt-input cgst_ledger">
                        ${buildLedgerOptions(CGST_LEDGERS, mappedGstLedgerId('cgst', existing[key]?.cgst ?? existing[`${rate}|${data.ledgerId || ''}`]?.cgst, data.ledgerId || '', data.ledgerName || '', data.itemMapping || null))}
                    </select>
                </td>
                <td>
                    <input type="number" value="${isIGST ? '0.00' : cg.toFixed(2)}" class="receipt-input cgst_amt">
                </td>

                <td>
                    <select class="receipt-input sgst_ledger">
                        ${buildLedgerOptions(SGST_LEDGERS, mappedGstLedgerId('sgst', existing[key]?.sgst ?? existing[`${rate}|${data.ledgerId || ''}`]?.sgst, data.ledgerId || '', data.ledgerName || '', data.itemMapping || null))}
                    </select>
                </td>
                <td>
                    <input type="number" value="${isIGST ? '0.00' : sg.toFixed(2)}" class="receipt-input sgst_amt">
                </td>
            </tr>
            `;

                    if (isIGST) {
                        igst += data.gst;
                    } else {
                        cgst += cg;
                        sgst += sg;
                    }
                });

                $('#customSlotsBody').html(html);
            }

            // =========================
            // FINAL TOTAL
            // =========================
            let grandTotal = taxable + cgst + sgst + igst;

            $('#sum_amount').text(taxable.toFixed(2));
            // $('#sum_grand_total').text(grandTotal.toFixed(2));
            setRoundOffSummary(grandTotal);
            
            $('#manual_igst').val(igst.toFixed(2));
            $('#manual_cgst').val(cgst.toFixed(2));
            $('#manual_sgst').val(sgst.toFixed(2));

            $('#edit_amount').val(taxable.toFixed(2));
            $('#edit_cgst').val(cgst.toFixed(2));
            $('#edit_sgst').val(sgst.toFixed(2));
            $('#edit_igst').val(igst.toFixed(2));
            $('#edit_total_amount').val(grandTotal.toFixed(2));

            $('#foot_amount').text(taxable.toFixed(2));
            $('#foot_total').text(grandTotal.toFixed(2));
        }

        function applyGstLedgerMapping(force = false) {
            const mapping = getSelectedSalesLedgerMapping();

            if (!mapping) {
                return;
            }

            if (mapping.igst_id && (force || !$('#igst_ledger').val())) {
                $('#igst_ledger').val(mapping.igst_id).trigger('change');
            }

            if (mapping.cgst_id && (force || !$('#cgst_ledger').val())) {
                $('#cgst_ledger').val(mapping.cgst_id).trigger('change');
            }

            if (mapping.sgst_id && (force || !$('#sgst_ledger').val())) {
                $('#sgst_ledger').val(mapping.sgst_id).trigger('change');
            }
        }

        function getSelectedSalesLedgerMapping(selectId = '#noitem_sales_ledger') {
            const select = $(selectId);
            const selectedValue = select.val();
            const selectedText = select.find('option:selected').text();

            return findSalesLedgerMapping(selectedValue, selectedText);
        }

        function findSalesLedgerMapping(ledgerValue = '', ledgerText = '') {
            return PURCHASE_GST_MAPPINGS.find(mapping =>
                String(mapping.id) === String(ledgerValue) ||
                normalizeLedgerName(mapping.name) === normalizeLedgerName(ledgerValue) ||
                normalizeLedgerName(mapping.name) === normalizeLedgerName(ledgerText)
            ) || null;
        }

        $(document).on('click', '#addNoItemRow', function() {
            addNoItemRow();
        });

        $(document).on('click', '.removeNoItem', function() {
            $(this).closest('tr').remove();
            recalcTotals();
        });

        $(document).on('input change', '.noitem-ledger, .noitem-gst, .noitem-amount', function() {
            recalcTotals();
        });

        $(document).on('change', '.item_name', function() {
            applyItemGstMapping($(this).find('option:selected').text(), true);
            recalcTotals();
        });

        
        $(document).on('click', '.receipt-del-btn', function() {
            $(this).closest('tr').remove();
            recalcTotals();
        });


        $('#manual_cgst, #manual_sgst, #manual_igst').on('input', function() {
            $(this).data('manual', true);
            recalcTotals();
        });

        $('#gst_calc_mode').change(function() {

            let mode = $(this).val();
            let entryMode = $('input[name="entry_mode"]:checked').val();
            // $('#gst_mode_wrap').toggle(entryMode === 'item');
            $('#gst_mode_wrap').show();

            if (mode === 'standard') {
                $('#standard_items_section').toggle(entryMode === 'item');
                $('#standard_tax_rows').show();

                $('#custom_slots_section').hide();
                $('#custom_tax_rows').hide();

                $('#igst_toggle_wrap').show();
            } else {

                $('#standard_items_section').toggle(entryMode === 'item');
                $('#standard_tax_rows').hide();

                $('#custom_slots_section').show();
                $('#custom_tax_rows').show();

                $('#igst_toggle_wrap').hide(); // optional
            }

            recalcTotals();
        });

        function normalizeLedgerName(name) {
            return String(name || '').trim().toLowerCase();
        }

        function findPurchaseLedgerMapping(ledgerValue = '', ledgerText = '') {
            let normalizedText = normalizeLedgerName(ledgerText);
            return PURCHASE_GST_MAPPINGS.find(mapping =>
                String(mapping.id || '') === String(ledgerValue || '') ||
                normalizeLedgerName(mapping.name) === normalizedText
            ) || null;
        }

        function itemGstMappingObject(item) {
            if (!item) {
                return null;
            }
            return {
                igst_id: item.IGSTLedgerId ? String(item.IGSTLedgerId) : null,
                cgst_id: item.CGSTLedgerId ? String(item.CGSTLedgerId) : null,
                sgst_id: item.SGSTLedgerId ? String(item.SGSTLedgerId) : null
            };
        }

        
       

        function normalizeLedgerName(name) {
            return String(name || '').trim().toLowerCase();
        }

        function findPurchaseLedgerMapping(ledgerValue = '', ledgerText = '') {
            let normalizedText = normalizeLedgerName(ledgerText);
            return PURCHASE_GST_MAPPINGS.find(mapping =>
                String(mapping.id || '') === String(ledgerValue || '') ||
                normalizeLedgerName(mapping.name) === normalizedText
            ) || null;
        }

        function itemGstMappingObject(item) {
            if (!item) {
                return null;
            }
            return {
                igst_id: item.IGSTLedgerId ? String(item.IGSTLedgerId) : null,
                cgst_id: item.CGSTLedgerId ? String(item.CGSTLedgerId) : null,
                sgst_id: item.SGSTLedgerId ? String(item.SGSTLedgerId) : null
            };
        }

        

        function calculateRoundOffAmountForSummary(total) {
            total = parseFloat(total) || 0;
            let roundedTotal;
            switch (ROUND_OFF_SIDE) {
                case 'upper_side':
                    roundedTotal = Math.ceil(total);
                    break;
                case 'lower_side':
                    roundedTotal = Math.floor(total);
                    break;
                default:
                    roundedTotal = Math.round(total);
                    break;
            }
            return Math.round((roundedTotal - total) * 100) / 100;
        }


        function getSummaryBaseTotal() {
            return (parseFloat($('#edit_amount').val()) || 0)
                + (parseFloat($('#edit_cgst').val()) || 0)
                + (parseFloat($('#edit_sgst').val()) || 0)
                + (parseFloat($('#edit_igst').val()) || 0);
        }

        function applyRoundOffSummary(total, roundOff) {
            total = parseFloat(total) || 0;
            // let roundOff = roundOffAmount === null || roundOffAmount === undefined
            //     ? calculateRoundOffAmountForSummary(total)
            //     : (parseFloat(roundOffAmount) || 0);
            roundOff = parseFloat(roundOff) || 0;
            let roundedTotal = total + roundOff;

            //$('#sum_roundoff').text(roundOff.toFixed(2));
            if ($('#sum_roundoff').is('input')) {
                $('#sum_roundoff').val(roundOff.toFixed(2));
            } else {
                $('#sum_roundoff').text(roundOff.toFixed(2));
            }
            $('#edit_roundoff').val(roundOff.toFixed(2));
            $('#sum_grand_total').text(roundedTotal.toFixed(2));
            $('#edit_total_amount').val(roundedTotal.toFixed(2));
            return roundedTotal;
        }

        function setRoundOffSummary(total, roundOffAmount = null) {
            total = parseFloat(total) || 0;
            if (roundOffAmount !== null && roundOffAmount !== undefined) {
                let roundOff = parseFloat(roundOffAmount) || 0;
                return applyRoundOffSummary(total - roundOff, roundOff);
            }

            let roundOff = calculateRoundOffAmountForSummary(total);

            return applyRoundOffSummary(total, roundOff);
        }

        $(document).on('input change', '#sum_roundoff', function() {
            applyRoundOffSummary(getSummaryBaseTotal(), $(this).val());
        });

        function findItemGstMapping(itemName = '') {
            let normalizedItem = normalizeLedgerName(itemName);
            if (!normalizedItem) {
                return null;
            }
            return ITEM_MASTER.find(item =>
                normalizeLedgerName(item.strItemName) === normalizedItem
            ) || null;
        }

        function applyItemGstMapping(itemName = '', force = false) {
            const mapping = itemGstMappingObject(findItemGstMapping(itemName));
            if (!mapping) {
                return;
            }

            if (mapping.igst_id && (force || !$('#igst_ledger').val())) {
                $('#igst_ledger').val(mapping.igst_id).trigger('change');
            }
            if (mapping.cgst_id && (force || !$('#cgst_ledger').val())) {
                $('#cgst_ledger').val(mapping.cgst_id).trigger('change');
            }
            if (mapping.sgst_id && (force || !$('#sgst_ledger').val())) {
                $('#sgst_ledger').val(mapping.sgst_id).trigger('change');
            }
        }

        function mappedGstLedgerId(type, existing = null, ledgerValue = '', ledgerText = '', itemMapping = null) {
            if (existing) {
                return existing;
            }
            if (itemMapping && itemMapping[`${type}_id`]) {
                return itemMapping[`${type}_id`];
            }
            let mapping = findPurchaseLedgerMapping(ledgerValue, ledgerText);
            return mapping ? mapping[`${type}_id`] : null;
        }

        function buildPurchaseLedgerOptions(selected = '') {
            let html = `<option value="">Select Ledger</option>`;
            PURCHASE_LEDGERS.forEach(ledger => {
                let selectedAttr = String(ledger.id || '') === String(selected || '') ||
                    normalizeLedgerName(ledger.name) === normalizeLedgerName(selected)
                    ? 'selected' : '';
                html += `<option value="${ledger.id}" ${selectedAttr}>${ledger.name}</option>`;
            });
            return html;
        }

        function addNoItemRow(data = {}) {
            let row = `
                <tr>
                    <td><select class="receipt-input noitem-ledger">${buildPurchaseLedgerOptions(data.ledger || data.ledger_id || data.ledger_name || '')}</select></td>
                    <td><input type="number" class="receipt-input noitem-gst" value="${data.gst || data.gst_rate || 18}"></td>
                    <td><input type="number" class="receipt-input noitem-amount" value="${data.amount || data.taxable || ''}"></td>
                    <td><button type="button" class="removeNoItem">×</button></td>
                </tr>
            `;
            $('#noItemBody').append(row);
            recalcTotals();
        }

        function collectNoItemRows() {
            let rows = [];
            $('#noItemBody tr').each(function() {
                let ledger = $(this).find('.noitem-ledger').val();
                let amount = parseFloat($(this).find('.noitem-amount').val()) || 0;
                if (ledger && amount > 0) {
                    rows.push({
                        ledger: ledger,
                        gst: $(this).find('.noitem-gst').val(),
                        amount: amount
                    });
                }
            });
            return rows;
        }

        function buildLedgerOptions(list, selected = null) {
            let html = `<option value="">Select Ledger</option>`;
            list.forEach(l => {
                let selectedAttr = String(selected || '') === String(l.id || '') ? 'selected' : '';
                html += `<option value="${l.id}" ${selectedAttr}>${l.name}</option>`;
            });
            return html;
        }

        

        function buildItemOptions(selected = '') {
            let html = '<option value="">Select Item</option>';
            ITEM_MASTER.forEach(item => {
                let name = item.strItemName;
                // 🔥 ESCAPE quotes
                let safeValue = name.replace(/"/g, '&quot;');
                html += `
                    <option value="${safeValue}" ${selected === name ? 'selected' : ''}>
                        ${name}
                    </option>
                `;
            });
            return html;
        }

        $(document).on('change','.item_name',function(){
            let itemId = $(this).val();
            let item = ITEM_MASTER.find(
                x => String(x.strItemName) === String(itemId)
            );
            if(item)
            {
                $(this)
                    .closest('tr')
                    .find('.unit')
                    .val(item.strBaseUnits ?? '');
            }
        });

        function openLedgerModal() {
            document.getElementById('ledgerModal').classList.add('show');
        }

        function closeLedgerModal() {
            document.getElementById('ledgerModal').classList.remove('show');
        }

        // Optional: handle form submit
        $('#ledgerForm').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "<?php echo e(route('purchase.ledger.store')); ?>",
                type: "POST",
                data: formData,
                success: function(response) {
                    showToast(response.message, 'success');
                    let name = $('input[name="Name"]').val();

                    // ✅ Add into EDIT MODAL dropdown
                    let newOption = new Option(name, name, true, true);
                    $('#edit_party').append(newOption).trigger('change');

                    // ✅ ALSO update table dropdowns (VERY IMPORTANT)
                    $('.ledgerSelect').each(function () {
                        $(this).append(new Option(name, name));
                    });

                    // ✅ Refresh Select2 UI
                    $('#edit_party').trigger('change');
                    $('.ledgerSelect').trigger('change');

                    // ✅ Clear form
                    $('#ledgerForm')[0].reset();

                    closeLedgerModal();
                    // location.reload();
                    // OPTIONAL: add new ledger in dropdown
                    // let name = $('input[name="Name"]').val();

                    // $('.ledgerSelect').append(
                    //     `<option value="${name}" selected>${name}</option>`
                    // ).trigger('change');

                },
                error: function(xhr) {
                    showToast('Error saving ledger','error');
                    console.log(xhr.responseText);
                }
            });
        });
        $('input[name="entry_mode"]').on('change', function () {

            let mode = $(this).val();

            if(mode === 'noitem')
            {
                // Force Custom GST
                $('#gst_calc_mode')
                    .val('custom')
                    .trigger('change')
                    .prop('disabled', true);

                $('#igst_toggle_wrap').hide();

                $('#standard_items_section').hide();
                $('#no_item_section').show();

                $('#addItemRow').hide();
                $('#addNoItemRow').show();

                $('#invoice_purcashe_ledger_wrap').hide();
                $('#gst_calc_mode').closest('.receipt-field-row').hide();
            }
            else
            {
                $('#gst_calc_mode')
                    .prop('disabled', false);

                $('#igst_toggle_wrap').show();

                $('#standard_items_section').show();
                $('#no_item_section').hide();

                $('#addItemRow').show();
                $('#addNoItemRow').hide();

                $('#invoice_purcashe_ledger_wrap').show();
                $('#gst_calc_mode').closest('.receipt-field-row').show();
            }

            recalcTotals();
        });

        $(document).on('change','input[name="entry_mode"]',function(){
            let mode = $(this).val();
            if(mode === 'item')
            {
                $('#standard_items_section').show();
                $('#addItemRow').show();
                $('#no_item_section').hide();
                $('#invoice_purcashe_ledger_wrap').show();
            }
            else
            {
                $('#standard_items_section').hide();
                $('#addItemRow').hide();
                $('#no_item_section').show();
                $('#invoice_purcashe_ledger_wrap').hide();
            }
        });
    </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/bulkupload/purchase/index.blade.php ENDPATH**/ ?>