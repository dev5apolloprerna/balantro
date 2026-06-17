
<?php $__env->startSection('content'); ?>
<style>
    /* Read-only input styling for view mode */
.receipt-input[readonly] {
    background-color: #f3f4f6 !important;
    color: #374151 !important;
    cursor: default !important;
    opacity: 0.8;
}

.dark .receipt-input[readonly] {
    background-color: #1f2937 !important;
    color: #9ca3af !important;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="container mx-auto">
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow border border-gray-200 dark:border-neutral-700">
        <!-- HEADER -->
        <div class="flex justify-between items-center px-5 py-3 border-b border-neutral-700">
            <!-- <div class="flex items-center gap-3">
                <h2 class="text-gray-900 dark:text-white text-lg font-semibold">
                    Purchase Transactions
                </h2>
                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                    <?php echo e($rows->count()); ?>

                </span>
            </div> -->
            <div class="flex items-center gap-3">
                <button type="button"
                    onclick="window.history.back()"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 text-lg">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>

                <h2 class="text-gray-900 dark:text-white text-lg font-semibold">
                    Purchase Transactions
                </h2>

                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                    <?php echo e($rows->count()); ?>

                </span>
            </div>

            <div class="flex gap-2">
                <?php if(session('client_name')): ?>
                <div class="text-sm text-green-600 font-semibold">
                    <?php echo e(session('client_name')); ?>

                </div>
                <?php endif; ?>
                <button class="border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-gray-300 px-3 py-1 rounded text-sm">
                    More Info
                </button>
                <button onclick="openLedgerModal()"
                    class="border border-blue-500 text-blue-400 px-3 py-1 rounded text-sm">
                    + Create Ledger
                </button>
                <button type="button"
                    id="saveBtn"
                    class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                    Sumbit
                </button>
            </div>
        </div>
        <!-- FILTERS -->
        <div class="flex gap-10 px-5 py-3 text-sm border-b border-neutral-700">
            <div>
                <div class="flex gap-4 items-end">
                    <div>
                        <label class="text-gray-700 dark:text-gray-300 text-sm block">
                            Update Bulk Records
                        </label>
                        <select id="bulkColumn"
                            class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-800 dark:text-white rounded px-3 py-1 mt-1">
                            <option value="">Select Column</option>
                            <option value="party">Party Name</option>
                            <!-- <option value="ledger">Ledger</option> -->
                            <option value="place">Place Of Supply</option>
                            <option value="voucher">Voucher Type</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-700 dark:text-gray-300 text-sm block">
                            Value
                        </label>
                        <select id="bulkValue"
                            class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-800 dark:text-white rounded px-3 py-1 mt-1">
                            <option value="">Select Value</option>
                        </select>
                    </div>
                    <div>
                        <button type="button" id="applyBulk"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
            <div>
                <label class="text-gray-700 dark:text-gray-300 text-sm">General Filters</label>
                <div class="flex gap-4 mt-2 text-gray-700 dark:text-gray-300">
                    <label>
                        <input type="checkbox" class="generalFilter" value="synced"> Hide Synced
                    </label>
                    <label>
                        <input type="checkbox" class="generalFilter" value="saved"> Saved
                    </label>
                    <label>
                        <input type="checkbox" class="generalFilter" value="blank"> Blank
                    </label>
                    <label>
                        <input type="checkbox" class="generalFilter" value="failed"> Failed
                    </label>
                </div>
            </div>
        </div>
        <!-- <form id="purchaseForm" method="POST" action="<?php echo e(route('purchase.save')); ?>"> -->
        <form id="purchaseForm">
            <?php echo csrf_field(); ?>
            <div class="overflow-x-auto">
                <table id="purchaseTable" class="min-w-full text-sm text-gray-700 dark:text-gray-300 border-collapse">
                    <thead class="bg-gray-100 dark:bg-neutral-800 text-xs text-gray-700 dark:text-gray-400 uppercase">
                        <tr>
                            <th class="px-3 py-2 w-8">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="px-3 py-2">SR</th>
                            <th class="px-3 py-2">DATE</th>
                            <th class="px-3 py-2">REFERENCE</th>
                            <th class="px-3 py-2">VOUCHER</th>
                            <th class="px-3 py-2">PARTY A/C NAME</th>
                            <th class="px-3 py-2">GSTIN/UIN</th>
                            <th class="px-3 py-2">PLACE</th>
                            <!-- <th class="px-3 py-2">PARTICULARS</th> -->
                            <th class="px-3 py-2 text-right">AMOUNT</th>
                            <th class="px-3 py-2">STATUS</th>
                            <th class="px-3 py-2">ACTION</th>
                        </tr>
                        <tr class="bg-white dark:bg-neutral-900">
                            <th></th>
                            <th></th>
                            <th>
                                <input class="searchInput">
                            </th>
                            <th>
                                <input class="searchInput">
                            </th>
                            <th>
                                <input class="searchInput">
                            </th>
                            <th>
                                <input class="searchInput">
                            </th>
                            <th>
                                <input class="searchInput">
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b border-neutral-700 hover:bg-neutral-800 transition">
                            <td class="px-3 py-2">
                                <input type="checkbox" name="selected[]" value="<?php echo e($row->id); ?>">
                            </td>
                            <td class="px-3 py-2">
                                <?php echo e($index+1); ?>

                            </td>
                            <td class="px-3 py-2">
                                <input type="date"
                                    name="date[<?php echo e($row->id); ?>]"
                                    value="<?php echo e(\Carbon\Carbon::parse($row->date)->format('Y-m-d')); ?>"
                                    class="inputCell">
                            </td>
                            <td class="px-3 py-2">
                                <input type="text"
                                    name="invoice_no[<?php echo e($row->id); ?>]"
                                    value="<?php echo e($row->invoice_no); ?>"
                                    class="inputCell">
                            </td>
                            <td class="px-3 py-2">
                                <select name="voucher_type[<?php echo e($row->id); ?>]" class="inputCell voucherSelect">
                                    <?php $__currentLoopData = $vchTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vchType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($vchType); ?>"
                                        <?php echo e(strtolower(trim($vchType)) == strtolower(trim($row->vchType))  ? 'selected' : ''); ?>><?php echo e($vchType); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <!-- Party Name -->
                                <input type="text"
                                    name="party_name[<?php echo e($row->id); ?>]"
                                    value="<?php echo e($row->party_name); ?>"
                                    class="inputCell mb-1">
                                <!-- Ledger -->
                                <select name="party_ledger[<?php echo e($row->id); ?>]"
                                    class="ledgerSelect inputCell">
                                    <option value="">Select Ledger</option>
                                    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->name); ?>"
                                        <?php echo e(trim($row->party_name) == trim($ledger->name) ? 'selected' : ''); ?>>
                                        <?php echo e($ledger->name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <?php echo e($row->gst_no); ?>

                            </td>
                            <td class="px-3 py-2">
                                <select name="place_of_supply[<?php echo e($row->id); ?>]"
                                    class="inputCell">
                                    <option value="">Select State</option>
                                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($state); ?>"
                                        <?php echo e(strtolower(trim($state)) == strtolower(trim($row->place_of_supply)) ? 'selected':''); ?>>
                                        <?php echo e($state); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <!-- <td class="px-3 py-2">
                                <select name="ledger[<?php echo e($row->id); ?>]" class="ledgerSelect inputCell">
                                    <option>Select Ledger</option>
                                    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->name); ?>"
                                        <?php echo e(trim($ledger->name) == trim($row->purchase_ledger) ? 'selected':''); ?>><?php echo e($ledger->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td> -->
                            <td class="px-3 py-2 text-right">
                                <?php echo e(number_format($row->total_amount,2)); ?>

                            </td>
                            <td class="px-3 py-2">
                                <span class="text-yellow-400">
                                    <?php echo e($row->status); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2">
                                
                                <button type="button" class="viewRow text-green-400 hover:text-green-300" 
                                    title="View" data-id="<?php echo e($row->id); ?>">
                                    <i class="fa-solid fa-eye action-icon"></i>
                                </button>

                                <!-- <button
                                    type="button"
                                    class="text-blue-400 editRow"
                                    data-id="<?php echo e($row->id); ?>"
                                    data-invoice="<?php echo e($row->invoice_no); ?>"
                                    data-date="<?php echo e($row->date); ?>"
                                    data-gst_no="<?php echo e($row->gst_no); ?>"
                                    data-vchtype="<?php echo e($row->vchType); ?>"
                                    data-party="<?php echo e($row->party_name); ?>"
                                    data-place="<?php echo e($row->place_of_supply); ?>"
                                    data-ledger="<?php echo e($row->purchase_ledger); ?>"
                                    data-amount="<?php echo e($row->total_amount); ?>"
                                    data-item="<?php echo e($row->item_name); ?>"
                                    data-qty="<?php echo e($row->quantity); ?>"
                                    data-rate="<?php echo e($row->rate); ?>"
                                    data-cgst="<?php echo e($row->cgst); ?>"
                                    data-sgst="<?php echo e($row->sgst); ?>"
                                    data-igst="<?php echo e($row->igst); ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button> -->
                                
                                <button type="button"
                                    class="text-blue-400 hover:text-blue-300 editRow"
                                    title="Edit"
                                    data-id="<?php echo e($row->id); ?>"
                                    data-invoice="<?php echo e($row->invoice_no); ?>"
                                    data-date="<?php echo e(\Carbon\Carbon::parse($row->date)->format('Y-m-d')); ?>"
                                    data-gst_no="<?php echo e($row->gst_no); ?>"
                                    data-vchtype="<?php echo e($row->vchType); ?>"
                                    data-party="<?php echo e($row->party_name); ?>"
                                    data-place="<?php echo e($row->place_of_supply); ?>"
                                    data-ledger="<?php echo e($row->purchase_ledger); ?>"
                                    data-amount="<?php echo e($row->total_amount); ?>"
                                    data-cgst="<?php echo e($row->cgst); ?>"
                                    data-sgst="<?php echo e($row->sgst); ?>"
                                    data-igst="<?php echo e($row->igst); ?>">
                                    <i class="fa-solid fa-pen action-icon"></i>
                                </button>

                                <button class="text-red-500 deleteRow" data-id="<?php echo e($row->id); ?>">
                                    <i class="fa-solid fa-trash action-icon"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>




<div id="editModal" class="modal">
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

        
        <div class="receipt-meta-grid">
            
            <div class="receipt-meta-block">
                <div class="receipt-block-title"><i class="fa-solid fa-building text-blue-400 mr-1"></i> Supplier Details</div>
                <div class="receipt-field-row">
                    <label>Party Name</label>
                    <div style="display:flex; gap:6px; width:100%;">
                        <select id="edit_party" class="receipt-input party-select" style="flex:1;">
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
                <div class="receipt-field-row">
                    <label>Purchase Ledger</label>
                    <select id="noitem_purchase_ledger" class="receipt-input ledgerSelect">
                        <option value="">Select Ledger</option>
                        <?php $__currentLoopData = $purchaseLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                    <input type="date" id="edit_date" class="receipt-input">
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
            <div class="receipt-items-header">
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
                    <button type="button" id="addNoItemRowBtn" class="receipt-add-btn" style="font-size:10px;padding:2px 8px;">
                        <i class="fa-solid fa-plus mr-1"></i> Add More
                    </button>
                </div>
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
                <div style="display:flex; gap:20px; align-items:center; max-width:600px;">
                    <div style="flex:1;">
                        <label style="font-size:11px;color: black;">GST Rate (%)</label>
                        <input type="number" id="noitem_gst_rate" class="receipt-input" value="0">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:11px;color: black;">Amount</label>
                        <input type="number" id="noitem_amount" class="receipt-input" placeholder="Enter Amount">
                    </div>
                </div>
            </div> -->
            <div id="no_item_section" style="display:none;">
                <div style="padding:8px 10px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <div style="font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
                        Purchase Ledger-wise Breakup
                    </div>
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th style="width:40%;">Purchase Ledger</th>
                                <th style="width:20%;">GST %</th>
                                <th style="width:30%;">Amount</th>
                                <th style="width:10%;"></th>
                            </tr>
                        </thead>
                        <tbody id="noItemBody"></tbody>
                    </table>
                    <div style="margin-top:8px;">
                        
                    </div>
                </div>
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
                    <div class="tax-row">
                        <span class="tax-label">IGST</span>
                        <select id="igst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            <?php $__currentLoopData = $iGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ledger->id); ?>"><?php echo e($ledger->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="tax-value" id="sum_igst">0.00</span>
                    </div>

                    <!-- CGST -->
                    <div class="tax-row">
                        <span class="tax-label">CGST</span>
                        <select id="cgst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            <?php $__currentLoopData = $cGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ledger->id); ?>"><?php echo e($ledger->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="tax-value" id="sum_cgst">0.00</span>
                    </div>

                    <!-- SGST -->
                    <div class="tax-row">
                        <span class="tax-label">SGST</span>
                        <select id="sgst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            <?php $__currentLoopData = $sGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ledger->id); ?>"><?php echo e($ledger->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="tax-value" id="sum_sgst">0.00</span>
                    </div>

                </div>

                
                <div id="custom_tax_rows" style="display:none;">
                    
                </div>

                <div class="tax-row">
                    <span class="tax-label">Round Off</span>
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

        
        <div class="receipt-footer">
            <div class="receipt-footer-note">This is a computer-generated purchase record.</div>
            <div class="receipt-footer-actions">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                <button type="button" id="updateRow" class="submit-btn">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Update
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


<div id="viewModal" class="modal">
    <div class="modal-content" style="width:780px;">
        <div class="modal-header">
            <h3>View Purchase</h3>
            <button type="button" class="close-btn" onclick="closeViewModal()">✕</button>
        </div>
        <div class="modal-body">

            <!-- HEADER SUMMARY -->
            <div class="view-card">
                <div class="view-grid">
                    <div><label>Invoice No</label><p id="v_invoice"></p></div>
                    <div><label>Date</label><p id="v_date"></p></div>
                    <div><label>Voucher Type</label><p id="v_voucher"></p></div>
                    <div><label>Party Name</label><p id="v_party"></p></div>
                    <div><label>GST No</label><p id="v_gst"></p></div>
                    <div><label>Place of Supply</label><p id="v_place"></p></div>
                    <div><label>Purchase Ledger</label><p id="v_ledger"></p></div>
                    <div><label>Status</label><p id="v_status" class="status-badge"></p></div>
                </div>
            </div>

            <!-- TOTALS -->
            <div class="view-totals">
                <div class="box">
                    <span>Amount</span>
                    <strong id="v_amount"></strong>
                </div>
                <div class="box">
                    <span>SGST</span>
                    <strong id="v_sgst"></strong>
                </div>
                <div class="box">
                    <span>CGST</span>
                    <strong id="v_cgst"></strong>
                </div>
                <div class="box">
                    <span>IGST</span>
                    <strong id="v_igst"></strong>
                </div>
                <div class="box">
                    <span>Round Off</span>
                    <strong id="v_roundoff"></strong>
                </div>
                <div class="box highlight">
                    <span>Total</span>
                    <strong id="v_total"></strong>
                </div>
            </div>

            <!-- ITEMS -->
            <div id="v_items_section" class="mt-4">
                <div class="section-title">Item Details</div>

                <div class="table-wrapper">
                    <table class="view-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>SGST</th>
                                <th>CGST</th>
                                <th>IGST</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="v_items_body"></tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button onclick="closeViewModal()" class="btn-cancel">Close</button>
        </div>
    </div>
</div>

<style>
    /* ── BASE ── */
    .inputCell { background:white; border:1px solid #d1d5db; color:#111827; padding:6px 8px; font-size:12px; width:100%; border-radius:4px; }
    .dark .inputCell { background:#020617; border:1px solid #374151; color:white; }
    .searchInput { background:white; border:1px solid #d1d5db; color:#111827; }
    .dark .searchInput { background:#020617; border:1px solid #374151; color:white; }
    #purchaseTable tbody tr:hover { background:#f3f4f6; }
    .dark #purchaseTable tbody tr:hover { background:#1f2937; }

    /* SELECT2 */
    .select2-container--default .select2-selection--single { background:#fff; border:1px solid #d1d5db; color:#111827; height:30px; }
    .select2-container--default .select2-selection__rendered { color:#111827; }
    .select2-container--default .select2-results__option { color:#111827; background:white; }
    .select2-container--default .select2-results__option--highlighted { background:#2563eb; color:white; }
    .select2-dropdown { background:white; border:1px solid #d1d5db; }
    .dark .select2-container--default .select2-selection--single { background:#020617; border:1px solid #374151; color:white; }
    .dark .select2-container--default .select2-results__option { background:#020617; color:white; }
    .dark .select2-container--default .select2-results__option--highlighted { background:#2563eb; color:white; }
    .dark .select2-dropdown { background:#020617; border:1px solid #374151; color:white; }

    /* MODAL BASE */
    .modal { display:none; position:fixed; inset:0; z-index:999; background:rgba(0,0,0,.65); align-items:center; justify-content:center; }
    .modal.show { display:flex; }
    .modal-content { width: 95%;max-width: 1100px;max-height: 95vh; background:#fff; color:#111827; border-radius:12px; display:flex; flex-direction:column; overflow:hidden; }
    .dark .modal-content { background:#1e293b; color:#e2e8f0; }
    .modal-header { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-bottom:1px solid #e5e7eb; }
    .dark .modal-header { border-color:#334155; }
    .modal-header h3 { font-size:16px; font-weight:600; margin:0; }
    .modal-body { padding:16px; overflow-y:auto; flex:1; max-height: calc(95vh - 120px);}
    .modal-body::-webkit-scrollbar { width:6px; }
    .modal-body::-webkit-scrollbar-thumb { background:#64748b; border-radius:10px; }
    .modal-footer { padding:12px 16px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; }
    .dark .modal-header,.dark .modal-footer { border-color:#334155; }
    .modal-content input,.modal-content select { width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db; background:#fff; color:#111827; font-size:13px; }
    .dark .modal-content input,.dark .modal-content select { background:#020617; border:1px solid #334155; color:#e2e8f0; }
    .modal-content input:focus,.modal-content select:focus { border-color:#3b82f6; outline:none; }
    .form-group { margin-bottom:14px; }
    .form-group label { font-size:12px; margin-bottom:4px; display:block; }
    .dark .form-group label { color:#cbd5f5; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .close-btn { font-size:18px; background:transparent; border:none; cursor:pointer; color:#6b7280; }
    .close-btn:hover { color:#ef4444; }
    .dark .close-btn { color:#94a3b8; }
    .submit-btn { background:#3b82f6; padding:4px 12px; border-radius:6px; color:white; cursor:pointer; border:none; }
    .btn-cancel { background:#374151; padding:4px 12px; border-radius:6px; color:white; cursor:pointer; border:none; }

    /* ══ RECEIPT MODAL ══ */
    #editModal.modal.show { align-items:flex-start; padding:16px; overflow-y:hidden; }
    .receipt-wrapper {  width: 95%;max-width: 1100px;max-height: 95vh; background:#fff; border-radius:8px; overflow:auto; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.4); border:1px solid #e2e8f0; }
    .dark #editModal input,.dark #editModal select,.dark #editModal textarea,.dark #editModal .receipt-input { background:#ffffff!important; color:#000000!important; border:1px solid #d1d5db!important; }
    .receipt-head { display:flex; justify-content:space-between; align-items:flex-start; padding:4px 8px; background:#fff; }
    .receipt-company { font-size:12px; font-weight:700; color:#000; }
    .receipt-subtitle { font-size:8px; color:#000; text-transform:uppercase; letter-spacing:1px; }
    .receipt-close-btn { background:rgba(0,0,0,.1); border:none; color:#000; width:28px; height:28px; border-radius:50%; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center; }
    .receipt-close-btn:hover { background:rgba(239,68,68,.15); color:#dc2626; }
    .receipt-meta-grid { display:grid; grid-template-columns:1fr 1fr; border-bottom:2px solid #e2e8f0; }
    .receipt-meta-block { padding:4px 9px; }
    .receipt-meta-block:first-child { border-right:1px solid #e2e8f0; }
    .receipt-block-title { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#000; border-bottom:1px dashed #e2e8f0; }
    .receipt-field-row { display:flex; align-items:center; gap:8px; margin-bottom:2px; }
    .receipt-field-row label { font-size:11px; color:#000; width:115px; flex-shrink:0; text-align:right; padding-right:6px; }
    .receipt-input { flex:1; background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:2px 8px; font-size:12px; color:#111827; width:100%; }
    .receipt-input:focus { border-color:#3b82f6; background:#eff6ff; outline:none; }
    .receipt-items-header { display:flex; justify-content:space-between; align-items:center; padding:4px 10px; font-size:12px; font-weight:600; color:#374151; border-bottom:1px solid #e2e8f0; }
    .receipt-add-btn { font-size:11px; background:#059669; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer; }
    .receipt-add-btn:hover { background:#047857; }
    .receipt-table-wrap { max-height:160px; overflow-y:auto; }
    .receipt-table-wrap::-webkit-scrollbar { width:4px; }
    .receipt-table-wrap::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:4px; }
    .receipt-table { width:100%; border-collapse:collapse; font-size:12px; }
    .receipt-table thead tr { background:#f1f5f9; border-bottom:2px solid #e2e8f0; position:sticky; top:0; }
    .receipt-table th { padding:6px 8px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#000; white-space:nowrap; }
    .receipt-table th.col-num { text-align:right; }
    .receipt-table th.col-sr { width:30px; }
    .receipt-table th.col-item { min-width:180px; }
    .receipt-table th.col-num { width:85px; }
    .receipt-table th.col-action { width:36px; }
    .receipt-table tbody tr { border-bottom:1px solid #f1f5f9; }
    .receipt-table tbody tr:hover { background:#f8fafc; }
    .receipt-table td { vertical-align:middle; }
    .receipt-table td.td-sr { text-align:center; font-size:11px; color:#9ca3af; padding-left:8px; }
    .receipt-table td input[type="text"],.receipt-table td input[type="number"] { width:100%; background:transparent; border:1px solid transparent; border-radius:3px; padding:3px 5px; font-size:12px; color:#111827; }
    .receipt-table td input:focus { border-color:#3b82f6; background:#eff6ff; outline:none; }
    .receipt-table td input[readonly] { background:#f8fafc; color:#374151; border-color:#e2e8f0; font-weight:600; }
    .receipt-table td input[type="number"] { text-align:right; }
    .receipt-table td:last-child { text-align:center; }
    .receipt-subtotal-row td { padding:5px 4px; font-size:12px; color:#000; background:#f8fafc; border-top:2px solid #e2e8f0; }
    .receipt-del-btn { background:none; border:none; cursor:pointer; color:#ef4444; padding:2px 6px; border-radius:3px; }
    .receipt-del-btn:hover { background:#fee2e2; color:#dc2626; }
    .receipt-tax-summary { display:flex; justify-content:space-between; align-items:flex-start; padding:6px 9px; border-top:2px dashed #e2e8f0; background:#f8fafc; }
    .tax-note { font-size:10px; color:#000; font-style:italic; margin-top:4px; }
    .tax-summary-right { min-width:320px; }
    .tax-row { display:flex; justify-content:space-between; align-items:center; padding:2px 0; border-bottom:1px solid #e2e8f0; font-size:12px; color:#000; gap:6px; }
    .tax-label { font-size:11px; flex:1; }
    .tax-value { font-weight:600; font-size:12px; font-variant-numeric:tabular-nums; white-space:nowrap; }
    .grand-total-row { background:#1e40af; border-radius:4px; padding:2px 8px!important; margin-top:4px; border-bottom:none!important; }
    .grand-total-row .tax-label,.grand-total-row .tax-value { color:#ffffff!important; font-size:13px!important; font-weight:700!important; }
    .receipt-footer { display:flex; justify-content:space-between; align-items:center; padding:5px 9px; border-top:1px solid #e2e8f0; background:#fff; position:sticky; bottom:0; z-index:10; }
    .receipt-footer-note { font-size:10px; color:#000; font-style:italic; }
    .receipt-footer-actions { display:flex; gap:10px; }

    /* ══ CUSTOM GST SLOTS TABLE ══ */
    .custom-slots-table { width:100%; border-collapse:collapse; font-size:11px; }
    .custom-slots-table th { background:#e0e7ff; color:#1e40af; padding:0px 8px; text-align:left; font-weight:700; text-transform:uppercase; font-size:10px; border:1px solid #c7d2fe; }
    .custom-slots-table td { padding:0px 6px; border:1px solid #e2e8f0; vertical-align:middle; }
    .custom-slots-table .rate-badge { display:inline-block; background:#1e40af; color:#fff; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; }
    .custom-slots-table select { width:100%; font-size:11px; padding:2px 4px; border:1px solid #d1d5db; border-radius:3px; background:#fff; color:#111827; }
    .custom-slots-table input[type="number"] { width:100%; font-size:11px; padding:2px 4px; border:1px solid #e2e8f0; border-radius:3px; background:#f8fafc; color:#374151; font-weight:600; text-align:right; }
    .custom-slots-table .zero-row { opacity:.4; }

    /* ══ VIEW MODAL STYLES ══ */
    .view-card { background:#1e293b; padding:16px; border-radius:10px; margin-bottom:16px; }
    .view-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px 20px; }
    .view-grid label { font-size:11px; color:#94a3b8; }
    .view-grid p { font-size:13px; font-weight:500; margin:2px 0 0; color:#e2e8f0; }
    .status-badge { display:inline-block; padding:3px 8px; border-radius:6px; font-size:11px; background:#f59e0b; color:white; }
    /* .view-totals { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; } */
    .view-totals { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; }
    .view-totals .box { background:#020617; padding:10px; border-radius:8px; text-align:center; }
    .view-totals span { font-size:11px; color:#94a3b8; }
    .view-totals strong { display:block; font-size:14px; margin-top:3px; }
    .view-totals .highlight { background:#2563eb; color:white; }
    .section-title { font-size:13px; font-weight:600; margin-bottom:8px; color:#cbd5f5; }
    .table-wrapper { overflow-x:auto; }
    .view-table { width:100%; border-collapse:collapse; }
    .view-table th { font-size:11px; background:#020617; padding:8px; color:#94a3b8; text-align:left; }
    .view-table td { padding:8px; border-bottom:1px solid #1e293b; font-size:12px; }
    .view-table td:nth-child(n+4) { text-align:right; }
    .view-table tbody tr:hover { background:#1e293b; }
    #no_item_section { border-top: 1px dashed #e2e8f0;margin-top: 10px;padding-top: 10px; }
    #no_item_section .receipt-field-row { max-width: 400px; }

    /* Select2 dropdown background fix */
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-results__option,
    .select2-dropdown {
        background: #ffffff !important;
        color: #000000 !important;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db !important;
    }

    .dark .select2-container--default .select2-selection--single,
    .dark .select2-container--default .select2-results__option,
    .dark .select2-dropdown,
    .select2-dropdown.dark-theme,
    .select2-dropdown.dark-theme .select2-results__option {
        background: #020617 !important;
        color: #ffffff !important;
        transition: none !important;
    }

    /* Selected item (top input box) */
    .dark .select2-container--default .select2-selection--single,
    .dark .select2-dropdown,
    .select2-dropdown.dark-theme {
        border: 1px solid #374151 !important;
    }

    /* Dropdown box */
    .select2-container--default .select2-results__option--highlighted,
    .dark .select2-container--default .select2-results__option--highlighted,
    .select2-dropdown.dark-theme .select2-results__option--highlighted {
        background: #2563eb !important;
        color: #ffffff !important;
    }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

<script>
    const ITEM_MASTER = <?php echo json_encode($stockItems, 15, 512) ?>;
    $(document).ready(function() {
        $('#selectAll').click(function() {
            $('tbody input[type=checkbox]').prop('checked', this.checked);
        });
        $('.searchInput').on('keyup', function() {
            let column = $(this).closest('th').index();
            let value = $(this).val().toLowerCase();
            $('#purchaseTable tbody tr').each(function() {
                let cell = $(this).find('td').eq(column);
                let text = cell.text().toLowerCase();
                let input = cell.find('input,select').val();
                if (input) {
                    text += input.toLowerCase();
                }
                $(this).toggle(text.indexOf(value) > -1);
            });
        });

        $('.ledgerSelect').select2({
            width: '100%'
        });
    });

    function openLedgerModal() {
        document.getElementById('ledgerModal').classList.add('show');
    }

    function closeLedgerModal() {
        document.getElementById('ledgerModal').classList.remove('show');
    }

    // Close when clicking outside
    window.onclick = function(event) {
        let modal = document.getElementById('ledgerModal');
        if (event.target === modal) {
            modal.classList.remove('show');
        }
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
                alert(response.message);

                closeLedgerModal();
                location.reload();
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


    const ledgers = <?php echo json_encode(collect($ledgers)->pluck('name'), 15, 512) ?>;
    const states = <?php echo json_encode($states, 15, 512) ?>;
    const vouchers = <?php echo json_encode($vchTypes, 15, 512) ?>;
    const PURCHASE_LEDGERS = <?php echo json_encode($purchaseLedgers ?? [], 15, 512) ?>;
    // const ITEM_MASTER = 
    $('#bulkColumn').on('change', function() {
        let column = $(this).val();
        let dropdown = $('#bulkValue');
        dropdown.empty();
        dropdown.append('<option value="">Select Value</option>');
        if (column === 'ledger') {
            ledgers.forEach(function(item) {
                dropdown.append(`<option value='${item}'>${item}</option>`);
            });
        }
        if (column === 'party') {
            ledgers.forEach(function(item) {
                dropdown.append(`<option value='${item}'>${item}</option>`);
            });
        }

        if (column === 'place') {
            states.forEach(function(item) {
                dropdown.append(`<option value='${item}'>${item}</option>`);
            });
        }

        if (column === 'voucher') {
            vouchers.forEach(function(item) {
                dropdown.append(`<option value='${item}'>${item}</option>`);
            });
        }
    });

    $('#applyBulk').click(function() {
        let column = $('#bulkColumn').val();
        let value = $('#bulkValue').val();

        if (column === '' || value === '') {
            alert('Select column and value');
            return;
        }
        // find selected rows
        let rows = $('tbody input[type=checkbox]:checked').closest('tr');
        // if none selected -> apply to all rows
        if (rows.length === 0) {
            rows = $('#purchaseTable tbody tr');
        }
        rows.each(function() {
            let row = $(this);
            // if (column === 'party') {
            //     row.find('input[name^="party_name"]').val(value);
            // }
            // if (column === 'ledger') {
            //     row.find('select[name^="ledger"]').val(value).trigger('change');
            // }
            if (column === 'party') {
                row.find('input[name^="party_name"]').val(value);
                row.find('select[name^="party_ledger"]').val(value).trigger('change'); // sync dropdown
            }

            if (column === 'ledger') {
                row.find('select[name^="ledger"]').val(value).trigger('change');

                // 🔥 IMPORTANT: also update party_name
                row.find('input[name^="party_name"]').val(value);
                row.find('select[name^="party_ledger"]').val(value).trigger('change');
            }

            if (column === 'place') {
                row.find('select[name^="place_of_supply"]').val(value);
            }
            if (column === 'voucher') {
                row.find('.voucherSelect').val(value);
            }
        });
    });

    $(document).on('change', 'select[name^="ledger"], select[name^="party_ledger"]', function () {
        let value = $(this).val();
        let row = $(this).closest('tr');

        row.find('input[name^="party_name"]').val(value);
    });

    $('#saveBtn').click(function() {
        let formData = $('#purchaseForm').serialize();
        $.ajax({
            url: "<?php echo e(route('transaction_processing.purchase_sumbit')); ?>",
            type: "POST",
            data: formData,
            success: function(response) {
                alert('Sumbit Successfully');
                location.reload(); // reload page and refresh table
            },
            error: function(xhr) {
                alert('Error saving data');
            }
        });
    });

    $(document).on('click', '.deleteRow', function() {
        let id = $(this).data('id');
        if (!confirm('Delete this row?')) return;
        $.ajax({
            url: "<?php echo e(route('purchase.delete', ':id')); ?>".replace(':id', id),
            type: "POST",
            data: {
                _token: "<?php echo e(csrf_token()); ?>"
            },
            success: function(response) {
                alert('Deleted Successfully');
                location.reload();
            },
            error: function() {
                alert('Delete failed');
            }
        });
    });

    $('#closeModal').click(function() {
        $('#editModal').addClass('hidden');
    });

   
    $('.generalFilter').on('change', function () {
        let filters = [];
        $('.generalFilter:checked').each(function () {
            filters.push($(this).val());
        });
        $('#purchaseTable tbody tr').each(function () {
            let row = $(this);
            let status = row.find('td:eq(10)').text().trim().toLowerCase(); // STATUS column
            let show = true;
            // Hide Synced
            if (filters.includes('synced') && status === 'synced') {
                show = false;
            }
            // Show only Saved
            if (filters.includes('saved') && status !== 'saved') {
                show = false;
            }
            // Show only Failed
            if (filters.includes('failed') && status !== 'failed') {
                show = false;
            }
            // Blank = missing ledger or party
            if (filters.includes('blank')) {
                let party = row.find('input[name^="party_name"]').val();
                let ledger = row.find('select[name^="ledger"]').val();
                if (party && ledger) {
                    show = false;
                }
            }
            row.toggle(show);
        });
    });


    function openViewModal()  { document.getElementById('viewModal').classList.add('show'); }
    function closeViewModal() { document.getElementById('viewModal').classList.remove('show'); }
    function normalizeLedgerValue(value) {
        return String(value || '').replace(/['"]/g, '').trim().toLowerCase();
    }
    function setSelectValueByTextOrValue($select, value) {
        if (!value) {
            $select.val('');
            return;
        }

        if ($select.find(`option[value="${value}"]`).length) {
            $select.val(value);
            return;
        }

        const normalized = normalizeLedgerValue(value);
        const match = $select.find('option').filter(function () {
            return normalizeLedgerValue($(this).val()) === normalized || normalizeLedgerValue($(this).text()) === normalized;
        }).first();

        if (match.length) {
            $select.val(match.val());
        } else {
            $select.val(value);
        }
    }
    function fmt(val) {
        return parseFloat(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    

    // ═══════════════════════════════════════════════════════════════════════
    // VIEW MODAL
    // ═══════════════════════════════════════════════════════════════════════
    // $(document).on('click', '.viewRow', function () {
    //     let id = $(this).data('id');

    //     // Open same edit modal
    //     openEditModal();

    //     // Hide update button
    //     $('#addItemRow').hide();
    //     $('#updateRow').hide();
    //     $(document).find('.receipt-del-btn').hide();

    //     // Disable all inputs
    //     $('#editModal input, #editModal select, #editModal textarea')
    //         .prop('disabled', true)
    //         .css('pointer-events', 'none');

    //     // Load data
    //     $.ajax({
    //         url: "<?php echo e(route('purchase.show', ':id')); ?>".replace(':id', id),
    //         type: "GET",
    //         success: function (res) {
    //             // Fill header fields
    //             $('#edit_id').val(res.id);
    //             $('#edit_invoice').val(res.invoice_no);
    //             $('#edit_date').val(res.date);
    //             $('#edit_gst').val(res.gst_no);
    //             $('#edit_party').val(res.party_name);
    //             $('#edit_place').val(res.place_of_supply);
    //             $('#edit_voucher_type').val(res.vchType);
    //             $('#edit_address').val(res.address);
    //             $('#edit_pincode').val(res.pincode);
    //             $('#edit_city').val(res.city);
    //             // $('#edit_is_igst').val(res.is_igst);
    //             $('#edit_is_igst').prop('checked', res.is_igst == 1);

    //             $('#edit_remarks').val(res.Remarks);
    //             // ✅ GST values set
    //             $('#edit_cgst').val(res.cgst || 0);
    //             $('#edit_sgst').val(res.sgst || 0);
    //             $('#edit_igst').val(res.igst || 0);
    //             $('#edit_total_amount').val(res.total_amount || 0);
    //             $('#edit_amount').val(res.amount || 0);

    //             // ✅ UI display
    //             $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
    //             $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
    //             $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
    //             $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));

    //             $('#igst_ledger').val(res.igst_id).trigger('change');
    //             $('#cgst_ledger').val(res.cgst_id).trigger('change');
    //             $('#sgst_ledger').val(res.sgst_id).trigger('change');
    //             $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
    //             toggleGSTLedger();
    //             // Items    
    //             setSelectValueByTextOrValue($('#noitem_purchase_ledger'), res.purchase_ledger);
    //             let tbody = $('#editItemsBody').empty();
    //             if (res.items && res.items.length > 0) {

    //                 $('#standard_items_section').show();
    //                 $('#no_item_section').hide();
    //                 (res.items || []).forEach(item => {
    //                     let row = $(buildItemRow(item));
    //                     // hide delete button in each row
    //                     row.find('.receipt-del-btn').hide();
    //                     // disable inputs inside row
    //                     row.find('input').prop('disabled', true);
    //                     tbody.append(row);
    //                     initItemSelect2();
    //                 });
    //             } else {
    //                 $('#standard_items_section').hide(); 
    //                 $('#no_item_section').show();

    //                 $('#noitem_amount').val(res.amount);
    //                 setSelectValueByTextOrValue($('#noitem_purchase_ledger'), res.purchase_ledger);
    //             }
    //             if (!res.items || res.items.length === 0) {
    //                 $('#noitem_amount').val(res.amount);
    //                 $('#noitem_gst_rate').val(res.gst_rate || 0);

    //                 recalcNoItemGST(); // ✅ ADD THIS
    //             }
    //             if ((res.gst_mode || 'standard') === 'custom') {
    //                 applyCustomGstSlots(res.custom_gst || []);
    //             }
    //             //recalcTotals();
    //         }
    //     });
    // });

    $(document).on('click', '.viewRow', function () {
        let id = $(this).data('id');

        // Open same edit modal
        openEditModal();

        // Hide update and add buttons
        $('#addItemRow').hide();
        $('#updateRow').hide();
        $(document).find('.receipt-del-btn').hide();

        // Disable all inputs
        $('#editModal input, #editModal select, #editModal textarea')
            .prop('disabled', true)
            .css('pointer-events', 'none');

        // Load data
        $.ajax({
            url: "<?php echo e(route('purchase.show', ':id')); ?>".replace(':id', id),
            type: "GET",
            success: function (res) {
                // Fill header fields
                $('#edit_id').val(res.id);
                $('#edit_invoice').val(res.invoice_no);
                $('#edit_date').val(res.date);
                $('#edit_gst').val(res.gst_no);
                $('#edit_party').val(res.party_name).trigger('change');
                //$('#edit_place').val(res.place_of_supply).trigger('change');
                // $('#edit_voucher_type').val(res.vchType).trigger('change');
                $('#edit_place option').each(function () {
                    if ($(this).val().toLowerCase().trim() === String(res.place_of_supply).toLowerCase().trim()) {
                        $(this).prop('selected', true);
                    }
                });
                $('#edit_voucher_type option').each(function () {
                    if ($(this).val().toLowerCase().trim() === String(res.vchType).toLowerCase().trim()) {
                        $(this).prop('selected', true);
                    }
                });
                $('#edit_address').val(res.address || '');
                $('#edit_pincode').val(res.pincode || '');
                $('#edit_city').val(res.city || '');
                $('#edit_remarks').val(res.Remarks || '');
                $('#edit_is_igst').prop('checked', res.is_igst == 1);
                
                // GST LEDGER VALUES
                $('#igst_ledger').val(res.igst_id).trigger('change');
                $('#cgst_ledger').val(res.cgst_id).trigger('change');
                $('#sgst_ledger').val(res.sgst_id).trigger('change');
                
                // Set GST mode
                let gstMode = res.gst_mode || 'standard';
                $('#gst_calc_mode').val(gstMode).trigger('change');
                toggleGSTLedger();
                
                // Set purchase ledger
                setSelectValueByTextOrValue($('#noitem_purchase_ledger'), 
                    res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger);
                
                // Check if has items
                if (res.items && res.items.length > 0) {
                    // WITH ITEMS
                    $('#standard_items_section').show();
                    $('#no_item_section').hide();
                    
                    let tbody = $('#editItemsBody').empty();
                    res.items.forEach(item => {
                        let row = buildReadOnlyItemRow(item);
                        tbody.append(row);
                    });
                    
                    // Update totals from response
                    $('#sum_amount').text(parseFloat(res.amount || 0).toFixed(2));
                    $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                    $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                    $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                    // $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
                    
                } else {
                    // WITHOUT ITEMS - Show no-item section
                    $('#standard_items_section').hide();
                    $('#no_item_section').show();
                    
                    // Populate no-item rows from custom_gst or direct values
                    $('#noItemBody').empty();
                    
                    if (res.custom_gst && res.custom_gst.length > 0) {
                        // Has custom GST slots
                        res.custom_gst.forEach(slot => {
                            addReadOnlyNoItemRow({
                                ledger: slot.ledger_id || slot.ledger_name || res.purchase_ledger_id || res.purchase_ledger,
                                ledger_name: slot.ledger_name || res.purchase_ledger_name || res.purchase_ledger,
                                gst: slot.gst_rate,
                                amount: slot.taxable || slot.amount || 0,
                                cgst: slot.cgst_amount || 0,
                                sgst: slot.sgst_amount || 0,
                                igst: slot.igst_amount || 0
                            });
                        });
                    } else {
                        // Single row from main transaction
                        addReadOnlyNoItemRow({
                            ledger: res.purchase_ledger_id || res.purchase_ledger,
                            ledger_name: res.purchase_ledger_name || res.purchase_ledger,
                            gst: res.gst_rate || 0,
                            amount: res.amount || 0,
                            cgst: res.cgst || 0,
                            sgst: res.sgst || 0,
                            igst: res.igst || 0
                        });
                    }
                    
                    // Update totals
                    $('#sum_amount').text(parseFloat(res.amount || 0).toFixed(2));
                    $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                    $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                    $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                    // $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
                    
                    // Store values in hidden fields
                    $('#edit_amount').val(res.amount || 0);
                    $('#edit_cgst').val(res.cgst || 0);
                    $('#edit_sgst').val(res.sgst || 0);
                    $('#edit_igst').val(res.igst || 0);
                    $('#edit_total_amount').val(res.total_amount || 0);
                    $('#noitem_amount').val(res.amount || 0);
                    $('#noitem_gst_rate').val(res.gst_rate || 0);
                }
                
                // Handle custom GST slots display
                if (gstMode === 'custom' && res.custom_gst && res.custom_gst.length > 0) {
                    $('#custom_slots_section').show();
                    renderReadOnlyCustomSlots(res.custom_gst);
                    calculateCustomGSTTotals(res.custom_gst);
                } else if (gstMode === 'custom') {
                    $('#custom_slots_section').show();
                    if ($('#no_item_section').is(':visible')) {
                        buildCustomSlotsFromNoItemRows();
                    } else {
                        renderCustomSlotsFromPurchaseItems(res.items || [], res.is_igst == 1);
                    }
                } else {
                    $('#custom_slots_section').hide();
                    
                    $('#sum_amount').text(parseFloat(res.amount || 0).toFixed(2));
                    $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                    $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                    $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                    // $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
                }

                
                // Update footer
                $('#foot_amount').text(parseFloat(res.amount || 0).toFixed(2));
                $('#foot_total').text(parseFloat(res.total_amount || 0).toFixed(2));
            },
            error: function() {
                console.error('Failed to load purchase data');
            }
        });
    });

    // Helper: Build read-only item row
    function buildReadOnlyItemRow(item) {
        let srNo = $('#editItemsBody tr').length + 1;
        return `<tr>
            <input type="hidden" class="item-id" value="${item.id || ''}">
            <td class="td-sr" style="width:28px;text-align:center;font-size:11px;color:#9ca3af;padding-left:6px;">${srNo}</td>
            <td style="min-width:180px;">
                <input type="text" class="receipt-input" value="${item.item_name || ''}" readonly style="background:#f3f4f6;">
            </td>
            <td style="width:80px;"><input type="text" class="receipt-input" value="${item.hsn || ''}" readonly style="background:#f3f4f6;text-align:center;"></td>
            <td style="width:65px;"><input type="text" class="receipt-input" value="${item.gst_rate || ''}" readonly style="background:#f3f4f6;text-align:right;"></td>
            <td style="width:65px;"><input type="text" class="receipt-input" value="${item.quantity || ''}" readonly style="background:#f3f4f6;text-align:right;"></td>
            <td style="width:55px;"><input type="text" class="receipt-input" value="${item.unit || 'NOS'}" readonly style="background:#f3f4f6;text-align:center;"></td>
            <td style="width:85px;"><input type="text" class="receipt-input" value="${parseFloat(item.rate || 0).toFixed(2)}" readonly style="background:#f3f4f6;text-align:right;"></td>
            <td style="width:85px;"><input type="text" class="receipt-input" value="${parseFloat(item.amount || 0).toFixed(2)}" readonly style="background:#f3f4f6;text-align:right;"></td>
            <td style="width:30px;text-align:center;"></td>
        </tr>`;
    }

    // Helper: Add read-only no-item row
    function addReadOnlyNoItemRow(data = {}) {
        let row = `
            <tr>
                <td>
                    <input type="text" class="receipt-input" value="${data.ledger_name || data.ledger || ''}" readonly style="background:#f3f4f6;">
                    <input type="hidden" class="noitem-ledger" value="${data.ledger || ''}">
                </td>
                <td>
                    <input type="text" class="receipt-input" value="${data.gst || 0}" readonly style="background:#f3f4f6;text-align:right;">
                    <input type="hidden" class="noitem-gst" value="${data.gst || 0}">
                </td>
                <td>
                    <input type="text" class="receipt-input" value="${parseFloat(data.amount || 0).toFixed(2)}" readonly style="background:#f3f4f6;text-align:right;">
                    <input type="hidden" class="noitem-amount" value="${data.amount || 0}">
                </td>
                <td>
                    <button type="button" class="removeNoItem receipt-del-btn" style="display:none;">×</button>
                </td>
            </tr>
        `;
        $('#noItemBody').append(row);
    }

    // Helper: Render read-only custom GST slots
    function renderReadOnlyCustomSlots(customGst) {
        let html = '';
        
        customGst.forEach((slot, index) => {
            let igstLedgerName = '';
            let cgstLedgerName = '';
            let sgstLedgerName = '';
            
            // Find ledger names
            let iGstLedgers = <?php echo json_encode($iGstLedgers ?? [], 15, 512) ?>;
            let cGstLedgers = <?php echo json_encode($cGstLedgers ?? [], 15, 512) ?>;
            let sGstLedgers = <?php echo json_encode($sGstLedgers ?? [], 15, 512) ?>;
            
            if (slot.igst_ledger_id) {
                let found = iGstLedgers.find(l => String(l.id) === String(slot.igst_ledger_id));
                igstLedgerName = found ? found.name : '';
            }
            if (slot.cgst_ledger_id) {
                let found = cGstLedgers.find(l => String(l.id) === String(slot.cgst_ledger_id));
                cgstLedgerName = found ? found.name : '';
            }
            if (slot.sgst_ledger_id) {
                let found = sGstLedgers.find(l => String(l.id) === String(slot.sgst_ledger_id));
                sgstLedgerName = found ? found.name : '';
            }
            
            // <br><small style="font-size:9px;color:#6b7280;">Taxable: ${parseFloat(slot.taxable || 0).toFixed(2)}</small>
            html += `
                <tr style="color: black;">
                    <td><span class="rate-badge">${slot.gst_rate || 0}%</span>
                    </td>
                    <td><strong>${parseFloat(slot.taxable || slot.amount || 0).toFixed(2)}</strong></td>
                    <td><input type="text" class="receipt-input" value="${igstLedgerName}" readonly style="background:#f3f4f6;"></td>
                    <td><input type="text" class="receipt-input" value="${parseFloat(slot.igst_amount || 0).toFixed(2)}" readonly style="background:#f3f4f6;text-align:right;"></td>
                    <td><input type="text" class="receipt-input" value="${cgstLedgerName}" readonly style="background:#f3f4f6;"></td>
                    <td><input type="text" class="receipt-input" value="${parseFloat(slot.cgst_amount || 0).toFixed(2)}" readonly style="background:#f3f4f6;text-align:right;"></td>
                    <td><input type="text" class="receipt-input" value="${sgstLedgerName}" readonly style="background:#f3f4f6;"></td>
                    <td><input type="text" class="receipt-input" value="${parseFloat(slot.sgst_amount || 0).toFixed(2)}" readonly style="background:#f3f4f6;text-align:right;"></td>
                </tr>
            `;
        });
        
        $('#customSlotsBody').html(html);
    }

    function renderCustomSlotsFromPurchaseItems(items, isIGST) {
        let rateMap = {};
        let baseAmount = 0;

        (items || []).forEach(item => {
            let rate = parseFloat(item.gst_rate || item.gst || 0) || 0;
            let amount = parseFloat(item.amount || 0) || 0;
            if (!rate || !amount) return;

            let key = rate.toString();
            let igst = parseFloat(item.igst || 0) || 0;
            let cgst = parseFloat(item.cgst || 0) || 0;
            let sgst = parseFloat(item.sgst || 0) || 0;

            if (!igst && !cgst && !sgst) {
                let tax = (amount * rate) / 100;
                if (isIGST) {
                    igst = tax;
                } else {
                    cgst = tax / 2;
                    sgst = tax / 2;
                }
            }

            if (!rateMap[key]) {
                rateMap[key] = { amt: 0, igst: 0, cgst: 0, sgst: 0, rate: rate };
            }

            rateMap[key].amt += amount;
            rateMap[key].igst += igst;
            rateMap[key].cgst += cgst;
            rateMap[key].sgst += sgst;
            baseAmount += amount;
        });

        $('#edit_amount').val(baseAmount.toFixed(2));
        renderCustomSlots(rateMap, baseAmount);
    }

    // Helper function for setting select values
    function setSelectValueByTextOrValue($select, value) {
        if (!value) {
            $select.val('').trigger('change');
            return;
        }
        
        // Try by value first
        if ($select.find(`option[value="${value}"]`).length) {
            $select.val(value).trigger('change');
            return;
        }
        
        // Try by text
        let found = false;
        $select.find('option').each(function() {
            if ($(this).text() === value || $(this).val() === value) {
                $select.val($(this).val()).trigger('change');
                found = true;
                return false;
            }
        });
        
        if (!found && $select.find('option[value=""]').length) {
            $select.val('').trigger('change');
        }
    }

    function openViewModal()  { document.getElementById('viewModal').classList.add('show'); }
    function closeViewModal() { document.getElementById('viewModal').classList.remove('show'); }
    
    // ═══════════════════════════════════════════════════════════════════════
    // EDIT MODAL
    // ═══════════════════════════════════════════════════════════════════════
    // ═══════ EDIT MODAL ═══════
    // $(document).on('click', '.editRow', function () {
    //     let btn = $(this), id = btn.data('id');

    //     $('#updateRow').show();
    //     $('#addItemRow').show();

    //     $('#editModal input, #editModal select, #editModal textarea')
    //         .prop('disabled', false)
    //         .css('pointer-events', 'auto');

    //     $('.receipt-del-btn').show();
    //     // Reset mode to standard
    //     $('#gst_calc_mode').val('standard').trigger('change');
    //     $('#edit_id').val(id);
    //     $('#edit_invoice').val(btn.data('invoice'));
    //     $('#edit_date').val(btn.data('date'));
    //     $('#edit_gst').val(btn.data('gst_no'));
    //     $('#edit_voucher_type').val(btn.data('vchtype'));
    //     $('#edit_party').val(btn.data('party'));
    //     $('#edit_place').val(btn.data('place'));
    //     $('#edit_ledger').val(btn.data('ledger'));

    //     $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">Loading…</td></tr>');
    //     openEditModal();

    //     $.ajax({
    //         url: "<?php echo e(route('purchase.show',':id')); ?>".replace(':id', id), type:"GET",
    //         success: function (res) {
    //             $('#edit_address').val(res.address || '');
    //             $('#edit_pincode').val(res.pincode || '');
    //             $('#edit_city').val(res.city || '');
    //             $('#edit_remarks').val(res.Remarks || '');
    //             $('#edit_is_igst').prop('checked', res.is_igst == 1);
    //             // ✅ GST values set
    //             $('#edit_cgst').val(res.cgst || 0);
    //             $('#edit_sgst').val(res.sgst || 0);
    //             $('#edit_igst').val(res.igst || 0);
    //             $('#edit_total_amount').val(res.total_amount || 0);
    //             $('#edit_amount').val(res.amount || 0);

    //             // ✅ UI display
    //             $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
    //             $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
    //             $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
    //             $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
    //             $('#igst_ledger').val(res.igst_id).trigger('change');
    //             $('#cgst_ledger').val(res.cgst_id).trigger('change');
    //             $('#sgst_ledger').val(res.sgst_id).trigger('change');
    //             $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
    //             toggleGSTLedger();
    //             let tbody = $('#editItemsBody').empty();
    //             // (res.items || []).forEach(item => tbody.append(buildItemRow(item)));
    //             if (res.items && res.items.length > 0) {
    //                 $('#standard_items_section').show();
    //                 $('#no_item_section').hide();
    //                 res.items.forEach(item => tbody.append(buildItemRow(item)));
    //                 recalcTotals();
    //             } else {

    //                 let amount = parseFloat(res.amount || 0);
    //                 let cgst   = parseFloat(res.cgst || 0);
    //                 let sgst   = parseFloat(res.sgst || 0);
    //                 let igst   = parseFloat(res.igst || 0);
    //                 let total  = parseFloat(res.total_amount || 0);

    //                 $('#sum_amount').text(amount.toFixed(2));
    //                 $('#sum_cgst').text(cgst.toFixed(2));
    //                 $('#sum_sgst').text(sgst.toFixed(2));
    //                 $('#sum_igst').text(igst.toFixed(2));
    //                 $('#sum_grand_total').text(total.toFixed(2));
    //                 $('#standard_items_section').hide();
    //                 $('#no_item_section').show();
    //                 $('#noitem_amount').val(res.amount);
    //                 tbody.html(''); // clear table
                    
    //             }
    //             if (!res.items || res.items.length === 0) {
    //                 $('#noitem_amount').val(res.amount);
    //                 $('#noitem_gst_rate').val(res.gst_rate || 0);

    //                 recalcNoItemGST(); // ✅ ADD THIS
    //             }
    //             setSelectValueByTextOrValue($('#noitem_purchase_ledger'), res.purchase_ledger);
    //             // if (!res.items || !res.items.length) {
    //             //     tbody.html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">No items — click Add Row</td></tr>');
    //             // }
    //             initItemSelect2(); // ✅ ADD THIS
    //             if (res.items && res.items.length > 0) {
    //                 recalcTotals();
    //             } else {
    //                 recalcNoItemGST();
    //             }

    //             if ((res.gst_mode || 'standard') === 'custom') {
    //                 applyCustomGstSlots(res.custom_gst || []);
    //             }
    //         },
    //         error: () => $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-3" style="color:#ef4444;">Failed to load.</td></tr>')
    //     });
    // });

    // ═══════════════════════════════════════════════════════════════════════
    // EDIT MODAL - FIXED FOR PURCHASE
    // ═══════════════════════════════════════════════════════════════════════
    $(document).on('click', '.editRow', function () {
        let btn = $(this), id = btn.data('id');

        // Show update button and hide view-specific buttons
        $('#updateRow').show();
        $('#addItemRow').show();
        $('#addNoItemRow').hide();

        // Enable all inputs
        $('#editModal input, #editModal select, #editModal textarea')
            .prop('disabled', false)
            .css('pointer-events', 'auto');

        $('.receipt-del-btn').show();
        
        // Reset mode to standard initially (will be overridden by data)
        $('#gst_calc_mode').val('standard').trigger('change');
        
        // Set basic fields from button data
        $('#edit_id').val(id);
        $('#edit_invoice').val(btn.data('invoice'));
        $('#edit_date').val(btn.data('date'));
        $('#edit_gst').val(btn.data('gst_no'));
        $('#edit_party').val(btn.data('party')).trigger('change');
        $('#edit_place').val(btn.data('place')).trigger('change');
        $('#edit_voucher_type').val(btn.data('vchtype')).trigger('change');
        
        // Set purchase ledger
        let ledger = btn.data('ledger');
        if (ledger) {
            $('#noitem_purchase_ledger').val(ledger).trigger('change');
        }
        
        // Clear and show loading
        $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">Loading…</td></tr>');
        openEditModal();

        // Fetch full data
        $.ajax({
            url: "<?php echo e(route('purchase.show',':id')); ?>".replace(':id', id), 
            type: "GET",
            success: function (res) {
                // Set additional fields
                $('#edit_address').val(res.address || '');
                $('#edit_pincode').val(res.pincode || '');
                $('#edit_city').val(res.city || '');
                $('#edit_remarks').val(res.Remarks || '');
                $('#edit_is_igst').prop('checked', res.is_igst == 1);
                
                // Set GST ledger values
                $('#igst_ledger').val(res.igst_id || '').trigger('change');
                $('#cgst_ledger').val(res.cgst_id || '').trigger('change');
                $('#sgst_ledger').val(res.sgst_id || '').trigger('change');
                
                // Set GST mode
                let gstMode = res.gst_mode || 'standard';
                $('#gst_calc_mode').val(gstMode).trigger('change');
                toggleGSTLedger();
                
                // Set purchase ledger (in case button data didn't have it)
                if (res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger) {
                    setSelectValueByTextOrValue($('#noitem_purchase_ledger'), 
                        res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger);
                }
                
                let tbody = $('#editItemsBody').empty();
                
                // Check if has items
                if (res.items && res.items.length > 0) {
                    // WITH ITEMS - Show items table
                    $('#standard_items_section').show();
                    $('#no_item_section').hide();
                    $('#addNoItemRow').hide();
                    $('#addItemRow').show();
                    
                    res.items.forEach(item => {
                        tbody.append(buildItemRow(item));
                    });
                    
                    initItemSelect2();
                    recalcTotals();
                    
                } else {
                    // WITHOUT ITEMS - Show no-item section
                    $('#standard_items_section').hide();
                    $('#no_item_section').show();
                    $('#addItemRow').hide();
                    $('#addNoItemRow').show();
                    
                    // Clear existing no-item rows
                    $('#noItemBody').empty();
                    
                    // Populate from custom_gst or create single row
                    if (res.custom_gst && res.custom_gst.length > 0) {
                        res.custom_gst.forEach(slot => {
                            addNoItemRow({
                                ledger: slot.ledger_id || slot.ledger_name || res.purchase_ledger_id || res.purchase_ledger,
                                gst: slot.gst_rate,
                                amount: slot.taxable || slot.amount || 0
                            });
                        });
                    } else {
                        addNoItemRow({
                            ledger: res.purchase_ledger_id || res.purchase_ledger,
                            gst: res.gst_rate || 0,
                            amount: res.amount || 0
                        });
                    }
                    
                    // Set no-item values
                    $('#noitem_amount').val(res.amount || 0);
                    $('#noitem_gst_rate').val(res.gst_rate || 0);
                    
                    // Update totals
                    $('#sum_amount').text(parseFloat(res.amount || 0).toFixed(2));
                    $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                    $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                    $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                    // $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
                    
                    // Store in hidden fields
                    $('#edit_amount').val(res.amount || 0);
                    $('#edit_cgst').val(res.cgst || 0);
                    $('#edit_sgst').val(res.sgst || 0);
                    $('#edit_igst').val(res.igst || 0);
                    $('#edit_total_amount').val(res.total_amount || 0);
                }
                
                // Handle custom GST slots display (for custom mode)
                if (gstMode === 'custom' && res.custom_gst && res.custom_gst.length > 0) {
                    $('#custom_slots_section').show();
                    renderCustomSlotsFromData(res.custom_gst, res);
                } else if (gstMode === 'custom') {
                    // If custom mode but no data, trigger recalc to generate slots
                    if ($('#no_item_section').is(':visible')) {
                        recalcNoItemGST();
                    } else {
                        recalcTotals();
                    }
                }
                
                // Update footer
                $('#foot_amount').text(parseFloat(res.amount || 0).toFixed(2));
                $('#foot_total').text(parseFloat(res.total_amount || 0).toFixed(2));

                if ((res.gst_mode || 'standard') === 'custom') {
                    applyCustomGstSlots(res.custom_gst || []);
                    if(
                        res.gst_mode === 'custom' &&
                        res.custom_gst &&
                        res.custom_gst.length > 0
                    ){
                        calculateCustomGSTTotals(res.custom_gst);
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading purchase data:', xhr);
                $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-3" style="color:#ef4444;">Failed to load data</td></tr>');
            }
        });
    });

    // Helper: Render custom slots from existing data
    function renderCustomSlotsFromData(customGst, transaction) {
        let iGstLedgers = <?php echo json_encode($iGstLedgers ?? [], 15, 512) ?>;
        let cGstLedgers = <?php echo json_encode($cGstLedgers ?? [], 15, 512) ?>;
        let sGstLedgers = <?php echo json_encode($sGstLedgers ?? [], 15, 512) ?>;
        
        let html = '';
        let customIgst = 0, customCgst = 0, customSgst = 0;
        
        customGst.forEach(slot => {
            let rate = slot.gst_rate || 0;
            let taxable = slot.taxable || slot.amount || 0;
            let igstAmt = slot.igst_amount || 0;
            let cgstAmt = slot.cgst_amount || 0;
            let sgstAmt = slot.sgst_amount || 0;
            
            customIgst += igstAmt * (1);
            customCgst += cgstAmt * (1);
            customSgst += sgstAmt * (1);
            
            // Build ledger options with selected values
            let iOpts = '<option value="">Select Ledger</option>';
            iGstLedgers.forEach(l => {
                let selected = String(l.id) === String(slot.igst_ledger_id) ? 'selected' : '';
                iOpts += `<option value="${l.id}" ${selected}>${l.name}</option>`;
            });
            
            let cOpts = '<option value="">Select Ledger</option>';
            cGstLedgers.forEach(l => {
                let selected = String(l.id) === String(slot.cgst_ledger_id) ? 'selected' : '';
                cOpts += `<option value="${l.id}" ${selected}>${l.name}</option>`;
            });
            
            let sOpts = '<option value="">Select Ledger</option>';
            sGstLedgers.forEach(l => {
                let selected = String(l.id) === String(slot.sgst_ledger_id) ? 'selected' : '';
                sOpts += `<option value="${l.id}" ${selected}>${l.name}</option>`;
            });
            
            // <br><small style="font-size:9px;color:#6b7280;">Taxable: ${parseFloat(taxable).toFixed(2)}</small>
            html += `
                <tr data-rate="${rate}" data-slot-key="${rate}" data-purchase-ledger-id="${slot.ledger_id || ''}">
                    <td><span class="rate-badge">${rate}%</span>
                    </td>
                    <td><strong>${parseFloat(taxable).toFixed(2)}</strong>
                        <input type="hidden" class="slot_purchase_ledger_id" value="${slot.ledger_id || ''}">
                    </td>
                    <td><select class="slot-igst-ledger receipt-input">${iOpts}</select></td>
                    <td><input type="number" class="slot-igst-amt receipt-input" value="${parseFloat(igstAmt).toFixed(2)}" step="any"></td>
                    <td><select class="slot-cgst-ledger receipt-input">${cOpts}</select></td>
                    <td><input type="number" class="slot-cgst-amt receipt-input" value="${parseFloat(cgstAmt).toFixed(2)}" step="any"></td>
                    <td><select class="slot-sgst-ledger receipt-input">${sOpts}</select></td>
                    <td><input type="number" class="slot-sgst-amt receipt-input" value="${parseFloat(sgstAmt).toFixed(2)}" step="any"></td>
                </tr>
            `;
        });
        
        $('#customSlotsBody').html(html);
        
        // Update custom tax summary
        let customSummaryHtml = `
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${customIgst.toFixed(2)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${customCgst.toFixed(2)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${customSgst.toFixed(2)}</span></div>`;
        $('#custom_tax_rows').html(customSummaryHtml);
        
        // Update hidden fields
        $('#edit_igst').val(customIgst.toFixed(2));
        $('#edit_cgst').val(customCgst.toFixed(2));
        $('#edit_sgst').val(customSgst.toFixed(2));
    }

    // Helper: Build item options for dropdown
    function buildItemOptions(selected = '') {
        let html = '<option value="">Select Item</option>';
        if (typeof ITEM_MASTER !== 'undefined' && ITEM_MASTER.length) {
            ITEM_MASTER.forEach(item => {
                let name = item.strItemName;
                let safeValue = name ? name.replace(/"/g, '&quot;') : '';
                let selectedAttr = (selected === name) ? 'selected' : '';
                html += `<option value="${safeValue}" ${selectedAttr}>${name}</option>`;
            });
        }
        return html;
    }

    // Helper: Build item row for editing
    function buildItemRow(item) {
        let srNo = $('#editItemsBody tr').length + 1;
        return `<tr>
            <input type="hidden" class="item-id" value="${item.id || ''}">
            <td class="td-sr" style="width:28px;text-align:center;font-size:11px;color:#9ca3af;padding-left:6px;">${srNo}</td>
            <td style="min-width:180px;">
                <select class="item-name itemSelect" style="width:100%;">
                    ${buildItemOptions(item.item_name || '')}
                </select>
            </td>
            <td style="width:80px;"><input type="text" class="item-hsn" value="${item.hsn || ''}" placeholder="HSN" style="text-align:center;"></td>
            <td style="width:65px;"><input type="number" class="item-gst_rate" value="${item.gst_rate || ''}" placeholder="%" step="any" style="text-align:right;"></td>
            <td style="width:65px;"><input type="number" class="item-qty" value="${item.quantity || ''}" placeholder="0" step="any" style="text-align:right;"></td>
            <td style="width:55px;"><input type="text" class="item-unit" value="${item.unit || 'NOS'}" style="text-align:center;"></td>
            <td style="width:85px;"><input type="number" class="item-rate" value="${item.rate || ''}" placeholder="0.00" step="any" style="text-align:right;"></td>
            <td style="width:85px;"><input type="number" class="item-amount" value="${item.amount || ''}" readonly style="text-align:right;"></td>
            <td style="width:30px;text-align:center;">
                <button type="button" class="removeItemRow receipt-del-btn" title="Remove">
                    <i class="fa-solid fa-times" style="font-size:11px;"></i>
                </button>
            </td>
            <input type="hidden" class="item-sgst" value="${item.sgst || 0}">
            <input type="hidden" class="item-cgst" value="${item.cgst || 0}">
            <input type="hidden" class="item-igst" value="${item.igst || 0}">
            <input type="hidden" class="item-total" value="${item.total_amount || 0}">
        </tr>`;
    }

    // Helper: Add no-item row for editing
    // function addNoItemRow(data = {}) {
    //     let ledgerId = data.ledger || '';
    //     let ledgerName = '';
        
    //     // Build ledger options
    //     let ledgerOptions = '<option value="">Select Ledger</option>';
    //     if (typeof PURCHASE_LEDGERS !== 'undefined' && PURCHASE_LEDGERS.length) {
    //         PURCHASE_LEDGERS.forEach(ledger => {
    //             let selected = (String(ledger.id) === String(ledgerId) || ledger.name === ledgerId) ? 'selected' : '';
    //             ledgerOptions += `<option value="${ledger.id}" ${selected}>${ledger.name}</option>`;
    //         });
    //     }
        
    //     let row = `
    //         <tr>
    //             <td><select class="receipt-input noitem-ledger">${ledgerOptions}</select></td>
    //             <td><input type="number" class="receipt-input noitem-gst" value="${data.gst || 0}" step="any"></td>
    //             <td><input type="number" class="receipt-input noitem-amount" value="${data.amount || 0}" step="any"></td>
    //             <td><button type="button" class="removeNoItem receipt-del-btn">×</button></td>
    //         </tr>
    //     `;
    //     $('#noItemBody').append(row);
        
    //     // Initialize select2 for the new row
    //     $('#noItemBody tr:last .noitem-ledger').select2({
    //         width: '100%',
    //         placeholder: 'Search Ledger...',
    //         dropdownParent: $('#editModal'),
    //         allowClear: true
    //     });
    // }

    function addNoItemRow(data = {}) {
        let ledgerId = data.ledger || '';
        let ledgerName = data.ledger_name || '';
        
        // Build ledger options
        let ledgerOptions = '<option value="">Select Ledger</option>';
        let ledgersList = typeof PURCHASE_LEDGERS !== 'undefined' ? PURCHASE_LEDGERS : [];
        
        if (ledgersList.length) {
            ledgersList.forEach(ledger => {
                let selected = (String(ledger.id) === String(ledgerId) || ledger.name === ledgerId || ledger.name === ledgerName) ? 'selected' : '';
                ledgerOptions += `<option value="${ledger.id}" ${selected}>${ledger.name}</option>`;
            });
        }
        
        let row = `
            <tr>
                <td>
                    <select class="receipt-input noitem-ledger" style="width:100%;">${ledgerOptions}</select>
                </td>
                <td>
                    <input type="number" class="receipt-input noitem-gst" value="${data.gst || 0}" step="any" style="text-align:right;">
                </td>
                <td>
                    <input type="number" class="receipt-input noitem-amount" value="${data.amount || 0}" step="any" style="text-align:right;">
                </td>
                <td style="text-align:center;">
                    <button type="button" class="removeNoItem receipt-del-btn" title="Remove Row">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#noItemBody').append(row);
        
        // Initialize select2 for the new row
        $('#noItemBody tr:last .noitem-ledger').select2({
            width: '100%',
            placeholder: 'Search Ledger...',
            dropdownParent: $('#editModal'),
            allowClear: true
        });
    }

    function calculateCustomGSTTotals(customGst)
    {
        let taxableAmount = 0;
        let totalCGST = 0;
        let totalSGST = 0;
        let totalIGST = 0;

        customGst.forEach(function(slot){

            taxableAmount += parseFloat(
                slot.taxable_amount ??
                slot.taxable ??
                slot.amount ??
                0
            );

            totalCGST += parseFloat(slot.cgst_amount || 0);
            totalSGST += parseFloat(slot.sgst_amount || 0);
            totalIGST += parseFloat(slot.igst_amount || 0);
        });

        let grandTotal =
            taxableAmount +
            totalCGST +
            totalSGST +
            totalIGST;

        $('#sum_amount').text(taxableAmount.toFixed(2));
        $('#sum_cgst').text(totalCGST.toFixed(2));
        $('#sum_sgst').text(totalSGST.toFixed(2));
        $('#sum_igst').text(totalIGST.toFixed(2));

        $('#txt_cgst').text(totalCGST.toFixed(2));
        $('#txt_sgst').text(totalSGST.toFixed(2));
        $('#txt_igst').text(totalIGST.toFixed(2));
        // $('#sum_grand_total').text(grandTotal.toFixed(2));
        setRoundOffSummary(grandTotal);
    }

    function populateNoItemRows(res, readonly = false) {
        $('#noItemBody').empty();
        
        if (res.custom_gst && res.custom_gst.length) {
            // Has custom GST slots - show each as a row
            res.custom_gst.forEach(slot => {
                addNoItemRow({
                    ledger: slot.ledger_id || slot.ledger_name || res.purchase_ledger,
                    ledger_name: slot.ledger_name || res.purchase_ledger_name || res.purchase_ledger,
                    gst: slot.gst_rate,
                    amount: slot.taxable || slot.amount || 0
                });
            });
        } else if (res.noitem_rows && res.noitem_rows.length) {
            // Has noitem_rows array
            res.noitem_rows.forEach(row => {
                addNoItemRow({
                    ledger: row.ledger,
                    gst: row.gst,
                    amount: row.amount
                });
            });
        } else {
            // Single row from main transaction
            addNoItemRow({
                ledger: res.purchase_ledger_id || res.purchase_ledger,
                ledger_name: res.purchase_ledger_name || res.purchase_ledger,
                gst: res.gst_rate || 0,
                amount: res.amount || 0
            });
        }

        if (readonly) {
            $('#noItemBody input, #noItemBody select, #noItemBody button')
                .prop('disabled', true)
                .css('pointer-events', 'none');
            $('#addNoItemRowBtn').hide();
        } else {
            $('#addNoItemRowBtn').show();
        }
    }

    $('#addNoItemRowBtn').click(function() {
        addNoItemRow({
            ledger: $('#noitem_purchase_ledger').val(),
            gst: 18,
            amount: 0
        });
        recalcNoItemGST();
    });

    function collectNoItemRows() {
        let rows = [];
        $('#noItemBody tr').each(function() {
            let ledger = $(this).find('.noitem-ledger').val();
            let gst = $(this).find('.noitem-gst').val();
            let amount = parseFloat($(this).find('.noitem-amount').val()) || 0;
            
            if (ledger && amount > 0) {
                rows.push({
                    ledger: ledger,
                    gst: gst,
                    amount: amount
                });
            }
        });
        return rows;
    }

    // Fix recalcNoItemGST function
    // function recalcNoItemGST() {
    //     let amount = 0;
    //     let gstRate = 0;
    //     let isIgst = $('#edit_is_igst').is(':checked');
    //     let mode = $('#gst_calc_mode').val();
        
    //     // Calculate totals from all no-item rows
    //     $('#noItemBody tr').each(function() {
    //         amount += parseFloat($(this).find('.noitem-amount').val()) || 0;
    //         let rowGstRate = parseFloat($(this).find('.noitem-gst').val()) || 0;
    //         if (rowGstRate > gstRate) gstRate = rowGstRate;
    //     });
        
    //     let cgst = 0, sgst = 0, igst = 0;
        
    //     if (isIgst) {
    //         igst = amount * gstRate / 100;
    //     } else {
    //         cgst = amount * (gstRate / 2) / 100;
    //         sgst = amount * (gstRate / 2) / 100;
    //     }
        
    //     let total = amount + cgst + sgst + igst;
        
    //     // Update UI
    //     $('#sum_amount').text(amount.toFixed(2));
    //     $('#sum_cgst').text(cgst.toFixed(2));
    //     $('#sum_sgst').text(sgst.toFixed(2));
    //     $('#sum_igst').text(igst.toFixed(2));
    //     $('#sum_grand_total').text(total.toFixed(2));
        
    //     // Update hidden fields
    //     $('#edit_amount').val(amount);
    //     $('#edit_cgst').val(cgst);
    //     $('#edit_sgst').val(sgst);
    //     $('#edit_igst').val(igst);
    //     $('#edit_total_amount').val(total);
    //     $('#noitem_amount').val(amount);
    //     $('#noitem_gst_rate').val(gstRate);
        
    //     // Render custom slots if in custom mode
    //     if (mode === 'custom' && amount > 0) {
    //         let rateMap = {};
    //         let key = gstRate > 0 ? gstRate.toString() : '0';
    //         rateMap[key] = { amt: amount, igst: igst, cgst: cgst, sgst: sgst };
    //         renderCustomSlots(rateMap, total);
            
    //         // Enable only the relevant GST row
    //         $('#customSlotsBody tr').each(function() {
    //             let rowRate = parseFloat($(this).data('rate')) || 0;
    //             let isActive = Math.abs(rowRate - gstRate) < 0.0001 && amount > 0 && gstRate > 0;
    //             $(this).toggleClass('zero-row', !isActive);
    //             $(this).find('input, select').prop('disabled', !isActive);
    //         });
    //     }
    // }

    function recalcNoItemGST() {
        let amount = 0;
        let totalCgst = 0, totalSgst = 0, totalIgst = 0;
        let isIgst = $('#edit_is_igst').is(':checked');
        let mode = $('#gst_calc_mode').val();
        
        // Calculate totals from all no-item rows
        $('#noItemBody tr').each(function() {
            let rowAmount = parseFloat($(this).find('.noitem-amount').val()) || 0;
            let rowGstRate = parseFloat($(this).find('.noitem-gst').val()) || 0;
            
            amount += rowAmount;
            
            let gstAmount = (rowAmount * rowGstRate) / 100;
            
            if (isIgst) {
                totalIgst += gstAmount;
            } else {
                totalCgst += gstAmount / 2;
                totalSgst += gstAmount / 2;
            }
        });
        
        let total = amount + totalCgst + totalSgst + totalIgst;
        
        // Update UI
        $('#sum_amount').text(amount.toFixed(2));
        $('#sum_cgst').text(totalCgst.toFixed(2));
        $('#sum_sgst').text(totalSgst.toFixed(2));
        $('#sum_igst').text(totalIgst.toFixed(2));
        // $('#sum_grand_total').text(total.toFixed(2));
        setRoundOffSummary(total);
        // Update hidden fields
        $('#edit_amount').val(amount);
        $('#edit_cgst').val(totalCgst);
        $('#edit_sgst').val(totalSgst);
        $('#edit_igst').val(totalIgst);
        // $('#edit_total_amount').val(total);
        setRoundOffSummary(total);
        // Render custom slots if in custom mode
        if (mode === 'custom' && amount > 0) {
            let rateMap = {};
            
            $('#noItemBody tr').each(function(index) {
                let rowAmount = parseFloat($(this).find('.noitem-amount').val()) || 0;
                let rowGstRate = parseFloat($(this).find('.noitem-gst').val()) || 0;
                let ledgerId = $(this).find('.noitem-ledger').val() || '';
                
                if (rowAmount > 0) {
                    let gstAmount = (rowAmount * rowGstRate) / 100;
                    let key = `${rowGstRate}|${ledgerId}`;
                    
                    if (!rateMap[key]) {
                        rateMap[key] = { 
                            amt: 0, 
                            igst: 0, 
                            cgst: 0, 
                            sgst: 0, 
                            rate: rowGstRate,
                            ledgerId: ledgerId
                        };
                    }
                    
                    rateMap[key].amt += rowAmount;
                    if (isIgst) {
                        rateMap[key].igst += gstAmount;
                    } else {
                        rateMap[key].cgst += gstAmount / 2;
                        rateMap[key].sgst += gstAmount / 2;
                    }
                }
            });
            
            renderCustomSlots(rateMap, total);
        }
    }

    // Fix toggleGSTLedger function
    function toggleGSTLedger() {
        let isIGST = $('#edit_is_igst').is(':checked');
        let mode = $('#gst_calc_mode').val();
        
        if (mode === 'custom') {
            $('#igst_ledger').closest('.tax-row').hide();
            $('#cgst_ledger').closest('.tax-row').hide();
            $('#sgst_ledger').closest('.tax-row').hide();
            return;
        }
        
        if (isIGST) {
            $('#igst_ledger').closest('.tax-row').show();
            $('#cgst_ledger').closest('.tax-row').hide();
            $('#sgst_ledger').closest('.tax-row').hide();
        } else {
            $('#igst_ledger').closest('.tax-row').hide();
            $('#cgst_ledger').closest('.tax-row').show();
            $('#sgst_ledger').closest('.tax-row').show();
        }
    }

    // Initialize Select2 on load
    $(document).ready(function() {
        // Initialize ledger selects
        $('.ledgerSelect').select2({
            width: '100%',
            placeholder: "Search Ledger...",
            allowClear: true,
            dropdownAutoWidth: true
        });
        
        // Initialize party select in modal
        $('#edit_party').select2({
            dropdownParent: $('#editModal'),
            width: '100%',
            placeholder: "Search Party...",
            allowClear: true
        });
    });

    // Add No Item Row button click
    $('#addNoItemRow').click(function() {
        addNoItemRow({
            ledger: $('#noitem_purchase_ledger').val(),
            gst: 18,
            amount: 0
        });
        recalcNoItemGST();
    });

    // Remove no item row handler
    $(document).on('click', '.removeNoItem', function() {
        $(this).closest('tr').remove();
        if ($('#noItemBody tr').length === 0) {
            addNoItemRow({
                ledger: $('#noitem_purchase_ledger').val(),
                gst: 18,
                amount: 0
            });
        }
        recalcNoItemGST();
    });

    // ═══════ ADD ITEM ROW ═══════
    $('#addItemRow').click(function () {
        // Remove "no items" placeholder if present
        if ($('#editItemsBody tr td[colspan]').length) $('#editItemsBody').empty();
        $('#editItemsBody').append(buildItemRow({}));
        initItemSelect2(); // 🔥 ADD THIS
        recalcTotals();
    });

    // ═══════ LIVE RECALC ON INPUT ═══════
    $(document).on('input', '#editItemsBody input', function () {
        recalcItemRow($(this).closest('tr'));
        recalcTotals();
    });

    // ═══════ REMOVE ROW ═══════
    $(document).on('click', '.removeItemRow', function () {
        $(this).closest('tr').remove();
        recalcTotals();
    });

    // ═══════ GST MODE SWITCH ═══════
    $('#gst_calc_mode').on('change', function () {
        let mode = $(this).val();
        if (mode === 'standard') {
            toggleGSTLedger();
            $('#standard_items_section').show();
            $('#standard_tax_rows').show();
            $('#custom_slots_section').hide();
            $('#custom_tax_rows').hide();
            $('#igst_toggle_wrap').show();
        } else {
            $('#standard_items_section').show(); // items table stays visible always
            $('#standard_tax_rows').hide();
            $('#custom_slots_section').show();
            $('#custom_tax_rows').show();
            $('#igst_toggle_wrap').hide();
        }
        // recalcTotals();
        if ($('#no_item_section').is(':visible')) {
            recalcNoItemGST();
        } else {
            recalcTotals();
        }
    });

    $('#edit_is_igst').on('change', function () {
         if ($('#no_item_section').is(':visible')) {
            recalcNoItemGST();
            return;
        }
        // 🔥 Recalculate each row GST
        $('#editItemsBody tr').each(function () {
            recalcItemRow($(this));
        });
        // 🔥 Then update totals
        recalcTotals();
    });

    // ── Save (Update) ────────────────────────────────────────────────────
    $('#updateRow').click(function () {
        let items = [];

        if ($('#no_item_section').is(':visible')) {
            items = []; // no items case
        } else {
            $('#editItemsBody tr').each(function () {
                let row = $(this);
                items.push({
                    id:           row.find('.item-id').val(),
                    hsn:          row.find('.item-hsn').val(),
                    item_name:    row.find('.item-name').val(),
                    gst_rate:     row.find('.item-gst_rate').val(),
                    quantity:     row.find('.item-qty').val(),
                    unit:         row.find('.item-unit').val(),
                    rate:         row.find('.item-rate').val(),
                    amount:       row.find('.item-amount').val(),
                    sgst:         row.find('.item-sgst').val(),
                    cgst:         row.find('.item-cgst').val(),
                    igst:         row.find('.item-igst').val(),
                    total_amount: row.find('.item-total').val(),
                });
            });
        }
        console.log({
            gst_mode: $('#gst_calc_mode').val(),
            custom_slots: collectCustomSlots(),
            noitem_amount: $('#noitem_amount').val()
        });
 
        $.ajax({
            url: "<?php echo e(route('purchase.update')); ?>",
            type: "POST",
            // contentType: "application/json",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                id: $('#edit_id').val(),
                invoice_no: $('#edit_invoice').val(),
                date: $('#edit_date').val(),
                party_name: $('#edit_party').val(),
                gst_no: $('#edit_gst').val(),
                place_of_supply: $('#edit_place').val(),
                // purchase_ledger: $('#edit_ledger').val(),
                purchase_ledger: $('#noitem_purchase_ledger option:selected').text(),  // ✅ Fixed

                vchType: $('#edit_voucher_type').val(),
                address: $('#edit_address').val(),
                pincode: $('#edit_pincode').val(),
                city: $('#edit_city').val(),
                is_igst: $('#edit_is_igst').is(':checked') ? 1 : 0,
                Remarks: $('#edit_remarks').val(),

                gst_mode: $('#gst_calc_mode').val(),

                amount: $('#edit_amount').val(),
                cgst: $('#edit_cgst').val(),
                sgst: $('#edit_sgst').val(),
                igst: $('#edit_igst').val(),
                total_amount: $('#edit_total_amount').val(),
                roundoff: $('#edit_roundoff').val(),
                igst_ledger: $('#igst_ledger').val(),
                cgst_ledger: $('#cgst_ledger').val(),
                sgst_ledger: $('#sgst_ledger').val(),

                noitem_amount: $('#noitem_amount').val(),
                purchase_ledger_id: $('#noitem_purchase_ledger').val(),
                purchase_ledger_name: $('#noitem_purchase_ledger option:selected').text(),
                gst_rate: $('#noitem_gst_rate').val(),

                items: items,
                custom_slots: collectCustomSlots()
            },
            success:  (res) => {
                // alert('Updated Successfully');
                // location.reload();
                if (res.status) {
                    showToast(res.message || 'Inserted successfully', 'success');
                    //closeEditModal();
                    location.reload();
                } else {
                    showToast(res.message || 'Something went wrong', 'error');

                    // 🔥 Enable button again
                    // btn.prop('disabled', false);
                    // btn.html('Save');
                }
            },
            // error:   () => alert('Update failed')
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Update failed';
                showToast(message, 'error');
            }
        });
    });

    
    function collectCustomSlots() {
        let slots = [];

        $('#customSlotsBody tr').each(function () {
            let row = $(this);

            let rateText = row.find('.slot-rate').text();

            slots.push({
                rate: parseFloat(rateText.replace('%','')) || 0,

                taxable: parseFloat(
                    row.find('.slot-taxable').text().replace(/[^0-9.]/g, '')
                ) || 0,

                igst_ledger_id: row.find('.slot-igst-ledger').val() || null,
                igst_amount: parseFloat(row.find('.slot-igst-amt').val()) || 0,

                cgst_ledger_id: row.find('.slot-cgst-ledger').val() || null,
                cgst_amount: parseFloat(row.find('.slot-cgst-amt').val()) || 0,

                sgst_ledger_id: row.find('.slot-sgst-ledger').val() || null,
                sgst_amount: parseFloat(row.find('.slot-sgst-amt').val()) || 0,
            });
        });

        return slots;
    }
        
    // ═══════════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════════
    function fmt(v) {
        return parseFloat(v||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
    }

    function buildItemRow(item) {
        let srNo = $('#editItemsBody tr').length + 1;
        return `<tr>
            <input type="hidden" class="item-id" value="${item.id||''}">
            <td class="td-sr" style="width:28px;text-align:center;font-size:11px;color:#9ca3af;padding-left:6px;">${srNo}</td>
            <td style="min-width:180px;">
                <select class="item-name itemSelect">
                    ${buildItemOptions(item.item_name || '')}
                </select>
            </td>
            <td style="width:80px;"><input type="text" class="item-hsn" value="${item.hsn||''}" placeholder="HSN" style="text-align:center;"></td>
            <td style="width:65px;"><input type="number" class="item-gst_rate" value="${item.gst_rate||''}" placeholder="%" step="any" style="text-align:right;"></td>
            <td style="width:65px;"><input type="number" class="item-qty" value="${item.quantity||''}" placeholder="0" step="any" style="text-align:right;"></td>
            <td style="width:55px;"><input type="text" class="item-unit" value="${item.unit||'NOS'}" style="text-align:center;"></td>
            <td style="width:85px;"><input type="number" class="item-rate" value="${item.rate||''}" placeholder="0.00" step="any" style="text-align:right;"></td>
            <td style="width:85px;"><input type="number" class="item-amount" value="${item.amount||''}" readonly style="text-align:right;"></td>
            <td style="width:30px;text-align:center;">
                <button type="button" class="removeItemRow receipt-del-btn" title="Remove">
                    <i class="fa-solid fa-times" style="font-size:11px;"></i>
                </button>
            </td>
            <input type="hidden" class="item-sgst"  value="${item.sgst||0}">
            <input type="hidden" class="item-cgst"  value="${item.cgst||0}">
            <input type="hidden" class="item-igst"  value="${item.igst||0}">
            <input type="hidden" class="item-total" value="${item.total_amount||0}">
        </tr>`;

        $('.itemSelect').last().select2({
            width: '100%',
            placeholder: "Search Item...",
            allowClear: true
        });
    }

    // Recalc one item row's GST values based on mode
    function recalcItemRow(row) {
        let qty  = parseFloat(row.find('.item-qty').val())  || 0;
        let rate = parseFloat(row.find('.item-rate').val()) || 0;
        let gstRate = parseFloat(row.find('.item-gst_rate').val()) || 0;
        let amount  = qty * rate;
        let isIGST  = $('#edit_is_igst').is(':checked');
        let mode    = $('#gst_calc_mode').val();

        let cgst=0, sgst=0, igst=0;

        if (mode === 'standard' && gstRate > 0) {
            if (isIGST) {
                igst = amount * gstRate / 100;
            } else {
                cgst = amount * (gstRate / 2) / 100;
                sgst = amount * (gstRate / 2) / 100;
            }
        }
        // In custom mode GST comes from slot ledger selection — item rows just store amount
        let total = amount + cgst + sgst + igst;

        row.find('.item-amount').val(amount.toFixed(2));
        row.find('.item-cgst').val(cgst.toFixed(2));
        row.find('.item-sgst').val(sgst.toFixed(2));
        row.find('.item-igst').val(igst.toFixed(2));
        row.find('.item-total').val(total.toFixed(2));
    }

    // Master recalc — updates summary, footer, and custom slots
    // function recalcTotals() {
    //     if ($('#no_item_section').is(':visible')) {

    //         let amount = parseFloat($('#noitem_amount').val()) || 0;
    //         let isIGST = $('#edit_is_igst').is(':checked');

    //         let cgst = 0, sgst = 0, igst = 0;
    //         let gstRate = 18;

    //         if (amount > 0) {
    //             if (isIGST) {
    //                 igst = amount * gstRate / 100;
    //             } else {
    //                 cgst = amount * (gstRate / 2) / 100;
    //                 sgst = amount * (gstRate / 2) / 100;
    //             }
    //         }

    //         let total = amount + cgst + sgst + igst;

    //         // UI
    //         $('#sum_amount').text(fmt(amount));
    //         $('#sum_cgst').text(fmt(cgst));
    //         $('#sum_sgst').text(fmt(sgst));
    //         $('#sum_igst').text(fmt(igst));
    //         $('#sum_grand_total').text(fmt(total));

    //         // Hidden
    //         $('#edit_amount').val(amount);
    //         $('#edit_cgst').val(cgst);
    //         $('#edit_sgst').val(sgst);
    //         $('#edit_igst').val(igst);
    //         $('#edit_total_amount').val(total);

    //     }
    //     let mode = $('#gst_calc_mode').val();
    //     let sumAmt=0, sumSgst=0, sumCgst=0, sumIgst=0, sumTotal=0;

    //     // Collect per-rate data for custom mode
    //     let rateMap = {}; // { '5': {amt,igst,cgst,sgst}, '18': {...}, ... }

    //     $('#editItemsBody tr').each(function () {
    //         let row     = $(this);
    //         let amt     = parseFloat(row.find('.item-amount').val()) || 0;
    //         let sgst    = parseFloat(row.find('.item-sgst').val())   || 0;
    //         let cgst    = parseFloat(row.find('.item-cgst').val())   || 0;
    //         let igst    = parseFloat(row.find('.item-igst').val())   || 0;
    //         let total   = parseFloat(row.find('.item-total').val())  || 0;
    //         let gstRate = row.find('.item-gst_rate').val() || '0';

    //         sumAmt   += amt;
    //         sumSgst  += sgst;
    //         sumCgst  += cgst;
    //         sumIgst  += igst;
    //         sumTotal += total;

    //         // Accumulate into rate bucket for custom mode
    //         if (!rateMap[gstRate]) rateMap[gstRate] = { amt:0, igst:0, cgst:0, sgst:0 };
    //         rateMap[gstRate].amt  += amt;
    //         rateMap[gstRate].igst += igst;
    //         rateMap[gstRate].cgst += cgst;
    //         rateMap[gstRate].sgst += sgst;
    //     });

    //     // Update hidden inputs (keep existing save working)
    //     $('#edit_amount').val(sumAmt.toFixed(2));
    //     $('#edit_sgst').val(sumSgst.toFixed(2));
    //     $('#edit_cgst').val(sumCgst.toFixed(2));
    //     $('#edit_igst').val(sumIgst.toFixed(2));
    //     $('#edit_total_amount').val(sumTotal.toFixed(2));

    //     // Update visible summary
    //     $('#sum_amount').text(fmt(sumAmt));
    //     $('#foot_amount').text(fmt(sumAmt));
    //     $('#foot_total').text(fmt(sumTotal));
    //     $('#sum_grand_total').text(fmt(sumTotal));

    //     // Renumber rows
    //     $('#editItemsBody tr').each(function(i) { $(this).find('.td-sr').text(i+1); });

    //     if (mode === 'standard') {
    //         toggleGSTLedger();
    //         $('#sum_sgst').text(fmt(sumSgst));
    //         $('#sum_cgst').text(fmt(sumCgst));
    //         $('#sum_igst').text(fmt(sumIgst));
    //     } else {
    //         // CUSTOM MODE: render rate-wise slots
    //         renderCustomSlots(rateMap, sumTotal);
    //     }
    // }

    function getSummaryBaseTotal() {
        return (parseFloat($('#edit_amount').val()) || 0)
            + (parseFloat($('#edit_cgst').val()) || 0)
            + (parseFloat($('#edit_sgst').val()) || 0)
            + (parseFloat($('#edit_igst').val()) || 0);
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

    function applyRoundOffSummary(total, roundOff) {
        total = parseFloat(total) || 0;
        roundOff = parseFloat(roundOff) || 0;
        let roundedTotal = total + roundOff;

        $('#sum_roundoff').val(roundOff.toFixed(2));
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

    function recalcTotals() {
        if ($('#no_item_section').is(':visible')) {
            recalcNoItemGST();
            return;
        }
        if ($('#no_item_section').is(':visible')) {
            let amount = parseFloat($('#noitem_amount').val()) || 0;
            let isIGST = $('#edit_is_igst').is(':checked');
            let gstRate = parseFloat($('#noitem_gst_rate').val()) || 0;  // ✅ Use actual rate

            let cgst = 0, sgst = 0, igst = 0;

            if (amount > 0) {
                if (isIGST) {
                    igst = amount * gstRate / 100;
                } else {
                    cgst = amount * (gstRate / 2) / 100;
                    sgst = amount * (gstRate / 2) / 100;
                }
            }

            let total = amount + cgst + sgst + igst;

            // UI
            $('#sum_amount').text(fmt(amount));
            $('#sum_cgst').text(fmt(cgst));
            $('#sum_sgst').text(fmt(sgst));
            $('#sum_igst').text(fmt(igst));
            // $('#sum_grand_total').text(fmt(total));
            $('#sum_grand_total').text(fmt(total));
            // Hidden
            $('#edit_amount').val(amount);
            $('#edit_cgst').val(cgst);
            $('#edit_sgst').val(sgst);
            $('#edit_igst').val(igst);
            // $('#edit_total_amount').val(total);
            setRoundOffSummary(total);
            
            return;  // ✅ Don't continue to item calculation
        }
        // ... rest of recalcTotals for items
        let mode = $('#gst_calc_mode').val();
        let sumAmt=0, sumSgst=0, sumCgst=0, sumIgst=0, sumTotal=0;

        // Collect per-rate data for custom mode
        let rateMap = {}; // { '5': {amt,igst,cgst,sgst}, '18': {...}, ... }

        $('#editItemsBody tr').each(function () {
            let row     = $(this);
            let amt     = parseFloat(row.find('.item-amount').val()) || 0;
            let sgst    = parseFloat(row.find('.item-sgst').val())   || 0;
            let cgst    = parseFloat(row.find('.item-cgst').val())   || 0;
            let igst    = parseFloat(row.find('.item-igst').val())   || 0;
            let total   = parseFloat(row.find('.item-total').val())  || 0;
            let gstRate = row.find('.item-gst_rate').val() || '0';

            sumAmt   += amt;
            sumSgst  += sgst;
            sumCgst  += cgst;
            sumIgst  += igst;
            sumTotal += total;

            // Accumulate into rate bucket for custom mode
            if (!rateMap[gstRate]) rateMap[gstRate] = { amt:0, igst:0, cgst:0, sgst:0 };
            rateMap[gstRate].amt  += amt;
            rateMap[gstRate].igst += igst;
            rateMap[gstRate].cgst += cgst;
            rateMap[gstRate].sgst += sgst;
        });

        // Update hidden inputs (keep existing save working)
        $('#edit_amount').val(sumAmt.toFixed(2));
        $('#edit_sgst').val(sumSgst.toFixed(2));
        $('#edit_cgst').val(sumCgst.toFixed(2));
        $('#edit_igst').val(sumIgst.toFixed(2));
        // $('#edit_total_amount').val(sumTotal.toFixed(2));
        setRoundOffSummary(sumTotal);

        // Update visible summary
        $('#sum_amount').text(fmt(sumAmt));
        $('#foot_amount').text(fmt(sumAmt));
        $('#foot_total').text(fmt(sumTotal));
        // $('#sum_grand_total').text(fmt(sumTotal));
        setRoundOffSummary(sumTotal);
        // Renumber rows
        $('#editItemsBody tr').each(function(i) { $(this).find('.td-sr').text(i+1); });

        if (mode === 'standard') {
            toggleGSTLedger();
            $('#sum_sgst').text(fmt(sumSgst));
            $('#sum_cgst').text(fmt(sumCgst));
            $('#sum_igst').text(fmt(sumIgst));
        } else {
            // CUSTOM MODE: render rate-wise slots
            renderCustomSlots(rateMap, sumTotal);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CUSTOM MODE — render rate-wise slots
    // Each unique GST% from items gets one row with IGST/CGST/SGST
    // ledger dropdowns and auto-computed tax amounts.
    // ═══════════════════════════════════════════════════════════════
    function renderCustomSlots(rateMap, grandTotal) {
        let sGstLedgers = <?php echo json_encode($sGstLedgers ?? [], 15, 512) ?>;
        let cGstLedgers = <?php echo json_encode($cGstLedgers ?? [], 15, 512) ?>;
        let iGstLedgers = <?php echo json_encode($iGstLedgers ?? [], 15, 512) ?>;

        let allRates = Object.keys(rateMap).filter(r => {
            let data = rateMap[r] || {};
            return (parseFloat(data.amt) || 0) !== 0 ||
                (parseFloat(data.igst) || 0) !== 0 ||
                (parseFloat(data.cgst) || 0) !== 0 ||
                (parseFloat(data.sgst) || 0) !== 0;
        });

        // Build the slot table body
        let slotHtml = '';
        let customSgst=0, customCgst=0, customIgst=0;

        allRates.forEach(function(rate) {
            let data   = rateMap[rate] || { amt:0, igst:0, cgst:0, sgst:0 };
            let halfR  = parseFloat(rate) / 2;
            // Auto-compute: use sum from item recalc (standard) or allow manual override
            let igstAmt = data.igst;
            let cgstAmt = data.cgst;
            let sgstAmt = data.sgst;
            customIgst += igstAmt * 1;
            customCgst += cgstAmt * 1;
            customSgst += sgstAmt * 1;

            // console.log(igstAmt);
            // console.log(cgstAmt);
            // console.log(sgstAmt);
            let isZero = data.amt === 0;

            // Build ledger options
            let iOpts = iGstLedgers.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
            let cOpts = cGstLedgers.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
            let sOpts = sGstLedgers.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
            // <br><small style="font-size:9px;color:#6b7280;">Taxable: ${fmt(data.amt)}</small>
            slotHtml += `<tr class="${isZero ? 'zero-row' : ''}" data-rate="${rate}">
                <td><span class="rate-badge"><span class="slot-rate"></span>${rate}%</span></td>
                <td class="slot-taxable"><strong>${fmt(data.amt)}</strong></td>
                <td><select class="slot-igst-ledger" data-rate="${rate}""><option value="">— Ledger —</option>${iOpts}</select></td>
                <td><input type="number" class="slot-igst-amt" data-rate="${rate}" value="${igstAmt.toFixed(2)}" step="any"></td>
                <td><select class="slot-cgst-ledger" data-rate="${rate}"><option value="">— Ledger —</option>${cOpts}</select></td>
                <td><input type="number" class="slot-cgst-amt" data-rate="${rate}" value="${cgstAmt.toFixed(2)}" step="any"></td>
                <td><select class="slot-sgst-ledger" data-rate="${rate}"><option value="">— Ledger —</option>${sOpts}</select></td>
                <td><input type="number" class="slot-sgst-amt" data-rate="${rate}" value="${sgstAmt.toFixed(2)}" step="any"></td>
            </tr>`;
        });

        $('#customSlotsBody').html(slotHtml);

        // Render custom mode summary
        let customSummaryHtml = `
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value" id="txt_igst">${fmt(customIgst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value" id="txt_cgst">${fmt(customCgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value" id="txt_sgst">${fmt(customSgst)}</span></div>`;
        $('#custom_tax_rows').html(customSummaryHtml);

        // Update hidden fields for save
        $('#edit_igst').val(customIgst.toFixed(2));
        $('#edit_cgst').val(customCgst.toFixed(2));
        $('#edit_sgst').val(customSgst.toFixed(2));
        let total = parseFloat($('#edit_amount').val()) + customIgst + customCgst + customSgst;
        // $('#edit_total_amount').val(total.toFixed(2));
        // $('#sum_grand_total').text(fmt(total));
        setRoundOffSummary(total);
        setRoundOffSummary(total);
    }

    // When user manually edits a slot amount → recalc grand total
    $(document).on('input', '.slot-igst-amt, .slot-cgst-amt, .slot-sgst-amt', function () {
        let igst=0, cgst=0, sgst=0;
        $('.slot-igst-amt').each(function () { igst += parseFloat($(this).val())||0; });
        $('.slot-cgst-amt').each(function () { cgst += parseFloat($(this).val())||0; });
        $('.slot-sgst-amt').each(function () { sgst += parseFloat($(this).val())||0; });
        let base  = parseFloat($('#edit_amount').val())||0;
        let total = base + igst + cgst + sgst;
        $('#edit_igst').val(igst.toFixed(2));
        $('#edit_cgst').val(cgst.toFixed(2));
        $('#edit_sgst').val(sgst.toFixed(2));
        // $('#edit_total_amount').val(total.toFixed(2));
        // $('#sum_grand_total').text(fmt(total));
        setRoundOffSummary(total);
        setRoundOffSummary(total);
        let customSummaryHtml = `
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(igst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(cgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(sgst)}</span></div>`;
        $('#custom_tax_rows').html(customSummaryHtml);
    });

    // Get slot ledger selections for save payload
    function getCustomSlotData() {
        let slots = [];
        $('#customSlotsBody tr').each(function () {
            let r = $(this).data('rate');
            slots.push({
                rate:         r,
                igst_ledger:  $(this).find('.slot-igst-ledger').val(),
                igst_amount:  $(this).find('.slot-igst-amt').val(),
                cgst_ledger:  $(this).find('.slot-cgst-ledger').val(),
                cgst_amount:  $(this).find('.slot-cgst-amt').val(),
                sgst_ledger:  $(this).find('.slot-sgst-ledger').val(),
                sgst_amount:  $(this).find('.slot-sgst-amt').val(),
            });
        });
        return slots;
    }

    function openEditModal()    { document.getElementById('editModal').classList.add('show'); }

    function applyCustomGstSlots(customSlots) {
        if (!Array.isArray(customSlots) || customSlots.length === 0) return;

        customSlots.forEach(function (slot) {
            let rate = parseFloat(slot.gst_rate || slot.rate || 0);
            if (!rate) return;

            let row = $('#customSlotsBody tr').filter(function () {
                return parseFloat($(this).data('rate')) === rate;
            });

            if (!row.length) return;

            row.find('.slot-igst-ledger').val(slot.igst_ledger_id || '');
            row.find('.slot-cgst-ledger').val(slot.cgst_ledger_id || '');
            row.find('.slot-sgst-ledger').val(slot.sgst_ledger_id || '');

            if (slot.igst_amount !== null && slot.igst_amount !== undefined) {
                row.find('.slot-igst-amt').val(parseFloat(slot.igst_amount).toFixed(2));
            }
            if (slot.cgst_amount !== null && slot.cgst_amount !== undefined) {
                row.find('.slot-cgst-amt').val(parseFloat(slot.cgst_amount).toFixed(2));
            }
            if (slot.sgst_amount !== null && slot.sgst_amount !== undefined) {
                row.find('.slot-sgst-amt').val(parseFloat(slot.sgst_amount).toFixed(2));
            }
        });

        $('#customSlotsBody .slot-igst-amt, #customSlotsBody .slot-cgst-amt, #customSlotsBody .slot-sgst-amt').first().trigger('input');
    }


    function closeEditModal()   { 
        document.getElementById('editModal').classList.remove('show');  // 🔥 RESET STATE
        $('#updateRow').show();
        $('#addItemRow').show();

        $('#editModal input, #editModal select, #editModal textarea')
            .prop('disabled', false)
            .css('pointer-events', 'auto');

        $('.receipt-del-btn').show();
    }
    function openViewModal()    { document.getElementById('viewModal').classList.add('show'); }
    function closeViewModal()   { document.getElementById('viewModal').classList.remove('show'); }
    function openLedgerModal()  { document.getElementById('ledgerModal').classList.add('show'); }
    function closeLedgerModal() { document.getElementById('ledgerModal').classList.remove('show'); }
    window.onclick = e => {
        if (e.target === document.getElementById('ledgerModal')) closeLedgerModal();
    };

    function toggleGSTLedger() {
        let isIGST = $('#edit_is_igst').is(':checked');

        if (isIGST) {
            $('#igst_ledger').closest('.tax-row').show();
            $('#cgst_ledger').closest('.tax-row').hide();
            $('#sgst_ledger').closest('.tax-row').hide();
        } else {
            $('#igst_ledger').closest('.tax-row').hide();
            $('#cgst_ledger').closest('.tax-row').show();
            $('#sgst_ledger').closest('.tax-row').show();
        }
    }

    $('#edit_is_igst').on('change', function () {
        toggleGSTLedger();
        recalcTotals();
    });

    $(window).on('load', function () {
        // table dropdown
        // $('.ledgerSelect').select2({
        //     width: '100%'
        // });
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

        // modal dropdown
        $('#edit_party').select2({
            dropdownParent: $('#editModal'),
            width: '100%'
        });
    });

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

    function initItemSelect2() {
        $('.itemSelect').select2({
            width: '100%',
            dropdownParent: $('#editModal'), // 🔥 VERY IMPORTANT
            placeholder: "Search Item...",
            allowClear: true
        });
    }

    $('#noitem_gst_rate, #noitem_amount').on('input', function () {
        recalcNoItemGST();
    });

    function recalcNoItemGST() {

        let amount = parseFloat($('#noitem_amount').val()) || 0;
        let gstRate = parseFloat($('#noitem_gst_rate').val()) || 0;
        let isIgst = $('#edit_is_igst').is(':checked');
        let mode = $('#gst_calc_mode').val();

        let cgst = 0, sgst = 0, igst = 0;

        if (isIgst) {
            igst = amount * gstRate / 100;
        } else {
            cgst = amount * (gstRate / 2) / 100;
            sgst = amount * (gstRate / 2) / 100;
        }

        let total = amount + cgst + sgst + igst;

        // ✅ UI update
        $('#sum_amount').text(amount.toFixed(2));
        // $('#sum_cgst').text(cgst.toFixed(2));
        // $('#sum_sgst').text(sgst.toFixed(2));
        // $('#sum_igst').text(igst.toFixed(2));
        // $('#sum_grand_total').text(total.toFixed(2));

        // ✅ hidden fields (IMPORTANT for save + custom total base)
        $('#edit_amount').val(amount);
        $('#edit_cgst').val(cgst);
        $('#edit_sgst').val(sgst);
        $('#edit_igst').val(igst);
        // $('#edit_total_amount').val(total);
        setRoundOffSummary(total);

        if (mode === 'custom') {
            let key = gstRate > 0 ? gstRate.toString() : '0';
            let rateMap = {};
            rateMap[key] = { amt: amount, igst: igst, cgst: cgst, sgst: sgst };
            renderCustomSlots(rateMap, total);

            // Enable + highlight only selected GST row in no-item custom mode
            $('#customSlotsBody tr').each(function () {
                let row = $(this);
                let rowRate = parseFloat(row.data('rate')) || 0;
                let isActiveRate = Math.abs(rowRate - gstRate) < 0.0001 && amount > 0 && gstRate > 0;

                row.toggleClass('zero-row', !isActiveRate);
                row.find('input, select').prop('disabled', !isActiveRate);
            });
        } else {
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));
            // $('#sum_grand_total').text(total.toFixed(2));
            setRoundOffSummary(total);
        }

        // ✅ hidden fields (IMPORTANT for save)
        // $('#edit_amount').val(amount);
        // $('#edit_cgst').val(cgst);
        // $('#edit_sgst').val(sgst);
        // $('#edit_igst').val(igst);
        // $('#edit_total_amount').val(total);
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/transaction-processing/purchase/preview.blade.php ENDPATH**/ ?>