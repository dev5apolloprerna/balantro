@extends('layouts.super_admin')
@section('content')
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
                    {{ $rows->count() }}
                </span>
            </div> -->
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
                    {{ $rows->count() }}
                </span>
            </div>

            <div class="flex gap-2">
                @if(session('client_name'))
                <div class="text-sm text-green-600 font-semibold">
                    {{ session('client_name') }}
                </div>
                @endif
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
        <!-- <form id="purchaseForm" method="POST" action="{{ route('purchase.save') }}"> -->
        <form id="purchaseForm">
            @csrf
            <div class="w-full">
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
                        @foreach($rows as $index=>$row)
                        <tr class="border-b border-neutral-700 hover:bg-neutral-800 transition">
                            <td class="px-3 py-2">
                                <input type="checkbox" name="selected[]" value="{{$row->id}}">
                            </td>
                            <td class="px-3 py-2">
                                {{ $index+1 }}
                            </td>
                            <td class="px-3 py-2">
                                <input type="date"
                                    name="date[{{$row->id}}]"
                                    value="{{ \Carbon\Carbon::parse($row->note_date)->format('Y-m-d') }}"
                                    class="inputCell">
                            </td>
                            <td class="px-3 py-2">
                                <input type="text"
                                    name="invoice_no[{{$row->id}}]"
                                    value="{{$row->invoice_no}}"
                                    class="inputCell">
                            </td>
                            <td class="px-3 py-2">
                                <select name="voucher_type[{{$row->id}}]" class="inputCell voucherSelect">
                                    @foreach($vchTypes as $vchType)
                                    <option value="{{$vchType}}"
                                        {{ strtolower(trim($vchType)) == strtolower(trim($row->vchType))  ? 'selected' : '' }}>{{$vchType}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <!-- Party Name -->
                                <input type="text"
                                    name="party_name[{{$row->id}}]"
                                    value="{{$row->party_name}}"
                                    class="inputCell mb-1">
                                <!-- Ledger -->
                                <select name="party_ledger[{{$row->id}}]"
                                    class="ledgerSelect inputCell">
                                    <option value="">Select Ledger</option>
                                    @foreach($ledgers as $ledger)
                                    <option value="{{$ledger->name}}"
                                        {{ trim($row->party_name) == trim($ledger->name) ? 'selected' : '' }}>
                                        {{$ledger->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                {{$row->gst_no}}
                            </td>
                            <td class="px-3 py-2">
                                <select name="place_of_supply[{{$row->id}}]"
                                    class="inputCell">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                    <option value="{{$state}}"
                                        {{ strtolower(trim($state)) == strtolower(trim($row->place_of_supply)) ? 'selected':''}}>
                                        {{$state}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- <td class="px-3 py-2">
                                <select name="ledger[{{$row->id}}]" class="ledgerSelect inputCell">
                                    <option>Select Ledger</option>
                                    @foreach($ledgers as $ledger)
                                    <option value="{{$ledger->name}}"
                                        {{ trim($ledger->name) == trim($row->purchase_ledger) ? 'selected':''}}>{{$ledger->name}}</option>
                                    @endforeach
                                </select>
                            </td> -->
                            <td class="px-3 py-2 text-right">
                                {{ number_format($row->total_amount,2) }}
                            </td>
                            <td class="px-3 py-2">
                                <span class="text-yellow-400">
                                    {{$row->status}}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                {{-- VIEW BUTTON --}}
                                <button type="button" class="viewRow text-green-400 hover:text-green-300" 
                                    title="View" data-id="{{ $row->id }}">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <!-- <button
                                    type="button"
                                    class="text-blue-400 editRow"
                                    data-id="{{$row->id}}"
                                    data-invoice="{{$row->invoice_no}}"
                                    data-date="{{$row->date}}"
                                    data-gst_no="{{$row->gst_no}}"
                                    data-vchtype="{{$row->vchType}}"
                                    data-party="{{$row->party_name}}"
                                    data-place="{{$row->place_of_supply}}"
                                    data-ledger="{{$row->purchase_ledger}}"
                                    data-amount="{{$row->total_amount}}"
                                    data-item="{{$row->item_name}}"
                                    data-qty="{{$row->quantity}}"
                                    data-rate="{{$row->rate}}"
                                    data-cgst="{{$row->cgst}}"
                                    data-sgst="{{$row->sgst}}"
                                    data-igst="{{$row->igst}}">
                                    <i class="fa-solid fa-pen"></i>
                                </button> -->
                                {{-- EDIT BUTTON --}}
                                <button type="button"
                                    class="text-blue-400 hover:text-blue-300 editRow"
                                    title="Edit"
                                    data-id="{{ $row->id }}"
                                    data-invoice="{{ $row->invoice_no }}"
                                    data-date="{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}"
                                    data-gst_no="{{ $row->gst_no }}"
                                    data-vchtype="{{ $row->vchType }}"
                                    data-party="{{ $row->party_name }}"
                                    data-place="{{ $row->place_of_supply }}"
                                    data-ledger="{{ $row->purchase_ledger }}"
                                    data-amount="{{ $row->total_amount }}"
                                    data-cgst="{{ $row->cgst }}"
                                    data-sgst="{{ $row->sgst }}"
                                    data-igst="{{ $row->igst }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button class="text-red-500 deleteRow" data-id="{{$row->id}}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     EDIT MODAL
══════════════════════════════════════════════════════ --}}

<div id="editModal" class="modal">
    <div class="receipt-wrapper">
        <input type="hidden" id="edit_id">

        {{-- RECEIPT HEADER --}}
        <div class="receipt-head">
            <div class="receipt-head-left">
                <div class="receipt-company">Debit Notes Bill</div>
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
                <div class="receipt-field-row">
                    <label>Purchase Ledger</label>
                    <select id="noitem_purchase_ledger" class="receipt-input ledgerSelect">
                        <option value="">Select Ledger</option>
                        @foreach($purchaseLedgers as $ledger)
                            <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                        @endforeach
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
                <div class="receipt-field-row">
                    <label>Against Invoice</label>
                    <input type="text" id="edit_against_invoice" class="receipt-input" placeholder="Invoice Number">
                </div>
            </div>
        </div>

        {{-- ITEMS SECTION --}}
        <div class="">
            <div class="receipt-items-header">
                <span><i class="fa-solid fa-list text-blue-400 mr-1"></i> Item Details</span>
                <div style="display:flex;align-items:center;gap:10px;">
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
                        <i class="fa-solid fa-plus mr-1"></i> Add Row
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

            <div id="no_item_section" style="display:none; padding:10px;">
                <input type="hidden" id="noitem_gst_rate" value="0">
                <input type="hidden" id="noitem_amount">
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
                    <input type="number" step="0.01" id="sum_roundoff" class="receipt-input tax-value" style="width:90px;text-align:right;" value="0.00">
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
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="Name">
                    </div>
                    <div class="form-group">
                        <label>Parent</label>
                        <select name="Parent">
                            <option>Select Parent</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->strParents }}">{{ $parent->strParents }}</option>
                            @endforeach
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
                            @foreach($states as $state)
                            <option value="{{$state}}">{{$state}}</option>
                            @endforeach
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

{{-- ══════════════════════════════════════════════════════
     VIEW MODAL  (read-only)
══════════════════════════════════════════════════════ --}}
<div id="viewModal" class="modal">
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
    #purchaseTable {
        width: 100%;
        table-layout: fixed;
    }

    #purchaseTable th,
    #purchaseTable td {
        word-wrap: break-word;
        white-space: normal;
    }
    #purchaseTable th:nth-child(1) { width: 40px; }   /* checkbox */
    #purchaseTable th:nth-child(2) { width: 50px; }   /* SR */
    #purchaseTable th:nth-child(3) { width: 120px; }  /* DATE */
    #purchaseTable th:nth-child(5) { width: 120px; }  /* VOUCHER */
    #purchaseTable th:nth-child(9) { width: 100px; }  /* AMOUNT */
    #purchaseTable th:nth-child(11) { width: 120px; } /* ACTION */
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
</style>
@endsection
@section('scripts')

