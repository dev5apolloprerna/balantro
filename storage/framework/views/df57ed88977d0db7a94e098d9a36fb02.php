
<?php $__env->startSection('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="container mx-auto">
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow border border-gray-200 dark:border-neutral-700">
        <!-- HEADER -->
        <div class="flex justify-between items-center px-5 py-3 border-b border-neutral-700">
            <div class="flex items-center gap-3">
                <button type="button"
                    onclick="window.history.back()"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 text-lg">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>

                <h2 class="text-gray-900 dark:text-white text-lg font-semibold">
                    Debit Notes Transactions
                </h2>

                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                    <?php echo e($rows->count()); ?>

                </span>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <?php if(session('client_name')): ?>
                <div class="bulk-client-name text-xl font-semibold text-green-600 whitespace-nowrap truncate max-w-[140px]" style="font-variant-caps: small-caps;">
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
                    Save
                </button>
            </div>
        </div>
        <!-- FILTERS -->
        <div class="flex flex-wrap gap-6 px-5 py-3 text-sm border-b border-neutral-700">
            <div>
                <div class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="flex gap-4 mt-2 text-gray-700 dark:text-gray-300 block">
                            Update Bulk Records
                        </label>
                        <select id="bulkColumn"
                            class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-white">
                            <option value="">Select Column</option>
                            <option value="party">Party Name</option>
                            <!-- <option value="ledger">Ledger</option> -->
                            <option value="place">Place Of Supply</option>
                            <option value="voucher">Voucher Type</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex gap-4 mt-2 text-gray-700 dark:text-gray-300 block">
                            Value
                        </label>
                        <select id="bulkValue"
                            class="bg-white placeSelect dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-white">
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
                <label class="flex flex-wrap gap-4 mt-2 text-gray-700 dark:text-gray-300">General Filters</label>
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

        <form id="salesForm">
            <?php echo csrf_field(); ?>
            <div class="debit-table-wrap overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 group-block">
                <table id="salesTable" class="debit-preview-table min-w-[1100px] w-full text-sm text-gray-700 dark:text-gray-300 border-collapse">
                    <colgroup>
                        <col class="col-select">
                        <col class="col-sr">
                        <col class="col-date">
                        <col class="col-reference">
                        <col class="col-voucher">
                        <col class="col-party">
                        <col class="col-gst">
                        <col class="col-place">
                        <col class="col-amount">
                        <col class="col-status">
                        <col class="col-action">
                    </colgroup>
                    <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-gray-900/40 text-xs text-gray-700 dark:text-gray-300 uppercase sticky top-0 z-10">
                        <tr>
                            <th class="w-8">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>SR</th>
                            <th>DATE</th>
                            <th>REFERENCE</th>
                            <th>VOUCHER</th>
                            <th>PARTY A/C NAME</th>
                            <th>GSTIN/UIN</th>
                            <th>PLACE</th>
                            <!-- <th>PARTICULARS</th> -->
                            <th class="text-right">AMOUNT</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                        <tr class="bg-white dark:bg-neutral-900">
                            <th></th>
                            <th></th>
                            <th>
                                <input class="searchInput" type="search" data-column="2" placeholder="Date" aria-label="Search date">
                            </th>
                            <th>
                                <input class="searchInput" type="search" data-column="3" placeholder="Ref" aria-label="Search reference">
                            </th>
                            <th>
                                <input class="searchInput" type="search" data-column="4" placeholder="Voucher" aria-label="Search voucher">
                            </th>
                            <th>
                                <input class="searchInput" type="search" data-column="5" placeholder="Party / Ledger" aria-label="Search party or ledger">
                            </th>
                            <th>
                                <input class="searchInput" type="search" data-column="6" placeholder="GSTIN" aria-label="Search GSTIN">
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="group transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] [&>*]:group-hover:text-black [&_*]:group-hover:text-black">
                            <td class="px-3 py-2">
                                <input type="checkbox" name="selected[]" value="<?php echo e($row->id); ?>">
                            </td>
                            <td>
                                <?php echo e($index+1); ?>

                            </td>
                            <td>
                                <input type="date"
                                    name="date[<?php echo e($row->id); ?>]"
                                    value="<?php echo e(\Carbon\Carbon::parse($row->note_date)->format('Y-m-d')); ?>"
                                    class="inputCell">
                            </td>
                            <td>
                                <input type="text"
                                    name="invoice_no[<?php echo e($row->id); ?>]"
                                    value="<?php echo e($row->note_no); ?>"
                                    class="inputCell">
                            </td>
                            <td>
                                <select name="voucher_type[<?php echo e($row->id); ?>]" class="inputCell voucherSelect">
                                    <?php $__currentLoopData = $vchTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vchType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($vchType); ?>"
                                        <?php echo e(strtolower(trim($vchType)) == strtolower(trim($row->vch_type))  ? 'selected' : ''); ?>><?php echo e($vchType); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td>
                                <!-- Party Name -->
                                <input type="text"
                                    name="party_name[<?php echo e($row->id); ?>]"
                                    value="<?php echo e($row->party_name); ?>"
                                    class="inputCell mb-1">
                                <!-- Ledger -->
                                <select name="ledger[<?php echo e($row->id); ?>]"
                                    class="ledgerSelect inputCell">
                                    <option value="">Select Ledger</option>
                                    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->name); ?>"
                                         <?php echo e(trim($row->purchase_ledger_name ?? $row->purchase_ledger) == trim($ledger->name)?'selected':''); ?>>
                                        <?php echo e($ledger->name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td>
                                <?php echo e($row->gst_no); ?>

                            </td>
                            <td>
                                <select name="place_of_supply[<?php echo e($row->id); ?>]"
                                    class="inputCell placeSelect">
                                    <option value="">Select State</option>
                                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($state); ?>"
                                        <?php echo e(strtolower(trim($state)) == strtolower(trim($row->place_of_supply)) ? 'selected':''); ?>>
                                        <?php echo e($state); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="text-right">
                                <?php echo e(number_format($row->total_amount,2)); ?>

                            </td>
                            <td>
                                <span class="text-yellow-400">
                                    <?php echo e($row->status); ?>

                                </span>
                            </td>
                            <td>
                                
                                <button type="button" class="viewRow text-green-400 hover:text-green-300" 
                                    title="View" data-id="<?php echo e($row->id); ?>">
                                    <i class="fa-solid fa-eye action-icon"></i>
                                </button>
                                <button type="button"
                                    class="text-blue-400 hover:text-blue-300 editRow"
                                    title="Edit"
                                    data-id="<?php echo e($row->id); ?>"
                                    data-invoice="<?php echo e($row->note_no); ?>"
                                    data-date="<?php echo e(\Carbon\Carbon::parse($row->note_date)->format('Y-m-d')); ?>"
                                    data-gst_no="<?php echo e($row->gst_no); ?>"
                                    data-vchtype="<?php echo e($row->vch_type); ?>"
                                    data-against_invoice="<?php echo e($row->against_invoice); ?>"
                                    data-purchase_ledger_name="<?php echo e($row->purchase_ledger_name); ?>"
                                    data-gst_rate = "<?php echo e($row->gst_rate); ?>"
                                    data-party="<?php echo e($row->party_name); ?>"
                                    data-place="<?php echo e($row->place_of_supply); ?>"
                                    data-ledger="<?php echo e($row->purchase_ledger_name ?? $row->purchase_ledger); ?>"
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
                <div class="mt-3">
                    <?php echo e($rows->links()); ?>

                </div>
            </div>
        </form>
    </div>
</div>


<div id="editModal" class="modal" style="display: none;">
    <div class="receipt-wrapper">
        <input type="hidden" id="edit_id">

        
        <div class="receipt-head">
            <div class="receipt-head-left">
                <div class="receipt-company">Debit Notes Bill</div>
                <div class="receipt-subtitle">Tax Invoice</div>
            </div>
            <div class="receipt-head-right">
                <button type="button" class="receipt-close-btn" onclick="closeEditModal()">✕</button>
            </div>
        </div>

        
        <div class="receipt-meta-grid">
            
            <div class="receipt-meta-block">
                <div class="receipt-block-title"><i class="fa-solid fa-building text-blue-400 mr-1"></i> Supplier Details</div>
                <div class="receipt-field-row" id="header_party_row">
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
                <div class="receipt-field-row" id="header_purchase_ledger_row">
                    <label>Purchase Ledger</label>
                    <select id="noitem_purchase_ledger" class="receipt-input ledgerSelect">
                        <option value="">Select Ledger</option>
                        <?php $__currentLoopData = $purchaseLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->name); ?>"><?php echo e($ledger->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="receipt-field-row">
                    <label>Debit Notes No.</label>
                    <input type="text" id="edit_invoice" class="receipt-input" placeholder="Debit Notes Number">
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
                <div class="receipt-field-row">
                    <label>Against Invoice</label>
                    <input type="text" id="edit_against_invoice" class="receipt-input" placeholder="Invoice Number">
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
                    <button type="button" id="addNoItemRow" class="receipt-add-btn" style="margin-top:8px;">
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

            <div id="no_item_section" style="display:none; padding:10px;">
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
                
                <input type="hidden" id="noitem_gst_rate" value="0">
                <input type="hidden" id="noitem_amount">
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
            <div class="receipt-footer-note">This is a computer-generated Debit Notes record.</div>
            <div class="receipt-footer-actions">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                <button type="button" id="updateRow" class="submit-btn">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Update
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


