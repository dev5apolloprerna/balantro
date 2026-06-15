
{{-- ══════════════════════════════════════════════════════
     EDIT MODAL
══════════════════════════════════════════════════════ --}}

<div id="editModal" class="modal">
    <div class="receipt-wrapper">
        <input type="hidden" id="edit_id">

        {{-- RECEIPT HEADER --}}
        <div class="receipt-head">
            <div class="receipt-head-left">
                <div class="receipt-company">Sales Bill</div>
                <div class="receipt-subtitle">Tax Invoice</div>
            </div>
            <div class="receipt-head-right">
                <button type="button" class="receipt-close-btn" onclick="closeEditModal()">✕</button>
            </div>
        </div>

        {{-- META GRID --}}
        <div class="receipt-meta-grid">
            {{-- Left: Supplier --}}
            <div class="receipt-meta-block">
                <div class="receipt-block-title"><i class="fa-solid fa-building text-blue-400 mr-1"></i> Supplier Details</div>
                <div class="receipt-field-row">
                    <label>Party Name</label>
                    <div style="display:flex; gap:6px; width:100%;">
                        <select id="edit_party" class="receipt-input party-select ledgerSelect" style="flex:1;">
                            @foreach($ledgers as $ledger)
                            <option value="{{ $ledger->name }}">{{ $ledger->name }}</option>
                            @endforeach
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
            {{-- Right: Invoice --}}
            <div class="receipt-meta-block">
                <div class="receipt-block-title"><i class="fa-solid fa-file-invoice text-blue-400 mr-1"></i> Invoice Details</div>
                <div id="invoice_sales_ledger_wrap" class="receipt-field-row">
                    <label>Sales Ledger</label>
                    <select id="noitem_sales_ledger" class="receipt-input ledgerSelect">
                        <option value="">Select Ledger</option>
                        @foreach($salesLedgers as $ledger)
                        <option value="{{ $ledger->name }}">{{ $ledger->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="receipt-field-row">
                    <label>Invoice No.</label>
                    <input type="text" id="edit_invoice" class="receipt-input" placeholder="Invoice Number">
                </div>
                <div class="receipt-field-row">
                    <label>Date</label>
                    <input type="date" id="edit_date" class="receipt-input" min="{{ session('year_from') }}" max="{{ session('year_to') }}">
                </div>
                <div class="receipt-field-row">
                    <label>Voucher Type</label>
                    <select id="edit_voucher_type" class="receipt-input">
                        @foreach($vchTypes as $vchType)
                        <option value="{{ $vchType }}">{{ $vchType }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="receipt-field-row">
                    <label>Place Of Supply</label>
                    <select id="edit_place" class="receipt-input">
                        <option value="">Select State</option>
                        @foreach($states as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ITEMS SECTION --}}
        <div class="">
            <div class="receipt-items-header">
                <span><i class="fa-solid fa-list text-blue-400 mr-1"></i> Item Details</span>
                <div style="display:flex;align-items:center;gap:10px;">
                    <label style="display:flex;align-items:center;gap:5px;margin:0;font-size:12px;">
                        <input type="radio" name="entry_mode" value="item" checked style="width:auto;">
                        With Item
                    </label>
                    <label style="display:flex;align-items:center;gap:5px;margin:0;font-size:12px;">
                        <input type="radio" name="entry_mode" value="noitem" style="width:auto;">
                        Without Item
                    </label>
                    {{-- GST Mode Toggle --}}
                    <div class="receipt-field-row" style="margin:0;">
                        <label style="width:auto;padding-right:4px;">GST Mode</label>
                        <select id="gst_calc_mode" name="gst_calc_mode" class="receipt-input" style="width:180px;">
                            <option value="standard">Standard (Auto Calculate)</option>
                            <option value="custom">Custom (Manual Slots)</option>
                        </select>
                    </div>
                    {{-- IGST toggle (only in standard mode) --}}
                    <div id="igst_toggle_wrap" class="receipt-field-row" style="margin:0;">
                        <label style="width:auto;padding-right:4px;white-space:nowrap;">Use IGST</label>
                        <input type="checkbox" id="edit_is_igst" style="width:auto;accent-color:#2563eb;">
                    </div>
                    <button type="button" id="addItemRow" class="receipt-add-btn">
                        <i class="fa-solid fa-plus mr-1"></i> Add Row
                    </button>
                    <button type="button" id="addNoItemRow" class="receipt-add-btn" style="display:none;">
                        <i class="fa-solid fa-plus mr-1"></i> Add More
                    </button>
                </div>
            </div>

            {{-- STANDARD MODE: items table --}}
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
                <div class="receipt-table-wrap">
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th>Sales Ledger</th>
                                <th>GST %</th>
                                <th>Amount</th>
                                <th class="col-action"></th>
                            </tr>
                        </thead>
                        <tbody id="noItemBody"></tbody>
                    </table>
                </div>
                <input type="hidden" id="noitem_gst_rate" value="0">
                <input type="hidden" id="noitem_amount" value="0">
            </div>

            {{-- CUSTOM MODE: rate-wise slots --}}
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
                            {{-- rendered by JS --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAX SUMMARY --}}
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

                {{-- Standard mode: single SGST/CGST/IGST row --}}
                <div id="standard_tax_rows">

                    <!-- IGST -->
                    <div class="tax-row">
                        <span class="tax-label">IGST</span>
                        <select id="igst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            @foreach($iGstLedgers as $ledger)
                            <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                            @endforeach
                        </select>
                        <span class="tax-value" id="sum_igst">0.00</span>
                    </div>

                    <!-- CGST -->
                    <div class="tax-row">
                        <span class="tax-label">CGST</span>
                        <select id="cgst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            @foreach($cGstLedgers as $ledger)
                            <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                            @endforeach
                        </select>
                        <span class="tax-value" id="sum_cgst">0.00</span>
                    </div>

                    <!-- SGST -->
                    <div class="tax-row">
                        <span class="tax-label">SGST</span>
                        <select id="sgst_ledger" class="receipt-input" style="width:140px;">
                            <option value="">Select Ledger</option>
                            @foreach($sGstLedgers as $ledger)
                            <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                            @endforeach
                        </select>
                        <span class="tax-value" id="sum_sgst">0.00</span>
                    </div>

                </div>

                {{-- Custom mode: rate-wise tax summary rows --}}
                <div id="custom_tax_rows" style="display:none;">
                    {{-- rendered by recalcTotals() --}}
                </div>

                <div class="tax-row">
                    <span class="tax-label">Round Off</span>
                    <span class="tax-value" id="sum_roundoff">0.00</span>
                </div>

                <div class="tax-row grand-total-row">
                    <span class="tax-label">GRAND TOTAL</span>
                    <span class="tax-value" id="sum_grand_total">0.00</span>
                </div>
            </div>
        </div>

        {{-- Hidden fields (keep existing save logic working) --}}
        <input type="hidden" id="edit_amount">
        <input type="hidden" id="edit_sgst">
        <input type="hidden" id="edit_cgst">
        <input type="hidden" id="edit_igst">
        <input type="hidden" id="edit_roundoff">
        <input type="hidden" id="edit_total_amount">

        {{-- RECEIPT FOOTER --}}
        <div class="receipt-footer">
            <div class="receipt-footer-note">This is a computer-generated Sales record.</div>
            <div class="receipt-footer-actions">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                <button type="button" id="updateRow" class="submit-btn">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>
