
<?php $__env->startSection('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="container mx-auto">
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow border border-gray-200 dark:border-neutral-700">
        <!-- HEADER -->
        <div class="flex justify-between items-center px-5 py-3 border-b border-neutral-700">
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.history.back()"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 text-lg">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <h2 class="text-gray-900 dark:text-white text-lg font-semibold">Credit Notes Transactions</h2>
                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full"><?php echo e($rows->count()); ?></span>
            </div>
            <div class="flex gap-2">
                <?php if(session('client_name')): ?>
                <div class="text-sm text-green-600 font-semibold"><?php echo e(session('client_name')); ?></div>
                <?php endif; ?>
                <button class="border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-gray-300 px-3 py-1 rounded text-sm">More Info</button>
                <button onclick="openLedgerModal()" class="border border-blue-500 text-blue-400 px-3 py-1 rounded text-sm">+ Create Ledger</button>
                <button type="button" id="saveBtn" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Save</button>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="flex gap-10 px-5 py-3 text-sm border-b border-neutral-700">
            <div>
                <div class="flex gap-4 items-end">
                    <div>
                        <label class="text-gray-700 dark:text-gray-300 text-sm block">Update Bulk Records</label>
                        <select id="bulkColumn" class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-white rounded px-2 py-1 mt-1">
                            <option value="">Select Column</option>
                            <option value="party">Party Name</option>
                            <option value="place">Place Of Supply</option>
                            <option value="voucher">Voucher Type</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-700 dark:text-gray-300 text-sm block">Value</label>
                        <select id="bulkValue" class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-white rounded px-2 py-1 mt-1">
                            <option value="">Select Value</option>
                        </select>
                    </div>
                    <button type="button" id="applyBulk" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Apply</button>
                </div>
            </div>
            <div>
                <label class="text-gray-700 dark:text-gray-300 text-sm">General Filters</label>
                <div class="flex gap-4 mt-2 text-gray-700 dark:text-gray-300">
                    <label><input type="checkbox" class="generalFilter" value="synced"> Hide Synced</label>
                    <label><input type="checkbox" class="generalFilter" value="saved"> Saved</label>
                    <label><input type="checkbox" class="generalFilter" value="blank"> Blank</label>
                    <label><input type="checkbox" class="generalFilter" value="failed"> Failed</label>
                </div>
            </div>
        </div>

        <form id="salesForm">
            <?php echo csrf_field(); ?>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 group-block">
                <table id="salesTable" class="min-w-[1100px] w-full text-sm text-gray-700 dark:text-gray-300 border-collapse">
                    <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-gray-900/40 text-xs text-gray-700 dark:text-gray-300 uppercase sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2 w-8"><input type="checkbox" id="selectAll"></th>
                            <th class="px-3 py-2">SR</th>
                            <th class="px-3 py-2">DATE</th>
                            <th class="px-3 py-2">REFERENCE</th>
                            <th class="px-3 py-2">VOUCHER</th>
                            <th class="px-3 py-2">PARTY A/C NAME</th>
                            <th class="px-3 py-2">GSTIN/UIN</th>
                            <th class="px-3 py-2">PLACE</th>
                            <th class="px-3 py-2 text-right">AMOUNT</th>
                            <th class="px-3 py-2">STATUS</th>
                            <th class="px-3 py-2">ACTION</th>
                        </tr>
                        <tr class="bg-white dark:bg-neutral-900">
                            <th></th><th></th>
                            <th><input class="searchInput w-full"></th>
                            <th><input class="searchInput w-full"></th>
                            <th><input class="searchInput w-full"></th>
                            <th><input class="searchInput w-full"></th>
                            <th><input class="searchInput w-full"></th>
                            <th></th><th></th><th></th><th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="group transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] [&>*]:group-hover:text-black [&_*]:group-hover:text-black">
                            <td class="px-3 py-2"><input type="checkbox" name="selected[]" value="<?php echo e($row->id); ?>"></td>
                            <td class="px-3 py-2"><?php echo e($index+1); ?></td>
                            <td class="px-3 py-2">
                                <input type="date" name="date[<?php echo e($row->id); ?>]"
                                    value="<?php echo e(\Carbon\Carbon::parse($row->note_date)->format('Y-m-d')); ?>" class="inputCell">
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" name="invoice_no[<?php echo e($row->id); ?>]" value="<?php echo e($row->note_no); ?>" class="inputCell">
                            </td>
                            <td class="px-3 py-2">
                                <select name="voucher_type[<?php echo e($row->id); ?>]" class="inputCell voucherSelect">
                                    <?php $__currentLoopData = $vchTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vchType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($vchType); ?>" <?php echo e(strtolower(trim($vchType))==strtolower(trim($row->vchType))?'selected':''); ?>><?php echo e($vchType); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" name="party_name[<?php echo e($row->id); ?>]" value="<?php echo e($row->party_name); ?>" class="inputCell mb-1">
                                <select name="ledger[<?php echo e($row->id); ?>]" class="ledgerSelect inputCell">
                                    <option value="">Select Ledger</option>
                                    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->name); ?>" <?php echo e($row->sales_ledger==$ledger->name?'selected':''); ?>><?php echo e($ledger->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="px-3 py-2"><?php echo e($row->gst_no); ?></td>
                            <td class="px-3 py-2">
                                <select name="place_of_supply[<?php echo e($row->id); ?>]" class="inputCell">
                                    <option value="">Select State</option>
                                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($state); ?>" <?php echo e(strtolower(trim($state))==strtolower(trim($row->place_of_supply))?'selected':''); ?>><?php echo e($state); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="px-3 py-2 text-right"><?php echo e(number_format($row->total_amount,2)); ?></td>
                            <td class="px-3 py-2"><span class="text-yellow-400"><?php echo e($row->status); ?></span></td>
                            <td class="px-3 py-2">
                                <button type="button" class="viewRow text-green-400 hover:text-green-300" title="View" data-id="<?php echo e($row->id); ?>">
                                    <i class="fa-solid fa-eye action-icon"></i>
                                </button>
                                <button type="button" class="text-blue-400 hover:text-blue-300 editRow" title="Edit"
                                    data-id="<?php echo e($row->id); ?>"
                                    data-invoice="<?php echo e($row->note_no); ?>"
                                    data-date="<?php echo e(\Carbon\Carbon::parse($row->note_date)->format('Y-m-d')); ?>"
                                    data-gst_no="<?php echo e($row->gst_no); ?>"
                                    data-vchtype="<?php echo e($row->vchType); ?>"
                                    data-party="<?php echo e($row->party_name); ?>"
                                    data-place="<?php echo e($row->place_of_supply); ?>"
                                    data-ledger="<?php echo e($row->sales_ledger_name); ?>"
                                    data-mode="<?php echo e($row->gst_mode); ?>"
                                    data-remarks="<?php echo e($row->remarks); ?>">
                                    <i class="fa-solid fa-pen action-icon"></i>
                                </button>
                                <button type="button" class="text-red-500 deleteRow" data-id="<?php echo e($row->id); ?>">
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