<div id="viewModal" class="modal" style="display: none;">
    <div class="modal-content" style="width:780px;">
        <div class="modal-header">
            <h3>View Debit Notes</h3>
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
                    <div><label>Debit Notes Ledger</label><p id="v_ledger"></p></div>
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
    .debit-table-wrap { width:100%; }
    .debit-preview-table { width:100%; table-layout:fixed; min-width:1040px; }
    .debit-preview-table th,
    .debit-preview-table td { vertical-align:top; overflow-wrap:anywhere; }
    .debit-preview-table th { white-space:normal; line-height:1.15; }
    /* .debit-preview-table td { padding-left:.45rem; padding-right:.45rem; } */
    .debit-preview-table .col-select { width:34px; }
    .debit-preview-table .col-sr { width:42px; }
    .debit-preview-table .col-date { width:105px; }
    .debit-preview-table .col-reference { width:100px; }
    .debit-preview-table .col-voucher { width:100px; }
    .debit-preview-table .col-party { width:210px; }
    .debit-preview-table .col-gst { width:138px; }
    .debit-preview-table .col-place { width:140px; }
    .debit-preview-table .col-amount { width:100px; }
    .debit-preview-table .col-status { width:65px; }
    .debit-preview-table .col-action { width:88px; }
    .debit-preview-table .inputCell { min-width:0; height:32px; }
    .debit-preview-table input[type="date"].inputCell { min-width:106px; padding-left:5px; padding-right:3px; }
    .debit-preview-table .voucherSelect { min-width:0; }
    .debit-preview-table select[name^="place_of_supply"] { min-width:112px; }
    .debit-preview-table .select2-container { max-width:100%; }
    .debit-preview-table .select2-selection__rendered { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .searchInput { width:100%; min-width:0; height:28px; padding:4px 6px; border-radius:4px; font-size:11px; }
    .searchInput::placeholder { color:#9ca3af; }
    @media (max-width: 1280px) {
        .container.mx-auto { max-width:100%; padding-left:.5rem; padding-right:.5rem; }
        .debit-preview-table { min-width:980px; font-size:12px; }
        .debit-preview-table .col-party { width:235px; }
        .debit-preview-table .col-gst { width:124px; }
        .debit-preview-table .col-place { width:118px; }
        .debit-preview-table td, .debit-preview-table th { padding-left:.35rem; padding-right:.35rem; }
    }
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
    .gst-summary-title { margin-top:4px; padding:2px 0; font-size:11px; font-weight:700; color:#1e40af; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #c7d2fe; }
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
    .select2-container--default .select2-results__option {
        background: #ffffff !important;
        color: #000000 !important;
    }

    .select2-container--default .select2-results__option--highlighted {
        background: #2563eb !important; /* blue highlight */
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
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#selectAll').click(function() {
            $('tbody input[type=checkbox]').prop('checked', this.checked);
        });
        const normalizeTableSearchText = function(value) {
            value = (value || '').toString().toLowerCase().trim();
            return value + ' ' + value.replace(/-/g, '/') + ' ' + value.split('-').reverse().join('-');
        };

        $('.searchInput').on('input keyup', function() {
            let column = Number($(this).data('column'));
            let value = normalizeTableSearchText($(this).val());
            $('#salesTable tbody tr').each(function() {
                let cell = $(this).find('td').eq(column);
                let text = normalizeTableSearchText(cell.text());

                cell.find('input,select').each(function() {
                    text += ' ' + normalizeTableSearchText($(this).val());
                    text += ' ' + normalizeTableSearchText($(this).find('option:selected').text());
                });

                $(this).toggle(text.indexOf(value) > -1);
            });
        });


        $('.placeSelect').select2({
            width: '100%',
            placeholder: "Search Place...",
            allowClear: true,
            dropdownAutoWidth: true
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
                closeLedgerModal();
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

    const ledgers = <?php echo json_encode(collect($ledgers)->pluck('name'), 15, 512) ?>;
    const PARTY_LEDGER_DETAILS = <?php echo json_encode($partyLedgerDetails ?? [], 15, 512) ?>;
    const states = <?php echo json_encode($states, 15, 512) ?>;
    const vouchers = <?php echo json_encode($vchTypes, 15, 512) ?>;
    let IGST_LEDGERS = <?php echo json_encode($iGstLedgers, 15, 512) ?>;
    let CGST_LEDGERS = <?php echo json_encode($cGstLedgers, 15, 512) ?>;
    let SGST_LEDGERS = <?php echo json_encode($sGstLedgers, 15, 512) ?>;
    const ITEM_MASTER = <?php echo json_encode($stockItems, 15, 512) ?>;
    const PURCHASE_LEDGERS = <?php echo json_encode($purchaseLedgers ?? [], 15, 512) ?>;
    const PURCHASE_GST_MAPPINGS = <?php echo json_encode($purchaseGstMappings ?? [], 15, 512) ?>;

    function normalizeName(value) {
        return String(value || '').replace(/['"]/g, '').trim().toLowerCase();
    }

    function findPartyLedgerDetails(ledgerValue = '', ledgerText = '') {
        return PARTY_LEDGER_DETAILS.find(ledger =>
            String(ledger.id || '') === String(ledgerValue || '') ||
            normalizeName(ledger.name) === normalizeName(ledgerValue) ||
            normalizeName(ledger.name) === normalizeName(ledgerText)
        ) || null;
    }

    function applyPartyLedgerDetails(ledgerValue = '', ledgerText = '') {
        const details = findPartyLedgerDetails(ledgerValue, ledgerText);
        if (!details) return;

        $('#edit_gst').val(details.gst_no || '');
        $('#edit_address').val(details.address || '');
        $('#edit_pincode').val(details.pincode || '');
        $('#edit_city').val(details.city || '');
        if (details.state) {
            $('#edit_place').val(details.state).trigger('change');
        }
    }

    $(document).on('change', '#edit_party', function () {
        applyPartyLedgerDetails($(this).val(), $(this).find('option:selected').text());
    });

    function purchaseLedgerId(ledger) {
        return ledger?.id || ledger?.iLedgerId || ledger?.name || ledger?.strCustomerName || '';
    }

    function purchaseLedgerName(ledger) {
        return ledger?.name || ledger?.strCustomerName || ledger?.strLedgerName || '';
    }

    function buildPurchaseLedgerOptions(selected = '') {
        let html = '<option value="">Select Ledger</option>';
        PURCHASE_LEDGERS.forEach(ledger => {
            const id = purchaseLedgerId(ledger);
            const name = purchaseLedgerName(ledger);
            const isSelected = String(selected || '') === String(id || '') || normalizeName(selected) === normalizeName(name);
            html += `<option value="${id}" ${isSelected ? 'selected' : ''}>${name}</option>`;
        });
        return html;
    }

    function findPurchaseLedgerMapping(ledgerId = '', ledgerName = '') {
        return PURCHASE_GST_MAPPINGS.find(mapping =>
            String(mapping.id || '') === String(ledgerId || '') ||
            normalizeName(mapping.name) === normalizeName(ledgerId) ||
            normalizeName(mapping.name) === normalizeName(ledgerName)
        ) || null;
    }

    function getPurchaseLedgerMapping(ledgerId = '', ledgerName = '') {
        return findPurchaseLedgerMapping(ledgerId, ledgerName) || {};
    }

    function mappedGstLedgerId(mapping, key) {
        return mapping && mapping[key] ? String(mapping[key]) : '';
    }

    function generateSlotsFromNoItemRows() {
        const slots = [];
        const isIgst = $('#edit_is_igst').is(':checked');

        collectNoItemRows().forEach(row => {
            const amount = parseFloat(row.amount) || 0;
            const rate = parseFloat(row.gst) || 0;
            const mapping = getPurchaseLedgerMapping(row.ledger, row.ledger_name);
            const gstAmount = amount * rate / 100;

            slots.push({
                gst_rate: rate,
                taxable: amount,
                amount: amount,
                ledger_id: row.ledger,
                ledger_name: row.ledger_name,
                igst_ledger_id: mappedGstLedgerId(mapping, 'igst_id'),
                cgst_ledger_id: mappedGstLedgerId(mapping, 'cgst_id'),
                sgst_ledger_id: mappedGstLedgerId(mapping, 'sgst_id'),
                igst_amount: isIgst ? gstAmount : 0,
                cgst_amount: isIgst ? 0 : gstAmount / 2,
                sgst_amount: isIgst ? 0 : gstAmount / 2,
            });
        });

        renderCustomSlots(slots);
    }

    function updateHeaderPurchaseLedgerVisibility() {
        const shouldHide = $('#gst_calc_mode').val() === 'custom' && $('#no_item_section').is(':visible');
        $('#header_purchase_ledger_row').toggle(!shouldHide);
    }

    function addNoItemRow(data = {}) {
        const row = `
            <tr>
                <td><select class="receipt-input noitem-ledger">${buildPurchaseLedgerOptions(data.ledger || data.ledger_name || '')}</select></td>
                <td><input type="number" class="receipt-input noitem-gst" value="${data.gst || 0}" step="any"></td>
                <td><input type="number" class="receipt-input noitem-amount" value="${data.amount || ''}" step="any"></td>
                <td><button type="button" class="receipt-del-btn removeNoItem">x</button></td>
            </tr>
        `;
        $('#noItemBody').append(row);
    }

    function syncEntryMode(hasItems, readonly = false) {
        if (hasItems) {
            $('#standard_items_section').show();
            $('#no_item_section').hide();
            $('#addItemRow').toggle(!readonly);
        } else {
            $('#standard_items_section').hide();
            $('#no_item_section').show();
            $('#addItemRow').hide();
            $('#addNoItemRow').toggle(!readonly);
            $('#noItemBody input, #noItemBody select, #noItemBody button')
                .prop('disabled', readonly)
                .css('pointer-events', readonly ? 'none' : 'auto');
            $('#noItemBody .removeNoItem').toggle(!readonly);
        }
        updateHeaderPurchaseLedgerVisibility();
    }

    function collectNoItemRows() {
        const rows = [];
        $('#noItemBody tr').each(function () {
            const row = $(this);
            const ledger = row.find('.noitem-ledger').val();
            const ledgerName = row.find('.noitem-ledger option:selected').text();
            const gst = parseFloat(row.find('.noitem-gst').val()) || 0;
            const amount = parseFloat(row.find('.noitem-amount').val()) || 0;
            if (ledger && amount > 0) {
                rows.push({ ledger, ledger_name: ledgerName, gst, amount });
            }
        });
        return rows;
    }

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
            showToast('Select column and value','error');
            return;
        }
        // find selected rows
        let rows = $('tbody input[type=checkbox]:checked').closest('tr');
        // if none selected -> apply to all rows
        if (rows.length === 0) {
            rows = $('#salesTable tbody tr');
        }
        rows.each(function() {
            let row = $(this);
            if (column === 'party') {
                row.find('input[name^="party_name"]').val(value);
                row.find('select[name^="ledger"]').val(value).trigger('change'); // sync
            }

            if (column === 'ledger') {
                row.find('select[name^="ledger"]').val(value).trigger('change');

                // 🔥 IMPORTANT: update party also
                row.find('input[name^="party_name"]').val(value);
            }

            if (column === 'place') {
                row.find('select[name^="place_of_supply"]').val(value);
            }
            if (column === 'voucher') {
                //row.find('.voucherSelect').val(value);
                row.find('.voucherSelect').val(value).trigger('change');
            }
        });
    });

    $('#saveBtn').click(function() {
        let formData = $('#salesForm').serialize();
        $.ajax({
            url: "<?php echo e(route('dn.save')); ?>",
            type: "POST",
            data: formData,
            success: function(response) {
                 if (response.status === false) {
                    showToast(response.message || 'Unable to save selected rows.','error');
                    return;
                }

                showToast(response.message || 'Saved Successfully', 'success');
                location.reload(); // reload page and refresh table
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Error saving data','error');    
            }
        });
    });

    $(document).on('click', '.deleteRow', function() {
        let id = $(this).data('id');
        if (!confirm('Delete this row?')) return;
        $.ajax({
            url: "<?php echo e(route('dn.delete', ':id')); ?>".replace(':id', id),
            type: "POST",
            data: {
                _token: "<?php echo e(csrf_token()); ?>"
            },
            success: function(response) {
                showToast('Deleted Successfully', 'success');
                location.reload();
            },
            error: function() {
                showToast('Delete failed','error');
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
        $('#salesTable tbody tr').each(function () {
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

    $(document).on('change', 'select[name^="ledger"]', function () {
        let value = $(this).val();
        let row = $(this).closest('tr');

        row.find('input[name^="party_name"]').val(value);
    });

    // ═══════════════════════════════════════════════════════════════════════
    // VIEW MODAL
    // ═══════════════════════════════════════════════════════════════════════
    $(document).on('click', '.viewRow', function () {
        let id = $(this).data('id');

        // Open same edit modal
        openEditModal();

        // Hide update button
        $('#addItemRow').hide();
        $('#updateRow').hide();
        $(document).find('.receipt-del-btn').hide();

        // Disable all inputs
        $('#editModal input, #editModal select, #editModal textarea')
            .prop('disabled', true)
            .css('pointer-events', 'none');

        // Load data
        $.ajax({
            url: "<?php echo e(route('dn.show', ':id')); ?>".replace(':id', id),
            type: "GET",
            success: function (res) {
                // Fill header fields
                $('#edit_id').val(res.id);
                $('#edit_invoice').val(res.note_no);
                $('#edit_date').val(res.note_date);
                $('#edit_gst').val(res.gst_no);
                // $('#edit_party').val(res.party_name);
                $('#edit_party').val(res.party_name).trigger('change'); // 🔥 IMPORTANT
                $('#edit_place').val(res.place_of_supply);
                $('#edit_voucher_type').val(res.vch_type);
                $('#edit_address').val(res.address);
                $('#edit_pincode').val(res.pincode);
                $('#edit_city').val(res.city);
                // $('#edit_is_igst').val(res.is_igst);
                $('#edit_is_igst').prop('checked', res.is_igst == 1);
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                $('#edit_amount').val(res.taxable_amount);
                
                // 🔥 ADD THIS
                
                $('#edit_cgst').val(res.cgst);
                $('#edit_sgst').val(res.sgst);
                $('#edit_igst').val(res.igst);
                $('#edit_total_amount').val(res.total_amount);

                $('#cgst_ledger').val(res.cgst_id).trigger('change');
                $('#sgst_ledger').val(res.sgst_id).trigger('change');
                $('#igst_ledger').val(res.igst_id).trigger('change');

                // UI update
                $('#sum_amount').text(parseFloat(res.taxable_amount).toFixed(2));
                $('#sum_cgst').text(parseFloat(res.cgst).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst).toFixed(2));
                // $('#sum_grand_total').text(parseFloat(res.total_amount).toFixed(2));
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                $('#edit_remarks').val(res.remarks);
                $('#noitem_purchase_ledger').val(res.purchase_ledger_name).trigger('change');
                $('#edit_against_invoice').val(res.against_invoice);
                $('#noitem_gst_rate').val(res.gst_rate);
                // Items
                let tbody = $('#editItemsBody').empty();
                const hasItems = res.items && res.items.length > 0;
                if (hasItems) {

                    syncEntryMode(true, true);
                    // (res.items || []).forEach(item => {
                    //     let row = $(buildItemRow(item));
                    //     // hide delete button in each row
                    //     row.find('.receipt-del-btn').hide();
                    //     // disable inputs inside row
                    //     row.find('input').prop('disabled', true);
                    //     tbody.append(row);
                    // });
                    res.items.forEach(item => {
                        let row = $(buildItemRow(item));
                        tbody.append(row);
                        row.find('select').prop('disabled', true);
                        row.find('input').prop('disabled', true);
                        row.find('button').prop('disabled', true);
                        // 🔥 APPLY SELECT2 HERE
                        row.find('.itemSelect').select2({
                            width: '100%',
                            placeholder: "Search Item...",
                            allowClear: true
                        });
                    });
                } else {
                    syncEntryMode(false, true);

                    $('#noItemBody').empty();
                    const noItemRows = (res.noitem_rows && res.noitem_rows.length)
                        ? res.noitem_rows
                        : [{
                            ledger: res.purchase_ledger_id || res.purchase_ledger_name || '',
                            ledger_name: res.purchase_ledger_name || '',
                            gst: res.gst_rate || '',
                            amount: res.taxable_amount || res.total_amount || 0
                        }];
                    noItemRows.forEach(row => addNoItemRow(row));
                    $('#noitem_gst_rate').val(noItemRows[0]?.gst || res.gst_rate || '');
                    $('#noitem_amount').val(noItemRows[0]?.amount || res.taxable_amount || 0);
                }       
                if (res.gst_mode === 'custom') {
                    if (res.custom_gst && res.custom_gst.length) {
                        renderCustomSlots(res.custom_gst);
                    } else if (!hasItems) {
                        generateSlotsFromNoItemRows();
                    }
                }
                syncEntryMode(hasItems, true);

                recalcTotals();
            }
        });
    });

    function openViewModal()  { document.getElementById('viewModal').classList.add('show'); }
    function closeViewModal() { document.getElementById('viewModal').classList.remove('show'); }
    
    // ═══════════════════════════════════════════════════════════════════════
    // EDIT MODAL
    // ═══════════════════════════════════════════════════════════════════════
   // ═══════ EDIT MODAL ═══════
    $(document).on('click', '.editRow', function () {
        let btn = $(this), id = btn.data('id');

        $('#updateRow').show();
        $('#addItemRow').show();

        $('#editModal input, #editModal select, #editModal textarea')
            .prop('disabled', false)
            .css('pointer-events', 'auto');

        $('.receipt-del-btn').show();
        // Reset mode to standard
        // $('#gst_calc_mode').val('standard').trigger('change');
        $('#edit_id').val(id);
        $('#edit_invoice').val(btn.data('invoice'));
        $('#edit_date').val(btn.data('date'));
        $('#edit_gst').val(btn.data('gst_no'));
        $('#edit_against_invoice').val(btn.data('against_invoice'));
        // $('#edit_voucher_type').val(btn.data('vchtype'));
        $('#edit_party').val(btn.data('party'));
        // $('#edit_place').val(btn.data('place'));
        $('#edit_ledger').val(btn.data('ledger'));
        $('#noitem_purchase_ledger').val(btn.data('purchase_ledger_name')).trigger('change');
        $('#noitem_gst_rate').val(btn.data('gst_rate'));
        let party = btn.data('party');
        $('#edit_party').val(party).trigger('change'); // 🔥 IMPORTANT
        // $('#edit_place').val(btn.data('place'));
        let vch = btn.data('vchtype');
        $('#edit_voucher_type option').each(function () {
            if ($(this).val().toLowerCase().trim() === String(vch).toLowerCase().trim()) {
                $(this).prop('selected', true);
            }
        });

        // Place of Supply (case-insensitive match)
        let place = btn.data('place');
        $('#edit_place option').each(function () {
            if ($(this).val().toLowerCase().trim() === String(place).toLowerCase().trim()) {
                $(this).prop('selected', true);
            }
        });
        $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">Loading…</td></tr>');
        openEditModal();

        $.ajax({
            url: "<?php echo e(route('dn.show',':id')); ?>".replace(':id', id), type:"GET",
            success: function (res) {
                $('#edit_address').val(res.address || '');
                $('#edit_pincode').val(res.pincode || '');
                $('#edit_city').val(res.city || '');
                $('#edit_remarks').val(res.remarks || '');
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                $('#edit_amount').val(res.taxable_amount);
                $('#edit_cgst').val(res.cgst);
                $('#edit_sgst').val(res.sgst);
                $('#edit_igst').val(res.igst);
                $('#edit_total_amount').val(res.total_amount);
                $('#edit_is_igst').prop('checked', res.is_igst == 1);
                $('#noitem_gst_rate').val(res.gst_rate);

               
                $('#cgst_ledger').val(res.cgst_id).trigger('change');
                $('#sgst_ledger').val(res.sgst_id).trigger('change');
                $('#igst_ledger').val(res.igst_id).trigger('change');
                // UI
                $('#sum_amount').text(parseFloat(res.taxable_amount).toFixed(2));
                $('#sum_cgst').text(parseFloat(res.cgst).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst).toFixed(2));
                // $('#sum_grand_total').text(parseFloat(res.total_amount).toFixed(2));
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
                let tbody = $('#editItemsBody').empty();
                const hasItems = res.items && res.items.length > 0;
                // (res.items || []).forEach(item => tbody.append(buildItemRow(item)));
                if (hasItems) {
                    syncEntryMode(true, false);
                    // res.items.forEach(item => tbody.append(buildItemRow(item)));
                    res.items.forEach(item => {
                        let row = $(buildItemRow(item));
                        tbody.append(row);
                        // 🔥 APPLY SELECT2 HERE
                        row.find('.itemSelect').select2({
                            width: '100%',
                            placeholder: "Search Item...",
                            allowClear: true
                        });
                    });
                } else {
                    syncEntryMode(false, false);
                    $('#noItemBody').empty();
                    const noItemRows = (res.noitem_rows && res.noitem_rows.length)
                        ? res.noitem_rows
                        : [{
                            ledger: res.purchase_ledger_id || res.purchase_ledger_name || '',
                            ledger_name: res.purchase_ledger_name || '',
                            gst: res.gst_rate || '',
                            amount: res.taxable_amount || res.total_amount || 0
                        }];
                    noItemRows.forEach(row => addNoItemRow(row));
                    $('#noitem_gst_rate').val(noItemRows[0]?.gst || res.gst_rate || '');
                    $('#noitem_amount').val(noItemRows[0]?.amount || res.taxable_amount || 0);
                    $('#noitem_purchase_ledger').val(res.purchase_ledger_name);
                    tbody.html(''); // clear table
                }

                if (res.gst_mode === 'custom') {
                    if (res.custom_gst && res.custom_gst.length) {
                        renderCustomSlots(res.custom_gst);
                    } else if (!hasItems) {
                        generateSlotsFromNoItemRows();
                    }
                } 
                syncEntryMode(hasItems, false);
                // else {
                    recalcTotals();
                //}

                // if (!res.items || !res.items.length) {
                //     tbody.html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">No items — click Add Row</td></tr>');
                // }
                // recalcTotals();
            },
            error: () => $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-3" style="color:#ef4444;">Failed to load.</td></tr>')
        });
    });

    function renderCustomSlots(slots) {
        let tbody = $('#customSlotsBody');
        tbody.empty();

        //let totalAmount = parseFloat($('#noitem_amount').val()) || 0;
        let totalAmount = 0;

        $('#editItemsBody tr').each(function () {
            let qty  = parseFloat($(this).find('.item-qty').val()) || 0;
            let rate = parseFloat($(this).find('.item-rate').val()) || 0;

            totalAmount += qty * rate;
        });

        let totalCgst = 0, totalSgst = 0, totalIgst = 0;

        slots.forEach(function (slot) {
            const slotMapping = getPurchaseLedgerMapping(slot.ledger_id || slot.purchase_ledger_id || '', slot.ledger_name || slot.purchase_ledger_name || '');
            const selectedIgstLedger = slot.igst_ledger_id || slot.igst_id || mappedGstLedgerId(slotMapping, 'igst_id');
            const selectedCgstLedger = slot.cgst_ledger_id || slot.cgst_id || mappedGstLedgerId(slotMapping, 'cgst_id');
            const selectedSgstLedger = slot.sgst_ledger_id || slot.sgst_id || mappedGstLedgerId(slotMapping, 'sgst_id');
            // let taxable = parseFloat(slot.taxable);
            // if (!taxable || taxable === 0) {
            //     taxable = totalAmount;
            // }

            let taxable = 0;

            $('#editItemsBody tr').each(function () {
                let rate = parseFloat($(this).find('.item-gst_rate').val()) || 0;

                if (rate == slot.gst_rate) {
                    let qty  = parseFloat($(this).find('.item-qty').val()) || 0;
                    let price = parseFloat($(this).find('.item-rate').val()) || 0;

                    taxable += qty * price;
                }
            });

            if (!taxable && $('#no_item_section').is(':visible')) {
                taxable = parseFloat(slot.taxable || slot.amount || 0) || 0;
            }
            if ($('#no_item_section').is(':visible')) {
                totalAmount += taxable;
            }

            let cgst = parseFloat(slot.cgst_amount) || 0;
            let sgst = parseFloat(slot.sgst_amount) || 0;
            let igst = parseFloat(slot.igst_amount) || 0;

            totalCgst += cgst;
            totalSgst += sgst;
            totalIgst += igst;

            // 🔥 CREATE ROW
            let row = `
                <tr data-rate="${slot.gst_rate}">
                    <td><span class="rate-badge">${slot.gst_rate}%</span></td>
                    <td style="color: black;" class="slot-taxable">${taxable.toFixed(2)}<input type="hidden" class="slot-purchase-ledger-id" value="${slot.ledger_id || slot.purchase_ledger_id || ''}"></td>

                    <td style="color: black;">
                        <select class="slot-igst-ledger" data-rate="${slot.gst_rate}">
                            <option value="">Select</option>
                            ${getLedgerOptions(IGST_LEDGERS, selectedIgstLedger)}
                        </select>
                    </td>
                    <td style="color: black;"><input type="number" class="slot-igst-amt" data-rate="${slot.gst_rate}" value="${igst.toFixed(2)}" step="any" style="width:100%; padding:2px 4px; border:1px solid #d1d5db; border-radius:3px;"></td>

                    <td>
                        <select class="slot-cgst-ledger" data-rate="${slot.gst_rate}">
                            <option value="">Select</option>
                            ${getLedgerOptions(CGST_LEDGERS, selectedCgstLedger)}
                        </select>
                    </td>
                    <td style="color: black;"><input type="number" class="slot-cgst-amt" data-rate="${slot.gst_rate}" value="${cgst.toFixed(2)}" step="any" style="width:100%; padding:2px 4px; border:1px solid #d1d5db; border-radius:3px;"></td>

                    <td>
                        <select class="slot-sgst-ledger" data-rate="${slot.gst_rate}">
                            <option value="">Select</option>
                            ${getLedgerOptions(SGST_LEDGERS, selectedSgstLedger)}
                        </select>
                    </td>
                    <td style="color: black;"><input type="number" class="slot-sgst-amt" data-rate="${slot.gst_rate}" value="${sgst.toFixed(2)}" step="any" style="width:100%; padding:2px 4px; border:1px solid #d1d5db; border-radius:3px;"></td>
                </tr>
                `;

            // 🔥 APPEND (MOST IMPORTANT)
            tbody.append(row);
        });

        // totals
        $('#sum_amount').text(totalAmount.toFixed(2));
        $('#sum_cgst').text(totalCgst.toFixed(2));
        $('#sum_sgst').text(totalSgst.toFixed(2));
        $('#sum_igst').text(totalIgst.toFixed(2));
        let grand = totalAmount + totalCgst + totalSgst + totalIgst;
        // $('#sum_grand_total').text(grand.toFixed(2));
        setRoundOffSummary(grand);
        $('#edit_amount').val(totalAmount.toFixed(2));
        $('#edit_cgst').val(totalCgst.toFixed(2));
        $('#edit_sgst').val(totalSgst.toFixed(2));
        $('#edit_igst').val(totalIgst.toFixed(2));
        // $('#edit_total_amount').val(grand.toFixed(2));
        // $('#sum_grand_total').text(grand.toFixed(2));
        setRoundOffSummary(grand);
    }

    function getLedgerOptions(list, selectedId = '') {
        return list.map(l => 
            `<option value="${l.id}" ${l.id == selectedId ? 'selected' : ''}>${l.name}</option>`
        ).join('');
    }

    function buildLedgerOptions(list, selectedId) {
        return list.map(l => 
            `<option value="${l.id}" ${l.id == selectedId ? 'selected' : ''}>${l.name}</option>`
        ).join('');
    }

    // ═══════ ADD ITEM ROW ═══════
    $('#addItemRow').click(function () {
        // Remove "no items" placeholder if present
        if ($('#editItemsBody tr td[colspan]').length) $('#editItemsBody').empty();
        //$('#editItemsBody').append(buildItemRow({}));
        let row = $(buildItemRow({}));
        $('#editItemsBody').append(row);

        row.find('.itemSelect').select2({
            width: '100%',
            placeholder: "Search Item...",
            allowClear: true
        });
        if ($('#gst_calc_mode').val() === 'custom') {
            generateSlotsFromItems();   // 🔥 ADD THIS
        }
        recalcTotals();
        applyStandardItemGstLedgerMapping();
    });

    // ═══════ LIVE RECALC ON INPUT ═══════
    // $(document).on('input', '#editItemsBody input', function () {
    $(document).on('input change', '#editItemsBody input, #editItemsBody select', function () {
        recalcItemRow($(this).closest('tr'));
        if ($('#gst_calc_mode').val() === 'custom') {
            generateSlotsFromItems();   // 🔥 ADD THIS
        }
        recalcTotals();
        applyStandardItemGstLedgerMapping();
    });

    // ═══════ REMOVE ROW ═══════
    $(document).on('click', '#addNoItemRow', function () {
        addNoItemRow();
        if ($('#gst_calc_mode').val() === 'custom') {
            generateSlotsFromNoItemRows();
        }
        recalcTotals();
    });

    $(document).on('click', '.removeNoItem', function (e) {
        e.stopPropagation();
        $(this).closest('tr').remove();
        if ($('#gst_calc_mode').val() === 'custom') {
            generateSlotsFromNoItemRows();
        }
        recalcTotals();
    });

    $(document).on('input change', '.noitem-ledger,.noitem-gst,.noitem-amount', function () {
        if ($('#gst_calc_mode').val() === 'custom') {
            generateSlotsFromNoItemRows();
        }
        recalcTotals();
    });

    $(document).on('click', '.removeItemRow', function () {
        $(this).closest('tr').remove();
        if ($('#gst_calc_mode').val() === 'custom') {
            generateSlotsFromItems();   // 🔥 ADD THIS
        }
        recalcTotals();
    });

    // ═══════ GST MODE SWITCH ═══════
    $('#gst_calc_mode').on('change', function () {
        let mode = $(this).val();
        if (mode === 'standard') {
            if ($('#no_item_section').is(':visible')) {
                $('#standard_items_section').hide();
            } else {
                $('#standard_items_section').show();
            }
            $('#standard_tax_rows').show();
            $('#custom_slots_section').hide();
            $('#custom_tax_rows').hide();
            $('#igst_toggle_wrap').show();
        } else {
            if ($('#no_item_section').is(':visible')) {
                $('#standard_items_section').hide();
            } else {
                $('#standard_items_section').show();
            }
            $('#standard_tax_rows').hide();
            $('#custom_slots_section').show();
            $('#custom_tax_rows').show();
            $('#igst_toggle_wrap').hide();
            if ($('#no_item_section').is(':visible')) {
                generateSlotsFromNoItemRows();
            }
        }
        recalcTotals();
        updateHeaderPurchaseLedgerVisibility();
        applyStandardItemGstLedgerMapping();
    });

    $('#edit_is_igst').on('change', function () {

        // 🔥 Recalculate each row GST
        $('#editItemsBody tr').each(function () {
            recalcItemRow($(this));
        });
        // 🔥 Then update totals
        if ($('#gst_calc_mode').val() === 'custom' && $('#no_item_section').is(':visible')) {
            generateSlotsFromNoItemRows();
        }
        recalcTotals();
    });

    // ── Save (Update) ────────────────────────────────────────────────────
    $('#updateRow').click(function () {
        let items = [];

        if ($('#no_item_section').is(':visible')) {
            items = []; // no items case
            // let amount = parseFloat($('#noitem_amount').val()) || 0;
            // let isIGST = $('#edit_is_igst').is(':checked');

            //let cgst = 0, sgst = 0, igst = 0;

            // Default GST % (you can make dynamic later)
            // let gstRate = 18;

            // if (isIGST) {
            //     igst = amount * gstRate / 100;
            // } else {
            //     cgst = amount * (gstRate / 2) / 100;
            //     sgst = amount * (gstRate / 2) / 100;
            // }
            
            // let total = amount + cgst + sgst + igst;
            // let cgst = parseFloat($('#edit_cgst').val()) || 0;
            // let sgst = parseFloat($('#edit_sgst').val()) || 0;
            // let igst = parseFloat($('#edit_igst').val()) || 0;
            let amount = parseFloat($('#noitem_amount').val()) || 0;
            let isIGST = $('#edit_is_igst').is(':checked');

            let gstRate = 18; // 🔥 or dynamic later

            let cgst = 0, sgst = 0, igst = 0;

            if (isIGST) {
                igst = (amount * gstRate) / 100;
            } else {
                cgst = (amount * (gstRate / 2)) / 100;
                sgst = (amount * (gstRate / 2)) / 100;
            }

            let total = amount + cgst + sgst + igst;
            // Update UI
            // $('#sum_amount').text(amount.toFixed(2));
            // $('#sum_cgst').text(cgst.toFixed(2));
            // $('#sum_sgst').text(sgst.toFixed(2));
            // $('#sum_igst').text(igst.toFixed(2));
            // $('#sum_grand_total').text(total.toFixed(2));
            // setRoundOffSummary(total);

            // // Hidden fields (VERY IMPORTANT)
            // $('#edit_amount').val(amount);
            // $('#edit_cgst').val(cgst);
            // $('#edit_sgst').val(sgst);
            // $('#edit_igst').val(igst);
            // $('#edit_total_amount').val(total);
            // setRoundOffSummary(total);
        } else {
            $('#editItemsBody tr').each(function () {
                let row = $(this);
                items.push({
                    id:           row.find('.item-id').val(),
                    hsn:          row.find('.item-hsn').val(),
                    //item_name:    row.find('.item-name').val(),
                    item_name:    row.find('.item-name option:selected').text(), // ✅ FULL NAME
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
        const noitemRows = collectNoItemRows();
        const selectedPurchaseLedger = $('#no_item_section').is(':visible') && noitemRows.length
            ? noitemRows[0].ledger_name
            : $('#noitem_purchase_ledger option:selected').text();
 
        $.ajax({
            url: "<?php echo e(route('dn.update')); ?>",
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
                sales_ledger: $('#edit_ledger').val(),
                vchType: $('#edit_voucher_type').val(),
                address: $('#edit_address').val(),
                pincode: $('#edit_pincode').val(),
                city: $('#edit_city').val(),
                is_igst: $('#edit_is_igst').is(':checked') ? 1 : 0,

                amount: $('#edit_amount').val(),
                cgst: $('#edit_cgst').val(),
                sgst: $('#edit_sgst').val(),
                igst: $('#edit_igst').val(),
                total_amount: $('#edit_total_amount').val(),
                roundoff: $('#edit_roundoff').val(),

                Remarks: $('#edit_remarks').val(),

                gst_mode: $('#gst_calc_mode').val(),

                igst_ledger: $('#igst_ledger').val(),
                cgst_ledger: $('#cgst_ledger').val(),
                sgst_ledger: $('#sgst_ledger').val(),

                noitem_amount: $('#noitem_amount').val(),
                against_invoice: $('#edit_against_invoice').val(),
                purchase_ledger: selectedPurchaseLedger,
                
                items: items,
                entry_mode: $('#no_item_section').is(':visible') ? 'noitem' : 'item',
                custom_slots: collectCustomSlots(),
                noitem_rows: noitemRows
            },
            success: (res) => { 
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
            // error:   (xhr) => { 
            //         let msg = 'Server error';
            //         if (xhr.responseJSON && xhr.responseJSON.message) {
            //             msg = xhr.responseJSON.message;
            //         }
            //         showToast(msg, 'error');
            //     }
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
                rate: parseFloat(rateText.replace('%','')) || parseFloat(row.data('rate')) || 0,

                taxable: parseFloat(
                    row.find('.slot-taxable').text().replace(/[^0-9.]/g, '')
                ) || 0,
                purchase_ledger_id: row.find('.slot-purchase-ledger-id').val() || null,

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

    function itemGstLedgerId(item, keys) {
        for (const key of keys) {
            if (item && item[key]) {
                return String(item[key]);
            }
        }
        return '';
    }

    function findItemMasterByName(itemName = '') {
        const normalized = normalizeName(itemName);
        return ITEM_MASTER.find(item => normalizeName(item.strItemName || item.name || item.item_name) === normalized) || null;
    }

    function getSelectedItemGstMapping() {
        let mapping = null;
        $('#editItemsBody tr').each(function () {
            if (mapping) return;
            const itemName = $(this).find('.item-name').val() || $(this).find('.item-name option:selected').text();
            const item = findItemMasterByName(itemName);
            if (!item) return;

            const cgstId = itemGstLedgerId(item, ['cgst_id', 'CGSTLedgerId']);
            const sgstId = itemGstLedgerId(item, ['sgst_id', 'SGSTLedgerId']);
            const igstId = itemGstLedgerId(item, ['igst_id', 'IGSTLedgerId']);
            if (cgstId || sgstId || igstId) {
                mapping = { cgst_id: cgstId, sgst_id: sgstId, igst_id: igstId };
            }
        });
        return mapping;
    }

    function getHeaderPurchaseLedgerGstMapping() {
        const ledgerId = $('#noitem_purchase_ledger').val();
        const ledgerName = $('#noitem_purchase_ledger option:selected').text();
        return getPurchaseLedgerMapping(ledgerId, ledgerName);
    }

    function selectGstLedger(selector, ledgerId) {
        if (ledgerId) {
            $(selector).val(String(ledgerId)).trigger('change');
        }
    }

    function applyStandardItemGstLedgerMapping() {
        if ($('#gst_calc_mode').val() !== 'standard' || $('#no_item_section').is(':visible')) {
            return;
        }

        const mapping = getSelectedItemGstMapping() || getHeaderPurchaseLedgerGstMapping();
        selectGstLedger('#igst_ledger', mappedGstLedgerId(mapping, 'igst_id'));
        selectGstLedger('#cgst_ledger', mappedGstLedgerId(mapping, 'cgst_id'));
        selectGstLedger('#sgst_ledger', mappedGstLedgerId(mapping, 'sgst_id'));
    }

    function renderCustomTaxSummary(igst = 0, cgst = 0, sgst = 0) {
        $('#custom_tax_rows').html(`
            <div class="gst-summary-title">GST Summary</div>
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(igst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(cgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(sgst)}</span></div>`);
    }

    // Master recalc — updates summary, footer, and custom slots
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
        let mode = $('#gst_calc_mode').val();
        let sumAmt=0, sumSgst=0, sumCgst=0, sumIgst=0, sumTotal=0;
        const recalcNoItemRows = () => {
            let rowAmount = 0, rowCgst = 0, rowSgst = 0, rowIgst = 0;
            const rowIsIGST = $('#edit_is_igst').is(':checked');

            collectNoItemRows().forEach(row => {
                const amount = parseFloat(row.amount) || 0;
                const gstRate = parseFloat(row.gst) || 0;
                const gstAmount = amount * gstRate / 100;
                rowAmount += amount;
                if (rowIsIGST) {
                    rowIgst += gstAmount;
                } else {
                    rowCgst += gstAmount / 2;
                    rowSgst += gstAmount / 2;
                }
            });

            if (mode === 'custom') {
                rowCgst = rowSgst = rowIgst = 0;
                $('#customSlotsBody tr').each(function () {
                    rowIgst += parseFloat($(this).find('.slot-igst-amt').val()) || 0;
                    rowCgst += parseFloat($(this).find('.slot-cgst-amt').val()) || 0;
                    rowSgst += parseFloat($(this).find('.slot-sgst-amt').val()) || 0;
                });
            }

            const rowTotal = rowAmount + rowCgst + rowSgst + rowIgst;
            $('#sum_amount').text(rowAmount.toFixed(2));
            $('#sum_cgst').text(rowCgst.toFixed(2));
            $('#sum_sgst').text(rowSgst.toFixed(2));
            $('#sum_igst').text(rowIgst.toFixed(2));
            // $('#sum_grand_total').text(rowTotal.toFixed(2));
            setRoundOffSummary(rowTotal);
            $('#edit_amount').val(rowAmount);
            $('#edit_cgst').val(rowCgst);
            $('#edit_sgst').val(rowSgst);
            $('#edit_igst').val(rowIgst);
            // $('#edit_total_amount').val(rowTotal);
            setRoundOffSummary(rowTotal);
            if (mode === 'custom') {
                // $('#custom_tax_rows').html(`
                //     <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${rowIgst.toFixed(2)}</span></div>
                //     <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${rowCgst.toFixed(2)}</span></div>
                //     <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${rowSgst.toFixed(2)}</span></div>`);
                renderCustomTaxSummary(rowIgst, rowCgst, rowSgst);
            }
        };
        
        // ✅ NO ITEM MODE
        if ($('#no_item_section').is(':visible')) {
            recalcNoItemRows();
            return;

            let amount = parseFloat($('#noitem_amount').val()) || 0;
            let isIGST = $('#edit_is_igst').is(':checked');

            let cgst = 0, sgst = 0, igst = 0;

            let gstRate = parseFloat($('#noitem_gst_rate').val()) || 0;

            if (mode === 'standard') {
                if (isIGST) {
                    igst = (amount * gstRate) / 100;
                } else {
                    cgst = (amount * gstRate / 2) / 100;
                    sgst = (amount * gstRate / 2) / 100;
                }
            } else if (mode === 'custom') {
                // 🔥 CUSTOM MODE: Recalculate GST for each slot based on new amount
                let totalCgst = 0, totalSgst = 0, totalIgst = 0;

                $('#customSlotsBody tr').each(function () {
                    let rate = parseFloat($(this).data('rate')) || 0;
                    
                    // Calculate GST amounts based on new amount and rate
                    let slotIgst = 0, slotCgst = 0, slotSgst = 0;
                    if (isIGST) {
                        slotIgst = (amount * rate) / 100;
                    } else {
                        slotCgst = (amount * (rate / 2)) / 100;
                        slotSgst = (amount * (rate / 2)) / 100;
                    }
                    
                    // Update the slot input fields
                    $(this).find('.slot-igst-amt').val(slotIgst.toFixed(2));
                    $(this).find('.slot-cgst-amt').val(slotCgst.toFixed(2));
                    $(this).find('.slot-sgst-amt').val(slotSgst.toFixed(2));
                    
                    // Update taxable amount display
                    $(this).find('.slot-taxable').text(amount.toFixed(2));
                    
                    totalIgst += slotIgst;
                    totalCgst += slotCgst;
                    totalSgst += slotSgst;
                });

                cgst = totalCgst;
                sgst = totalSgst;
                igst = totalIgst;
            }

            // ✅ UPDATE UI
            $('#sum_amount').text(amount.toFixed(2));
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));

            let total = amount + cgst + sgst + igst;
            // $('#sum_grand_total').text(total.toFixed(2));
            setRoundOffSummary(total);

            // 🔥 UPDATE CUSTOM TAX ROWS SUMMARY (if in custom mode)
            if (mode === 'custom') {
                let customSummaryHtml = `
                    <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${igst.toFixed(2)}</span></div>
                    <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${cgst.toFixed(2)}</span></div>
                    <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${sgst.toFixed(2)}</span></div>`;
                $('#custom_tax_rows').html(customSummaryHtml);
            }

            // ✅ IMPORTANT (for save)
            $('#edit_amount').val(amount);
            $('#edit_cgst').val(cgst);
            $('#edit_sgst').val(sgst);
            $('#edit_igst').val(igst);
            // $('#edit_total_amount').val(total);
            setRoundOffSummary(total);

            return; // 🔥 STOP HERE
        }

        if ($('#gst_calc_mode').val() === 'custom') {

            let totalTaxable = 0;
            let totalCgst = 0;
            let totalSgst = 0;
            let totalIgst = 0;

            $('#customSlotsBody tr').each(function () {

                let taxable = parseFloat($(this).find('.slot-taxable').text()) || 0;
                let cgst = parseFloat($(this).find('.slot-cgst-amt').val()) || 0;
                let sgst = parseFloat($(this).find('.slot-sgst-amt').val()) || 0;
                let igst = parseFloat($(this).find('.slot-igst-amt').val()) || 0;

                totalTaxable += taxable;
                totalCgst += cgst;
                totalSgst += sgst;
                totalIgst += igst;
            });

            let grandTotal = totalTaxable + totalCgst + totalSgst + totalIgst;

            $('#sum_amount').text(totalTaxable.toFixed(2));
            $('#sum_cgst').text(totalCgst.toFixed(2));
            $('#sum_sgst').text(totalSgst.toFixed(2));
            $('#sum_igst').text(totalIgst.toFixed(2));
            // $('#sum_grand_total').text(grandTotal.toFixed(2));
            setRoundOffSummary(grandTotal);

            $('#edit_amount').val(totalTaxable);
            $('#edit_cgst').val(totalCgst);
            $('#edit_sgst').val(totalSgst);
            $('#edit_igst').val(totalIgst);
            // $('#edit_total_amount').val(grandTotal);
            setRoundOffSummary(grandTotal);
            renderCustomTaxSummary(totalIgst, totalCgst, totalSgst);

            return;
        }
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
        
        if ($('#no_item_section').is(':visible')) {

            let amount = parseFloat($('#noitem_amount').val()) || 0;
            let isIGST = $('#edit_is_igst').is(':checked');

            let gstRate = 18;

            let cgst = 0, sgst = 0, igst = 0;

            if (isIGST) {
                igst = amount * gstRate / 100;
            } else {
                cgst = amount * (gstRate / 2) / 100;
                sgst = amount * (gstRate / 2) / 100;
            }

            let total = amount + cgst + sgst + igst;

            $('#sum_amount').text(amount.toFixed(2));
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));
            // $('#sum_grand_total').text(total.toFixed(2));
            setRoundOffSummary(total);

            // hidden
            $('#edit_amount').val(amount);
            $('#edit_cgst').val(cgst);
            $('#edit_sgst').val(sgst);
            $('#edit_igst').val(igst);
            // $('#edit_total_amount').val(total);
            setRoundOffSummary(total);

            return;
        }
        // Renumber rows
        $('#editItemsBody tr').each(function(i) { $(this).find('.td-sr').text(i+1); });

        if (mode === 'standard') {
            $('#sum_sgst').text(fmt(sumSgst));
            $('#sum_cgst').text(fmt(sumCgst));
            $('#sum_igst').text(fmt(sumIgst));
            applyStandardItemGstLedgerMapping();
        } else {
            // CUSTOM MODE: render rate-wise slots
            //renderCustomSlots(rateMap, sumTotal);
            
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CUSTOM MODE — render rate-wise slots
    // Each unique GST% from items gets one row with IGST/CGST/SGST
    // ledger dropdowns and auto-computed tax amounts.
    // ═══════════════════════════════════════════════════════════════
    // function renderCustomSlots(rateMap, grandTotal) {
    //     let sGstLedgers = <?php echo json_encode($sGstLedgers ?? [], 15, 512) ?>;
    //     let cGstLedgers = <?php echo json_encode($cGstLedgers ?? [], 15, 512) ?>;
    //     let iGstLedgers = <?php echo json_encode($iGstLedgers ?? [], 15, 512) ?>;

    //     let cgst = slot.cgst_amount ?? ($('#edit_cgst').val() || 0);
    //     let sgst = slot.sgst_amount ?? ($('#edit_sgst').val() || 0);
    //     let igst = slot.igst_amount ?? ($('#edit_igst').val() || 0);
    //     // Always show these rates as rows; highlight non-zero ones
    //     let allRates = ['5', '12', '18', '28'];

    //     // Also include any other rates found in items
    //     Object.keys(rateMap).forEach(r => { if (r && !allRates.includes(r)) allRates.push(r); });

    //     // Build the slot table body
    //     let slotHtml = '';
    //     let customSgst=0, customCgst=0, customIgst=0;

    //     allRates.forEach(function(rate) {
    //         let data   = rateMap[rate] || { amt:0, igst:0, cgst:0, sgst:0 };
    //         let halfR  = parseFloat(rate) / 2;
    //         // Auto-compute: use sum from item recalc (standard) or allow manual override
    //         let igstAmt = data.igst;
    //         let cgstAmt = data.cgst;
    //         let sgstAmt = data.sgst;
    //         customIgst += igstAmt;
    //         customCgst += cgstAmt;
    //         customSgst += sgstAmt;

    //         let isZero = data.amt === 0;

    //         // Build ledger options
    //         let iOpts = iGstLedgers.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
    //         let cOpts = cGstLedgers.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
    //         let sOpts = sGstLedgers.map(l => `<option value="${l.id}">${l.name}</option>`).join('');

    //         slotHtml += `<tr class="${isZero ? 'zero-row' : ''}" data-rate="${rate}">
    //             <td><span class="rate-badge"><span class="slot-rate"></span>${rate}%</span><br><small style="font-size:9px;color:#6b7280;">Taxable: ${fmt(data.amt)}</small></td>
    //             <td><strong>${fmt(data.amt)}</strong></td>
    //             <td><select class="slot-igst-ledger" data-rate="${rate}""><option value="">— Ledger —</option>${iOpts}</select></td>
    //             <td><input type="number" class="slot-igst-amt" data-rate="${rate}" value="${parseFloat(igstAmt).toFixed(2)}" step="any"></td>
    //             <td><select class="slot-cgst-ledger" data-rate="${rate}"><option value="">— Ledger —</option>${cOpts}</select></td>
    //             <td><input type="number" class="slot-cgst-amt" data-rate="${rate}" value="${parseFloat(cgstAmt).toFixed(2)}" step="any"></td>
    //             <td><select class="slot-sgst-ledger" data-rate="${rate}"><option value="">— Ledger —</option>${sOpts}</select></td>
    //             <td><input type="number" class="slot-sgst-amt" data-rate="${rate}" value="${parseFloat(sgstAmt).toFixed(2)}" step="any"></td>
    //         </tr>`;
    //     });

    //     $('#customSlotsBody').html(slotHtml);

    //     // Render custom mode summary
    //     let customSummaryHtml = `
    //         <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(customIgst)}</span></div>
    //         <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(customCgst)}</span></div>
    //         <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(customSgst)}</span></div>`;
    //     $('#custom_tax_rows').html(customSummaryHtml);

    //     // Update hidden fields for save
    //     $('#edit_igst').val(customIgst.toFixed(2));
    //     $('#edit_cgst').val(customCgst.toFixed(2));
    //     $('#edit_sgst').val(customSgst.toFixed(2));
    //     let total = parseFloat($('#edit_amount').val()) + customIgst + customCgst + customSgst;
    //     $('#edit_total_amount').val(total.toFixed(2));
    //     $('#sum_grand_total').text(fmt(total));
    // }

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
        // let customSummaryHtml = `
        //     <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(igst)}</span></div>
        //     <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(cgst)}</span></div>
        //     <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(sgst)}</span></div>`;
        // $('#custom_tax_rows').html(customSummaryHtml);
        renderCustomTaxSummary(igst, cgst, sgst);
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

    $('.party-select').select2({
        dropdownParent: $('#editModal'),
        width: '100%',
        placeholder: "Search Party...",
        allowClear: true
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

    $(document).on('input', '#noitem_amount', function () {
        recalcTotals();
    });

    $(document).on('input', '#noitem_gst_rate', function () {
        recalcTotals();
    });

    $(document).on('input', '.slot-igst-amt, .slot-cgst-amt, .slot-sgst-amt', function () {
        recalcTotals();
    });

    function generateSlotsFromItems() {
        let map = {};

        $('#editItemsBody tr').each(function () {
            let rate = parseFloat($(this).find('.item-gst_rate').val()) || 0;
            let qty  = parseFloat($(this).find('.item-qty').val()) || 0;
            let price = parseFloat($(this).find('.item-rate').val()) || 0;

            let amount = qty * price;

            if (!map[rate]) {
                map[rate] = {
                    gst_rate: rate,
                    taxable: 0,
                    cgst_amount: 0,
                    sgst_amount: 0,
                    igst_amount: 0,
                    cgst_id: '',
                    sgst_id: '',
                    igst_id: ''
                };
            }

            map[rate].taxable += amount;
        });

        // 🔥 Convert to array
        let slots = Object.values(map);

        // 🔥 Calculate GST
        slots.forEach(slot => {
            let rate = slot.gst_rate;

            if ($('#edit_is_igst').is(':checked')) {
                slot.igst_amount = slot.taxable * rate / 100;
            } else {
                slot.cgst_amount = slot.taxable * (rate / 2) / 100;
                slot.sgst_amount = slot.taxable * (rate / 2) / 100;
            }
        });

        renderCustomSlots(slots);
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
    $(document).on('select2:open', function() {
        setTimeout(function() {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/bulkupload/debit_note/preview.blade.php ENDPATH**/ ?>