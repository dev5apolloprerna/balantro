@extends('layouts.super_admin')

@section('content')
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
                    Purchase Transactions
                </h2>

                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                    {{ $rows->count() }}
                </span>
            </div>

            <div class="flex gap-2">
                @if(session('client_name'))
                <div class="bulk-client-name text-xl font-semibold text-green-600 whitespace-nowrap truncate max-w-[140px]" style="font-variant-caps: small-caps;">
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
                    Save
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
                            class="placeSelect bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-800 dark:text-white rounded px-3 py-1 mt-1">
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
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 group-block">
                <table id="salesTable" class="sales-preview-table min-w-[1120px] xl:min-w-0 w-full text-sm text-gray-700 dark:text-gray-300 border-collapse">
                    <colgroup>
                        <col class="col-select">
                        <col class="col-sr">
                        <col class="col-date">
                        <col class="col-reference">
                        <col class="col-voucher">
                        <col class="col-party">
                        <col class="col-gstin">
                        <col class="col-place">
                        <col class="col-amount">
                        <col class="col-status">
                        <col class="col-action">
                    </colgroup>
                    <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-gray-900/40 text-xs text-gray-700 dark:text-gray-300 uppercase sticky top-0 z-10">
                        <tr>
                            <th class=" w-8">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="w-14">SR</th>
                            <th class="compact-col">DATE</th>
                            <th class="compact-col">REFERENCE</th>
                            <th class="compact-col">VOUCHER</th>
                            <th class="party-col">PARTY A/C NAME</th>
                            <th class="gstin-col">GSTIN/UIN</th>
                            <th class="place-col">PLACE</th>
                            <!-- <th class="">PARTICULARS</th> -->
                            <th class=" text-right">AMOUNT</th>
                            <th class="">STATUS</th>
                            <th class="">ACTION</th>
                        </tr>
                        <tr class="bg-white dark:bg-neutral-900 column-search-row">
                            <th></th>
                            <th></th>
                            <th><input class="searchInput" data-column="2" type="search" placeholder="Date"></th>
                            <th><input class="searchInput" data-column="3" type="search" placeholder="Ref"></th>
                            <th><input class="searchInput" data-column="4" type="search" placeholder="Voucher"></th>
                            <th><input class="searchInput" data-column="5" type="search" placeholder="Party"></th>
                            <th><input class="searchInput" data-column="6" type="search" placeholder="GSTIN"></th>
                            <th><input class="searchInput" data-column="7" type="search" placeholder="Place"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                        @foreach($rows as $index=>$row)
                        <tr class="group transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] [&>*]:group-hover:text-black [&_*]:group-hover:text-black">
                            <td class="">
                                <input type="checkbox" name="selected[]" value="{{$row->id}}">
                            </td>
                            <td class="">
                                {{ $index+1 }}
                            </td>
                            <td class="">
                                <input type="date"
                                    name="date[{{$row->id}}]"
                                    value="{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}"
                                    class="inputCell">
                            </td>
                            <td class="">
                                <input type="text"
                                    name="invoice_no[{{$row->id}}]"
                                    value="{{$row->invoice_no}}"
                                    class="inputCell">
                            </td>
                            <td class="">
                                <select name="voucher_type[{{$row->id}}]" class="inputCell voucherSelect">
                                    @foreach($vchTypes as $vchType)
                                    <option value="{{$vchType}}"
                                        {{ strtolower(trim($vchType)) == strtolower(trim($row->vchType))  ? 'selected' : '' }}>{{$vchType}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="">
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
                            <td class="">
                                {{$row->gst_no}}
                            </td>
                            <td class="">
                                <select name="place_of_supply[{{$row->id}}]"
                                    class="inputCell placeSelect">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                    <option value="{{$state}}"
                                        {{ strtolower(trim($state)) == strtolower(trim($row->place_of_supply)) ? 'selected':''}}>
                                        {{$state}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- <td class="">
                                <select name="ledger[{{$row->id}}]" class="ledgerSelect inputCell">
                                    <option>Select Ledger</option>
                                    @foreach($ledgers as $ledger)
                                    <option value="{{$ledger->name}}"
                                        {{ trim($ledger->name) == trim($row->purchase_ledger) ? 'selected':''}}>{{$ledger->name}}</option>
                                    @endforeach
                                </select>
                            </td> -->
                            <td class=" text-right">
                                {{ number_format($row->total_amount,2) }}
                            </td>
                            <td class="">
                                <span class="text-yellow-400">
                                    {{$row->status}}
                                </span>
                            </td>
                            <td class="">
                                {{-- VIEW BUTTON --}}
                                <button type="button" class="viewRow text-green-400 hover:text-green-300" 
                                    title="View" data-id="{{ $row->id }}">
                                    <i class="fa-solid fa-eye action-icon"></i>
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
                                    <i class="fa-solid fa-trash action-icon"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $rows->links() }}
                </div>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     EDIT MODAL
