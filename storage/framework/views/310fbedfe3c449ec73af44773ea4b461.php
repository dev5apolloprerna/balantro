
<?php $__env->startSection('content'); ?>
<div data-controller="confirm-delete"
    x-data="{ openUpload:false, openClient: <?php echo e(session('iPartyId') ? 'false' : 'true'); ?> }">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h6 class="font-semibold mb-0 dark:text-white"><?php echo e(__("Sales")); ?></h6>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="col-span-12">
                <div class="card !border-0 rounded-lg overflow-hidden bg-white dark:bg-neutral-800">
                    <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-2 sm:p-3">
                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col sm:flex-row gap-3 w-full">
                                <div class="flex flex-col sm:flex-row justify-between items-center w-full gap-3">
                                    <!-- Left Side Tabs -->
                                    <?php echo $__env->make('admin.bulkupload.bulk-upload-tabs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <!-- Right Side Actions -->
                                    <div class="flex items-center justify-end gap-0 mt-3 sm:mt-0 w-full">

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
                                                <option value=""> Select </option>
                                                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <!-- || (!session('year') && $key == 0)  -->
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
                                            <button id="addEntryBtn"
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
                                        <?php if($upload->status == 'Completed'): ?>
                                        <a href="<?php echo e(route('sales.preview',$upload->id)); ?>"
                                            class="text-green-500 font-semibold hover:underline">
                                            Completed
                                        </a>
                                        <?php elseif($upload->status == 'Processing'): ?>
                                        <a href="<?php echo e(route('sales.preview',$upload->id)); ?>"
                                            class="text-yellow-500 font-semibold hover:underline">
                                            Processing
                                        </a>
                                        <?php elseif($upload->status == 'Pending'): ?>
                                        <a href="<?php echo e(route('sales.preview',$upload->id)); ?>"
                                            class="text-yellow-500 font-semibold hover:underline">
                                            Pending
                                        </a>
                                        <?php else: ?>
                                        <a href="<?php echo e(route('sales.preview',$upload->id)); ?>"
                                            class="text-red-500 font-semibold hover:underline">
                                            Failed
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-right flex justify-end gap-4">
                                        <a href="<?php echo e(route('sales.preview',$upload->id)); ?>">
                                            <i class="fa-regular fa-eye text-gray-500 cursor-pointer"></i>
                                        </a>
                                        <!-- <i class="fa-regular fa-file-lines text-gray-500 cursor-pointer"></i> -->
                                        <div x-data="{ open:false }" class="relative inline-block">

                                            <!-- Button -->
                                            <button onclick="openDropdown(event, <?php echo e($upload->id); ?>)"
                                                class="text-gray-500 hover:text-gray-700 px-2">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>

                                            <!-- Dropdown -->
                                            <div id="globalDropdown"
                                                class="globalDropdown hidden fixed bg-white dark:bg-neutral-800 border rounded-xl shadow-xl w-48 z-[99999]">

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

    <!-- Upload Sales Modal -->
    <div
        x-cloak
        style="display: none;"
        x-show="openUpload"
        x-transition
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
        <div class="bg-white dark:bg-neutral-800 w-[720px] rounded-lg shadow-xl">
            <!-- Header -->
            <div class="flex justify-between items-center border-b px-5 py-3">
                <h2 class="text-lg font-semibold">Upload sales</h2>
                <button @click="openUpload=false" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="<?php echo e(route('sales.upload')); ?>" enctype="multipart/form-data">
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
                                name="sales_file"
                                id="salesFileUpload"
                                class="hidden"
                                accept=".xlsx,.xls"
                                onchange="showFileName(this)">
                            <!-- Styled Button -->
                            <button
                                type="button"
                                onclick="document.getElementById('salesFileUpload').click()"
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
                            <li>Please make sure the uploaded excel file does not contain the dot(.) and dollar($) symbol in the column header and other then sales/purchase field do not add anything above header.</li>
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
        <div class="relative w-[420px] h-full bg-white dark:bg-neutral-800 shadow-xl">
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
            <div class="overflow-y-auto h-[calc(100%-120px)]">
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
                    <div class="receipt-company">Sales Bill</div>
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
                        <div id="invoice_sales_ledger_wrap" class="receipt-field-row">
                            <label>Sales Ledger</label>
                            <select id="noitem_sales_ledger" class="receipt-input ledgerSelect" required>
                                <option value="">Select Ledger</option>
                                <?php $__currentLoopData = $salesLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ledger->name); ?>"><?php echo e($ledger->name); ?></option>
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
                    <!-- <div class="receipt-items-header">
                        <span><i class="fa-solid fa-list text-blue-400 mr-1"></i> Item Details</span>
                        <div style="display:flex;align-items:center;gap:10px;">
                            
                            <div class="receipt-field-row" style="margin:0;">
                                <label style="width:auto;padding-right:4px;">GST Mode</label>
                                <select id="gst_calc_mode" name="gst_calc_mode" class="receipt-input" style="width:180px;">
                                    <option value="standard">Standard (Auto Calculate)</option>
                                    <option value="custom">Custom (Manual Slots)</option>
                                </select>
                            </div>
                            
                            <div id="igst_toggle_wrap" class="receipt-field-row" style="margin:0;">
                                <label style="width:auto;padding-right:4px;white-space:nowrap;">Use IGST</label>
                                <input type="checkbox" id="edit_is_igst" style="width:auto;accent-color:#2563eb;">
                            </div>
                            <button type="button" id="addItemRow" class="receipt-add-btn">
                                <i class="fa-solid fa-plus mr-1"></i> Add Row
                            </button>
                        </div>
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
                        <div class="receipt-field-row" style="margin:0;">
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
                        <button type="button" id="addNoItemRow"
                                class="receipt-add-btn"
                                style="display:none;">
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

                    <!-- <div id="no_item_section" style="display:none; padding:10px;">
                        <div class="receipt-field-row">
                            <label>GST Rate (%)</label>
                            <input type="number" id="noitem_gst_rate" class="receipt-input" placeholder="Enter GST %" value="0">
                        </div>
                        <div class="receipt-field-row">
                            <label>Amount</label>
                            <input type="number" id="noitem_amount" class="receipt-input" placeholder="Enter Amount">
                        </div>
                    </div> -->
                    <!-- <div id="no_item_section" style="display:none; padding:10px;">
                        <div style="display:flex; gap:20px; align-items:center; max-width:600px;">
                            <div style="flex:1;">
                                <label style="font-size:11px;">GST Rate (%)</label>
                                <input type="number" id="noitem_gst_rate" class="receipt-input" value="0">
                            </div>
                            <div style="flex:1;">
                                <label style="font-size:11px;">Amount</label>
                                <input type="number" id="noitem_amount" class="receipt-input" placeholder="Enter Amount">
                            </div>
                        </div>
                    </div> -->
                    <div id="no_item_section" style="display:none;">
                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th>Sales Ledger</th>
                                    <th>GST %</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="noItemBody">
                            </tbody>
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
                                <select id="cgst_ledger" name="cgst_ledger" class="receipt-input" style="width:140px;">
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
                                <select id="sgst_ledger" name="sgst_ledger" class="receipt-input" style="width:140px;">
                                    <option value="">Select Ledger</option>
                                    <?php $__currentLoopData = $sGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->id); ?>"><?php echo e($ledger->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <!-- <span class="tax-value" id="sum_sgst">0.00</span> -->
                                <input type="number" id="manual_sgst" name="igst_ledger" class="receipt-input" style="width:80px;" placeholder="Amt">
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
                <div class="receipt-footer-note">This is a computer-generated Sales record.</div>
                <div class="receipt-footer-actions">
                    <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                    <button type="button" id="saveRow" class="submit-btn">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="ledgerModal" class="modal">
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

        /* .modal-content {
            width: 95%;
            max-width: 1100px;
            max-height: 95vh;
            background: #fff;
            color: #111827;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        } */

        /* .dark .modal-content {
            background: #1e293b;
            color: #e2e8f0;
        } */

        /* .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
        } */

        /* .dark .modal-header {
            border-color: #334155;
        } */

        /* .modal-header h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        } */

        /* .modal-body {
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
        } */

        /* .modal-footer {
            padding: 12px 16px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .dark .modal-header,
        .dark .modal-footer {
            border-color: #334155;
        } */

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
            /* overflow: hidden; */
            overflow: visible !important;
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
            /* overflow-y: auto; */
            overflow-y: visible !important;
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
            position: relative;
            z-index: 999999 !important;
            pointer-events: auto !important;
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

        /* 🔥 FORCE FULL LIGHT MODE FOR SALES MODAL */

        #editModal,
        #editModal * {
            color-scheme: light;
        }

        /* Background fix */
        #editModal .receipt-wrapper {
            background: #ffffff !important;
            color: #111827 !important;
            overflow: visible !important;
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

        /* #editModal {
            background: rgba(0, 0, 0, 0.5);
           
        } */

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
            z-index: 99999;
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

    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
    <script>
        let IGST_LEDGERS = <?php echo json_encode($iGstLedgers, 15, 512) ?>;
        let CGST_LEDGERS = <?php echo json_encode($cGstLedgers, 15, 512) ?>;
        let SGST_LEDGERS = <?php echo json_encode($sGstLedgers, 15, 512) ?>;
        const SALES_GST_MAPPINGS = <?php echo json_encode($salesGstMappings ?? [], 15, 512) ?>;
        const ITEM_MASTER = <?php echo json_encode($stockItems, 15, 512) ?>;
    </script>
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
                    window.location.href = "/samples/Sales-with-item-sample-file.xlsx";
                }
                if (type == 'without-item') {
                    window.location.href = "/samples/Sales-without-item-sample-file.xlsx";
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

        let currentId = null;

        function normalizeLedgerName(name) {
            return String(name || '').replace(/["']/g, '').trim().toLowerCase();
        }

        function findSalesLedgerMapping(ledgerValue = '', ledgerText = '') {
            return SALES_GST_MAPPINGS.find(mapping =>
                String(mapping.id) === String(ledgerValue) ||
                normalizeLedgerName(mapping.name) === normalizeLedgerName(ledgerValue) ||
                normalizeLedgerName(mapping.name) === normalizeLedgerName(ledgerText)
            ) || null;
        }

        function getSelectedSalesLedgerMapping(selectId = '#noitem_sales_ledger') {
            const select = $(selectId);
            const selectedValue = select.val();
            const selectedText = select.find('option:selected').text();

            return findSalesLedgerMapping(selectedValue, selectedText);
        }

        function findGstMappingForLedgerOrItem(ledgerValue = '', ledgerText = '') {
            const salesMapping = findSalesLedgerMapping(ledgerValue, ledgerText);
            if (salesMapping) {
                return salesMapping;
            }

            const item = findItemGstMapping(ledgerValue) || findItemGstMapping(ledgerText);
            if (!item) {
                return null;
            }

            return {
                igst_id: item.IGSTLedgerId || null,
                cgst_id: item.CGSTLedgerId || null,
                sgst_id: item.SGSTLedgerId || null,
            };
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

        function mappedGstLedgerId(type, existing = null, ledgerValue = '', ledgerText = '') {
            if (existing) {
                return existing;
            }

            const mapping = ledgerValue || ledgerText
                ? findGstMappingForLedgerOrItem(ledgerValue, ledgerText)
                : getSelectedSalesLedgerMapping();

            return mapping ? mapping[`${type}_id`] : null;
        }

        const ROUND_OFF_SIDE = <?php echo json_encode($roundOffSide ?? 'normal', 15, 512) ?>;
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
            let normalizedItem = String(itemName || '').trim().toLowerCase();
            if (!normalizedItem) return null;
            return ITEM_MASTER.find(item =>
                String(item.strItemName || '').trim().toLowerCase() === normalizedItem
            ) || null;
        }

        function applyItemGstMapping(itemName = '', force = false) {
            const item = findItemGstMapping(itemName);
            if (!item) return;

            let gstMode = $('#gst_calc_mode').val();

            if (gstMode === 'standard') {
                if (item.IGSTLedgerId && (force || !$('#igst_ledger').val())) {
                    $('#igst_ledger').val(item.IGSTLedgerId);
                }

                if (item.CGSTLedgerId && (force || !$('#cgst_ledger').val())) {
                    $('#cgst_ledger').val(item.CGSTLedgerId);
                }

                if (item.SGSTLedgerId && (force || !$('#sgst_ledger').val())) {
                    $('#sgst_ledger').val(item.SGSTLedgerId);
                }

                return;
            }

            let normalizedItem = String(itemName || '').trim().toLowerCase();
            if (!normalizedItem) return;

            $('#customSlotsBody tr').each(function() {
                let slotItemName = String($(this).find('.slot_item_name').val() || $(this).data('item-name') || '').trim().toLowerCase();
                if (!slotItemName || slotItemName !== normalizedItem) {
                    return;
                }

                let igstSelect = $(this).find('.igst_ledger');
                let cgstSelect = $(this).find('.cgst_ledger');
                let sgstSelect = $(this).find('.sgst_ledger');

                if (item.IGSTLedgerId && (force || !igstSelect.val())) {
                    igstSelect.val(item.IGSTLedgerId);
                }

                if (item.CGSTLedgerId && (force || !cgstSelect.val())) {
                    cgstSelect.val(item.CGSTLedgerId);
                }

                if (item.SGSTLedgerId && (force || !sgstSelect.val())) {
                    sgstSelect.val(item.SGSTLedgerId);
                }
            });
        }

        $(document).on('change', '#noitem_sales_ledger', function() {
            recalcTotals();
            applyGstLedgerMapping(true);
        });

        function openDropdown(e, id) {
            e.stopPropagation();
            currentId = id;

            //let dropdown = document.getElementById('globalDropdown');
            let dropdown = document.querySelector('.globalDropdown');
            dropdown.classList.remove('hidden');

            let rect = e.target.getBoundingClientRect();
            dropdown.style.top = rect.bottom + "px";
            dropdown.style.left = (rect.left - 150) + "px";
        }

        // document.addEventListener('click', function() {
        //     document.getElementById('globalDropdown').classList.add('hidden');
        // });
        document.addEventListener('click', function(e) {

            // ❌ if click inside custom GST → do nothing
            if (e.target.closest('.custom-slots-table')) {
                return;
            }

            document.querySelectorAll('.globalDropdown').forEach(function(dd) {
                dd.classList.add('hidden');
            });
        });

        // $(document).on('input', '.qty, .rate, .gst', function() {
        //     recalcTotals();
        // });

        function handleStatus(status) {
            changeStatus(currentId, status);
        }

        function changeStatus(id, status) {
            $.post("<?php echo e(route('sales.change.status')); ?>", {
                _token: "<?php echo e(csrf_token()); ?>",
                id: id,
                status: status
            }, function(res) {
                alert(res.message);
                location.reload();
            });
        }

        function handleDelete() {
            deleteUpload(currentId);
        }

        function deleteUpload(id) {

            if (!confirm('Delete full upload?')) return;

            $.post("<?php echo e(route('sales.bulk.delete')); ?>", {
                _token: "<?php echo e(csrf_token()); ?>",
                ids: [id] // 🔥 important (array)
            }, function(res) {
                alert(res.message);
                location.reload();
            });

        }

        function openCreateModal() {
            // 🔥 Clear ID → means NEW Add
            $('#edit_id').val('');
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
            // Reset all fields
            $('#edit_invoice').val('');
            $('#edit_date').val(new Date().toISOString().split('T')[0]);
            $('#edit_gst').val('');

            $('#edit_party').val('').trigger('change');
            $('#edit_place').val('');
            $('#edit_voucher_type').val('Sales');

            $('#edit_address').val('');
            $('#edit_pincode').val('');
            $('#edit_city').val('');
            $('#edit_remarks').val('');

            $('#edit_is_igst').prop('checked', false);

            // Reset GST values
            $('#edit_amount').val(0);
            $('#edit_cgst').val(0);
            $('#edit_sgst').val(0);
            $('#edit_igst').val(0);
            $('#edit_total_amount').val(0);

            // UI reset
            $('#sum_amount').html('0.00');
            $('#manual_cgst').val() || $('#sum_cgst').html('0.00');
            $('#manual_sgst').val() || $('#sum_sgst').html('0.00');
            $('#manual_igst').val() || $('#sum_igst').html('0.00');
            $('#sum_roundoff').val('0.00');
            $('#edit_roundoff').val(0);
            $('#sum_grand_total').html('0.00');

            // Clear items
            $('#editItemsBody').empty();

            // Default mode
            $('#standard_items_section').show();
            $('#gst_calc_mode').val('standard').trigger('change');
            $('#no_item_section').hide();
            $('input[name="entry_mode"][value="item"]').prop('checked', true);

            $('#noitem_amount').val('');
            //$('#noitem_sales_ledger').val('');
            $('#noitem_sales_ledger').val('').trigger('change');
            $('#igst_ledger, #cgst_ledger, #sgst_ledger').val('').trigger('change.select2');


            // Enable fields
            $('#editModal input, #editModal select, #editModal textarea')
                .prop('disabled', false)
                .css('pointer-events', 'auto');

            $('#updateRow').show();
            $('#addItemRow').show();
            $('.receipt-del-btn').show();

            // 🔥 Open SAME modal
            openEditModal();
            recalcTotals();
        }

        $('#addEntryBtn').click(function() {
            openCreateModal();
        });

        function openEditModal() {
            $('#editModal').addClass('show');
        }

        function closeEditModal() {
            $('#editModal').removeClass('show');
        }

        $('#saveRow').click(function() {
            let btn = $(this);

            // 🚫 Prevent multiple click
            if (btn.prop('disabled')) return;

            // 🔒 Disable button
            btn.prop('disabled', true);

            // 💡 Show loader
            btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            let party = $('#edit_party').val();
            let invoice = $('#edit_invoice').val();

            if (!party) {
                alert('Please select Party');
                btn.prop('disabled', false);
                btn.html('Save');
                return;
            }

            if (!invoice) {
                alert('Please enter Invoice Number');
                btn.prop('disabled', false);
                btn.html('Save');
                return;
            }

            let mode = $('input[name="entry_mode"]:checked').val();
            let amount = 0;

            // ✅ HANDLE MODE
            if (mode === 'noitem') {

                // amount = $('#noitem_amount').val();
                amount = $('#edit_amount').val(); // calculated value
                if (!amount) {
                    alert('Please enter amount');
                    btn.prop('disabled', false);
                    btn.html('Save');
                    return;
                }
                let ledgerRows = [];
                $('#noItemBody tr').each(function(){
                    ledgerRows.push({
                        ledger : $(this).find('.noitem-ledger').val(),
                        gst    : $(this).find('.noitem-gst').val(),
                        amount : $(this).find('.noitem-amount').val()
                    });
                });

                let customGstArray = [];

                if(mode === 'noitem')
                {
                    customGstArray = ledgerRows.map(row => ({
                        sales_ledger : row.ledger,
                        gst_rate     : parseFloat(row.gst) || 0,
                        taxable_amt  : parseFloat(row.amount) || 0
                    }));
                }

                if(ledgerRows.length === 0)
                {
                    alert('Please add at least one ledger row');
                    return;
                }
                // if (!$('#noitem_sales_ledger').val()) {
                //     alert('Please select Sales Ledger');
                //     btn.prop('disabled', false);
                //     btn.html('Save');
                //     return;
                // }

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
                    item: row.find('.item_name option:selected').text(),
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
                    taxable: row.find('.slot_taxable').val() || parseFloat(row.find('td:eq(1)').text()) || 0,
                    sales_ledger_id: row.find('.slot_sales_ledger_id').val(),

                    // ✅ match backend naming
                    igst_ledger_id: row.find('.igst_ledger').val(),
                    igst_amount: row.find('.igst_amt').val(),

                    cgst_ledger_id: row.find('.cgst_ledger').val(),
                    cgst_amount: row.find('.cgst_amt').val(),

                    sgst_ledger_id: row.find('.sgst_ledger').val(),
                    sgst_amount: row.find('.sgst_amt').val()
                });

            });

            let ledgerRows = [];

            $('#noItemBody tr').each(function(){
                ledgerRows.push({
                    ledger : $(this).find('.noitem-ledger').val(),
                    gst    : $(this).find('.noitem-gst').val(),
                    amount : $(this).find('.noitem-amount').val()
                });
            });
            if($('input[name="entry_mode"]:checked').val() === 'noitem')
            {
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

                cgst_ledger: $('#cgst_ledger').val(),
                sgst_ledger: $('#sgst_ledger').val(),
                igst_ledger: $('#igst_ledger').val(),

                entry_mode: mode,
                //sales_ledger: $('#noitem_sales_ledger').val(),
                sales_ledger: $('#noitem_sales_ledger option:selected').text(),
                sales_ledger_name: $('#noitem_sales_ledger option:selected').text(),
                sales_ledger_id: $('#noitem_sales_ledger').val(),

                items: items,
                custom_slots: custom_gst,
                gst_mode: $('#gst_calc_mode').val(),
                gst_rate: $('#noitem_gst_rate').val(),
                noitem_rows: ledgerRows,
            };

            // $.post("<?php echo e(route('sales.manual.create')); ?>", data, function(res) {
            //     if (res.status) {
            //         alert('Inserted successfully');
            //         closeEditModal();
            //         location.reload();
            //     } else {
            //         alert('Save failed');
            //     }
            // });

            $.ajax({
                url: "<?php echo e(route('sales.manual.create')); ?>",
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

        });

        function showToast(message, type = 'success') {

            let bg = type === 'success' ? '#16a34a' : '#dc2626';

            let toast = document.createElement('div');

            toast.innerText = message;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = bg;
            toast.style.color = '#fff';
            toast.style.padding = '10px 16px';
            toast.style.borderRadius = '6px';
            toast.style.fontSize = '13px';
            toast.style.zIndex = '99999';
            toast.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        $(document).on('change', 'input[name="entry_mode"]', function() {

            let mode = $(this).val();

            if (mode === 'item') {
                $('#standard_items_section').show();
                $('#no_item_section').hide();
                $('#addItemRow').show();
                $('#addNoItemRow').hide();
            } else {
                $('#standard_items_section').hide();
                $('#no_item_section').show();
                $('#addItemRow').hide();
                $('#addNoItemRow').show();
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
                    <td><select class="item_name itemSelect">
                        ${buildItemOptions(data.item_name || '')}
                    </select></td>
                    <td><input type="text" class="hsn" value="${data.hsn || ''}"></td>
                    <td><input type="number" class="gst"  value="${data.gst || 0}"></td>
                    <td><input type="number" class="qty" value="${data.qty || 1}"></td>
                    <td><input type="text" class="unit receipt-input" value="${data.unit || 'NOS'}"></td>
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
            let mode = $('#gst_calc_mode').val();

            if (mode === 'standard') {
                recalcTotals();
            } else {
                // ✅ Only update amounts, not rebuild
                recalcTotals();
            }
        });

        $(document).on('change', '.item_name', function() {
            recalcTotals();
            applyItemGstMapping($(this).val(), true);
        });


        function recalcTotals() {

            let modeEntry = $('input[name="entry_mode"]:checked').val();
            let gstMode = $('#gst_calc_mode').val();
            let isIGST = $('#edit_is_igst').is(':checked');

            applyGstLedgerMapping(false);

            let taxable = 0;
            let totalIGST = 0;
            let totalCGST = 0;
            let totalSGST = 0;

            let rateMap = {};

            // =========================
            // ✅ WITH ITEM MODE
            // =========================
            if (modeEntry === 'item') {

                $('#editItemsBody tr').each(function() {

                    let row = $(this);

                    let qty = parseFloat(row.find('.qty').val()) || 0;
                    let rate = parseFloat(row.find('.rate').val()) || 0;
                    let gst = parseFloat(row.find('.gst').val()) || 0;

                    let amount = qty * rate;
                    row.find('.amount').val(amount.toFixed(2));

                    taxable += amount;

                    let gstAmt = (amount * gst) / 100;

                    if (!rateMap[gst]) {
                        rateMap[gst] = {
                            taxable: 0,
                            gst: 0,
                            rate: gst
                        };
                    }

                    rateMap[gst].taxable += amount;
                    rateMap[gst].gst += gstAmt;

                    if (isIGST) {
                        totalIGST += gstAmt;
                    } else {
                        totalCGST += gstAmt / 2;
                        totalSGST += gstAmt / 2;
                    }
                });
            }

            // =========================
            // ✅ WITHOUT ITEM MODE
            // =========================
            // if (modeEntry === 'noitem') {

            //     let amount = parseFloat($('#noitem_amount').val()) || 0;
            //     let gstRate = parseFloat($('#noitem_gst_rate').val()) || 0;

            //     let gstAmt = (amount * gstRate) / 100;

            //     taxable = amount;

            //     rateMap = {}; // reset
            //     rateMap[gstRate] = {
            //         taxable: amount,
            //         gst: gstAmt
            //     };

            //     if (isIGST) {
            //         totalIGST = gstAmt;
            //     } else {
            //         totalCGST = gstAmt / 2;
            //         totalSGST = gstAmt / 2;
            //     }
            // }

            if (modeEntry === 'noitem') {

                taxable = 0;
                totalIGST = 0;
                totalCGST = 0;
                totalSGST = 0;

                rateMap = {};

                $('#noItemBody tr').each(function (index) {

                    let gstRate = parseFloat($(this).find('.noitem-gst').val()) || 0;
                    let amount  = parseFloat($(this).find('.noitem-amount').val()) || 0;
                    let ledgerSelect = $(this).find('.noitem-ledger');
                    let ledgerId = ledgerSelect.val() || '';
                    let ledgerName = ledgerSelect.find('option:selected').text() || '';

                    let gstAmt = (amount * gstRate) / 100;

                    taxable += amount;

                    let key = `row:${index}|${gstRate}|${ledgerId}`;

                    if (!rateMap[key]) {
                        rateMap[key] = {
                            taxable: 0,
                            gst: 0,
                            rate: gstRate,
                            ledgerId: ledgerId,
                            ledgerName: ledgerName,
                            slotKey: key
                        };
                    }

                    rateMap[key].taxable += amount;
                    rateMap[key].gst += gstAmt;

                    if (isIGST) {
                        totalIGST += gstAmt;
                    } else {
                        totalCGST += gstAmt / 2;
                        totalSGST += gstAmt / 2;
                    }
                });
            }

            let grandTotal = taxable + totalIGST + totalCGST + totalSGST;

            // =========================
            // UI UPDATE
            // =========================
            $('#sum_amount').text(taxable.toFixed(2));
            //$('#sum_grand_total').text(grandTotal.toFixed(2));
            setRoundOffSummary(grandTotal);

            $('#manual_igst').val(totalIGST.toFixed(2));
            $('#manual_cgst').val(totalCGST.toFixed(2));
            $('#manual_sgst').val(totalSGST.toFixed(2));

            $('#edit_amount').val(taxable.toFixed(2));
            $('#edit_igst').val(totalIGST.toFixed(2));
            $('#edit_cgst').val(totalCGST.toFixed(2));
            $('#edit_sgst').val(totalSGST.toFixed(2));
            $('#edit_total_amount').val(grandTotal.toFixed(2));

            // =========================
            // 🔥 CUSTOM GST MODE FIX
            // =========================
            if (gstMode === 'custom') {

                $('#custom_slots_section').show();
                $('#standard_tax_rows').hide();

                // ✅ preserve selected ledger
                let existing = {};

                $('#customSlotsBody tr').each(function() {

                    let rate = $(this).find('.gst_rate').text().replace('%', '');
                    let salesLedgerId = $(this).find('.slot_sales_ledger_id').val() || '';
                    let key = $(this).data('slot-key') || `${rate}|${salesLedgerId}`;

                    existing[key] = {
                        igst: $(this).find('.igst_ledger').val(),
                        cgst: $(this).find('.cgst_ledger').val(),
                        sgst: $(this).find('.sgst_ledger').val()
                    };
                });

                let html = '';

                Object.keys(rateMap).forEach(key => {

                    let slot = rateMap[key];
                    let taxableAmt = slot.taxable;
                    let gstAmt = slot.gst;
                    let rate = slot.rate != null ? slot.rate : (parseFloat(String(key).split('|')[1] || key) || 0);

                    let igstVal = isIGST ? gstAmt : 0;
                    let cgstVal = isIGST ? 0 : gstAmt / 2;
                    let sgstVal = isIGST ? 0 : gstAmt / 2;

                    html += `
            <tr data-slot-key="${slot.slotKey || key}" data-sales-ledger-id="${slot.ledgerId || ''}" data-item-name="${slot.itemName || ''}">
                <td class="gst_rate">${rate}%</td>
                <td>
                    ${taxableAmt.toFixed(2)}
                    <input type="hidden" class="slot_taxable" value="${taxableAmt.toFixed(2)}">
                    <input type="hidden" class="slot_sales_ledger_id" value="${slot.ledgerId || ''}">
                    <input type="hidden" class="slot_item_name" value="${slot.itemName || ''}">
                </td>

                <td>
                    <select class="igst_ledger">
                        ${buildLedgerOptions(IGST_LEDGERS, mappedGstLedgerId('igst', existing[key]?.igst ?? existing[`${rate}|${slot.ledgerId || ''}`]?.igst ?? existing[rate]?.igst, slot.ledgerId || '', slot.ledgerName || ''))}
                    </select>
                </td>
                <td><input type="number" class="igst_amt" value="${igstVal.toFixed(2)}"></td>

                <td>
                    <select class="cgst_ledger">
                        ${buildLedgerOptions(CGST_LEDGERS, mappedGstLedgerId('cgst', existing[key]?.cgst ?? existing[`${rate}|${slot.ledgerId || ''}`]?.cgst ?? existing[rate]?.cgst, slot.ledgerId || '', slot.ledgerName || ''))}
                    </select>
                </td>
                <td><input type="number" class="cgst_amt" value="${cgstVal.toFixed(2)}"></td>

                <td>
                    <select class="sgst_ledger">
                        ${buildLedgerOptions(SGST_LEDGERS, mappedGstLedgerId('sgst', existing[key]?.sgst ?? existing[`${rate}|${slot.ledgerId || ''}`]?.sgst ?? existing[rate]?.sgst, slot.ledgerId || '', slot.ledgerName || ''))}
                    </select>
                </td>
                <td><input type="number" class="sgst_amt" value="${sgstVal.toFixed(2)}"></td>
            </tr>
            `;
                });

                $('#customSlotsBody').html(html);

            } else {
                $('#custom_slots_section').hide();
                $('#standard_tax_rows').show();
            }
        }

        $(document).on('click', '.receipt-del-btn', function() {
            $(this).closest('tr').remove();
            recalcTotals();
        });

        $(document).on('input', '#noitem_amount, #noitem_gst_rate', function() {
            recalcTotals();
        });
        $(document).on('change', '.igst_ledger, .cgst_ledger, .sgst_ledger', function(e) {
            e.stopPropagation();
        });

        $(document).on('mousedown', '.custom-slots-table select', function(e) {
            e.stopPropagation();
        });

        $(document).on('click', '.custom-slots-table select', function(e) {
            e.stopPropagation();
        });

        function detectGSTType(row) {

            let igstLedger = row.find('.igst_ledger').val();
            let cgstLedger = row.find('.cgst_ledger').val();
            let sgstLedger = row.find('.sgst_ledger').val();

            if (igstLedger) return 'IGST';
            if (cgstLedger || sgstLedger) return 'CGST_SGST';

            return 'NONE';
        }

        $(document).on('change', '.igst_ledger, .cgst_ledger, .sgst_ledger', function() {

            let row = $(this).closest('tr');

            let amount = parseFloat(row.find('td:eq(1)').text()) || 0;
            let rate = parseFloat(row.find('.gst_rate').text()) || 0;

            let gstAmt = (amount * rate) / 100;

            if ($(this).hasClass('igst_ledger')) {

                row.find('.igst_amt').val(gstAmt.toFixed(2));
                row.find('.cgst_amt').val(0);
                row.find('.sgst_amt').val(0);

            } else {

                row.find('.igst_amt').val(0);
                row.find('.cgst_amt').val((gstAmt / 2).toFixed(2));
                row.find('.sgst_amt').val((gstAmt / 2).toFixed(2));
            }

            recalcTotals();
        });

        $('#gst_calc_mode').change(function() {
            applyGstLedgerMapping(false);
            recalcTotals(); // 🔥 recalc everything
        });

        $('#edit_is_igst').change(function() {
            recalcTotals();
        });

        $('#manual_cgst, #manual_sgst, #manual_igst').on('input', function() {
            $(this).data('manual', true);
            recalcTotals();
        });

        $('#gst_calc_mode').change(function() {

            let mode = $(this).val();

            if (mode === 'standard') {
                $('#standard_items_section').show();
                $('#standard_tax_rows').show();

                $('#custom_slots_section').hide();
                $('#custom_tax_rows').hide();

                $('#igst_toggle_wrap').show();
                let modeEntry = $('input[name="entry_mode"]:checked').val();

                if (modeEntry === 'item') {
                    $('#standard_items_section').show();
                    $('#addItemRow').show();
                } else {
                    $('#standard_items_section').hide();
                    $('#addItemRow').hide();
                }
            } else {

                // $('#standard_items_section').show(); // items still needed
                let modeEntry = $('input[name="entry_mode"]:checked').val();

                if (modeEntry === 'item') {
                    $('#standard_items_section').show();
                    $('#addItemRow').show();
                } else {
                    $('#standard_items_section').hide();
                    $('#addItemRow').hide();
                }

                $('#standard_tax_rows').hide();

                $('#custom_slots_section').show();
                $('#custom_tax_rows').show();

                $('#igst_toggle_wrap').hide(); // optional
            }

            recalcTotals();
        });

        function buildLedgerOptions(ledgers, selected = null) {

            let html = '<option value="">Select Ledger</option>';

            ledgers.forEach(l => {
                let sel = selected == l.id ? 'selected' : '';
                html += `<option value="${l.id}" ${sel}>${l.name}</option>`;
            });

            return html;
        }

        $('#noitem_amount, #noitem_gst_rate').on('input', function() {
            recalcTotals();
        });

        function buildItemOptions(selected = '') {

            let html = `<option value="">Select Item</option>`;

            ITEM_MASTER.forEach(item => {

                let name = item.strItemName;

                // 🔥 FIX: escape quotes
                let safeValue = name.replace(/"/g, '&quot;');

                let isSelected =
                    name.trim().toLowerCase() === String(selected).trim().toLowerCase()
                        ? 'selected'
                        : '';

                html += `<option value="${safeValue}" ${isSelected}>${name}</option>`;
            });

            return html;
        }

        function openLedgerModal() {
            let modal = document.getElementById('ledgerModal');
            if (!modal) {
                console.error('ledgerModal not found');
                return;
            }
            modal.classList.add('show');
        }

        function closeLedgerModal() {
            let modal = document.getElementById('ledgerModal');
            if (!modal) {
                return;
            }
            modal.classList.remove('show');
        }

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

                $('#invoice_sales_ledger_wrap').hide();
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

                $('#invoice_sales_ledger_wrap').show();
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
                $('#invoice_sales_ledger_wrap').show();
            }
            else
            {
                $('#standard_items_section').hide();
                $('#addItemRow').hide();
                $('#no_item_section').show();
                $('#invoice_sales_ledger_wrap').hide();
            }
        });

        function calculateNoItemTotal()
        {
            let taxable = 0;
            $('.noitem-amount').each(function(){
                taxable += parseFloat($(this).val()) || 0;
            });
            $('#sum_amount').text(taxable.toFixed(2));
        }

        $('#addNoItemRow').click(function () {
            let row = `
                <tr>
                    <td>
                        <select class="receipt-input noitem-ledger">
                            <option value="">Select Ledger</option>
                            <?php $__currentLoopData = $salesLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ledger->id); ?>">
                                    <?php echo e($ledger->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td>
                        <input type="number"
                            class="receipt-input noitem-gst"
                            value="18">
                    </td>
                    <td>
                        <input type="number"
                            class="receipt-input noitem-amount">
                    </td>
                    <td>
                        <button type="button"
                                class="removeNoItem">
                            ✕
                        </button>
                    </td>
                </tr>
            `;
            $('#noItemBody').append(row);
            recalcNoItemTotals();
        });
        $(document).on('click','.removeNoItem',function(){
            $(this).closest('tr').remove();
            calculateNoItemTotal();
            recalcTotals();
        });

        $(document).on('input','.noitem-amount',function(){
            calculateNoItemTotal();
        });

        function calculateNoItemTotal()
        {
            let total = 0;
            $('.noitem-amount').each(function(){
                total += parseFloat($(this).val()) || 0;
            });

            $('#sum_amount').html(total.toFixed(2));
            $('#edit_amount').val(total.toFixed(2));
            recalcTotals();
        }

        function recalcNoItemTotals()
        {
            let taxableAmount = 0;
            let totalCGST = 0;
            let totalSGST = 0;
            let totalIGST = 0;

            let isIGST = $('#edit_is_igst').is(':checked');
            let gstMode = $('#gst_calc_mode').val();

            $('#noItemBody tr').each(function () {

                let amount = parseFloat($(this).find('.noitem-amount').val()) || 0;
                let gstRate = parseFloat($(this).find('.noitem-gst').val()) || 0;

                taxableAmount += amount;

                let gstAmt = (amount * gstRate) / 100;

                if (isIGST) {
                    totalIGST += gstAmt;
                } else {
                    totalCGST += gstAmt / 2;
                    totalSGST += gstAmt / 2;
                }
            });

            $('#edit_amount').val(taxableAmount);

            $('#edit_cgst').val(totalCGST.toFixed(2));
            $('#edit_sgst').val(totalSGST.toFixed(2));
            $('#edit_igst').val(totalIGST.toFixed(2));

            $('#sum_amount').html(taxableAmount.toFixed(2));

            $('#manual_cgst').val(totalCGST.toFixed(2));
            $('#manual_sgst').val(totalSGST.toFixed(2));
            $('#manual_igst').val(totalIGST.toFixed(2));

            let grandTotal =
                taxableAmount +
                totalCGST +
                totalSGST +
                totalIGST;

           // $('#sum_grand_total').html(grandTotal.toFixed(2));
           setRoundOffSummary(grandTotal);
            $('#edit_total_amount').val(grandTotal.toFixed(2));
        }

        $(document).on('input change','.noitem-ledger, .noitem-amount, .noitem-gst, #edit_is_igst, #gst_calc_mode', function () {
                recalcNoItemTotals();
            }
        );

        $(document).on('input change', '.noitem-ledger, .noitem-gst, .noitem-amount', function () {
            recalcTotals();
        });

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

        // Optional: handle form submit
    $('#ledgerForm').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "<?php echo e(route('sales.ledger.store')); ?>",
            type: "POST",
            data: formData,
            success: function(response) {
                closeLedgerModal();
                let name = $('input[name="Name"]').val();

                // ✅ Add into EDIT MODAL dropdown
                let newOption = new Option(name, name, true, true);
                $('#edit_party').append(newOption).trigger('change');

                // ✅ ALSO update table dropdowns (VERY IMPORTANT)
                $('.ledgerSelect').each(function() {
                    $(this).append(new Option(name, name));
                });

                // ✅ Refresh Select2 UI
                $('#edit_party').trigger('change');
                $('.ledgerSelect').trigger('change');

                // ✅ Clear form
                $('#ledgerForm')[0].reset();


                // location.reload();
                // OPTIONAL: add new ledger in dropdown
                // let name = $('input[name="Name"]').val();

                // $('.ledgerSelect').append(
                //     `<option value="${name}" selected>${name}</option>`
                // ).trigger('change');
            },
            error: function(xhr) {
                alert('Error saving ledger');
                console.log(xhr.responseText);
            }
        });
    });
    </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/bulkupload/sales/index.blade.php ENDPATH**/ ?>