<script>
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
            url: "{{ route('purchase.ledger.store') }}",
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


    const ledgers = @json(collect($ledgers)->pluck('name'));
    const states = @json($states);
    const vouchers = @json($vchTypes);
    let IGST_LEDGERS = @json($iGstLedgers);
    let CGST_LEDGERS = @json($cGstLedgers);
    let SGST_LEDGERS = @json($sGstLedgers);
    const ITEM_MASTER = @json($stockItems);
    const PURCHASE_GST_MAPPINGS = @json($purchaseGstMappings ?? []);
    const PURCHASE_LEDGERS = @json($purchaseLedgers ?? []);

    function normalizeLedgerName(name) {
        return String(name || '').replace(/["']/g, '').trim().toLowerCase();
    }

    function findPurchaseLedgerMapping(ledgerValue = '', ledgerText = '') {
        return PURCHASE_GST_MAPPINGS.find(mapping =>
            String(mapping.id) === String(ledgerValue) ||
            normalizeLedgerName(mapping.name) === normalizeLedgerName(ledgerValue) ||
            normalizeLedgerName(mapping.name) === normalizeLedgerName(ledgerText)
        ) || null;
    }

    function getSelectedPurchaseLedgerMapping() {
        const select = $('#noitem_purchase_ledger');
        return findPurchaseLedgerMapping(select.val(), select.find('option:selected').text());
    }

    function mappedGstLedgerId(type, existing = null, ledgerValue = '', ledgerText = '') {
        if (existing) {
            return existing;
        }

        const mapping = ledgerValue || ledgerText
            ? findPurchaseLedgerMapping(ledgerValue, ledgerText)
            : getSelectedPurchaseLedgerMapping();

        return mapping ? mapping[`${type}_id`] : null;
    }

    $(document).on('change', '#noitem_purchase_ledger', function() {
        $('#igst_ledger').val(mappedGstLedgerId('igst')).trigger('change');
        $('#cgst_ledger').val(mappedGstLedgerId('cgst')).trigger('change');
        $('#sgst_ledger').val(mappedGstLedgerId('sgst')).trigger('change');
        recalcTotals();
    });

    function buildNoItemLedgerOptions(selected = '') {
        let html = '<option value="">Select Ledger</option>';

        PURCHASE_LEDGERS.forEach(ledger => {
            const selectedMatch =
                String(ledger.id) === String(selected) ||
                normalizeLedgerName(ledger.name) === normalizeLedgerName(selected);

            html += `<option value="${ledger.id}" ${selectedMatch ? 'selected' : ''}>${ledger.name}</option>`;
        });

        return html;
    }

    function addNoItemRow(row = {}) {
        const selectedLedger = row.ledger || row.ledger_id || row.purchase_ledger_id || row.ledger_name || row.purchase_ledger_name || row.purchase_ledger || '';
        const tr = `
            <tr>
                <td>
                    <select class="receipt-input noitem-ledger">
                        ${buildNoItemLedgerOptions(selectedLedger)}
                    </select>
                </td>
                <td>
                    <input type="number" class="receipt-input noitem-gst" value="${row.gst || row.gst_rate || 0}" step="any">
                </td>
                <td>
                    <input type="number" class="receipt-input noitem-amount" value="${row.amount || row.taxable || row.taxable_amount || 0}" step="any">
                </td>
                <td>
                    <button type="button" class="removeNoItem receipt-del-btn">&times;</button>
                </td>
            </tr>
        `;

        $('#noItemBody').append(tr);
        $('#noItemBody tr:last .noitem-ledger').select2({
            width: '100%',
            placeholder: 'Search Ledger...',
            dropdownParent: $('#editModal'),
            allowClear: true
        });
    }

    function collectNoItemRows() {
        let rows = [];

        $('#noItemBody tr').each(function() {
            rows.push({
                ledger: $(this).find('.noitem-ledger').val(),
                gst: $(this).find('.noitem-gst').val(),
                amount: $(this).find('.noitem-amount').val()
            });
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
            url: "{{ route('transaction_processing.debit_note_sumbit') }}",
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
            url: "{{ route('dn.delete', ':id') }}".replace(':id', id),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
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
    //         url: "{{ route('dn.show', ':id') }}".replace(':id', id),
    //         type: "GET",
    //         success: function (res) {

    //             $('#edit_id').val(res.id);
    //             $('#edit_invoice').val(res.note_no);
    //             $('#edit_date').val(res.note_date);
    //             $('#edit_gst').val(res.gst_no);
    //             // $('#edit_party').val(res.party_name);
    //             $('#edit_party').val(res.party_name).trigger('change');
    //             $('#edit_place').val(res.place_of_supply);
    //             $('#edit_voucher_type').val(res.vchType);
    //             $('#edit_address').val(res.address);
    //             $('#edit_pincode').val(res.pincode);
    //             $('#edit_city').val(res.city);
    //             // $('#edit_is_igst').val(res.is_igst);
    //             $('#edit_is_igst').prop('checked', res.is_igst == 1);
    //             $('#edit_remarks').val(res.remarks);
    //             $('#cgst_ledger').val(res.cgst_id).trigger('change');
    //             $('#sgst_ledger').val(res.sgst_id).trigger('change');
    //             $('#igst_ledger').val(res.igst_id).trigger('change');
    //             // Items
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
    //                 });
    //             } else {
    //                 $('#standard_items_section').hide(); 
    //                 $('#no_item_section').show();
    //                 $('#noitem_amount').val(res.total_amount);
    //                 $('#noitem_sales_ledger').val(res.sales_ledger_id).trigger('change');
    //             }
    //             recalcTotals();
    //         }
    //     });
    // });

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
            url: "{{ route('dn.show', ':id') }}".replace(':id', id),
            type: "GET",
            success: function (res) {
                // Fill header fields
                $('#edit_id').val(res.id);
                $('#edit_invoice').val(res.note_no);
                $('#edit_date').val(res.note_date);
                $('#edit_gst').val(res.gst_no);
                $('#edit_party').val(res.party_name);
                $('#edit_place').val(res.place_of_supply);
                $('#edit_voucher_type').val(res.vch_type);
                $('#edit_address').val(res.address);
                $('#edit_pincode').val(res.pincode);
                $('#edit_city').val(res.city);
                // $('#edit_is_igst').val(res.is_igst);
                $('#edit_is_igst').prop('checked', res.is_igst == 1);
                toggleGSTLedger();
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                $('#edit_amount').val(parseFloat(res.taxable_amount || 0).toFixed(2));
                $('#edit_cgst').val(parseFloat(res.cgst || 0).toFixed(2));
                $('#edit_sgst').val(parseFloat(res.sgst || 0).toFixed(2));
                $('#edit_igst').val(parseFloat(res.igst || 0).toFixed(2));
                $('#edit_total_amount').val(parseFloat(res.total_amount || 0).toFixed(2));

                $('#sum_amount').text(parseFloat(res.taxable_amount || 0).toFixed(2));
                $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                // $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                $('#cgst_ledger').val(res.cgst_id).trigger('change');
                $('#sgst_ledger').val(res.sgst_id).trigger('change');
                $('#igst_ledger').val(res.igst_id).trigger('change');

                // UI update
                $('#sum_amount').text(parseFloat(res.taxable_amount).toFixed(2));
                // $('#sum_cgst').text(parseFloat(res.cgst).toFixed(2));
                // $('#sum_sgst').text(parseFloat(res.sgst).toFixed(2));
                // $('#sum_igst').text(parseFloat(res.igst).toFixed(2));
                // $('#sum_grand_total').text(parseFloat(res.total_amount).toFixed(2));

                if ($('#editModal input:disabled').length > 0) {
                    // VIEW MODE → use API values
                    $('#sum_amount').text(parseFloat(res.taxable_amount).toFixed(2));
                    $('#sum_cgst').text(parseFloat(res.cgst).toFixed(2));
                    $('#sum_sgst').text(parseFloat(res.sgst).toFixed(2));
                    $('#sum_igst').text(parseFloat(res.igst).toFixed(2));
                    //$('#sum_grand_total').text(parseFloat(res.total_amount).toFixed(2));
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
                } else {
                    recalcTotals(); // EDIT MODE
                }

                $('#edit_remarks').val(res.remarks);
                setSelectValueByTextOrValue($('#noitem_purchase_ledger'), res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger);
                $('#edit_against_invoice').val(res.against_invoice);
                $('#noitem_gst_rate').val(res.gst_rate);
                // Items
                let tbody = $('#editItemsBody').empty();
                if (res.items && res.items.length > 0) {

                    $('#standard_items_section').show();
                    $('#no_item_section').hide();
                    $('#addNoItemRow').hide();
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
                    if (res.gst_mode === 'custom') {
                        generateSlotsFromItems();
                    }
                } else {
                    $('#standard_items_section').hide(); 
                    $('#no_item_section').show();
                    $('#addNoItemRow').hide();
                    $('#noItemBody').empty();

                    // $('#noitem_amount').val(res.total_amount);
                    if (res.custom_gst && res.custom_gst.length) {
                        res.custom_gst.forEach(slot => addNoItemRow({
                            ledger: slot.ledger_id || slot.purchase_ledger_id || slot.ledger_name || res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger,
                            gst: slot.gst_rate,
                            amount: slot.taxable || slot.amount || 0
                        }));
                    } else {
                        addNoItemRow({
                            ledger: res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger,
                            gst: res.gst_rate || 0,
                            amount: res.taxable_amount || res.total_amount || 0
                        });
                    }

                    $('#noItemBody input, #noItemBody select').prop('disabled', true).css('pointer-events', 'none');
                    $('#noItemBody .receipt-del-btn').hide();
                    $('#noitem_amount').val(res.taxable_amount);
                    recalcTotals();
                }       
                // if (res.gst_mode === 'custom' && res.custom_gst && res.custom_gst.length) {
                //     //renderCustomSlots(res.custom_gst);
                //     //renderCustomSlotsFromItems();
                //     //renderCustomSlots(res.custom_gst);
                //     generateSlotsFromItems();
                //     // autoFillTaxableFromItems();
                // }           

                // recalcTotals();
            }
        });
    });

    function openViewModal()  { document.getElementById('viewModal').classList.add('show'); }
    function closeViewModal() { document.getElementById('viewModal').classList.remove('show'); }
    function normalizeLedgerValue(value) {
        return String(value || '').replace(/['"]/g, '').trim().toLowerCase();
    }
    function setSelectValueByTextOrValue($select, value) {
        if (!value) {
            $select.val('').trigger('change');
            return;
        }

        if ($select.find(`option[value="${value}"]`).length) {
            $select.val(value).trigger('change');
            return;
        }

        const normalized = normalizeLedgerValue(value);
        const match = $select.find('option').filter(function () {
            return normalizeLedgerValue($(this).val()) === normalized || normalizeLedgerValue($(this).text()) === normalized;
        }).first();

        if (match.length) {
            $select.val(match.val()).trigger('change');
        } else {
            $select.val(value).trigger('change');
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
    //         url: "{{ route('dn.show', ':id') }}".replace(':id', id),
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
    //             $('#edit_remarks').val(res.remarks);

    //             // Items
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
    //                 });
    //             } else {

    //                 $('#standard_items_section').hide(); 
    //                 $('#no_item_section').show();

    //                 $('#noitem_amount').val(res.total_amount);
    //                 $('#noitem_purchase_ledger').val(res.purchase_ledger);
    //             }
                

    //             recalcTotals();
    //         }
    //     });
    // });

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
        
        $('#edit_gst').val(btn.data('gst_no'));
        $('#edit_against_invoice').val(btn.data('against_invoice'));
        // $('#edit_voucher_type').val(btn.data('vchtype'));
        $('#edit_party').val(btn.data('party'));
        // $('#edit_place').val(btn.data('place'));
        $('#edit_ledger').val(btn.data('ledger'));
        
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
            url: "{{ route('dn.show',':id') }}".replace(':id', id), type:"GET",
            success: function (res) {
                $('#edit_address').val(res.address || '');
                $('#edit_pincode').val(res.pincode || '');
                $('#edit_invoice').val(res.note_no || '');
                $('#edit_city').val(res.city || '');
                $('#edit_remarks').val(res.remarks || '');
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                $('#edit_amount').val(res.taxable_amount);
                $('#edit_cgst').val(res.cgst);
                $('#edit_sgst').val(res.sgst);
                $('#edit_igst').val(res.igst);
                $('#edit_total_amount').val(res.total_amount);
                $('#edit_is_igst').prop('checked', res.is_igst == 1);
                toggleGSTLedger();
                $('#noitem_gst_rate').val(res.gst_rate);

                $('#cgst_ledger').val(res.cgst_id).trigger('change');
                $('#sgst_ledger').val(res.sgst_id).trigger('change');
                $('#igst_ledger').val(res.igst_id).trigger('change');
                // UI
                $('#sum_amount').text(parseFloat(res.taxable_amount).toFixed(2));
                // $('#sum_cgst').text(parseFloat(res.cgst).toFixed(2));
                // $('#sum_sgst').text(parseFloat(res.sgst).toFixed(2));
                // $('#sum_igst').text(parseFloat(res.igst).toFixed(2));
                // $('#sum_grand_total').text(parseFloat(res.total_amount).toFixed(2));

                $('#sum_cgst').text(parseFloat(res.cgst).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst).toFixed(2));
                // $('#sum_grand_total').text(parseFloat(res.total_amount).toFixed(2));
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                setSelectValueByTextOrValue($('#noitem_purchase_ledger'), res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger);
                $("#edit_against_invoice").val(res.against_invoice);
                $('#edit_date').val(res.note_date);
                let tbody = $('#editItemsBody').empty();
                // (res.items || []).forEach(item => tbody.append(buildItemRow(item)));
                if (res.items && res.items.length > 0) {
                    $('#standard_items_section').show();
                    $('#no_item_section').hide();
                    $('#addNoItemRow').hide();
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
                    if (res.gst_mode === 'custom') {
                        generateSlotsFromItems();
                    }
                } else {
                    $('#standard_items_section').hide();
                    $('#no_item_section').show();
                    $('#addNoItemRow').show();
                    $('#noItemBody').empty();
                    // $('#noitem_amount').val(res.total_amount);
                    if (res.custom_gst && res.custom_gst.length) {
                        res.custom_gst.forEach(slot => addNoItemRow({
                            ledger: slot.ledger_id || slot.purchase_ledger_id || slot.ledger_name || res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger,
                            gst: slot.gst_rate,
                            amount: slot.taxable || slot.amount || 0
                        }));
                    } else {
                        addNoItemRow({
                            ledger: res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger,
                            gst: res.gst_rate || 0,
                            amount: res.taxable_amount || res.total_amount || 0
                        });
                    }

                    $('#noitem_amount').val(res.taxable_amount);
                    setSelectValueByTextOrValue($('#noitem_purchase_ledger'), res.purchase_ledger_id || res.purchase_ledger_name || res.purchase_ledger);
                    tbody.html(''); // clear table
                }

                if (res.gst_mode === 'custom') {

                    // renderCustomSlots(res.custom_gst);
                    // renderCustomSlots(res.custom_gst);

                    setTimeout(() => {
                        recalcTotals();   // 🔥 MUST CALL
                    }, 100);

                    // if (res.custom_gst && res.custom_gst.length) {
                    //     renderCustomSlots(res.custom_gst);   // ✅ correct
                    // } else {
                    //     setTimeout(() => {
                    //         renderCustomSlotsFromItems();    // fallback after DOM ready
                    //     }, 100);
                    // }
                    //autoFillTaxableFromItems();

                    // ✅ DO NOT CALL recalc
                    // $('#sum_amount').text(parseFloat(res.taxable_amount).toFixed(2));
                    // $('#sum_cgst').text(parseFloat(res.cgst).toFixed(2));
                    // $('#sum_sgst').text(parseFloat(res.sgst).toFixed(2));
                    // $('#sum_igst').text(parseFloat(res.igst).toFixed(2));
                    // $('#sum_grand_total').text(parseFloat(res.total_amount).toFixed(2));
                    $('#sum_amount').text(parseFloat(res.taxable_amount).toFixed(2));
                    $('#sum_cgst').text(parseFloat(res.cgst).toFixed(2));
                    $('#sum_sgst').text(parseFloat(res.sgst).toFixed(2));
                    $('#sum_igst').text(parseFloat(res.igst).toFixed(2));
                    // $('#sum_grand_total').text(parseFloat(res.total_amount).toFixed(2));
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                } else {
                    recalcTotals(); // only for standard mode
                }


                $('#sum_amount').text(parseFloat(res.taxable_amount || 0).toFixed(2));
                $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                //$('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                // if (!res.items || !res.items.length) {
                //     tbody.html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">No items — click Add Row</td></tr>');
                // }
                // recalcTotals();
            },
            error: () => $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-3" style="color:#ef4444;">Failed to load.</td></tr>')
        });
    });

    function autoFillTaxableFromItems() {
        let map = {};

        $('#editItemsBody tr').each(function () {
            let rate = parseFloat($(this).find('.gst').val()) || 0;
            let amount = parseFloat($(this).find('.amount').val()) || 0;

            if (!map[rate]) map[rate] = 0;
            map[rate] += amount;
        });

        $('#customSlotsBody tr').each(function () {
            let rate = parseFloat($(this).data('rate'));
            $(this).find('.taxable').val(map[rate] || 0);
        });
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
        setTimeout(() => {
            if ($('#gst_calc_mode').val() === 'custom') {
                setTimeout(() => {
                    renderCustomSlotsFromItems();
                    recalcTotals();
                }, 50);
            }
        }, 100);
    });

    // ═══════ LIVE RECALC ON INPUT ═══════
    // $(document).on('input', '#editItemsBody input', function () {
    //     recalcItemRow($(this).closest('tr'));
    //     recalcTotals(); // calculate amount
        
    //     if ($('#gst_calc_mode').val() === 'custom') {
    //         renderCustomSlotsFromItems(); // rebuild GST table
    //     }
    // });

    $(document).on('input', '#editItemsBody input', function () {
        recalcTotals();
        if ($('#gst_calc_mode').val() === 'custom') {
            renderCustomSlotsFromItems();
            setTimeout(() => {
                recalcTotals();   // 🔥 second pass for GST
            }, 50);
        }
    });

    $('#addNoItemRow').click(function () {
        addNoItemRow({
            ledger: $('#noitem_purchase_ledger').val(),
            gst: 18
        });
        recalcTotals();
    });

    $(document).on('click', '.removeNoItem', function() {
        $(this).closest('tr').remove();

        if (!$('#noItemBody tr').length) {
            addNoItemRow({
                ledger: $('#noitem_purchase_ledger').val(),
                gst: 18
            });
        }

        recalcTotals();
    });

    $(document).on('input change', '.noitem-gst, .noitem-amount, .noitem-ledger', function () {
        recalcTotals();
    });

    // ═══════ REMOVE ROW ═══════
    $(document).on('click', '.removeItemRow', function () {
        $(this).closest('tr').remove();
        recalcTotals();
    });

    // ═══════ GST MODE SWITCH ═══════
    $('#gst_calc_mode').on('change', function () {
        // let mode = $(this).val();
        // if (mode === 'standard') {
        //     $('#standard_items_section').show();
        //     $('#standard_tax_rows').show();
        //     $('#custom_slots_section').hide();
        //     $('#custom_tax_rows').hide();
        //     $('#igst_toggle_wrap').show();
        // } else {
        //     $('#standard_items_section').show(); // items table stays visible always
        //     $('#standard_tax_rows').hide();
        //     $('#custom_slots_section').show();
        //     $('#custom_tax_rows').show();
        //     $('#igst_toggle_wrap').hide();
        // }
        // recalcTotals();
        let mode = $(this).val();
        if (mode === 'custom') {
            $('#custom_slots_section').show();
            $('#standard_tax_rows').hide();
            $('#custom_tax_rows').show();
            generateSlotsFromItems(); // 🔥 IMPORTANT
        } else {
            $('#custom_slots_section').hide();
            $('#standard_tax_rows').show();
            $('#custom_tax_rows').hide();
        }
    });

    $('#edit_is_igst').on('change', function () {
        toggleGSTLedger();
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

            let amount = parseFloat($('#noitem_amount').val()) || 0;
            let gstRate = parseFloat($('#noitem_gst_rate').val()) || 0;
            let isIGST = $('#edit_is_igst').is(':checked');

            let cgst = 0, sgst = 0, igst = 0;
            let gstAmount = (amount * gstRate) / 100;

            if (isIGST) {
                igst = gstAmount;
            } else {
                cgst = gstAmount / 2;
                sgst = gstAmount / 2;
            }

            let total = amount + cgst + sgst + igst;

            $('#edit_amount').val(amount.toFixed(2));
            $('#edit_cgst').val(cgst.toFixed(2));
            $('#edit_sgst').val(sgst.toFixed(2));
            $('#edit_igst').val(igst.toFixed(2));
            // $('#edit_total_amount').val(total.toFixed(2));
            setRoundOffSummary(total);
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
 
        $.ajax({
            url: "{{ route('dn.update') }}",
            type: "POST",
            // contentType: "application/json",
            data: {
                _token: "{{ csrf_token() }}",
                id: $('#edit_id').val(),

                invoice_no: $('#edit_invoice').val(),
                date: $('#edit_date').val(),
                party_name: $('#edit_party').val(),
                gst_no: $('#edit_gst').val(),
                place_of_supply: $('#edit_place').val(),
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
                noitem_rows: collectNoItemRows(),
                purchase_ledger_id: $('#noitem_purchase_ledger').val(),
                against_invoice: $('#edit_against_invoice').val(),
                purchase_ledger: $('#noitem_purchase_ledger option:selected').text(),
                purchase_ledger_name: $('#noitem_purchase_ledger option:selected').text(),
                entry_mode: $('#no_item_section').is(':visible') ? 'noitem' : 'item',
                gst_rate: $('#noitem_gst_rate').val(),
                items: items,
                custom_slots: collectCustomSlots()
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
            error:   (xhr) => { 
                    let msg = 'Server error';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    showToast(msg, 'error');
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
                purchase_ledger_id: row.find('.slot_purchase_ledger_id').val() || null,

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

    function renderNoItemCustomSlots(rateMap) {
        let slotHtml = '';
        let customIgst = 0;
        let customCgst = 0;
        let customSgst = 0;

        Object.keys(rateMap).forEach(function(mapKey) {
            let data = rateMap[mapKey] || { amt: 0, igst: 0, cgst: 0, sgst: 0 };
            let rate = (parseFloat(data.rate ?? mapKey) || 0).toString();
            let igstAmt = parseFloat(data.igst) || 0;
            let cgstAmt = parseFloat(data.cgst) || 0;
            let sgstAmt = parseFloat(data.sgst) || 0;

            customIgst += igstAmt;
            customCgst += cgstAmt;
            customSgst += sgstAmt;

            let iOpts = IGST_LEDGERS.map(l => {
                let sel = String(l.id) === String(mappedGstLedgerId('igst', null, data.ledgerId || '', data.ledgerName || '')) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');
            let cOpts = CGST_LEDGERS.map(l => {
                let sel = String(l.id) === String(mappedGstLedgerId('cgst', null, data.ledgerId || '', data.ledgerName || '')) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');
            let sOpts = SGST_LEDGERS.map(l => {
                let sel = String(l.id) === String(mappedGstLedgerId('sgst', null, data.ledgerId || '', data.ledgerName || '')) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');

            slotHtml += `<tr data-slot-key="${data.slotKey || mapKey}" data-rate="${rate}">
                <td><span class="rate-badge"><span class="slot-rate">${rate}%</span></span><br><small class="slot-taxable" style="font-size:9px;color:#6b7280;">Taxable: ${fmt(data.amt)}</small></td>
                <td><strong>${fmt(data.amt)}</strong><input type="hidden" class="slot_purchase_ledger_id" value="${data.ledgerId || ''}"></td>
                <td><select class="slot-igst-ledger" data-rate="${rate}"><option value="">-- Ledger --</option>${iOpts}</select></td>
                <td><input type="number" class="slot-igst-amt" data-rate="${rate}" value="${igstAmt.toFixed(2)}" step="any"></td>
                <td><select class="slot-cgst-ledger" data-rate="${rate}"><option value="">-- Ledger --</option>${cOpts}</select></td>
                <td><input type="number" class="slot-cgst-amt" data-rate="${rate}" value="${cgstAmt.toFixed(2)}" step="any"></td>
                <td><select class="slot-sgst-ledger" data-rate="${rate}"><option value="">-- Ledger --</option>${sOpts}</select></td>
                <td><input type="number" class="slot-sgst-amt" data-rate="${rate}" value="${sgstAmt.toFixed(2)}" step="any"></td>
            </tr>`;
        });

        $('#customSlotsBody').html(slotHtml);
        $('#custom_tax_rows').html(`
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(customIgst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(customCgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(customSgst)}</span></div>
        `);
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

    // Master recalc — updates summary, footer, and custom slots
    // function recalcTotals() {
    //     let mode = $('#gst_calc_mode').val();
    //     let sumAmt=0, sumSgst=0, sumCgst=0, sumIgst=0, sumTotal=0;
        
    //     // ✅ NO ITEM MODE
    //     if ($('#no_item_section').is(':visible')) {

    //         let amount = parseFloat($('#noitem_amount').val()) || 0;
    //         let isIGST = $('#edit_is_igst').is(':checked');

    //         let cgst = 0, sgst = 0, igst = 0;

    //         let gstRate = parseFloat($('#noitem_gst_rate').val()) || 0;

    //         if (mode === 'standard') {
    //             if (isIGST) {
    //                 igst = (amount * gstRate) / 100;
    //             } else {
    //                 cgst = (amount * gstRate / 2) / 100;
    //                 sgst = (amount * gstRate / 2) / 100;
    //             }
    //         } else if (mode === 'custom') {
    //             // 🔥 CUSTOM MODE: Recalculate GST for each slot based on new amount
    //             let totalCgst = 0, totalSgst = 0, totalIgst = 0;

    //             $('#customSlotsBody tr').each(function () {
    //                 let rate = parseFloat($(this).data('rate')) || 0;
                    
    //                 // Calculate GST amounts based on new amount and rate
    //                 let slotIgst = 0, slotCgst = 0, slotSgst = 0;
    //                 if (isIGST) {
    //                     slotIgst = (amount * rate) / 100;
    //                 } else {
    //                     slotCgst = (amount * (rate / 2)) / 100;
    //                     slotSgst = (amount * (rate / 2)) / 100;
    //                 }
                    
    //                 // Update the slot input fields
    //                 $(this).find('.slot-igst-amt').val(slotIgst.toFixed(2));
    //                 $(this).find('.slot-cgst-amt').val(slotCgst.toFixed(2));
    //                 $(this).find('.slot-sgst-amt').val(slotSgst.toFixed(2));
                    
    //                 // Update taxable amount display
    //                 $(this).find('.slot-taxable').text(amount.toFixed(2));
                    
    //                 totalIgst += slotIgst;
    //                 totalCgst += slotCgst;
    //                 totalSgst += slotSgst;
    //             });

    //             cgst = totalCgst;
    //             sgst = totalSgst;
    //             igst = totalIgst;
    //         }

    //         // ✅ UPDATE UI
    //         $('#sum_amount').text(amount.toFixed(2));
    //         $('#sum_cgst').text(cgst.toFixed(2));
    //         $('#sum_sgst').text(sgst.toFixed(2));
    //         $('#sum_igst').text(igst.toFixed(2));

    //         let total = amount + cgst + sgst + igst;
    //         $('#sum_grand_total').text(total.toFixed(2));

    //         // 🔥 UPDATE CUSTOM TAX ROWS SUMMARY (if in custom mode)
    //         if (mode === 'custom') {
    //             let customSummaryHtml = `
    //                 <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${igst.toFixed(2)}</span></div>
    //                 <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${cgst.toFixed(2)}</span></div>
    //                 <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${sgst.toFixed(2)}</span></div>`;
    //             $('#custom_tax_rows').html(customSummaryHtml);
    //         }

    //         // ✅ IMPORTANT (for save)
    //         $('#edit_amount').val(amount);
    //         $('#edit_cgst').val(cgst);
    //         $('#edit_sgst').val(sgst);
    //         $('#edit_igst').val(igst);
    //         $('#edit_total_amount').val(total);

    //         return; // 🔥 STOP HERE
    //     }

    //     if ($('#gst_calc_mode').val() === 'custom') {

    //         let totalTaxable = 0;
    //         let totalCgst = 0;
    //         let totalSgst = 0;
    //         let totalIgst = 0;

    //         $('#customSlotsBody tr').each(function () {

    //             let taxable = parseFloat($(this).find('.slot-taxable').text()) || 0;
    //             let cgst = parseFloat($(this).find('.slot-cgst-amt').val()) || 0;
    //             let sgst = parseFloat($(this).find('.slot-sgst-amt').val()) || 0;
    //             let igst = parseFloat($(this).find('.slot-igst-amt').val()) || 0;

    //             totalTaxable += taxable;
    //             totalCgst += cgst;
    //             totalSgst += sgst;
    //             totalIgst += igst;
    //         });

    //         let grandTotal = totalTaxable + totalCgst + totalSgst + totalIgst;

    //         $('#sum_amount').text(totalTaxable.toFixed(2));
    //         $('#sum_cgst').text(totalCgst.toFixed(2));
    //         $('#sum_sgst').text(totalSgst.toFixed(2));
    //         $('#sum_igst').text(totalIgst.toFixed(2));
    //         $('#sum_grand_total').text(grandTotal.toFixed(2));

    //         $('#edit_amount').val(totalTaxable);
    //         $('#edit_cgst').val(totalCgst);
    //         $('#edit_sgst').val(totalSgst);
    //         $('#edit_igst').val(totalIgst);
    //         $('#edit_total_amount').val(grandTotal);

    //         return;
    //     }
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
        
    //     if ($('#no_item_section').is(':visible')) {

    //         let amount = parseFloat($('#noitem_amount').val()) || 0;
    //         let isIGST = $('#edit_is_igst').is(':checked');

    //         let gstRate = 18;

    //         let cgst = 0, sgst = 0, igst = 0;

    //         if (isIGST) {
    //             igst = amount * gstRate / 100;
    //         } else {
    //             cgst = amount * (gstRate / 2) / 100;
    //             sgst = amount * (gstRate / 2) / 100;
    //         }

    //         let total = amount + cgst + sgst + igst;

    //         $('#sum_amount').text(amount.toFixed(2));
    //         $('#sum_cgst').text(cgst.toFixed(2));
    //         $('#sum_sgst').text(sgst.toFixed(2));
    //         $('#sum_igst').text(igst.toFixed(2));
    //         $('#sum_grand_total').text(total.toFixed(2));

    //         // hidden
    //         $('#edit_amount').val(amount);
    //         $('#edit_cgst').val(cgst);
    //         $('#edit_sgst').val(sgst);
    //         $('#edit_igst').val(igst);
    //         $('#edit_total_amount').val(total);

    //         return;
    //     }
    //     // Renumber rows
    //     $('#editItemsBody tr').each(function(i) { $(this).find('.td-sr').text(i+1); });

    //     if (mode === 'standard') {
    //         $('#sum_sgst').text(fmt(sumSgst));
    //         $('#sum_cgst').text(fmt(sumCgst));
    //         $('#sum_igst').text(fmt(sumIgst));
    //     } else {
    //         // CUSTOM MODE: render rate-wise slots
    //         //renderCustomSlots(rateMap, sumTotal);
            
    //     }
    // }

    // function recalcTotals() {

    //     let isIGST = $('#edit_is_igst').is(':checked');
    //     let gstMode = $('#gst_calc_mode').val();

    //     let sumAmount = 0;
    //     let sumCgst = 0;
    //     let sumSgst = 0;
    //     let sumIgst = 0;

    //     // =========================
    //     // 🟢 WITH ITEM
    //     // =========================
    //     $('#editItemsBody tr').each(function () {

    //         let qty = parseFloat($(this).find('.item-qty').val()) || 0;
    //         let rate = parseFloat($(this).find('.item-rate').val()) || 0;
    //         let gstRate = parseFloat($(this).find('.item-gst_rate').val()) || 0;

    //         let amount = qty * rate;
    //         $(this).find('.amount').val(amount.toFixed(2));

    //         let gstAmount = (amount * gstRate) / 100;

    //         if (isIGST) {
    //             $(this).find('.slot-igst-amt').val(gstAmount.toFixed(2));
    //             $(this).find('.slot-cgst-amt').val(0);
    //             $(this).find('.slot-sgst-amt').val(0);

    //             sumIgst += gstAmount;
    //         } else {
    //             let half = gstAmount / 2;

    //             $(this).find('.slot-cgst-amt').val(half.toFixed(2));
    //             $(this).find('.slot-sgst-amt').val(half.toFixed(2));
    //             $(this).find('.slot-igst-amt').val(0);

    //             sumCgst += half;
    //             sumSgst += half;
    //         }

    //         sumAmount += amount;
    //     });

    //     // =========================
    //     // 🟡 NO ITEM
    //     // =========================
    //     if ($('#no_item_section').is(':visible')) {

    //         let amount = parseFloat($('#noitem_amount').val()) || 0;
    //         let gstRate = parseFloat($('#noitem_gst_rate').val()) || 0;

    //         let gstAmount = (amount * gstRate) / 100;

    //         sumAmount = amount;

    //         if (isIGST) {
    //             sumIgst = gstAmount;
    //             sumCgst = 0;
    //             sumSgst = 0;
    //         } else {
    //             sumCgst = gstAmount / 2;
    //             sumSgst = gstAmount / 2;
    //             sumIgst = 0;
    //         }
    //     }

    //     // =========================
    //     // 🔵 CUSTOM MODE (OVERRIDE)
    //     // =========================
    //     if (gstMode === 'custom') {

    //         sumCgst = 0;
    //         sumSgst = 0;
    //         sumIgst = 0;

    //         $('#customSlotsBody tr').each(function () {
    //             sumCgst += parseFloat($(this).find('.cgst_amount').val()) || 0;
    //             sumSgst += parseFloat($(this).find('.sgst_amount').val()) || 0;
    //             sumIgst += parseFloat($(this).find('.igst_amount').val()) || 0;
    //         });
    //     }

    //     // =========================
    //     // FINAL UI UPDATE
    //     // =========================
    //     $('#sum_amount').text(sumAmount.toFixed(2));
    //     $('#sum_cgst').text(sumCgst.toFixed(2));
    //     $('#sum_sgst').text(sumSgst.toFixed(2));
    //     $('#sum_igst').text(sumIgst.toFixed(2));

    //     let grand = sumAmount + sumCgst + sumSgst + sumIgst;
    //     $('#sum_grand_total').text(grand.toFixed(2));

    //     // hidden fields
    //     $('#edit_amount').val(sumAmount);
    //     $('#edit_cgst').val(sumCgst);
    //     $('#edit_sgst').val(sumSgst);
    //     $('#edit_igst').val(sumIgst);
    //     $('#edit_total_amount').val(grand);

    // }

    function getSummaryBaseTotal() {
        return (parseFloat($('#edit_amount').val()) || 0)
            + (parseFloat($('#edit_cgst').val()) || 0)
            + (parseFloat($('#edit_sgst').val()) || 0)
            + (parseFloat($('#edit_igst').val()) || 0);
    }

    function calculateRoundOffAmountForSummary(total) {
        total = parseFloat(total) || 0;
        return Math.round((Math.round(total) - total) * 100) / 100;
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
        let isIGST = $('#edit_is_igst').is(':checked');

        if ($('#no_item_section').is(':visible')) {
            let amount = 0;
            let cgst = 0;
            let sgst = 0;
            let igst = 0;
            let rateMap = {};

            $('#noItemBody tr').each(function(index) {
                let rowAmount = parseFloat($(this).find('.noitem-amount').val()) || 0;
                let gstRate = parseFloat($(this).find('.noitem-gst').val()) || 0;
                let gstAmount = (rowAmount * gstRate) / 100;
                let ledgerSelect = $(this).find('.noitem-ledger');
                let ledgerId = ledgerSelect.val() || '';
                let ledgerName = ledgerSelect.find('option:selected').text() || '';
                let rateKey = `row:${index}|${gstRate || 0}|${ledgerId}`;

                amount += rowAmount;

                if (!rateMap[rateKey]) {
                    rateMap[rateKey] = {
                        amt: 0,
                        igst: 0,
                        cgst: 0,
                        sgst: 0,
                        rate: gstRate,
                        ledgerId: ledgerId,
                        ledgerName: ledgerName,
                        slotKey: rateKey
                    };
                }

                rateMap[rateKey].amt += rowAmount;

                if (isIGST) {
                    igst += gstAmount;
                    rateMap[rateKey].igst += gstAmount;
                } else {
                    cgst += gstAmount / 2;
                    sgst += gstAmount / 2;
                    rateMap[rateKey].cgst += gstAmount / 2;
                    rateMap[rateKey].sgst += gstAmount / 2;
                }
            });

            let total = amount + cgst + sgst + igst;

            $('#edit_amount').val(amount.toFixed(2));
            $('#edit_cgst').val(cgst.toFixed(2));
            $('#edit_sgst').val(sgst.toFixed(2));
            $('#edit_igst').val(igst.toFixed(2));
            // $('#edit_total_amount').val(total.toFixed(2));
            setRoundOffSummary(total);

            $('#sum_amount').text(amount.toFixed(2));
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));
            // $('#sum_grand_total').text(total.toFixed(2));
            setRoundOffSummary(total);
            $('#noitem_amount').val(amount.toFixed(2));
            $('#noitem_gst_rate').val($('#noItemBody tr:first .noitem-gst').val() || 0);

            if ($('#gst_calc_mode').val() === 'custom') {
                renderNoItemCustomSlots(rateMap);
            }
            return;
        }

        let sumAmount = 0;
        let sumCgst = 0;
        let sumSgst = 0;
        let sumIgst = 0;

        $('#editItemsBody tr').each(function () {

            let qty  = parseFloat($(this).find('.item-qty').val()) || 0;
            let rate = parseFloat($(this).find('.item-rate').val()) || 0;
            let gst  = parseFloat($(this).find('.item-gst_rate').val()) || 0;

            if (!qty || !rate) return;

            let amount = qty * rate;
            $(this).find('.item-amount').val(amount.toFixed(2));

            let gstAmount = (amount * gst) / 100;

            if (isIGST) {
                sumIgst += gstAmount;
            } else {
                sumCgst += gstAmount / 2;
                sumSgst += gstAmount / 2;
            }

            sumAmount += amount;
        });

        $('#sum_amount').text(sumAmount.toFixed(2));
        $('#sum_cgst').text(sumCgst.toFixed(2));
        $('#sum_sgst').text(sumSgst.toFixed(2));
        $('#sum_igst').text(sumIgst.toFixed(2));

        let grand = sumAmount + sumCgst + sumSgst + sumIgst;

        // $('#sum_grand_total').text(grand.toFixed(2));
        setRoundOffSummary(grand);

        // let isIGST = $('#edit_is_igst').is(':checked');
        // let gstMode = $('#gst_calc_mode').val();

        // let sumAmount = 0;
        // let sumCgst = 0;
        // let sumSgst = 0;
        // let sumIgst = 0;

        // // =========================
        // // 🟢 ITEM CALCULATION
        // // =========================
        // $('#editItemsBody tr').each(function () {

        //     let qty = parseFloat($(this).find('.qty').val()) || 0;
        //     let rate = parseFloat($(this).find('.rate').val()) || 0;
        //     let gstRate = parseFloat($(this).find('.gst').val()) || 0;

        //     let amount = qty * rate;

        //     // IMPORTANT: set correct class
        //     $(this).find('.amount').val(amount.toFixed(2));
        //     // let amount = qty * rate;

        //     // $(this).find('.item-amount').val(amount.toFixed(2));

        //     sumAmount += amount;

        //     // ❌ ONLY calculate GST if NOT custom
        //     if (gstMode !== 'custom') {

        //         let gstAmount = (amount * gstRate) / 100;

        //         if (isIGST) {
        //             sumIgst += gstAmount;
        //         } else {
        //             sumCgst += gstAmount / 2;
        //             sumSgst += gstAmount / 2;
        //         }
        //     }
        // });

        // // =========================
        // // 🔵 CUSTOM MODE (ONLY TAX FROM TABLE)
        // // =========================
        // if (gstMode === 'custom') {

        //     let totalTaxable = 0;
        //     let totalGST = 0;

        //     $('#customSlotsBody tr').each(function () {
        //         let taxable = parseFloat($(this).find('.taxable').val()) || 0;
        //         let igst = parseFloat($(this).find('.igst').val()) || 0;
        //         let cgst = parseFloat($(this).find('.cgst').val()) || 0;
        //         let sgst = parseFloat($(this).find('.sgst').val()) || 0;

        //         totalTaxable += taxable;
        //         totalGST += (igst + cgst + sgst);
        //     });

        //     $('#sum_amount').text(totalTaxable.toFixed(2));
        //     $('#sum_grand_total').text((totalTaxable + totalGST).toFixed(2));

        //     return; // 🚨 STOP HERE (IMPORTANT)
        // }

        // // =========================
        // // FINAL UI
        // // =========================
        // $('#sum_amount').text(sumAmount.toFixed(2));
        // $('#sum_cgst').text(sumCgst.toFixed(2));
        // $('#sum_sgst').text(sumSgst.toFixed(2));
        // $('#sum_igst').text(sumIgst.toFixed(2));

        // let grand = sumAmount + sumCgst + sumSgst + sumIgst;

        // $('#sum_grand_total').text(grand.toFixed(2));

        // // hidden
        // $('#edit_amount').val(sumAmount);
        // $('#edit_cgst').val(sumCgst);
        // $('#edit_sgst').val(sumSgst);
        // $('#edit_igst').val(sumIgst);
        // $('#edit_total_amount').val(grand);
    }

    // ═══════════════════════════════════════════════════════════════
    // CUSTOM MODE — render rate-wise slots
    // Each unique GST% from items gets one row with IGST/CGST/SGST
    // ledger dropdowns and auto-computed tax amounts.
    // ═══════════════════════════════════════════════════════════════
    // function renderCustomSlots(slots) {
    //     let tbody = $('#customSlotsBody');
    //     tbody.empty();

    //     let totalAmount = parseFloat($('#noitem_amount').val()) || 0;

    //     let totalCgst = 0, totalSgst = 0, totalIgst = 0;

    //     slots.forEach(function (slot) {

    //         let taxable = parseFloat(slot.taxable);
    //         if (!taxable || taxable === 0) {
    //             taxable = totalAmount;
    //         }

    //         let cgst = parseFloat(slot.cgst_amount) || 0;
    //         let sgst = parseFloat(slot.sgst_amount) || 0;
    //         let igst = parseFloat(slot.igst_amount) || 0;

    //         totalCgst += cgst;
    //         totalSgst += sgst;
    //         totalIgst += igst;

    //         // 🔥 CREATE ROW
    //         let row = `
    //             <tr data-rate="${slot.gst_rate}">
    //                 <td><span class="rate-badge">${slot.gst_rate}%</span></td>
    //                 <td style="color: black;" class="slot-taxable">${taxable.toFixed(2)}</td>

    //                 <td style="color: black;">
    //                     <select class="slot-igst-ledger" data-rate="${slot.gst_rate}">
    //                         <option value="">Select</option>
    //                         ${getLedgerOptions(IGST_LEDGERS, slot.igst_id)}
    //                     </select>
    //                 </td>
    //                 <td style="color: black;"><input type="number" class="slot-igst-amt" data-rate="${slot.gst_rate}" value="${igst.toFixed(2)}" step="any" style="width:100%; padding:2px 4px; border:1px solid #d1d5db; border-radius:3px;"></td>

    //                 <td>
    //                     <select class="slot-cgst-ledger" data-rate="${slot.gst_rate}">
    //                         <option value="">Select</option>
    //                         ${getLedgerOptions(CGST_LEDGERS, slot.cgst_id)}
    //                     </select>
    //                 </td>
    //                 <td style="color: black;"><input type="number" class="slot-cgst-amt" data-rate="${slot.gst_rate}" value="${cgst.toFixed(2)}" step="any" style="width:100%; padding:2px 4px; border:1px solid #d1d5db; border-radius:3px;"></td>

    //                 <td>
    //                     <select class="slot-sgst-ledger" data-rate="${slot.gst_rate}">
    //                         <option value="">Select</option>
    //                         ${getLedgerOptions(SGST_LEDGERS, slot.sgst_id)}
    //                     </select>
    //                 </td>
    //                 <td style="color: black;"><input type="number" class="slot-sgst-amt" data-rate="${slot.gst_rate}" value="${sgst.toFixed(2)}" step="any" style="width:100%; padding:2px 4px; border:1px solid #d1d5db; border-radius:3px;"></td>
    //             </tr>
    //             `;

    //         // 🔥 APPEND (MOST IMPORTANT)
    //         tbody.append(row);
    //     });

    //     // totals
    //     $('#sum_amount').text(totalAmount.toFixed(2));
    //     $('#sum_cgst').text(totalCgst.toFixed(2));
    //     $('#sum_sgst').text(totalSgst.toFixed(2));
    //     $('#sum_igst').text(totalIgst.toFixed(2));

    //     let grand = totalAmount + totalCgst + totalSgst + totalIgst;

    //     $('#edit_amount').val(totalAmount.toFixed(2));
    //     $('#edit_cgst').val(totalCgst.toFixed(2));
    //     $('#edit_sgst').val(totalSgst.toFixed(2));
    //     $('#edit_igst').val(totalIgst.toFixed(2));
    //     $('#edit_total_amount').val(grand.toFixed(2));
    //     $('#sum_grand_total').text(grand.toFixed(2));
    // }

    $(document).on('keyup change', '.item-qty, .item-rate, .item-gst_rate', function () {
        recalcTotals();
    });

    function renderCustomSlots(data) {
        let tbody = $('#customSlotsBody');
        //tbody.empty();
        data.forEach(row => {
            let tr = `
            <tr>
                <td><span class="rate-badge">${row.gst_rate}%</span></td>
                <td><input type="number" class="slot-taxable" value="${row.taxable}" readonly></td>
                <td>
                    <select class="igst_ledger">
                        <option value="">Select</option>
                        ${IGST_LEDGERS.map(l =>
                            `<option value="${l.id}" ${l.id == row.igst_ledger_id ? 'selected' : ''}>${l.name}</option>`
                        ).join('')}
                    </select>
                </td>
                <td><input type="number" class="slot-igst-amt" value="${row.igst_amount}" readonly></td>
                <td>
                    <select class="cgst_ledger">
                        <option value="">Select</option>
                        ${CGST_LEDGERS.map(l =>
                            `<option value="${l.id}" ${l.id == row.cgst_ledger_id ? 'selected' : ''}>${l.name}</option>`
                        ).join('')}
                    </select>
                </td>
                <td><input type="number" class="slot-cgst-amt" value="${row.cgst_amount}" readonly></td>
                <td>
                    <select class="sgst_ledger">
                        <option value="">Select</option>
                        ${SGST_LEDGERS.map(l =>
                            `<option value="${l.id}" ${l.id == row.sgst_ledger_id ? 'selected' : ''}>${l.name}</option>`
                        ).join('')}
                    </select>
                </td>
                <td><input type="number" class="slot-sgst-amt" value="${row.sgst_amount}" readonly></td>
            </tr>
            `;
            tbody.append(tr);
        });
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

    function getLedgerOptions(list, selectedId = '') {
        return list.map(l => 
            `<option value="${l.id}" ${l.id == selectedId ? 'selected' : ''}>${l.name}</option>`
        ).join('');
    }

    function openEditModal()    { 
        document.getElementById('editModal').classList.add('show'); 
        setTimeout(() => {
            recalcTotals(); // 🔥 MUST
        }, 100);
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

    // $('#edit_is_igst').on('change', function () {
    //     toggleGSTLedger();
    //     recalcTotals();
    // });

    $('.party-select').select2({
        dropdownParent: $('#editModal'),
        width: '100%',
        placeholder: "Search Party...",
        allowClear: true
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

    $(document).on('input', '.item-qty, .item-rate, .item-gst_rate', function () {
        recalcTotals(); // 1️⃣ always calculate amount first
        if ($('#gst_calc_mode').val() === 'custom') {
            renderCustomSlotsFromItems(); // 2️⃣ rebuild GST table
        }
    });

    // no item inputs
    $('#noitem_amount, #noitem_gst_rate').on('input', function () {
        recalcTotals();
    });

    // custom slot inputs
    $(document).on('input', '.cgst_amount, .sgst_amount, .igst_amount', function () {
        recalcTotals();
    });

    function generateCustomSlotsFromItems() {

        let gstMap = {};

        $('#editItemsBody tr').each(function () {

            // let gstRate = parseFloat($(this).find('.item-gst_rate').val()) || 0;
            // let qty = parseFloat($(this).find('.item-qty').val()) || 0;
            // let rate = parseFloat($(this).find('.item-rate').val()) || 0;
            let inputs = $(this).find('input');

            let qty = parseFloat(inputs.eq(3).val()) || 0;   // Qty column
            let rate = parseFloat(inputs.eq(5).val()) || 0;  // Rate column
            let gstRate = parseFloat(inputs.eq(2).val()) || 0; // GST column
            let amount = qty * rate;

            inputs.eq(6).val(amount.toFixed(2)); // Amount column

            //let amount = qty * rate;

            if (!gstRate || gstRate <= 0 || !amount || amount <= 0) return;

            if (!gstMap[gstRate]) {
                gstMap[gstRate] = 0;
            }

            gstMap[gstRate] += amount;
        });

        return gstMap;
    }

    function renderCustomSlotsFromItems() {

        let gstMap = generateCustomSlotsFromItems();
        let tbody = $('#customSlotsBody');
        tbody.empty();

        Object.keys(gstMap).forEach(rate => {

            let taxable = gstMap[rate];

            let cgstAmt = (taxable * rate / 2) / 100;
            let sgstAmt = (taxable * rate / 2) / 100;
            let igstAmt = (taxable * rate) / 100;
            let row = `
            <tr>
                <td><span class="rate-badge">${rate}%</span></td>
                <td><input type="number" class="taxable" value="${taxable.toFixed(2)}"></td>

                <td>
                    <select class="igst_ledger">
                        <option value="">Select</option>
                        ${buildLedgerOptions(IGST_LEDGERS)}
                    </select>
                </td>
                <td><input type="number" class="igst_amount" value="${igstAmt.toFixed(2)}"></td>

                <td>
                    <select class="cgst_ledger">
                        ${buildLedgerOptions(CGST_LEDGERS)}
                    </select>
                </td>
                <td><input type="number" class="cgst_amount" value="${cgstAmt.toFixed(2)}"></td>

                <td>
                    <select class="sgst_ledger">
                        ${buildLedgerOptions(SGST_LEDGERS)}
                    </select>
                </td>
                <td><input type="number" class="sgst_amount" value="${sgstAmt.toFixed(2)}"></td>
            </tr>`;

            tbody.append(row);
        });

        recalcTotals(); // 🔥 important
    }

    function buildLedgerOptions(ledgers) {
        let html = '<option value="">Select</option>';

        (ledgers || []).forEach(l => {
            html += `<option value="${l.id}">${l.name}</option>`;
        });

        return html;
    }

    $('#gst_calc_mode').change(function () {
        let mode = $(this).val();
        if (mode === 'custom') {
            $('#custom_slots_section').show();
            $('#standard_tax_rows').hide();
            renderCustomSlotsFromItems();
        } else {
            $('#custom_slots_section').hide();
            $('#standard_tax_rows').show();
            recalcTotals();
        }
    });

    $(document).on('input', '.item-qty, .item-rate, .item-gst_rate', function () {
        updateRowAmount($(this).closest('tr'));
        renderCustomSlotsFromItems();
        recalcTotals();
    });

    function updateRowAmount(row) {
        let qty = parseFloat(row.find('.item-qty').val()) || 0;
        let rate = parseFloat(row.find('.item-rate').val()) || 0;
        let amount = qty * rate;
        row.find('.item-amount').val(amount.toFixed(2));
    }

     function generateSlotsFromItems() {
        if ($('#no_item_section').is(':visible')) {
            recalcTotals();
            return;
        }

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
</script>
@endsection