<div id="editModal" class="modal" style="display: none;">
    <div class="receipt-wrapper">
        <input type="hidden" id="edit_id">

        <div class="receipt-head">
            <div class="receipt-head-left">
                <div class="receipt-company">Credit Notes Bill</div>
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
                    <div style="display:flex;gap:6px;width:100%;">
                        <select id="edit_party" class="receipt-input ledgerSelect party-select" style="flex:1;">
                            <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ledger->name); ?>"><?php echo e($ledger->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="button" onclick="openLedgerModal()"
                            style="padding:4px 8px;font-size:12px;background:#2563eb;color:white;border:none;border-radius:4px;">+</button>
                    </div>
                </div>
                <div class="receipt-field-row"><label>GSTIN / UIN</label><input type="text" id="edit_gst" class="receipt-input" placeholder="GST Number"></div>
                <div class="receipt-field-row"><label>Address</label><textarea id="edit_address" class="receipt-input" placeholder="Address" rows="2"></textarea></div>
                <div class="receipt-field-row"><label>Pincode</label><input type="text" id="edit_pincode" class="receipt-input" placeholder="Pincode"></div>
                <div class="receipt-field-row"><label>City</label><input type="text" id="edit_city" class="receipt-input" placeholder="City"></div>
            </div>
            
            <div class="receipt-meta-block">
                <div class="receipt-block-title"><i class="fa-solid fa-file-invoice text-blue-400 mr-1"></i> Invoice Details</div>
                <div class="receipt-field-row">
                    <label>Sales Ledger</label>
                    <select id="noitem_sales_ledger" class="receipt-input ledgerSelect">
                        <option value="">Select Ledger</option>
                        <?php $__currentLoopData = $salesLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->name); ?>"><?php echo e($ledger->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="receipt-field-row"><label>CN No.</label><input type="text" id="edit_invoice" class="receipt-input" placeholder="Credit Note Number"></div>
                <div class="receipt-field-row"><label>Date</label><input type="date" id="edit_date" class="receipt-input"></div>
                <div class="receipt-field-row">
                    <label>Voucher Type</label>
                    <select id="edit_voucher_type" class="receipt-input">
                        <?php $__currentLoopData = $vchTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vchType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($vchType); ?>"><?php echo e($vchType); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="receipt-field-row">
                    <label>Place Of Supply</label>
                    <select id="edit_place" class="receipt-input">
                        <option value="">Select State</option>
                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($state); ?>"><?php echo e($state); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="receipt-field-row">
                    <label>Against Invoice</label>
                    <input type="text" id="edit_against_invoice" class="receipt-input" placeholder="Invoice Number">
                </div>
            </div>
        </div>

        
        <div>
            <div class="receipt-items-header">
                <span><i class="fa-solid fa-list text-blue-400 mr-1"></i> Item Details</span>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div class="receipt-field-row" style="margin:0;">
                        <label style="width:auto;padding-right:4px;">GST Mode</label>
                        <select id="gst_calc_mode" class="receipt-input" style="width:180px;">
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
                                <td class="text-right font-semibold" id="foot_rate_total">0.00</td>
                                <td class="text-right font-semibold" id="foot_amount_total">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            
            <div id="no_item_section" style="display:none;padding:10px;border-top:1px dashed #e2e8f0;">
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>Sales Ledger</th>
                            <th>GST %</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="noItemBody"></tbody>
                </table>
                <button type="button" id="addNoItemRow" class="receipt-add-btn" style="margin-top:8px;">
                    <i class="fa-solid fa-plus mr-1"></i> Add More
                </button>
                <input type="hidden" id="noitem_gst_rate">
                <input type="hidden" id="noitem_amount">
            </div>

            
            <div id="custom_slots_section" style="display:none;">
                <div style="padding:8px 10px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <div style="font-size:11px;color:#6b7280;margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
                        GST Rate-wise Breakup (auto-populated from item GST%)
                    </div>
                    <table class="custom-slots-table">
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
                        <tbody id="customSlotsBody"></tbody>
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
                <div class="tax-row"><span class="tax-label">Taxable Amount</span><span class="tax-value" id="sum_amount">0.00</span></div>

                
                <div id="standard_tax_rows">
                    <div class="tax-row">
                        <span class="tax-label">IGST</span>
                        <select id="igst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            <?php $__currentLoopData = $iGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($l->id); ?>"><?php echo e($l->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="tax-value" id="sum_igst">0.00</span>
                    </div>
                    <div class="tax-row">
                        <span class="tax-label">CGST</span>
                        <select id="cgst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            <?php $__currentLoopData = $cGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($l->id); ?>"><?php echo e($l->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="tax-value" id="sum_cgst">0.00</span>
                    </div>
                    <div class="tax-row">
                        <span class="tax-label">SGST</span>
                        <select id="sgst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            <?php $__currentLoopData = $sGstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($l->id); ?>"><?php echo e($l->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="tax-value" id="sum_sgst">0.00</span>
                    </div>
                </div>

                
                <div id="custom_tax_rows" style="display:none;"></div>
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
            <div class="receipt-footer-note">This is a computer-generated Credit Notes record.</div>
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
        <div class="modal-header">
            <h3>Create Ledger</h3>
            <button type="button" class="close-btn" onclick="closeLedgerModal()">✕</button>
        </div>
        <div class="modal-body">
            <form id="ledgerForm">
                <?php echo csrf_field(); ?>
                <div class="form-grid">
                    <div class="form-group"><label>Name</label><input type="text" name="Name"></div>
                    <div class="form-group"><label>Parent</label>
                        <select name="Parent"><option>Select Parent</option>
                            <?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->strParents); ?>"><?php echo e($p->strParents); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Mailing Name</label><input type="text" name="MailingName"></div>
                    <div class="form-group"><label>Address Line 1</label><input type="text" name="AddressLine1"></div>
                    <div class="form-group"><label>Address Line 2</label><input type="text" name="AddressLine2"></div>
                    <div class="form-group"><label>City</label><input type="text" name="City"></div>
                    <div class="form-group"><label>Pincode</label><input type="text" name="Pincode"></div>
                    <div class="form-group"><label>State</label>
                        <select name="State"><option value="">Select State</option>
                            <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>"><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Country</label><input type="text" name="Country"></div>
                    <div class="form-group"><label>GST No</label><input type="text" name="GstNo"></div>
                    <div class="form-group"><label>GST Registration Type</label>
                        <select name="GstRegistrationType">
                            <option value="">Select</option>
                            <option>Regular</option><option>Composition</option><option>Unregistered</option>
                            <option>Casual Taxable</option><option>Non-resident Taxable</option>
                            <option>Input Service Distributor</option><option>Special Economic Zone</option>
                            <option>E-commerce Operators</option><option>Tax Deduction at Source</option>
                            <option>TCS Collector</option><option>Voluntary Registration</option>
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
            <h3>View Credit Notes</h3>
            <button type="button" class="close-btn" onclick="closeViewModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="view-card">
                <div class="view-grid">
                    <div><label>Invoice No</label><p id="v_invoice"></p></div>
                    <div><label>Date</label><p id="v_date"></p></div>
                    <div><label>Voucher Type</label><p id="v_voucher"></p></div>
                    <div><label>Party Name</label><p id="v_party"></p></div>
                    <div><label>GST No</label><p id="v_gst"></p></div>
                    <div><label>Place of Supply</label><p id="v_place"></p></div>
                    <div><label>Sales Ledger</label><p id="v_ledger"></p></div>
                    <div><label>Status</label><p id="v_status" class="status-badge"></p></div>
                </div>
            </div>
            <div class="view-totals">
                <div class="box"><span>Amount</span><strong id="v_amount"></strong></div>
                <div class="box"><span>SGST</span><strong id="v_sgst"></strong></div>
                <div class="box"><span>CGST</span><strong id="v_cgst"></strong></div>
                <div class="box"><span>IGST</span><strong id="v_igst"></strong></div>
                <div class="box"><span>Round Off</span><strong id="v_roundoff"></strong></div>
                <div class="box highlight"><span>Total</span><strong id="v_total"></strong></div>
            </div>
            <div class="mt-4">
                <div class="section-title">Item Details</div>
                <div class="table-wrapper">
                    <table class="view-table">
                        <thead><tr><th>#</th><th>Item</th><th>Qty</th><th>Rate</th><th>Amount</th><th>SGST</th><th>CGST</th><th>IGST</th><th>Total</th></tr></thead>
                        <tbody id="v_items_body"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer"><button onclick="closeViewModal()" class="btn-cancel">Close</button></div>
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

    .dark #editModal input { color:#000 !important; }
    .custom-slots-table input[type="number"] {
        background:#f8fafc;
        color:#374151;
    }
    .custom-slots-table td {
        color: #000 !important;
        font-weight: 600;
    }

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script> -->

<script>
// ─── CONSTANTS (from server) ───────────────────────────────────────────────
const LEDGER_NAMES    = <?php echo json_encode(collect($ledgers)->pluck('name'), 15, 512) ?>;
const PARTY_LEDGER_DETAILS = <?php echo json_encode($ledgerDetails ?? [], 15, 512) ?>;
const STATES_LIST     = <?php echo json_encode($states, 15, 512) ?>;
const VCH_TYPES       = <?php echo json_encode($vchTypes, 15, 512) ?>;
const ITEM_MASTER     = <?php echo json_encode($stockItems, 15, 512) ?>;
const SALES_GST_MAPPINGS = <?php echo json_encode($salesGstMappings ?? [], 15, 512) ?>;
const SALES_LEDGERS    = <?php echo json_encode($salesLedgers ?? [], 15, 512) ?>;
const CGST_LEDGERS    = <?php echo json_encode($cGstLedgers, 15, 512) ?>;
const SGST_LEDGERS    = <?php echo json_encode($sGstLedgers, 15, 512) ?>;
const IGST_LEDGERS    = <?php echo json_encode($iGstLedgers, 15, 512) ?>;

function normalizeName(value) {
    return String(value || '').replace(/['"]/g, '').trim().toLowerCase();
}

function findPartyLedgerDetails(ledgerValue = '', ledgerText = '') {
    return PARTY_LEDGER_DETAILS.find(ledger =>
        String(ledger.id) === String(ledgerValue || '') ||
        normalizeName(ledger.name) === normalizeName(ledgerValue) ||
        normalizeName(ledger.name) === normalizeName(ledgerText)
    ) || null;
}

function fillPartyLedgerDetails(ledgerValue = '', ledgerText = '') {
    const ledger = findPartyLedgerDetails(ledgerValue, ledgerText);
    if (!ledger) return;

    $('#edit_gst').val(ledger.gst_no || '');
    $('#edit_address').val(ledger.address || '');
    $('#edit_pincode').val(ledger.pincode || '');
    $('#edit_city').val(ledger.city || '');
    $('#edit_place').val(ledger.state || '').trigger('change');
}

$(document).on('change', '#edit_party', function () {
    fillPartyLedgerDetails($(this).val(), $(this).find('option:selected').text());
});

function findSalesLedgerMapping(ledgerValue = '', ledgerText = '') {
    ledgerValue = String(ledgerValue || '').trim();
    ledgerText  = String(ledgerText || '').trim();

    return SALES_GST_MAPPINGS.find(mapping =>
        String(mapping.id) === ledgerValue ||
        normalizeName(mapping.name) === normalizeName(ledgerValue) ||
        normalizeName(mapping.name) === normalizeName(ledgerText)
    ) || null;
}

function getSelectedSalesLedgerMapping() {
    const selectedValue = $('#noitem_sales_ledger').val();
    const selectedText = $('#noitem_sales_ledger option:selected').text();
    return findSalesLedgerMapping(selectedValue, selectedText);
}

function salesLedgerId(ledger) {
    return ledger?.id || ledger?.iLedgerId || ledger?.name || ledger?.strCustomerName || '';
}

function salesLedgerName(ledger) {
    return ledger?.name || ledger?.strCustomerName || ledger?.strLedgerName || '';
}

function buildSalesLedgerOptions(selected = '') {
    let html = '<option value="">Select Ledger</option>';
    SALES_LEDGERS.forEach(ledger => {
        const id = salesLedgerId(ledger);
        const name = salesLedgerName(ledger);
        const isSelected = String(selected || '') === String(id || '') || normalizeName(selected) === normalizeName(name);
        html += `<option value="${id}" ${isSelected ? 'selected' : ''}>${name}</option>`;
    });
    return html;
}

function addNoItemRow(data = {}) {
    const row = `
        <tr>
            <td><select class="receipt-input noitem-ledger">${buildSalesLedgerOptions(data.ledger || data.ledger_name || '')}</select></td>
            <td><input type="number" class="receipt-input noitem-gst" value="${data.gst || 0}" step="any"></td>
            <td><input type="number" class="receipt-input noitem-amount" value="${data.amount || ''}" step="any"></td>
            <td><button type="button" class="receipt-del-btn removeNoItem">x</button></td>
        </tr>
    `;
    $('#noItemBody').append(row);
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
            rows.push({
                ledger,
                ledger_name: ledgerName,
                gst,
                amount
            });
        }
    });
    return rows;
}

function findItemGstMapping(itemName = '') {
    if (!itemName) return null;
    const normalizedItemName = normalizeName(itemName);
    return ITEM_MASTER.find(item => normalizeName(item.strItemName || item.name || '') === normalizedItemName) || null;
}

function valueByPossibleKeys(source, keys) {
    if (!source) return '';

    for (const key of keys) {
        if (source[key] !== undefined && source[key] !== null && source[key] !== '') {
            return source[key];
        }
    }

    const normalizedKeys = keys.map(key => key.toLowerCase());
    const matchedKey = Object.keys(source).find(key => normalizedKeys.includes(key.toLowerCase()));
    return matchedKey ? source[matchedKey] : '';
}

function itemGstLedgerId(item, type) {
    const keyMap = {
        igst: ['igst_id', 'IGSTLedgerId', 'igstLedgerId', 'igst_ledger_id'],
        cgst: ['cgst_id', 'CGSTLedgerId', 'cgstLedgerId', 'cgst_ledger_id'],
        sgst: ['sgst_id', 'SGSTLedgerId', 'sgstLedgerId', 'sgst_ledger_id']
    };

    return valueByPossibleKeys(item, keyMap[type] || []);
}

function getItemGstMappingByRate(rate) {
    const normalizedRate = parseFloat(rate) || 0;
    let foundItem = null;

    $('#editItemsBody tr').each(function () {
        const itemName = $(this).find('.itemSelect').val();
        const itemRate = parseFloat($(this).find('.item-gst_rate').val()) || 0;
        if (itemRate !== normalizedRate) return;
        const item = findItemGstMapping(itemName);
        if (item) {
            foundItem = item;
            return false;
        }
    });

    return foundItem;
}

function applyGstLedgerMapping(force = false) {
    const mapping = getSelectedSalesLedgerMapping();
    if (!mapping) return;

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

function applyItemGstMapping(itemName = '', force = false) {
    const item = findItemGstMapping(itemName);
    if (!item) return;

    if ($('#gst_calc_mode').val() === 'standard') {
        const igstLedgerId = itemGstLedgerId(item, 'igst');
        const cgstLedgerId = itemGstLedgerId(item, 'cgst');
        const sgstLedgerId = itemGstLedgerId(item, 'sgst');

        if (igstLedgerId && (force || !$('#igst_ledger').val())) {
            $('#igst_ledger').val(igstLedgerId).trigger('change');
        }
        if (cgstLedgerId && (force || !$('#cgst_ledger').val())) {
            $('#cgst_ledger').val(cgstLedgerId).trigger('change');
        }
        if (sgstLedgerId && (force || !$('#sgst_ledger').val())) {
            $('#sgst_ledger').val(sgstLedgerId).trigger('change');
        }
        return;
    }

    const rate = parseFloat(item.gst_rate) || 0;
    if (!rate) return;

    $('#customSlotsBody tr').each(function () {
        const slotRate = parseFloat($(this).data('rate')) || 0;
        if (slotRate !== rate) return;

        const igstSelect = $(this).find('.slot-igst-ledger');
        const cgstSelect = $(this).find('.slot-cgst-ledger');
        const sgstSelect = $(this).find('.slot-sgst-ledger');

        const igstLedgerId = itemGstLedgerId(item, 'igst');
        const cgstLedgerId = itemGstLedgerId(item, 'cgst');
        const sgstLedgerId = itemGstLedgerId(item, 'sgst');

        if (igstLedgerId && (force || !igstSelect.val())) {
            igstSelect.val(igstLedgerId);
        }
        if (cgstLedgerId && (force || !cgstSelect.val())) {
            cgstSelect.val(cgstLedgerId);
        }
        if (sgstLedgerId && (force || !sgstSelect.val())) {
            sgstSelect.val(sgstLedgerId);
        }
    });
}

function applyCustomSlotGstLedgerMapping() {
    $('#customSlotsBody tr').each(function () {
        const rate = parseFloat($(this).data('rate')) || 0;
        const item = getItemGstMappingByRate(rate);
        if (!item) return;

        const igstSelect = $(this).find('.slot-igst-ledger');
        const cgstSelect = $(this).find('.slot-cgst-ledger');
        const sgstSelect = $(this).find('.slot-sgst-ledger');

        const igstLedgerId = itemGstLedgerId(item, 'igst');
        const cgstLedgerId = itemGstLedgerId(item, 'cgst');
        const sgstLedgerId = itemGstLedgerId(item, 'sgst');

        if (igstLedgerId && !igstSelect.val()) {
            igstSelect.val(igstLedgerId);
        }
        if (cgstLedgerId && !cgstSelect.val()) {
            cgstSelect.val(cgstLedgerId);
        }
        if (sgstLedgerId && !sgstSelect.val()) {
            sgstSelect.val(sgstLedgerId);
        }
    });
}

// ─── STATE ─────────────────────────────────────────────────────────────────
let loadingCustomFromDB = false; // flag: true while loading saved custom slots

// ─── INIT ──────────────────────────────────────────────────────────────────
$(document).ready(function () {

    $('#selectAll').click(function () {
        $('tbody input[type=checkbox]').prop('checked', this.checked);
    });

    $('.searchInput').on('keyup', function () {
        let col = $(this).closest('th').index();
        let val = $(this).val().toLowerCase();
        $('#salesTable tbody tr').each(function () {
            let cell = $(this).find('td').eq(col);
            let text = cell.text().toLowerCase() + (cell.find('input,select').val() || '').toLowerCase();
            $(this).toggle(text.includes(val));
        });
    });

    // Init Select2 on table ledger dropdowns
    $('.ledgerSelect').select2({ width:'100%', placeholder:'Search Ledger...', allowClear:true });

    // Sync ledger → party name in table rows
    $(document).on('change', 'select[name^="ledger"]', function () {
        $(this).closest('tr').find('input[name^="party_name"]').val($(this).val());
    });
});

// ─── BULK UPDATE ───────────────────────────────────────────────────────────
$('#bulkColumn').on('change', function () {
    let col = $(this).val();
    let dd = $('#bulkValue').empty().append('<option value="">Select Value</option>');
    let src = col === 'place' ? STATES_LIST : col === 'voucher' ? VCH_TYPES : LEDGER_NAMES;
    src.forEach(v => dd.append(`<option value="${v}">${v}</option>`));
});

$('#applyBulk').click(function () {
    let col = $('#bulkColumn').val(), val = $('#bulkValue').val();
    if (!col || !val) { showToast('Select column and value', 'error'); return; }
    let rows = $('tbody input[type=checkbox]:checked').closest('tr');
    if (!rows.length) rows = $('#salesTable tbody tr');
    rows.each(function () {
        let r = $(this);
        if (col === 'party')   { r.find('input[name^="party_name"]').val(val); r.find('select[name^="ledger"]').val(val).trigger('change'); }
        if (col === 'place')   r.find('select[name^="place_of_supply"]').val(val);
        if (col === 'voucher') r.find('.voucherSelect').val(val).trigger('change');
    });
});

// ─── SAVE / DELETE ─────────────────────────────────────────────────────────
$('#saveBtn').click(function () {
    $.ajax({
        url: "<?php echo e(route('cn.save')); ?>", type:'POST', data:$('#salesForm').serialize(),
        success: (response) => {
            if (response.status === false) {
                showToast(response.message || 'Unable to save selected records', 'error');
                return;
            }
            showToast(response.message || 'Saved Successfully', 'success');
            location.reload();
        },
        error: xhr => showToast(xhr.responseJSON?.message || 'Server error', 'error')
    });
});

$(document).on('click', '.deleteRow', function () {
    if (!confirm('Delete this row?')) return;
    $.ajax({
        url: "<?php echo e(route('cn.delete',':id')); ?>".replace(':id', $(this).data('id')),
        type:'POST', data:{_token:"<?php echo e(csrf_token()); ?>"},
        success: () => { showToast('Deleted'); location.reload(); },
        error:   () => showToast('Delete failed','error')
    });
});

$('.generalFilter').on('change', function () {
    let filters = $('.generalFilter:checked').map((_, el) => el.value).get();
    $('#salesTable tbody tr').each(function () {
        let row = $(this), status = row.find('td:eq(9)').text().trim().toLowerCase(), show = true;
        if (filters.includes('synced') && status === 'synced') show = false;
        if (filters.includes('saved')  && status !== 'saved')  show = false;
        if (filters.includes('failed') && status !== 'failed') show = false;
        if (filters.includes('blank')) {
            if (row.find('input[name^="party_name"]').val() && row.find('select[name^="ledger"]').val()) show = false;
        }
        row.toggle(show);
    });
});

// ─── GST MODE SWITCH ───────────────────────────────────────────────────────
$('#gst_calc_mode').on('change', function () {
    let mode = $(this).val();
    if (mode === 'custom') {
        $('#custom_slots_section').show();
        $('#standard_tax_rows').hide();
        $('#custom_tax_rows').show();
        $('#igst_toggle_wrap').hide();
        // Only auto-build from items if NOT loading from DB
        if (!loadingCustomFromDB && $('#customSlotsBody tr').length === 0) {
            if ($('#no_item_section').is(':visible')) {
                buildCustomSlotsFromNoItemRows();
            } else {
                buildCustomSlotsFromItems();
            }
        }
    } else {
        $('#custom_slots_section').hide();
        $('#standard_tax_rows').show();
        $('#custom_tax_rows').hide();
        $('#igst_toggle_wrap').show();
        recalcTotals();
    }
});

$(document).on('change', '#noitem_sales_ledger', function () {
    applyGstLedgerMapping(true);
    recalcTotals();
});

$('#edit_is_igst').on('change', function () {
    if ($('#gst_calc_mode').val() === 'custom') {
        // In custom mode, IGST toggle rebuilds slot calculations
        applyCustomModeTaxSplit();
        recalcCustomSummary();
    } else {
        $('#editItemsBody tr').each(function () { recalcItemRow($(this)); });
        recalcTotals();
    }
});

// ─── VIEW MODAL ─────────────────────────────────────────────────────────────
$(document).on('click', '.viewRow', function () {
    let id = $(this).data('id');
    openEditModal();
    $('#addItemRow,#updateRow').hide();
    $('#editModal input,#editModal select,#editModal textarea').prop('disabled', true).css('pointer-events','none');

    $.ajax({
        url: "<?php echo e(route('cn.show',':id')); ?>".replace(':id', id), type:'GET',
        success: res => loadIntoModal(res, false)
    });
});

// ─── EDIT MODAL ──────────────────────────────────────────────────────────────
$(document).on('click', '.editRow', function () {
    let btn = $(this);
    $('#addItemRow,#updateRow').show();
    $('#editModal input,#editModal select,#editModal textarea').prop('disabled', false).css('pointer-events','auto');

    openEditModal();
    $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">Loading…</td></tr>');

    $.ajax({
        url: "<?php echo e(route('cn.show',':id')); ?>".replace(':id', btn.data('id')), type:'GET',
        success: res => loadIntoModal(res, true),
        error:   ()  => $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-3" style="color:#ef4444;">Failed to load.</td></tr>')
    });
});

// ─── CENTRAL LOAD FUNCTION (shared by edit + view) ──────────────────────────
function loadIntoModal(res, editable) {
    // Header fields
    $('#edit_id').val(res.id);
    $('#edit_invoice').val(res.note_no);
    $('#edit_date').val(res.note_date);
    $('#edit_gst').val(res.gst_no || '');
    $('#edit_address').val(res.address || '');
    $('#edit_pincode').val(res.pincode || '');
    $('#edit_city').val(res.city || '');
    $('#edit_remarks').val(res.remarks || res.Remarks || '');
    $('#edit_against_invoice').val(res.against_invoice || '');
    $('#noitem_gst_rate').val(res.gst_rate || '');

    // Select fields (trigger change for Select2)
    $('#edit_party').val(res.party_name).trigger('change');
    $('#edit_place').val(res.place_of_supply);
    $('#edit_voucher_type').val(res.vch_type || res.vchType);
    $('#noitem_sales_ledger').val(res.sales_ledger_name || res.sales_ledger).trigger('change');
    $('#igst_ledger').val(res.igst_id);
    $('#cgst_ledger').val(res.cgst_id);
    $('#sgst_ledger').val(res.sgst_id);
    $('#edit_is_igst').prop('checked', res.is_igst == 1);

    // Set initial summary from DB values
    setHiddenFields(res.taxable_amount||0, res.cgst||0, res.sgst||0, res.igst||0, res.total_amount||0);
    // updateSummaryUI(res.taxable_amount||0, res.cgst||0, res.sgst||0, res.igst||0, res.total_amount||0);
    updateSummaryUI(res.taxable_amount||0, res.cgst||0, res.sgst||0, res.igst||0, res.total_amount||0, res.roundoff || 0);

    // Items
    let tbody = $('#editItemsBody').empty();
    if (res.items && res.items.length > 0) {
        $('#standard_items_section').show();
        $('#no_item_section').hide();
        res.items.forEach(item => {
            let row = $(buildItemRow(item));
            tbody.append(row);
            row.find('.itemSelect').select2({ dropdownParent:$('#editModal'), width:'100%', placeholder:'Search Item...' });
        });
        updateSubtotalFooter();
    } else {
        $('#standard_items_section').hide();
        $('#no_item_section').show();
        $('#noItemBody').empty();
        const noItemRows = (res.noitem_rows && res.noitem_rows.length)
            ? res.noitem_rows
            : [{
                ledger: res.sales_ledger_id || res.sales_ledger_name || res.sales_ledger || '',
                ledger_name: res.sales_ledger_name || res.sales_ledger || '',
                gst: res.gst_rate || '',
                amount: res.taxable_amount || res.total_amount || 0
            }];

        noItemRows.forEach(row => addNoItemRow(row));
        $('#noitem_gst_rate').val(noItemRows[0]?.gst || res.gst_rate || '');
        $('#noitem_amount').val(noItemRows[0]?.amount || res.taxable_amount || res.total_amount || 0);
    }

    // Set GST mode and load custom slots if present
    let gstMode = res.gst_mode || 'standard';

    if (res.custom_gst && res.custom_gst.length > 0) {
        gstMode = 'custom';
        loadingCustomFromDB = true;
        $('#gst_calc_mode').val('custom').trigger('change');

        let cBody = $('#customSlotsBody').empty();
        const fallbackTaxable = parseFloat(res.taxable_amount || res.total_amount || 0);
        const isIGSTMode = res.is_igst == 1;

        res.custom_gst.forEach(slot => {
            // let taxable = parseFloat(slot.taxable) > 0 ? parseFloat(slot.taxable) : parseFloat(res.taxable_amount || 0);
            const rate = parseFloat(slot.gst_rate) || 0;
            const taxable = parseFloat(slot.taxable) > 0 ? parseFloat(slot.taxable) : fallbackTaxable;

            const hasAnyTaxAmount = [slot.igst_amount, slot.cgst_amount, slot.sgst_amount]
                .some(val => (parseFloat(val) || 0) > 0);

            if (!hasAnyTaxAmount && taxable > 0 && rate > 0) {
                if (isIGSTMode) {
                    slot.igst_amount = (taxable * rate / 100).toFixed(2);
                    slot.cgst_amount = 0;
                    slot.sgst_amount = 0;
                } else {
                    const halfTax = roundCurrency(taxable * rate / 2 / 100);
                    slot.igst_amount = 0;
                    slot.cgst_amount = halfTax.toFixed(2);
                    slot.sgst_amount = halfTax.toFixed(2);
                }
            }
            cBody.append(buildCustomSlotRow(slot, taxable));
        });

        loadingCustomFromDB = false;
        applyCustomModeTaxSplit();
        recalcCustomSummary(); // recalc from loaded slot values
    } else {
        $('#gst_calc_mode').val(gstMode).trigger('change');
        if (gstMode === 'standard') recalcTotals();
    }

    // Disable for view mode
    if (!editable) {
        $('#editModal input,#editModal select,#editModal textarea').prop('disabled', true).css('pointer-events','none');
        $('#addNoItemRow,.removeNoItem').hide();
    } else {
        $('#addNoItemRow,.removeNoItem').show();
    }
}

// ─── BUILD CUSTOM SLOT ROW (from DB data) ───────────────────────────────────
function buildCustomSlotRow(slot, taxable) {
    let igstOpts = IGST_LEDGERS.map(l => `<option value="${l.id}" ${l.id==slot.igst_ledger_id?'selected':''}>${l.name}</option>`).join('');
    let cgstOpts = CGST_LEDGERS.map(l => `<option value="${l.id}" ${l.id==slot.cgst_ledger_id?'selected':''}>${l.name}</option>`).join('');
    let sgstOpts = SGST_LEDGERS.map(l => `<option value="${l.id}" ${l.id==slot.sgst_ledger_id?'selected':''}>${l.name}</option>`).join('');

    return `<tr data-rate="${parseFloat(slot.gst_rate)}">
        <td><strong>${parseFloat(slot.gst_rate).toFixed(2)}%</strong></td>
        <td class="slot-taxable">${parseFloat(taxable).toFixed(2)}<input type="hidden" class="slot-sales-ledger-id" value="${slot.ledger_id || ''}"></td>
        <td><select class="slot-igst-ledger"><option value="">Select</option>${igstOpts}</select></td>
        <td><input type="number" class="slot-igst-amt" value="${parseFloat(slot.igst_amount||0).toFixed(2)}" step="any"></td>
        <td><select class="slot-cgst-ledger"><option value="">Select</option>${cgstOpts}</select></td>
        <td><input type="number" class="slot-cgst-amt" value="${parseFloat(slot.cgst_amount||0).toFixed(2)}" step="any"></td>
        <td><select class="slot-sgst-ledger"><option value="">Select</option>${sgstOpts}</select></td>
        <td><input type="number" class="slot-sgst-amt" value="${parseFloat(slot.sgst_amount||0).toFixed(2)}" step="any"></td>
    </tr>`;
}

// ─── BUILD CUSTOM SLOTS FROM ITEMS (new/fresh) ──────────────────────────────
function buildCustomSlotsFromItems() {
    let map = {}; // rate → taxable amount

    $('#editItemsBody tr').each(function () {
        let rate   = parseFloat($(this).find('.item-gst_rate').val()) || 0;
        let amount = parseFloat($(this).find('.item-amount').val())   || 0;
        if (!rate || !amount) return;
        if (!map[rate]) map[rate] = 0;
        map[rate] += amount;
    });

    let tbody = $('#customSlotsBody').empty();
    let isIGST = $('#edit_is_igst').is(':checked');

    Object.keys(map).sort((a,b) => a-b).forEach(rate => {
        let taxable = map[rate];
        let r       = parseFloat(rate);
        let igst    = isIGST ? (taxable * r / 100) : 0;
        let cgst    = isIGST ? 0 : (taxable * r / 2 / 100);
        let sgst    = isIGST ? 0 : (taxable * r / 2 / 100);

        let igstOpts = IGST_LEDGERS.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
        let cgstOpts = CGST_LEDGERS.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
        let sgstOpts = SGST_LEDGERS.map(l => `<option value="${l.id}">${l.name}</option>`).join('');

        tbody.append(`<tr data-rate="${r}">
            <td><strong>${r.toFixed(2)}%</strong></td>
            <td class="slot-taxable">${taxable.toFixed(2)}</td>
            <td><select class="slot-igst-ledger"><option value="">Select</option>${igstOpts}</select></td>
            <td><input type="number" class="slot-igst-amt" value="${igst.toFixed(2)}" step="any"></td>
            <td><select class="slot-cgst-ledger"><option value="">Select</option>${cgstOpts}</select></td>
            <td><input type="number" class="slot-cgst-amt" value="${cgst.toFixed(2)}" step="any"></td>
            <td><select class="slot-sgst-ledger"><option value="">Select</option>${sgstOpts}</select></td>
            <td><input type="number" class="slot-sgst-amt" value="${sgst.toFixed(2)}" step="any"></td>
        </tr>`);
    });

    applyCustomSlotGstLedgerMapping();
    recalcCustomSummary();
}

function buildCustomSlotsFromNoItemRows() {
    const tbody = $('#customSlotsBody').empty();
    const isIGST = $('#edit_is_igst').is(':checked');

    collectNoItemRows().forEach((row, index) => {
        const rate = parseFloat(row.gst) || 0;
        const taxable = parseFloat(row.amount) || 0;
        if (!rate || !taxable) return;

        const totalTax = taxable * rate / 100;
        const igst = isIGST ? roundCurrency(totalTax) : 0;
        const cgst = isIGST ? 0 : roundCurrency(taxable * rate / 200);
        const sgst = isIGST ? 0 : roundCurrency(taxable * rate / 200);
        const mapping = findSalesLedgerMapping(row.ledger, row.ledger_name) || {};
        const igstOpts = IGST_LEDGERS.map(l => `<option value="${l.id}" ${String(l.id) === String(mapping.igst_id || '') ? 'selected' : ''}>${l.name}</option>`).join('');
        const cgstOpts = CGST_LEDGERS.map(l => `<option value="${l.id}" ${String(l.id) === String(mapping.cgst_id || '') ? 'selected' : ''}>${l.name}</option>`).join('');
        const sgstOpts = SGST_LEDGERS.map(l => `<option value="${l.id}" ${String(l.id) === String(mapping.sgst_id || '') ? 'selected' : ''}>${l.name}</option>`).join('');

        tbody.append(`<tr data-rate="${rate}" data-row-index="${index}">
            <td><strong>${rate.toFixed(2)}%</strong></td>
            <td class="slot-taxable">${taxable.toFixed(2)}<input type="hidden" class="slot-sales-ledger-id" value="${row.ledger || ''}"></td>
            <td><select class="slot-igst-ledger"><option value="">Select</option>${igstOpts}</select></td>
            <td><input type="number" class="slot-igst-amt" value="${igst.toFixed(2)}" step="any"></td>
            <td><select class="slot-cgst-ledger"><option value="">Select</option>${cgstOpts}</select></td>
            <td><input type="number" class="slot-cgst-amt" value="${cgst.toFixed(2)}" step="any"></td>
            <td><select class="slot-sgst-ledger"><option value="">Select</option>${sgstOpts}</select></td>
            <td><input type="number" class="slot-sgst-amt" value="${sgst.toFixed(2)}" step="any"></td>
        </tr>`);
    });

    recalcCustomSummary();
}

function applyCustomModeTaxSplit() {
    if ($('#gst_calc_mode').val() !== 'custom') return;

    let isIGST = $('#edit_is_igst').is(':checked');

    $('#customSlotsBody tr').each(function () {
        let row = $(this);
        let rate = parseFloat(row.attr('data-rate')) || 0;
        let taxable = parseFloat(row.find('.slot-taxable').text()) || 0;
        let totalTax = (taxable * rate) / 100;

        if (isIGST) {
            row.find('.slot-igst-amt').val(roundCurrency(totalTax).toFixed(2));
            row.find('.slot-cgst-amt').val('0.00');
            row.find('.slot-sgst-amt').val('0.00');
        } else {
            let halfTax = roundCurrency(taxable * rate / 200);
            row.find('.slot-igst-amt').val('0.00');
            row.find('.slot-cgst-amt').val(halfTax.toFixed(2));
            row.find('.slot-sgst-amt').val(halfTax.toFixed(2));
        }
    });
}

// ─── RECALC CUSTOM SUMMARY (reads <input> values from slot rows) ─────────────
function recalcCustomSummary() {
    let totalTaxable = 0, totalIGST = 0, totalCGST = 0, totalSGST = 0;

    $('#customSlotsBody tr').each(function () {
        totalTaxable += parseFloat($(this).find('.slot-taxable').text()) || 0;
        totalIGST    += parseFloat($(this).find('.slot-igst-amt').val()) || 0;
        totalCGST    += parseFloat($(this).find('.slot-cgst-amt').val()) || 0;
        totalSGST    += parseFloat($(this).find('.slot-sgst-amt').val()) || 0;
    });

    let grand = totalTaxable + totalIGST + totalCGST + totalSGST;

    updateSummaryUI(totalTaxable, totalCGST, totalSGST, totalIGST, grand);
    setHiddenFields(totalTaxable, totalCGST, totalSGST, totalIGST, grand);

    // Update custom tax summary rows
    $('#custom_tax_rows').html(`
        <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(totalIGST)}</span></div>
        <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(totalCGST)}</span></div>
        <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(totalSGST)}</span></div>
    `);
}

// Live recalc when slot amounts are manually edited
$(document).on('input', '.slot-igst-amt,.slot-cgst-amt,.slot-sgst-amt', function () {
    recalcCustomSummary();
});

// ─── RECALC STANDARD TOTALS ──────────────────────────────────────────────────
function recalcTotals() {
    // Custom mode is handled by recalcCustomSummary — do not run standard code
    if ($('#gst_calc_mode').val() === 'custom') {
        recalcCustomSummary();
        return;
    }

    let totalAmt = 0, totalCGST = 0, totalSGST = 0, totalIGST = 0;

    if ($('#standard_items_section').is(':visible')) {
        $('#editItemsBody tr').each(function () {
            totalAmt  += parseFloat($(this).find('.item-amount').val()) || 0;
            totalCGST += parseFloat($(this).find('.item-cgst').val())   || 0;
            totalSGST += parseFloat($(this).find('.item-sgst').val())   || 0;
            totalIGST += parseFloat($(this).find('.item-igst').val())   || 0;
        });
    }

    if ($('#no_item_section').is(':visible')) {
        let isIGST = $('#edit_is_igst').is(':checked');
        collectNoItemRows().forEach(row => {
            const amount = parseFloat(row.amount) || 0;
            const rate = parseFloat(row.gst) || 0;
            const gstAmount = amount * rate / 100;
            totalAmt += amount;
            if (isIGST) {
                totalIGST += gstAmount;
            } else {
                const halfGst = roundCurrency(amount * rate / 200);
                totalCGST += halfGst;
                totalSGST += halfGst;
            }
        });
    }

    let grand = roundCurrency(totalAmt) + roundCurrency(totalCGST) + roundCurrency(totalSGST) + roundCurrency(totalIGST);
    updateSummaryUI(totalAmt, totalCGST, totalSGST, totalIGST, grand);
    setHiddenFields(totalAmt, totalCGST, totalSGST, totalIGST, grand);
    updateSubtotalFooter();
}

// ─── ITEM ROW RECALC ─────────────────────────────────────────────────────────
function recalcItemRow(row) {
    let qty    = parseFloat(row.find('.item-qty').val())      || 0;
    let rate   = parseFloat(row.find('.item-rate').val())     || 0;
    let gst    = parseFloat(row.find('.item-gst_rate').val()) || 0;
    let isIGST = $('#edit_is_igst').is(':checked');
    let amount = qty * rate;
    let cgst = 0, sgst = 0, igst = 0;

    if (gst > 0 && $('#gst_calc_mode').val() === 'standard') {
        if (isIGST) { igst = roundCurrency(amount * gst / 100); }
        else        { cgst = roundCurrency(amount * gst / 200); sgst = roundCurrency(amount * gst / 200); }
    }

    row.find('.item-amount').val(amount.toFixed(2));
    row.find('.item-cgst').val(cgst.toFixed(2));
    row.find('.item-sgst').val(sgst.toFixed(2));
    row.find('.item-igst').val(igst.toFixed(2));
    row.find('.item-total').val((roundCurrency(amount)+roundCurrency(cgst)+roundCurrency(sgst)+roundCurrency(igst)).toFixed(2));
}

// ─── NO-ITEM MODE RECALC ─────────────────────────────────────────────────────
$('#noitem_amount,#noitem_gst_rate').on('input', recalcTotals);

$(document).on('click', '#addNoItemRow', function () {
    addNoItemRow();
    if ($('#gst_calc_mode').val() === 'custom') {
        buildCustomSlotsFromNoItemRows();
    } else {
        recalcTotals();
    }
});

$(document).on('click', '.removeNoItem', function (e) {
    e.stopPropagation();
    $(this).closest('tr').remove();
    if ($('#gst_calc_mode').val() === 'custom') {
        buildCustomSlotsFromNoItemRows();
    } else {
        recalcTotals();
    }
});

$(document).on('input change', '.noitem-ledger,.noitem-gst,.noitem-amount', function () {
    if ($('#gst_calc_mode').val() === 'custom') {
        buildCustomSlotsFromNoItemRows();
    } else {
        recalcTotals();
    }
});

// ─── LIVE INPUT ON ITEMS ─────────────────────────────────────────────────────
$(document).on('input', '.item-qty,.item-rate,.item-gst_rate', function () {
    recalcItemRow($(this).closest('tr'));
    recalcTotals();
    // If custom mode, rebuild slots from updated items
    if ($('#gst_calc_mode').val() === 'custom' && !loadingCustomFromDB) {
        buildCustomSlotsFromItems();
    }
});

// ─── ITEM SELECT CHANGE ───────────────────────────────────────────────────────
$(document).on('change', '.itemSelect', function () {
    let name = $(this).val();
    let row  = $(this).closest('tr');
    let item = ITEM_MASTER.find(i => normalizeName(i.strItemName || i.name || '') === normalizeName(name));
    if (item) {
        row.find('.item-hsn').val(item.hsn || '');
        row.find('.item-gst_rate').val(item.gst_rate || '');
        row.find('.item-rate').val(item.rate || '');
        recalcItemRow(row);
        if ($('#gst_calc_mode').val() === 'custom' && !loadingCustomFromDB) {
            buildCustomSlotsFromItems();
        }
        applyItemGstMapping(name, true);
        recalcTotals();
    }
});

// ─── ADD ITEM ROW ─────────────────────────────────────────────────────────────
$('#addItemRow').click(function () {
    let row = $(buildItemRow({}));
    $('#editItemsBody').append(row);
    row.find('.itemSelect').select2({ dropdownParent:$('#editModal'), width:'100%', placeholder:'Search Item...' });
    updateSrNo();
    recalcTotals();
});

// ─── REMOVE ITEM ROW ──────────────────────────────────────────────────────────
$(document).on('click', '.receipt-del-btn', function () {
    $(this).closest('tr').remove();
    updateSrNo();
    recalcTotals();
    if ($('#gst_calc_mode').val() === 'custom' && !loadingCustomFromDB) {
        buildCustomSlotsFromItems();
    }
});

// ─── SAVE / UPDATE ────────────────────────────────────────────────────────────
$('#updateRow').click(function () {
    let mode  = $('#gst_calc_mode').val();
    let items = [];

    if ($('#standard_items_section').is(':visible')) {
        $('#editItemsBody tr').each(function () {
            let r = $(this);
            items.push({
                id:           r.find('.item-id').val(),
                item_name:    r.find('.item-name option:selected').text(),
                hsn_code:     r.find('.item-hsn').val(),
                gst_rate:     r.find('.item-gst_rate').val(),
                quantity:     r.find('.item-qty').val(),
                unit:         r.find('.item-unit').val(),
                rate:         r.find('.item-rate').val(),
                amount:       r.find('.item-amount').val(),
                sgst:         r.find('.item-sgst').val(),
                cgst:         r.find('.item-cgst').val(),
                igst:         r.find('.item-igst').val(),
                total_amount: r.find('.item-total').val(),
            });
        });
    }

    // Collect custom slots
    let custom_slots = [];
    if (mode === 'custom') {
        $('#customSlotsBody tr').each(function () {
            let r = $(this);
            custom_slots.push({
                rate:           parseFloat(r.data('rate')) || 0,
                taxable:        parseFloat(r.find('.slot-taxable').text()) || 0,
                sales_ledger_id: r.find('.slot-sales-ledger-id').val() || null,
                igst_ledger_id: r.find('.slot-igst-ledger').val() || null,
                igst_amount:    parseFloat(r.find('.slot-igst-amt').val()) || 0,
                cgst_ledger_id: r.find('.slot-cgst-ledger').val() || null,
                cgst_amount:    parseFloat(r.find('.slot-cgst-amt').val()) || 0,
                sgst_ledger_id: r.find('.slot-sgst-ledger').val() || null,
                sgst_amount:    parseFloat(r.find('.slot-sgst-amt').val()) || 0,
            });
        });
    }
    const noitemRows = collectNoItemRows();

    $.ajax({
        url: "<?php echo e(route('cn.update')); ?>", type:'POST',
        data: {
            _token:           "<?php echo e(csrf_token()); ?>",
            id:               $('#edit_id').val(),
            invoice_no:       $('#edit_invoice').val(),
            date:             $('#edit_date').val(),
            party_name:       $('#edit_party').val(),
            gst_no:           $('#edit_gst').val(),
            place_of_supply:  $('#edit_place').val(),
            sales_ledger:     $('#noitem_sales_ledger').val(),
            vchType:          $('#edit_voucher_type').val(),
            address:          $('#edit_address').val(),
            pincode:          $('#edit_pincode').val(),
            city:             $('#edit_city').val(),
            is_igst:          $('#edit_is_igst').is(':checked') ? 1 : 0,
            amount:           $('#edit_amount').val(),
            cgst:             $('#edit_cgst').val(),
            sgst:             $('#edit_sgst').val(),
            igst:             $('#edit_igst').val(),
            total_amount:     $('#edit_total_amount').val(),
            roundoff:         $('#edit_roundoff').val(),
            Remarks:          $('#edit_remarks').val(),
            gst_mode:         mode,
            igst_ledger:      $('#igst_ledger').val(),
            cgst_ledger:      $('#cgst_ledger').val(),
            sgst_ledger:      $('#sgst_ledger').val(),
            noitem_amount:    $('#noitem_amount').val(),
            sales_ledger_id:  $('#noitem_sales_ledger').val(),
            sales_ledger_name:$('#noitem_sales_ledger option:selected').text(),
            gst_rate:         $('#noitem_gst_rate').val(),
            against_invoice:  $('#edit_against_invoice').val(),
            items:            items,
            entry_mode: $('#no_item_section').is(':visible') ? 'noitem' : 'item',
            custom_slots:     custom_slots,
            noitem_rows:       noitemRows,
        },
        success: (res) => {
                if (res.status) {
                    showToast(res.message || 'Updated successfully', 'success');
                    //closeEditModal();
                    location.reload();
                } else {
                    showToast(res.message || 'Something went wrong', 'error');
                }
             },
        //error:   () => showToast('Update failed', 'error')
        error: (xhr) => {
            const message = xhr.responseJSON?.message || 'Update failed';
            showToast(message, 'error');
        }
    });
});

// ─── LEDGER FORM ──────────────────────────────────────────────────────────────
$('#ledgerForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        url: "<?php echo e(route('sales.ledger.store')); ?>", type:'POST', data:$(this).serialize(),
        success: () => {
            let name = $('input[name="Name"]').val();
            ['#edit_party','#noitem_sales_ledger'].forEach(sel => {
                $(sel).append(new Option(name, name)).trigger('change');
            });
            $('.ledgerSelect').each(function () { $(this).append(new Option(name, name)); });
            closeLedgerModal();
            $('#ledgerForm')[0].reset();
        },
        error: () => showToast('Error saving ledger', 'error')
    });
});

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function roundCurrency(value) {
    return Math.round(((parseFloat(value) || 0) + Number.EPSILON) * 100) / 100;
}