══════════════════════════════════════════════════════ --}}
<div id="editModal" class="modal" style="display: none;">
    <div class="receipt-wrapper">
        <input type="hidden" id="edit_id">

        {{-- RECEIPT HEADER --}}
        <div class="receipt-head">
            <div class="receipt-head-left">
                <div class="receipt-company">Purchase Bill</div>
                <div class="receipt-subtitle">Tax Invoice</div>
            </div>
            <div class="receipt-head-right">
                <button type="button" class="receipt-close-btn" onclick="closeEditModal()">✕</button>
            </div>
        </div>

        <div id="pendingIssueAlert" class="pending-issue-alert" style="display:none;">
            <div class="pending-issue-title">
                <i class="fa-solid fa-triangle-exclamation"></i> This purchase entry is still pending
            </div>
            <ul id="pendingIssueList" class="pending-issue-list"></ul>
        </div>

        {{-- META GRID --}}
        <div class="receipt-meta-grid">
            {{-- Left: Supplier --}}
            <div class="receipt-meta-block">
                <div class="receipt-block-title"><i class="fa-solid fa-building text-blue-400 mr-1"></i> Supplier Details</div>
                <div class="receipt-field-row">
                    <label>Party Name</label>
                    <div style="display:flex; gap:6px; width:100%;">
                        <select id="edit_party" class="receipt-input party-select" style="flex:1;">
                            <option value="">Select Party</option>
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
                    <label>Purchase ledger</label>
                    <select id="noitem_purchase_ledger" class="receipt-input ledgerSelect" required>
                        <option value="">Select Ledger</option>
                        @foreach($purcasheLedgers as $ledger)
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
                    <!-- <span class="tax-value" id="sum_roundoff">0.00</span> -->
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
<div id="viewModal" class="modal" style="display: none;">
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
                                <th>HSN</th>
                                <th>Qty</th>
                                <th>Unit</th>
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
    .py-2 {
        padding-top: 0.15rem;
        padding-bottom: 0.15rem;
    }
    .px-3 {
        padding-left: 0.15rem;
        padding-right: 0.15rem;
    }
    /* ── BASE ── */
    .purchase-table-wrap { width:100%; }
    .purchase-preview-table { table-layout:fixed; min-width:1060px; }
    .purchase-preview-table th,
    .purchase-preview-table td { vertical-align:top; }
    .purchase-preview-table .col-select { width:34px; }
    .purchase-preview-table .col-sr { width:42px; }
    .purchase-preview-table .col-date { width:122px; }
    .purchase-preview-table .col-reference { width:112px; }
    .purchase-preview-table .col-voucher { width:112px; }
    .purchase-preview-table .col-party { width:260px; }
    .purchase-preview-table .col-gstin { width:132px; }
    .purchase-preview-table .col-place { width:145px; }
    .purchase-preview-table .col-amount { width:95px; }
    .purchase-preview-table .col-status { width:82px; }
    .purchase-preview-table .col-action { width:70px; }
    .inputCell { background:white; border:1px solid #d1d5db; color:#111827; padding:4px 6px; font-size:12px; border-radius:4px; width:100%; min-width:0; height:30px;}
     /*  */
    .dark .inputCell { background:#020617; border:1px solid #374151; color:white; }
    .searchInput { background:white; border:1px solid #d1d5db; color:#111827; width:100%; min-width:0; height:26px; border-radius:4px; padding:3px 6px; font-size:11px; text-transform:none; }
    .dark .searchInput { background:#020617; border:1px solid #374151; color:white; }
    .placeSelect { min-width:128px; }
    @media (min-width:1280px) {
        .purchase-preview-table { min-width:100%; }
        .purchase-preview-table .col-date { width:116px; }
        .purchase-preview-table .col-reference,
        .purchase-preview-table .col-voucher { width:104px; }
        .purchase-preview-table .col-place { width:138px; }
    }
    @media (max-width:768px) {
        .purchase-preview-table { min-width:980px; }
        .purchase-preview-table .col-party { width:220px; }
        .purchase-preview-table .col-gstin { width:120px; }
        .purchase-preview-table .col-place { width:140px; }
    }
    /* #purchaseTable tbody tr:hover { background:#f3f4f6; }
    .dark #purchaseTable tbody tr:hover { background:#1f2937; } */

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
    .pending-issue-alert { margin:0 8px 8px; padding:8px 10px; border:1px solid #fca5a5; border-left:4px solid #dc2626; border-radius:6px; background:#fef2f2; color:#991b1b; font-size:12px; }
    .pending-issue-title { font-weight:700; margin-bottom:4px; }
    .pending-issue-list { margin:0; padding-left:18px; }
    #editModal .pending-field-error, #editModal .pending-field-error + .select2 .select2-selection, #editModal .pending-field-error.select2-hidden-accessible + .select2 .select2-selection { border-color:#dc2626!important; background:#fff1f2!important; box-shadow:0 0 0 1px rgba(220,38,38,.35)!important; }
    #editModal .pending-field-error-row { outline:2px solid #dc2626; outline-offset:-2px; background:#fff1f2; }
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
const ITEM_MASTER = @json($stockItems);
const PURCHASE_LEDGERS = @json($purcasheLedgers ?? []);
const PURCHASE_GST_MAPPINGS = @json($purchaseGstMappings ?? []);
const IGST_LEDGERS = @json($iGstLedgers ?? []);
const CGST_LEDGERS = @json($cGstLedgers ?? []);
const SGST_LEDGERS = @json($sGstLedgers ?? []);
const PARTY_LEDGER_DETAILS = @json($ledgers ?? []);

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

function ensureSelectOption(selector, value, label = value) {
    if (!value) {
        return;
    }

    const select = $(selector);
    const exists = select.find('option').filter(function () {
        return normalizedLedgerName($(this).val()) === normalizedLedgerName(value);
    }).length > 0;

    if (!exists) {
        select.append(new Option(label || value, value, true, true));
    }
}

$(document).on('change', '#edit_party', function () {
    applyPartyLedgerDetails($(this).val());
});

window.addEventListener('load', function () {
    console.log('Final Select2:', typeof $.fn.select2);
});
</script>
<script>
    $(document).on('focus', '.itemSelect', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                dropdownParent: $('#editModal'),
                width: '100%'
            });
        }
    });

    $(document).ready(function() {
        $('#selectAll').click(function() {
            $('tbody input[type=checkbox]').prop('checked', this.checked);
        });
        $('.searchInput').on('keyup', function() {
            // let column = $(this).closest('th').index();
            let column = parseInt($(this).data('column'), 10);
            let value = $(this).val().toLowerCase();
            $('#purchaseTable tbody tr').each(function() {
                let cell = $(this).find('td').eq(column);
                let text = cell.text().toLowerCase();
                let input = cell.find('input,select').val();
                if (input) {
                    text += input.toLowerCase();
                    if (cell.find('input[type="date"]').length && input.includes('-')) {
                        const parts = input.split('-');
                        text += ` ${parts[2]}/${parts[1]}/${parts[0]} ${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }
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

         // modal dropdown
        $('#edit_party').select2({
            dropdownParent: $('#editModal'),
            width: '100%',
            placeholder: "Select Party",
            allowClear: true
        });
    });

    function openLedgerModal() {
        document.getElementById('ledgerModal').classList.add('show');
    }

    function closeLedgerModal() {
        document.getElementById('ledgerModal').classList.remove('show');
    }

    function openEditModal() {
        $('#editModal').addClass('show');
        // re-init select2 inside modal
        setTimeout(() => {
            if ($.fn.select2) {
                $('#edit_party').select2({
                    dropdownParent: $('#editModal'),
                    width: '100%',
                    placeholder: "Select Party",
                    allowClear: true
                });
            }
        }, 200);
    }

    function closeEditModal()   { document.getElementById('editModal').classList.remove('show'); }
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
                showToast(response.message,'success');
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

    const ledgers = @json(collect($ledgers)->pluck('name'));
    const states = @json($states);
    const vouchers = @json($vchTypes);
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
            url: "{{ route('purchase.save') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.status === false) {
                    showToast(response.message || 'Unable to save data','error');
                    return;
                }

                showToast(response.message || 'Saved Successfully','success');
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
            url: "{{ route('purchase.delete', ':id') }}".replace(':id', id),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                showToast('Deleted Successfully','success');
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
    $(document).on('click', '.viewRow', function () {
        let id = $(this).data('id');
        clearPendingIssueHighlights();
        // Open same edit modal
        openEditModal();

        // Hide update button
        $('#addItemRow').hide();
        $('#addNoItemRow').hide();
        $('#updateRow').hide();
        $(document).find('.receipt-del-btn').hide();

        // Disable all inputs
        $('#editModal input, #editModal select, #editModal textarea')
            .prop('disabled', true)
            .css('pointer-events', 'none');

        // Load data
        $.ajax({
            url: "{{ route('purchase.show', ':id') }}".replace(':id', id),
            type: "GET",
            success: function (res) {
                applyPendingIssueHighlights(res.pending_issues, res.status);
                // Fill header fields
                $('#edit_id').val(res.id);
                $('#edit_invoice').val(res.invoice_no);
                $('#edit_date').val(res.date);
                $('#edit_gst').val(res.gst_no);
                // $('#edit_party').val(res.party_name);
                 ensureSelectOption('#edit_party', res.party_name);
                $('#edit_party').val(res.party_name).trigger('change'); // 🔥 IMPORTANT
                //$('#edit_place').val(res.place_of_supply);
                $('#edit_place option').each(function () {
                    if ($(this).val().toLowerCase().trim() === String(res.place_of_supply).toLowerCase().trim()) {
                        $(this).prop('selected', true);
                    }
                });
                //$('#edit_voucher_type').val(res.vchType);
                $('#edit_voucher_type option').each(function () {
                    if ($(this).val().toLowerCase().trim() === String(res.vchType).toLowerCase().trim()) {
                        $(this).prop('selected', true);
                    }
                });
                $('#edit_address').val(res.address);
                $('#edit_pincode').val(res.pincode);
                $('#edit_city').val(res.city);
                // $('#edit_is_igst').val(res.is_igst);
                $('#edit_is_igst').prop('checked', res.is_igst == 1);
                $('#edit_remarks').val(res.Remarks);
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                 // 👉 ADD HERE 👇
                $('#cgst_ledger').val(res.cgst_id).trigger('change');
                $('#sgst_ledger').val(res.sgst_id).trigger('change');
                $('#igst_ledger').val(res.igst_id).trigger('change');
                $('#noitem_purchase_ledger').val(res.purchase_ledger).trigger('change');

                $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                // $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                $('#edit_cgst').val(res.cgst);
                $('#edit_sgst').val(res.sgst);
                $('#edit_igst').val(res.igst);
                $('#edit_total_amount').val(res.total_amount);
                // Items
                let tbody = $('#editItemsBody').empty();
                if (res.items && res.items.length > 0) {
                    
                    $('#standard_items_section').show();
                    $('#no_item_section').hide();
                    (res.items || []).forEach(item => {
                        let row = $(buildItemRow(item));
                        // hide delete button in each row
                        row.find('.receipt-del-btn').hide();
                        // disable inputs inside row
                        row.find('input').prop('disabled', true);
                        tbody.append(row);
                    });
                } else {
                    $('#standard_items_section').hide();
                    $('#no_item_section').show();
                    populateNoItemRows(res, true);

                    // Display stored GST values directly for view mode
                    $('#sum_amount').text(parseFloat(res.amount || 0).toFixed(2));
                    $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                    $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                    $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                    //$('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
                }

                // Handle custom GST mode display
                if (res.gst_mode === 'custom' && res.custom_gst && res.custom_gst.length) {
                    let iGstLedgers = @json($iGstLedgers);
                    let cGstLedgers = @json($cGstLedgers);
                    let sGstLedgers = @json($sGstLedgers);
                    let html = '';

                    res.custom_gst.forEach(slot => {
                        let igstLedgerName = iGstLedgers.find(l => l.id == slot.igst_ledger_id)?.name || '';
                        let cgstLedgerName = cGstLedgers.find(l => l.id == slot.cgst_ledger_id)?.name || '';
                        let sgstLedgerName = sGstLedgers.find(l => l.id == slot.sgst_ledger_id)?.name || '';

                        html += `
                        <tr style="color: black;" data-rate="${slot.gst_rate}" data-slot-key="${slot.gst_rate}">
                            <td>${slot.gst_rate}%</td>
                            <td class="slot-taxable">${slot.taxable || 0}</td>
                            <td>${igstLedgerName}</td>
                            <td>${slot.igst_amount || 0}</td>
                            <td>${cgstLedgerName}</td>
                            <td>${slot.cgst_amount || 0}</td>
                            <td>${sgstLedgerName}</td>
                            <td>${slot.sgst_amount || 0}</td>
                        </tr>`;
                    });

                    $('#customSlotsBody').html(html);
                } else if (res.gst_mode === 'custom') {
                    renderCustomSlotsFromPurchaseItems(res.items || [], res.is_igst == 1);
                }
                applyPendingIssueHighlights(res.pending_issues, res.status);

                //recalcTotals();
            }
        });
    });

    function openViewModal()  { document.getElementById('viewModal').classList.add('show'); }
    function closeViewModal() { document.getElementById('viewModal').classList.remove('show'); }

    let currentPurchasePendingIssues = [];
    let currentPurchasePendingStatus = '';

    function clearPendingIssueHighlights(clearStoredIssues = true) {
        if (clearStoredIssues) {
            currentPurchasePendingIssues = [];
            currentPurchasePendingStatus = '';
        }
        $('#pendingIssueAlert').hide();
        $('#pendingIssueList').empty();
        $('#editModal .pending-field-error').removeClass('pending-field-error');
        $('#editModal .pending-field-error-row').removeClass('pending-field-error-row');
    }

    function pendingIssueTargets(field) {
        const targets = {
            purchase_ledger: ['#noitem_purchase_ledger', '.noitem-ledger', '.slot-igst-ledger', '.slot-cgst-ledger', '.slot-sgst-ledger'],
            party_name: ['#edit_party'],
            gst_no: ['#edit_gst'],
            date: ['#edit_date'],
            gst_rate: ['.item-gst_rate', '.noitem-gst'],
            invoice_no: ['#edit_invoice'],
            amount: ['.item-quantity', '.item-rate', '.item-amount', '.noitem-amount'],
            gst_ledger: ['#igst_ledger', '#cgst_ledger', '#sgst_ledger', '.slot-igst-ledger', '.slot-cgst-ledger', '.slot-sgst-ledger']
        };

        return targets[field] || [];
    }

    function applyPendingIssueHighlights(issues, status = '') {
        const normalizedStatus = String(status || '').trim().toLowerCase();
        const issueList = Array.isArray(issues) ? issues : [];

        currentPurchasePendingIssues = issueList;
        currentPurchasePendingStatus = normalizedStatus;
        clearPendingIssueHighlights(false);

        if (!issueList.length) {
            if (normalizedStatus === 'pending') {
                $('#pendingIssueList').html('<li>This purchase entry is pending. Please review the highlighted/required fields before updating.</li>');
                $('#pendingIssueAlert').show();
            }
            return;
        }

        const list = issueList.map(issue => `<li>${$('<div>').text(issue.message || 'Please review this field.').html()}</li>`).join('');
        $('#pendingIssueList').html(list);
        $('#pendingIssueAlert').show();

        issueList.forEach(issue => {
            pendingIssueTargets(issue.field).forEach(selector => {
                const field = $(selector);
                field.addClass('pending-field-error');
                field.closest('tr').addClass('pending-field-error-row');
            });

            if (issue.field === 'gst_ledger') {
                highlightMissingPurchaseGstLedgerInputs();
            }
        });
    }

    function highlightMissingPurchaseGstLedgerInputs() {
        $('#customSlotsBody tr').each(function () {
            const row = $(this);
            const igstAmount = parseFloat(row.find('.slot-igst-amt').val()) || 0;
            const cgstAmount = parseFloat(row.find('.slot-cgst-amt').val()) || 0;
            const sgstAmount = parseFloat(row.find('.slot-sgst-amt').val()) || 0;

            [
                { amount: igstAmount, selector: '.slot-igst-ledger' },
                { amount: cgstAmount, selector: '.slot-cgst-ledger' },
                { amount: sgstAmount, selector: '.slot-sgst-ledger' }
            ].forEach(({ amount, selector }) => {
                const ledger = row.find(selector);
                if (amount > 0 && !ledger.val()) {
                    ledger.addClass('pending-field-error');
                    row.addClass('pending-field-error-row');
                }
            });
        });

        if ((parseFloat($('#sum_igst').text()) || parseFloat($('#edit_igst').val()) || 0) > 0 && !$('#igst_ledger').val()) {
            $('#igst_ledger').addClass('pending-field-error');
        }
        if ((parseFloat($('#sum_cgst').text()) || parseFloat($('#edit_cgst').val()) || 0) > 0 && !$('#cgst_ledger').val()) {
            $('#cgst_ledger').addClass('pending-field-error');
        }
        if ((parseFloat($('#sum_sgst').text()) || parseFloat($('#edit_sgst').val()) || 0) > 0 && !$('#sgst_ledger').val()) {
            $('#sgst_ledger').addClass('pending-field-error');
        }
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
    
    // ═══════════════════════════════════════════════════════════════════════
    // EDIT MODAL
    // ═══════════════════════════════════════════════════════════════════════
   // ═══════ EDIT MODAL ═══════
    $(document).on('click', '.editRow', function () {
        let btn = $(this), id = btn.data('id');
        clearPendingIssueHighlights();
        $('#updateRow').show();
        $('#addItemRow').show();
        $('#addNoItemRow').hide();

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
        // $('#edit_voucher_type').val(btn.data('vchtype'));

        //$('#edit_party').val(btn.data('party'));
        let party = btn.data('party');
         ensureSelectOption('#edit_party', party);
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
        // $('#edit_ledger').val(btn.data('ledger'));
        $('#noitem_purchase_ledger').val(btn.data('ledger')).trigger('change');
        
            
        $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">Loading…</td></tr>');
        openEditModal();

        $.ajax({
            url: "{{ route('purchase.show',':id') }}".replace(':id', id), type:"GET",
            success: function (res) {
                applyPendingIssueHighlights(res.pending_issues, res.status);
                $('#edit_address').val(res.address || '');
                $('#edit_pincode').val(res.pincode || '');
                $('#edit_city').val(res.city || '');
                $('#edit_remarks').val(res.Remarks || '');
                let tbody = $('#editItemsBody').empty();
                $('#cgst_ledger').val(res.cgst_id).trigger('change');
                $('#sgst_ledger').val(res.sgst_id).trigger('change');
                $('#igst_ledger').val(res.igst_id).trigger('change');
                $('#edit_is_igst').prop('checked', res.is_igst == 1).trigger('change');
                
                // (res.items || []).forEach(item => tbody.append(buildItemRow(item)));
                if (res.items && res.items.length > 0) {
                    $('#standard_items_section').show();
                    $('#no_item_section').hide();
                    // res.items.forEach(item => tbody.append(buildItemRow(item)));
                    res.items.forEach(item => {
                        let row = buildItemRow(item);
                        tbody.append(row);
                    });
                } else {
                    $('#standard_items_section').hide();
                    $('#no_item_section').show();
                    $('#addItemRow').hide();
                    $('#addNoItemRow').show();
                    populateNoItemRows(res);

                    $('#edit_amount').val(res.amount || 0);
                    $('#edit_cgst').val(res.cgst || 0);
                    $('#edit_sgst').val(res.sgst || 0);
                    $('#edit_igst').val(res.igst || 0);
                    $('#edit_total_amount').val(res.total_amount || 0);

                    $('#sum_cgst').html(res.cgst);
                    $('#sum_igst').html(res.igst);
                    $('#sum_sgst').html(res.sgst);

                    let amount = parseFloat(res.amount) || 0;

                    let cgst = parseFloat(res.cgst) || 0;
                    let sgst = parseFloat(res.sgst) || 0;
                    let igst = parseFloat(res.igst) || 0;
                    let total = amount + cgst + sgst + igst;

                    // $('#sum_grand_total').html(total);
                    setRoundOffSummary(res.total_amount || total, res.roundoff || 0);
                    tbody.html(''); // clear table
                }
                if (res.gst_mode === 'custom' && res.custom_gst && res.custom_gst.length) {
                    let iGstLedgers = @json($iGstLedgers);
                    let cGstLedgers = @json($cGstLedgers);
                    let sGstLedgers = @json($sGstLedgers);
                    let html = '';

                    res.custom_gst.forEach(slot => {

                        html += `
                        <tr data-rate="${slot.gst_rate}" data-slot-key="${slot.gst_rate}" data-purchase-ledger-id="${slot.ledger_id || ''}" style="color: black;">
                            <td>${slot.gst_rate}%</td>

                            <td class="slot-taxable">${slot.taxable}<input type="hidden" class="slot_purchase_ledger_id" value="${slot.ledger_id || ''}"></td>

                            <td>
                                <select class="slot-igst-ledger">
                                    ${buildLedgerOptions(iGstLedgers, slot.igst_ledger_id)}
                                </select>
                            </td>

                            <td>
                                <input type="number" class="slot-igst-amt" value="${slot.igst_amount || 0}">
                            </td>

                            <td>
                                <select class="slot-cgst-ledger">
                                    ${buildLedgerOptions(cGstLedgers, slot.cgst_ledger_id)}
                                </select>
                            </td>

                            <td>
                                <input type="number" class="slot-cgst-amt" value="${slot.cgst_amount || 0}">
                            </td>

                            <td>
                                <select class="slot-sgst-ledger">
                                    ${buildLedgerOptions(sGstLedgers, slot.sgst_ledger_id)}
                                </select>
                            </td>

                            <td>
                                <input type="number" class="slot-sgst-amt" value="${slot.sgst_amount || 0}">
                            </td>
                        </tr>`;
                    });

                    $('#customSlotsBody').html(html);
                }
                // if (!res.items || !res.items.length) {
                //     tbody.html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">No items — click Add Row</td></tr>');
                // }
                // Set GST values from DB
                // $('#sum_cgst').text((res.cgst || 0).toFixed(2));
                // $('#sum_sgst').text((res.sgst || 0).toFixed(2));
                // $('#sum_igst').text((res.igst || 0).toFixed(2));

                $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));

                // $('#sum_amount').text((res.amount || 0).toFixed(2));
                // $('#sum_grand_total').text((res.total_amount || 0).toFixed(2));
                $('#sum_amount').text(parseFloat(res.amount || 0).toFixed(2));
                // $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                // Hidden fields (VERY IMPORTANT)
                $('#edit_amount').val(res.amount || 0);
                $('#edit_cgst').val(res.cgst || 0);
                $('#edit_sgst').val(res.sgst || 0);
                $('#edit_igst').val(res.igst || 0);
                $('#edit_total_amount').val(res.amount || 0);
                if (!res.items || res.items.length === 0) {

                    $('#standard_items_section').hide();
                    //$('#custom_slots_section').hide();
                    $('#no_item_section').show();

                    // $('#gst_calc_mode').val('standard'); // optional

                }
                //if (res.items && res.items.length > 0) {
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                    setTimeout(() => {

                        // 🔥 Ensure correct section is visible
                        if ($('#no_item_section').is(':visible')) {
                            $('#noItemBody input').trigger('input');
                        } else {
                            $('#editItemsBody input').trigger('input');
                        }

                        recalcTotals();
                        applyPendingIssueHighlights(currentPurchasePendingIssues, currentPurchasePendingStatus);

                    }, 200);
                //}    
                applyPendingIssueHighlights(res.pending_issues, res.status);
            },
            error: () => $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-3" style="color:#ef4444;">Failed to load.</td></tr>')
        });
    });

    $(document).on('change', '#gst_calc_mode, #edit_is_igst', function () {
        recalcTotals();
    });

        $(document).on('change', '.item-name', function () {
            let selectedValue = $(this).find('option:selected').text() || $(this).val();
            applyItemUnit($(this));
            applyItemGstMapping(selectedValue, true);
            recalcTotals();
        });


    $(document).on('click', '.removeNoItem', function () {
        $(this).closest('tr').remove();
        recalcTotals();
    });

    // Recalculate no-item totals immediately when amount/rate/ledger changes
    $(document).on('input change', '.noitem-ledger, .noitem-gst, .noitem-amount', function () {
        recalcTotals();
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
    // $(document).on('input', '#editItemsBody input', function () {
    $(document).on('input change', '#editItemsBody input, #editItemsBody select', function () {
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

        let isNoItem = $('#no_item_section').is(':visible');

        if (mode === 'standard') {

            $('#custom_slots_section').hide();
            $('#standard_tax_rows').show();
            $('#custom_tax_rows').hide();
            $('#igst_toggle_wrap').show();

            if (!isNoItem) {
                $('#standard_items_section').show();
                $('#no_item_section').hide();
            }

        } else if (mode === 'custom') {

            $('#custom_slots_section').show();
            $('#standard_tax_rows').hide();
            $('#custom_tax_rows').show();
            $('#igst_toggle_wrap').hide();

            if (!isNoItem) {
                $('#standard_items_section').show();
                $('#no_item_section').hide();
            }
        }

        setTimeout(() => {
            recalcTotals();
        }, 100);
    });

    $('#edit_is_igst').on('change', function () {

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
        let partyName = $('#edit_party').val();

        if ($('#no_item_section').is(':visible')) {

            let noitemRows = collectNoItemRows();
            let amount = noitemRows.reduce((sum, row) => sum + (parseFloat(row.amount) || 0), 0);
            let isIGST = $('#edit_is_igst').is(':checked');

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
            let cgst = parseFloat($('#edit_cgst').val()) || 0;
            let sgst = parseFloat($('#edit_sgst').val()) || 0;
            let igst = parseFloat($('#edit_igst').val()) || 0;
            let total = amount + cgst + sgst + igst;
            // Update UI
            $('#sum_amount').text(parseFloat(amount).toFixed(2));
            $('#sum_cgst').text(parseFloat(cgst).toFixed(2));
            $('#sum_sgst').text(parseFloat(sgst).toFixed(2));
            $('#sum_igst').text(parseFloat(igst).toFixed(2));
            // $('#sum_grand_total').text(parseFloat(total).toFixed(2));
            setRoundOffSummary(total);

            // Hidden fields (VERY IMPORTANT)
            $('#edit_amount').val(amount);
            $('#edit_cgst').val(cgst);
            $('#edit_sgst').val(sgst);
            $('#edit_igst').val(igst);
            $('#edit_total_amount').val(total);

            //return; // ⛔ stop further item logic
        } else {
            $('#editItemsBody tr').each(function () {
                let row = $(this);
                items.push({
                    id:           row.find('.item-id').val(),
                    hsn:          row.find('.item-hsn').val(),
                    //item_name:    row.find('.item-name').val(),
                    item_name:    row.find('.item-name option:selected').text(),
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
        let noitemRows = collectNoItemRows();
        let hasItem = items.some(item => (item.item_name || '').trim() !== '');
        let hasNoItemLedger = noitemRows.some(row => String(row.ledger || '').trim() !== '');
        let selectedPurchaseLedger = $('#noitem_purchase_ledger').val();

        if (!partyName || (!hasItem && !hasNoItemLedger && !selectedPurchaseLedger)) {
            showToast('Please select Party and at least one Item or Purchase Ledger before updating.', 'error');
            return;
        }
        console.log({
            gst_mode: $('#gst_calc_mode').val(),
            custom_slots: collectCustomSlots(),
            noitem_rows: noitemRows
        });
 
        $.ajax({
            url: "{{ route('purchase.update') }}",
            type: "POST",
            // contentType: "application/json",
            data: {
                _token: "{{ csrf_token() }}",
                id: $('#edit_id').val(),

                invoice_no: $('#edit_invoice').val(),
                date: $('#edit_date').val(),
                //party_name: $('#edit_party').val(),
                party_name: partyName,
                gst_no: $('#edit_gst').val(),
                place_of_supply: $('#edit_place').val(),
                //purchase_ledger: $('#edit_ledger').val(),
                purchase_ledger: $('#noitem_purchase_ledger option:selected').text(),
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

                noitem_amount: $('#edit_amount').val(),
                purchase_ledger_id: $('#noitem_purchase_ledger').val(),
                purchase_ledger_name: $('#noitem_purchase_ledger option:selected').text(),
                items: items,
                entry_mode: $('#no_item_section').is(':visible') ? 'noitem' : 'item',
                custom_slots: collectCustomSlots(),
                noitem_rows: noitemRows //noitem_rows: collectNoItemRows()
            },
            success: (res) => {
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

            // let rate = parseFloat(
            //     row.find('.slot-rate').text().replace('%', '')
            // ) || 0;
            let rate = toNumber(row.data('rate')) || toNumber(row.find('.slot-rate').text().replace('%', ''));

            let taxable = toNumber(
                row.find('.slot-taxable').text()
            );

            slots.push({
                rate: rate,
                taxable: taxable,
                purchase_ledger_id: row.find('.slot_purchase_ledger_id').val() || row.data('purchase-ledger-id') || null,

                igst_ledger_id: row.find('.slot-igst-ledger, .igst_ledger').val(),
                igst_amount: toNumber(row.find('.slot-igst-amt, .igst_amt').val()),

                cgst_ledger_id: row.find('.slot-cgst-ledger, .cgst_ledger').val(),
                cgst_amount: toNumber(row.find('.slot-cgst-amt, .cgst_amt').val()),

                sgst_ledger_id: row.find('.slot-sgst-ledger, .sgst_ledger').val(),
                sgst_amount: toNumber(row.find('.slot-sgst-amt, .sgst_amt').val())
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
    function toNumber(v) {
        return parseFloat(String(v || 0).replace(/,/g, '')) || 0;
    }
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
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
        // 🔥 Apply Select2 AFTER append
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

        row.find('.item-amount').val(parseFloat(amount).toFixed(2));
        row.find('.item-cgst').val(parseFloat(cgst).toFixed(2));
        row.find('.item-sgst').val(parseFloat(sgst).toFixed(2));
        row.find('.item-igst').val(parseFloat(igst).toFixed(2));
        row.find('.item-total').val(parseFloat(total).toFixed(2));
    }

    function toggleNoItemHeaderPurchaseLedger() {
        const hideHeaderLedger = $('#no_item_section').is(':visible') && $('#gst_calc_mode').val() === 'custom';
        $('#noitem_purchase_ledger').closest('.receipt-field-row').toggle(!hideHeaderLedger);
    }

    // Master recalc — updates summary, footer, and custom slots
    function recalcTotals() {
        toggleNoItemHeaderPurchaseLedger();
        // let mode = $('#gst_calc_mode').val();
        //let mode = $('#standard_items_section').is(':visible') ? 'item' : 'noitem';
        let mode = $('#no_item_section').is(':visible') ? 'noitem' : 'item';
        
        let sumAmt=0, sumSgst=0, sumCgst=0, sumIgst=0, sumTotal=0;
        let gstMode = $('#gst_calc_mode').val(); // ✅ FIX

        let isIGST = $('#edit_is_igst').is(':checked');

        let totalAmount = 0;
        let totalCGST = 0;
        let totalSGST = 0;
        let totalIGST = 0;

        // Collect per-rate data for custom mode
        let rateMap = {}; // { '5': {amt,igst,cgst,sgst}, '18': {...}, ... }

        $('#editItemsBody tr').each(function () {
            let row     = $(this);
            let amt     = parseFloat(row.find('.item-amount').val()) || 0;
            let sgst    = parseFloat(row.find('.item-sgst').val())   || 0;
            let cgst    = parseFloat(row.find('.item-cgst').val())   || 0;
            let igst    = parseFloat(row.find('.item-igst').val())   || 0;
            // let total   = parseFloat(row.find('.item-total').val())  || 0;
            let gstRate = row.find('.item-gst_rate').val() || '0';
            if (gstMode === 'custom') {
                let taxParts = calculateGstParts(amt, gstRate, isIGST);
                igst = taxParts.igst;
                cgst = taxParts.cgst;
                sgst = taxParts.sgst;
            }

            let total   = amt + sgst + cgst + igst;
            let itemName = row.find('.item-name option:selected').text() || row.find('.item-name').val() || '';
            let itemMapping = findItemGstMapping(itemName);

            sumAmt   += amt;
            sumSgst  += sgst;
            sumCgst  += cgst;
            sumIgst  += igst;
            sumTotal += total;

            // Accumulate into rate bucket for custom mode
            if (!rateMap[gstRate]) rateMap[gstRate] = { amt:0, igst:0, cgst:0, sgst:0, itemName: '', itemMapping: null };
            rateMap[gstRate].amt  += amt;
            rateMap[gstRate].igst += igst;
            rateMap[gstRate].cgst += cgst;
            rateMap[gstRate].sgst += sgst;
            if (!rateMap[gstRate].itemName && itemName) {
                rateMap[gstRate].itemName = itemName;
            }
            if (!rateMap[gstRate].itemMapping && itemMapping) {
                rateMap[gstRate].itemMapping = itemGstMappingObject(itemMapping);
            }
        });

        // if (gstMode === 'custom') {

        //     $('#custom_slots_section').show();
        //     $('#standard_tax_rows').hide();

        //     let html = '';

        //     Object.keys(rateMap).forEach(rate => {

        //         // let taxableAmt = parseFloat(rateMap[rate].taxable);
        //         // let gstAmt = parseFloat(rateMap[rate].gst);
        //         let taxableAmt = parseFloat(rateMap[rate].amt || 0);
        //         let gstAmt = taxableAmt * (parseFloat(rate) || 0) / 100;

        //         let igstVal = isIGST ? gstAmt : 0;
        //         let cgstVal = isIGST ? 0 : gstAmt / 2;
        //         let sgstVal = isIGST ? 0 : gstAmt / 2;

        //         html += `
        //         <tr style="color: black;">
        //             <td class="slot-rate">${rate}%</td>
        //             <td class="slot-taxable">${parseFloat(taxableAmt).toFixed(2)}</td>

        //             <td>
        //                 <select class="slot-igst-ledger receipt-input">
        //                     ${buildLedgerOptions(IGST_LEDGERS)}
        //                 </select>
        //             </td>
        //             <td>
        //                 <input type="number" class="slot-igst-amt receipt-input" value="${parseFloat(igstVal).toFixed(2)}">
        //             </td>

        //             <td>
        //                 <select class="slot-cgst-ledger receipt-input">
        //                     ${buildLedgerOptions(CGST_LEDGERS)}
        //                 </select>
        //             </td>
        //             <td>
        //                 <input type="number" class="slot-cgst-amt receipt-input" value="${parseFloat(cgstVal).toFixed(2)}">
        //             </td>

        //             <td>
        //                 <select class="slot-sgst-ledger receipt-input">
        //                     ${buildLedgerOptions(SGST_LEDGERS)}
        //                 </select>
        //             </td>
        //             <td>
        //                 <input type="number" class="slot-sgst-amt receipt-input" value="${parseFloat(sgstVal).toFixed(2)}">
        //             </td>
        //         </tr>
        //         `;
        //     });

        //     $('#customSlotsBody').html(html);
        // }

        if (mode === 'noitem') {

            let amount = 0, cgst = 0, sgst = 0, igst = 0;
            rateMap = {};

            $('#noItemBody tr').each(function(index) {
                let rate = parseFloat($(this).find('.noitem-gst').val()) || 0;
                let rowAmount = parseFloat($(this).find('.noitem-amount').val()) || 0;
                let ledgerSelect = $(this).find('.noitem-ledger');
                let ledgerId = ledgerSelect.val() || '';
                let ledgerName = ledgerSelect.find('option:selected').text() || '';
                let gstAmount = (rowAmount * rate) / 100;
                let key = `row:${index}|${rate}|${ledgerId}`;

                amount += rowAmount;
                if (isIGST) {
                    igst += gstAmount;
                } else {
                    cgst += gstAmount / 2;
                    sgst += gstAmount / 2;
                }

                rateMap[key] = {
                    amt: rowAmount,
                    igst: isIGST ? gstAmount : 0,
                    cgst: isIGST ? 0 : gstAmount / 2,
                    sgst: isIGST ? 0 : gstAmount / 2,
                    rate: rate,
                    ledgerId: ledgerId,
                    ledgerName: ledgerName,
                    slotKey: key
                };
            });

            $('#sum_amount').text(amount.toFixed(2));
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));
            // $('#sum_grand_total').text((amount + cgst + sgst + igst).toFixed(2));
            setRoundOffSummary(amount + cgst + sgst + igst);
            
            // Keep custom GST slots visible/updated for no-item purchase mode
            if (gstMode === 'custom') {
                $('#custom_slots_section').show();
                $('#standard_tax_rows').hide();
                $('#custom_tax_rows').show();

                $('#edit_amount').val(parseFloat(amount).toFixed(2));
                renderCustomSlots(rateMap, amount + cgst + sgst + igst);
            }
            return; // 🔥 VERY IMPORTANT
        }

        // Update hidden inputs (keep existing save working)
        $('#edit_amount').val(parseFloat(sumAmt).toFixed(2));
        $('#edit_sgst').val(parseFloat(sumSgst).toFixed(2));
        $('#edit_cgst').val(parseFloat(sumCgst).toFixed(2));
        $('#edit_igst').val(parseFloat(sumIgst).toFixed(2));
        $('#edit_total_amount').val(parseFloat(sumTotal).toFixed(2));

        // Update visible summary
        $('#sum_amount').text(fmt(sumAmt));
        $('#foot_amount').text(fmt(sumAmt));
        // $('#foot_total').text(fmt(sumTotal));
        // $('#sum_grand_total').text(fmt(sumTotal));
        const roundedSumTotal = setRoundOffSummary(sumTotal);
        $('#foot_total').text(fmt(roundedSumTotal));

        // Renumber rows
        $('#editItemsBody tr').each(function(i) { $(this).find('.td-sr').text(i+1); });

        if (mode === 'standard') {
            $('#standard_tax_rows').show();
            $('#custom_slots_section').hide();
            $('#custom_tax_rows').hide();
            $('#sum_sgst').text(fmt(sumSgst));
            $('#sum_cgst').text(fmt(sumCgst));
            $('#sum_igst').text(fmt(sumIgst));
        } else {
            $('#standard_tax_rows').hide();
            $('#custom_slots_section').show();
            $('#custom_tax_rows').show();
            // CUSTOM MODE: render rate-wise slots
            renderCustomSlots(rateMap, sumTotal);
        }
        console.log("MODE:", mode);
        console.log("No item rows:", collectNoItemRows());
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

    function calculateGstParts(amount, rate, isIGST) {
        amount = parseFloat(amount) || 0;
        rate = parseFloat(rate) || 0;
        let gstAmount = (amount * rate) / 100;

        return {
            igst: isIGST ? gstAmount : 0,
            cgst: isIGST ? 0 : gstAmount / 2,
            sgst: isIGST ? 0 : gstAmount / 2
        };
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
    const ROUND_OFF_SIDE = @json($roundOffSide ?? 'normal');
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
        let item = findItemGstMapping(itemName);
        if (!item) {
            return;
        }

        if (item.IGSTLedgerId && (force || !$('#igst_ledger').val())) {
            $('#igst_ledger').val(item.IGSTLedgerId).trigger('change');
        }
        if (item.CGSTLedgerId && (force || !$('#cgst_ledger').val())) {
            $('#cgst_ledger').val(item.CGSTLedgerId).trigger('change');
        }
        if (item.SGSTLedgerId && (force || !$('#sgst_ledger').val())) {
            $('#sgst_ledger').val(item.SGSTLedgerId).trigger('change');
        }

        if ($('#gst_calc_mode').val() === 'custom') {
            let normalizedItem = normalizeLedgerName(itemName);
            $('#customSlotsBody tr').each(function() {
                let slotItemName = String($(this).data('item-name') || $(this).find('.slot_item_name').val() || '').trim().toLowerCase();
                if (!slotItemName || slotItemName !== normalizedItem) {
                    return;
                }

                let igstSelect = $(this).find('.slot-igst-ledger');
                let cgstSelect = $(this).find('.slot-cgst-ledger');
                let sgstSelect = $(this).find('.slot-sgst-ledger');

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
        let html = '<option value="">Select Ledger</option>';
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
                <td><button type="button" class="removeNoItem receipt-del-btn">×</button></td>
            </tr>
        `;
        $('#noItemBody').append(row);
    }

    function populateNoItemRows(res, readonly = false) {
        $('#noItemBody').empty();
        if (res.custom_gst && res.custom_gst.length) {
            res.custom_gst.forEach(slot => {
                addNoItemRow({
                    ledger: slot.ledger_id || slot.ledger_name || res.purchase_ledger,
                    gst: slot.gst_rate,
                    amount: slot.taxable || slot.amount || 0
                });
            });
        } else {
            addNoItemRow({
                ledger: res.purchase_ledger,
                gst: res.gst_rate || 0,
                amount: res.amount || 0
            });
        }

        if (readonly) {
            $('#noItemBody input, #noItemBody select, #noItemBody button')
                .prop('disabled', true)
                .css('pointer-events', 'none');
        }
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

    function buildLedgerOptions(list, selectedId) {
        let html = '<option value="">Select Ledger</option>';

        list.forEach(l => {
            let selected = (String(l.id) === String(selectedId)) ? 'selected' : '';
            html += `<option value="${l.id}" ${selected}>${l.name}</option>`;
        });

        return html;
    }

    // ═══════════════════════════════════════════════════════════════
    // CUSTOM MODE — render rate-wise slots
    // Each unique GST% from items gets one row with IGST/CGST/SGST
    // ledger dropdowns and auto-computed tax amounts.
    // ═══════════════════════════════════════════════════════════════
    function renderCustomSlots(rateMap, grandTotal) {
        let sGstLedgers = @json($sGstLedgers ?? []);
        let cGstLedgers = @json($cGstLedgers ?? []);
        let iGstLedgers = @json($iGstLedgers ?? []);

        let allRates = Object.keys(rateMap).filter(r => {
            let data = rateMap[r] || {};
            return (parseFloat(data.amt) || 0) !== 0 ||
                (parseFloat(data.igst) || 0) !== 0 ||
                (parseFloat(data.cgst) || 0) !== 0 ||
                (parseFloat(data.sgst) || 0) !== 0;
        });

        // Preserve any existing selected slot metadata before rebuilding
        let existing = {};
        $('#customSlotsBody tr').each(function () {
            let row = $(this);
            let key = row.data('slot-key') || String(row.find('.slot-rate').text().replace('%','')).trim();
            if (!key) return;
            existing[key] = {
                igst_ledger: row.find('.slot-igst-ledger').val(),
                cgst_ledger: row.find('.slot-cgst-ledger').val(),
                sgst_ledger: row.find('.slot-sgst-ledger').val(),
                igst_amount: row.find('.slot-igst-amt').val(),
                cgst_amount: row.find('.slot-cgst-amt').val(),
                sgst_amount: row.find('.slot-sgst-amt').val(),
                igst_manual: row.find('.slot-igst-amt').data('manual') == 1,
                cgst_manual: row.find('.slot-cgst-amt').data('manual') == 1,
                sgst_manual: row.find('.slot-sgst-amt').data('manual') == 1,
                purchase_ledger_id: row.data('purchase-ledger-id') || row.find('.slot_purchase_ledger_id').val(),
                itemName: row.data('item-name') || row.find('.slot_item_name').val() || ''
            };
        });

        // Build the slot table body
        let slotHtml = '';
        let customSgst=0, customCgst=0, customIgst=0;

        allRates.forEach(function(rate) {
            let data   = rateMap[rate] || { amt:0, igst:0, cgst:0, sgst:0, itemName: '', itemMapping: null };
            let slotKey = data.slotKey || rate;
            let existingSlot = existing[slotKey] || existing[`${rate}|${data.ledgerId || ''}`] || null;
            let displayRate = data.rate ?? rate;
            let halfR  = parseFloat(rate) / 2;
            // Auto-compute: use sum from item recalc (standard) or preserve existing custom values
            // let igstAmt = existingSlot?.igst_amount ?? data.igst;
            // let cgstAmt = existingSlot?.cgst_amount ?? data.cgst;
            // let sgstAmt = existingSlot?.sgst_amount ?? data.sgst;
            // Auto-compute GST from the current taxable amount/rate. Only keep a
            // manually edited slot amount; stale loaded values must not block recalculation
            // when the user changes item qty/rate/GST or no-item amount/rate.
            let igstAmt = existingSlot?.igst_manual ? existingSlot.igst_amount : data.igst;
            let cgstAmt = existingSlot?.cgst_manual ? existingSlot.cgst_amount : data.cgst;
            let sgstAmt = existingSlot?.sgst_manual ? existingSlot.sgst_amount : data.sgst;
            customIgst += parseFloat(igstAmt);
            customCgst += parseFloat(cgstAmt);
            customSgst += parseFloat(sgstAmt);

            let isZero = data.amt === 0;

            // Build ledger options
            let iOpts = buildLedgerOptions(iGstLedgers, existingSlot?.igst_ledger || mappedGstLedgerId('igst', null, data.ledgerId || '', data.ledgerName || '', data.itemMapping || null));
            let cOpts = buildLedgerOptions(cGstLedgers, existingSlot?.cgst_ledger || mappedGstLedgerId('cgst', null, data.ledgerId || '', data.ledgerName || '', data.itemMapping || null));
            let sOpts = buildLedgerOptions(sGstLedgers, existingSlot?.sgst_ledger || mappedGstLedgerId('sgst', null, data.ledgerId || '', data.ledgerName || '', data.itemMapping || null));

            slotHtml += `<tr style="color: black;" class="${isZero ? 'zero-row' : ''}" data-rate="${displayRate}" data-slot-key="${data.slotKey || rate}" data-purchase-ledger-id="${data.ledgerId || ''}" data-item-name="${escapeHtml(data.itemName || '')}">
                <td><span class="rate-badge"><span class="slot-rate"></span>${displayRate}%</span></td>
                <td><strong class="slot-taxable">${fmt(data.amt)}</strong><input type="hidden" class="slot_purchase_ledger_id" value="${data.ledgerId || ''}"><input type="hidden" class="slot_item_name" value="${escapeHtml(data.itemName || '')}"></td>
                <td><select class="slot-igst-ledger" data-rate="${displayRate}">${iOpts}</select></td>
                <td><input type="number" class="slot-igst-amt" data-rate="${rate}" value="${parseFloat(igstAmt).toFixed(2)}" step="any"></td>
                <td><select class="slot-cgst-ledger" data-rate="${displayRate}">${cOpts}</select></td>
                <td><input type="number" class="slot-cgst-amt" data-rate="${rate}" value="${parseFloat(cgstAmt).toFixed(2)}" step="any"></td>
                <td><select class="slot-sgst-ledger" data-rate="${displayRate}">${sOpts}</select></td>
                <td><input type="number" class="slot-sgst-amt" data-rate="${rate}" value="${parseFloat(sgstAmt).toFixed(2)}" step="any"></td>
            </tr>`;
        });

        $('#customSlotsBody').html(slotHtml);
        if (currentPurchasePendingIssues.length || currentPurchasePendingStatus === 'pending') {
            applyPendingIssueHighlights(currentPurchasePendingIssues, currentPurchasePendingStatus);
        }

        // Render custom mode summary
        let customSummaryHtml = `
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(customIgst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(customCgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(customSgst)}</span></div>`;
        $('#custom_tax_rows').html(customSummaryHtml);

        // Update hidden fields for save
        $('#edit_igst').val(parseFloat(customIgst).toFixed(2));
        $('#edit_cgst').val(parseFloat(customCgst).toFixed(2));
        $('#edit_sgst').val(parseFloat(customSgst).toFixed(2));
        let total = parseFloat($('#edit_amount').val()) + parseFloat($('#edit_igst').val()) + parseFloat($('#edit_cgst').val()) + parseFloat($('#edit_sgst').val());
        $('#edit_total_amount').val(parseFloat(total).toFixed(2));
        // $('#sum_grand_total').text(fmt(parseFloat(total)));
        setRoundOffSummary(total);
    }

    // When user manually edits a slot amount → recalc grand total
    $(document).on('input', '.slot-igst-amt, .slot-cgst-amt, .slot-sgst-amt', function () {
        $(this).data('manual', 1);
        let igst=0, cgst=0, sgst=0;
        $('.slot-igst-amt').each(function () { igst += parseFloat($(this).val())||0; });
        $('.slot-cgst-amt').each(function () { cgst += parseFloat($(this).val())||0; });
        $('.slot-sgst-amt').each(function () { sgst += parseFloat($(this).val())||0; });
        let base  = parseFloat($('#edit_amount').val())||0;
        let total = base + igst + cgst + sgst;
        $('#edit_igst').val(parseFloat(igst).toFixed(2));
        $('#edit_cgst').val(parseFloat(cgst).toFixed(2));
        $('#edit_sgst').val(parseFloat(sgst).toFixed(2));
        $('#edit_total_amount').val(parseFloat(total).toFixed(2));
        // $('#sum_grand_total').text(fmt(parseFloat(total)));
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
    function closeEditModal()   { 
        document.getElementById('editModal').classList.remove('show');  // 🔥 RESET STATE
        $('#updateRow').show();
        $('#addItemRow').show();
        $('#addNoItemRow').hide();

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

    function initItemSelect2() {
        if ($.fn.select2) {
            $('#editItemsBody .itemSelect').select2({
                dropdownParent: $('#editModal'), // 🔥 MOST IMPORTANT
                width: '100%',
                placeholder: "Search Item...",
                allowClear: true
            });
        }
    }

   function applyItemUnit($select) {
        let itemName = $select.find('option:selected').text() || $select.val();
        let item = ITEM_MASTER.find(
            x => String(x.strItemName || '').trim() === String(itemName || '').trim()
        );
        if (!item) return;
        $select.closest('tr').find('.item-unit').val(item.strBaseUnits || 'NOS');
    }
</script>
@endsection