function fmt(v) {
    return parseFloat(v||0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
}

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
    return roundCurrency(roundedTotal - total);
}

function applyRoundOffSummary(total, roundOff) {
            total = parseFloat(total) || 0;
            roundOff = parseFloat(roundOff) || 0;
            let roundedTotal = roundCurrency(total) + roundCurrency(roundOff);

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

function updateSummaryUI(amt, cgst, sgst, igst, grand, roundoff = null) {
    $('#sum_amount').text(fmt(amt));
    $('#sum_cgst').text(fmt(cgst));
    $('#sum_sgst').text(fmt(sgst));
    $('#sum_igst').text(fmt(igst));
    //$('#sum_grand_total').text(fmt(grand));
    setRoundOffSummary(grand, roundoff);
}

function setHiddenFields(amt, cgst, sgst, igst, grand) {
    $('#edit_amount').val(parseFloat(amt).toFixed(2));
    $('#edit_cgst').val(parseFloat(cgst).toFixed(2));
    $('#edit_sgst').val(parseFloat(sgst).toFixed(2));
    $('#edit_igst').val(parseFloat(igst).toFixed(2));
    // $('#edit_total_amount').val(parseFloat(grand).toFixed(2));
    $('#edit_total_amount').val(parseFloat($('#sum_grand_total').text().replace(/,/g, '') || grand).toFixed(2));
}

function updateSubtotalFooter() {
    let sumRate = 0, sumAmt = 0;
    $('#editItemsBody tr').each(function () {
        sumRate += parseFloat($(this).find('.item-rate').val()) || 0;
        sumAmt  += parseFloat($(this).find('.item-amount').val()) || 0;
    });
    $('#foot_rate_total').text(fmt(sumRate));
    $('#foot_amount_total').text(fmt(sumAmt));
}

function updateSrNo() {
    $('#editItemsBody tr').each(function (i) { $(this).find('.td-sr').text(i+1); });
}

function buildItemRow(item) {
    let srNo = $('#editItemsBody tr').length + 1;
    return `<tr>
        <input type="hidden" class="item-id" value="${item.id||''}">
        <td class="td-sr" style="width:28px;text-align:center;font-size:11px;color:#9ca3af;">${srNo}</td>
        <td style="min-width:180px;">
            <select class="item-name itemSelect" style="width:100%;">${buildItemOptions(item.item_name||'')}</select>
        </td>
        <td style="width:80px;"><input type="text" class="item-hsn" value="${item.hsn_code||''}" placeholder="HSN" style="text-align:center;"></td>
        <td style="width:60px;"><input type="number" class="item-gst_rate" value="${item.gst_rate||''}" placeholder="%" step="any" style="text-align:right;"></td>
        <td style="width:65px;"><input type="number" class="item-qty" value="${item.quantity||''}" placeholder="0" step="any" style="text-align:right;"></td>
        <td style="width:55px;"><input type="text" class="item-unit" value="${item.unit||'NOS'}" style="text-align:center;"></td>
        <td style="width:85px;"><input type="number" class="item-rate" value="${item.rate||''}" placeholder="0.00" step="any" style="text-align:right;"></td>
        <td style="width:85px;"><input type="number" class="item-amount" value="${item.amount||''}" readonly style="text-align:right;background:#f8fafc;"></td>
        <td style="width:30px;text-align:center;">
            <button type="button" class="receipt-del-btn" title="Remove"><i class="fa-solid fa-times" style="font-size:11px;"></i></button>
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

function buildItemOptions(selected) {
    let opts = '<option value="">Select Item</option>';
    ITEM_MASTER.forEach(item => {
        let name = item.strItemName || item.name || '';
        opts += `<option value="${name}" ${name===selected?'selected':''}>${name}</option>`;
    });
    return opts;
}

// ─── MODAL OPEN / CLOSE ───────────────────────────────────────────────────────
function openEditModal()    { document.getElementById('editModal').classList.add('show'); }
function closeEditModal()   {
    document.getElementById('editModal').classList.remove('show');
    // Reset state
    $('#updateRow,#addItemRow').show();
    $('#editModal input,#editModal select,#editModal textarea').prop('disabled', false).css('pointer-events','auto');
    // Reset custom flag
    loadingCustomFromDB = false;
}
function openViewModal()    { document.getElementById('viewModal').classList.add('show'); }
function closeViewModal()   { document.getElementById('viewModal').classList.remove('show'); }
function openLedgerModal()  { document.getElementById('ledgerModal').classList.add('show'); }
function closeLedgerModal() { document.getElementById('ledgerModal').classList.remove('show'); }

window.onclick = e => {
    if (e.target === document.getElementById('ledgerModal')) closeLedgerModal();
};

// ─── SELECT2 INIT for modal party dropdown ────────────────────────────────────
$(function () {
    $('#edit_party').select2({ dropdownParent:$('#editModal'), width:'100%', placeholder:'Search Party...', allowClear:true });
    $('#noitem_sales_ledger').select2({ dropdownParent:$('#editModal'), width:'100%', placeholder:'Search Ledger...', allowClear:true });
});

// ─── TOAST ────────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    let toast = document.createElement('div');
    toast.innerText = message;
    Object.assign(toast.style, {
        position:'fixed', bottom:'20px', right:'20px',
        background: type==='success' ? '#16a34a' : '#dc2626',
        color:'#fff', padding:'10px 16px', borderRadius:'6px',
        fontSize:'13px', zIndex:'99999', boxShadow:'0 4px 10px rgba(0,0,0,.2)'
    });
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/bulkupload/credit_note/preview.blade.php ENDPATH**/ ?>