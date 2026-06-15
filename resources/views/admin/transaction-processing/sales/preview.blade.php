@extends('layouts.super_admin')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="container mx-auto">
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow border border-gray-200 dark:border-neutral-700">
        <!-- HEADER -->
        <div class="flex justify-between items-center px-5 py-3 border-b border-neutral-700">
            <!-- <div class="flex items-center gap-3">
                <h2 class="text-white text-lg font-semibold">
                    Sales Transactions
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
                    Sales Transactions
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
                            class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-white">
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
                <label class="flex gap-4 mt-2 text-gray-700 dark:text-gray-300">General Filters</label>
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
        <!-- <form id="salesForm" method="POST" action="{{ route('sales.save') }}"> -->
        <form id="salesForm">
            @csrf
            <div class="overflow-x-auto">
                <table id="salesTable" class="min-w-full text-sm text-gray-700 dark:text-gray-300 border-collapse">
                    <thead class="bg-gray-100 dark:bg-neutral-800 text-xs text-gray-600 dark:text-gray-400 uppercase">
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
                                    value="{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}"
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
                                <select name="ledger[{{$row->id}}]"
                                    class="ledgerSelect inputCell">
                                    <option value="">Select Ledger</option>
                                    @foreach($ledgers as $ledger)
                                    <option value="{{$ledger->name}}"
                                        {{ $row->sales_ledger==$ledger->name?'selected':'' }}>
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
                                    <option value="">Select Ledger</option>
                                    @foreach($ledgers as $ledger)
                                    <option value="{{$ledger->name}}"
                                        {{ $row->sales_ledger==$ledger->name?'selected':'' }}>
                                        {{$ledger->name}}
                                    </option>
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
                                    data-date="{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}"
                                    data-gst_no="{{$row->gst_no}}"
                                    data-vchtype="{{$row->vchType}}"
                                    data-party="{{$row->party_name}}"
                                    data-place="{{$row->place_of_supply}}"
                                    data-ledger="{{$row->sales_ledger}}"
                                    data-amount="{{$row->total_amount}}"
                                    data-item="{{$row->item_name}}"
                                    data-qty="{{$row->quantity}}"
                                    data-rate="{{$row->rate}}"
                                    data-cgst="{{$row->cgst}}"
                                    data-sgst="{{$row->sgst}}"
                                    data-igst="{{$row->igst}}">
                                    <i class="fa-solid fa-pen"></i>
                                </button> -->
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
                                    data-ledger="{{ $row->sales_ledger }}"
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
                <div class="receipt-field-row">
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

            <div id="no_item_section" style="display:none; padding:10px;">
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
            <h3>View Sales</h3>
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
                    <div><label>Sales Ledger</label><p id="v_ledger"></p></div>
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
    #editModal.view-only .custom-slots-table .zero-row { opacity:1; }
    #editModal.view-only .custom-slots-table td { color:#111827; background:#fff; font-weight:600; }
    #editModal.view-only .custom-slots-table .view-cell-num { text-align:right; font-variant-numeric:tabular-nums; }
    #editModal.view-only #no_item_section select,
    #editModal.view-only #no_item_section input {
        opacity: 1;
        color: #111827 !important;
        -webkit-text-fill-color: #111827;
        background: #fff !important;
    }
    #editModal.view-only #no_item_section .select2-container--disabled,
    #editModal.view-only #no_item_section .select2-selection {
        opacity: 1 !important;
        color: #111827 !important;
        background: #fff !important;
    }

    /* ══ VIEW MODAL STYLES ══ */
    .view-card { background:#1e293b; padding:16px; border-radius:10px; margin-bottom:16px; }
    .view-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px 20px; }
    .view-grid label { font-size:11px; color:#94a3b8; }
    .view-grid p { font-size:13px; font-weight:500; margin:2px 0 0; color:#e2e8f0; }
    .status-badge { display:inline-block; padding:3px 8px; border-radius:6px; font-size:11px; background:#f59e0b; color:white; }
    .view-totals { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; }
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
            $('#salesTable tbody tr').each(function() {
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
            url: "{{ route('sales.ledger.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
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
    const ITEM_MASTER = @json($stockItems);
    const SALES_GST_MAPPINGS = @json($salesGstMappings ?? []);
    const SALES_LEDGERS = @json($salesLedgers ?? []);

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
        return findSalesLedgerMapping(select.val(), select.find('option:selected').text());
    }

    function mappedGstLedgerId(type, existing = null, ledgerValue = '', ledgerText = '') {
        if (existing) {
            return existing;
        }

        const mapping = ledgerValue || ledgerText
            ? findSalesLedgerMapping(ledgerValue, ledgerText)
            : getSelectedSalesLedgerMapping();

        return mapping ? mapping[`${type}_id`] : null;
    }

    $(document).on('change', '#noitem_sales_ledger', function() {
        recalcTotals();
    });

    function buildNoItemLedgerOptions(selected = '') {
        let html = '<option value="">Select Ledger</option>';

        SALES_LEDGERS.forEach(ledger => {
            const selectedMatch =
                String(ledger.id) === String(selected) ||
                normalizeLedgerName(ledger.name) === normalizeLedgerName(selected);

            html += `<option value="${ledger.id}" ${selectedMatch ? 'selected' : ''}>${ledger.name}</option>`;
        });

        return html;
    }

    function addNoItemRow(row = {}) {
        const tr = `
            <tr>
                <td>
                    <select class="receipt-input noitem-ledger">
                        ${buildNoItemLedgerOptions(row.ledger || row.ledger_id || row.ledger_name || '')}
                    </select>
                </td>
                <td>
                    <input type="number" class="receipt-input noitem-gst" value="${row.gst || row.gst_rate || 0}" step="any">
                </td>
                <td>
                    <input type="number" class="receipt-input noitem-amount" value="${row.amount || row.taxable || 0}" step="any">
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
            url: "{{ route('transaction_processing.sales_sumbit') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                alert('Saved Successfully');
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
            url: "{{ route('sales.delete', ':id') }}".replace(':id', id),
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

    function getIsIgstInput() {
        return $('[id="edit_is_igst"]');
    }

    function isIgstChecked() {
        let $visible = getIsIgstInput().filter(':visible').first();
        if ($visible.length) return $visible.is(':checked');
        return getIsIgstInput().first().is(':checked');
    }

    function setIsIgstChecked(checked) {
        getIsIgstInput().prop('checked', !!checked);
    }


    // ═══════════════════════════════════════════════════════════════════════
    // VIEW MODAL
    // ═══════════════════════════════════════════════════════════════════════
    $(document).on('click', '.viewRow', function() {
        let id = $(this).data('id');

        // Open same edit modal
        openEditModal();
        $('#editModal').addClass('view-only');

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
            url: "{{ route('sales.show', ':id') }}".replace(':id', id),
            type: "GET",
            success: function(res) {
                // console.log(res);

                // Fill header fields
                $('#edit_id').val(res.id);
                $('#edit_invoice').val(res.invoice_no);
                $('#edit_date').val(res.date);
                $('#edit_gst').val(res.gst_no);
                $('#edit_party').val(res.party_name);
                // $('#edit_place').val(res.place_of_supply);
                $('#edit_place option').each(function () {
                    if ($(this).val().toLowerCase().trim() === String(res.place_of_supply).toLowerCase().trim()) {
                        $(this).prop('selected', true);
                    }
                });
                // $('#edit_voucher_type').val(res.vchType);
                $('#edit_voucher_type option').each(function () {
                    if ($(this).val().toLowerCase().trim() === String(res.vchType).toLowerCase().trim()) {
                        $(this).prop('selected', true);
                    }
                });
                $('#edit_address').val(res.address);
                $('#edit_pincode').val(res.pincode);
                $('#edit_city').val(res.city);
                // $('#edit_is_igst').val(res.is_igst);
                // $('#edit_is_igst').prop('checked', res.is_igst == 1);
                setIsIgstChecked(res.is_igst == 1);
                $('#edit_remarks').val(res.Remarks);

                // Respect stored GST mode at edit time
                //$('#edit_is_igst').prop('checked', Number(res.is_igst) === 1);
                setIsIgstChecked(Number(res.is_igst) === 1);
                toggleGSTLedger();
                
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                $('#igst_ledger').val(mappedGstLedgerId('igst', res.igst_id, res.sales_ledger, res.sales_ledger)).trigger('change');
                $('#cgst_ledger').val(mappedGstLedgerId('cgst', res.cgst_id, res.sales_ledger, res.sales_ledger)).trigger('change');
                $('#sgst_ledger').val(mappedGstLedgerId('sgst', res.sgst_id, res.sales_ledger, res.sales_ledger)).trigger('change');
                $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));

                $('#edit_cgst').val(res.cgst);
                $('#edit_sgst').val(res.sgst);
                $('#edit_igst').val(res.igst);
                $('#edit_total_amount').val(res.total_amount);
                // Items
                let tbody = $('#editItemsBody').empty();
                $('#noitem_sales_ledger').val(res.sales_ledger).trigger('change.select2');
                if (res.items && res.items.length > 0) {

                    $('#standard_items_section').show();
                    $('#no_item_section').hide();
                    $('#addItemRow').hide();
                    $('#addNoItemRow').hide();
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
                    $('#addItemRow').hide();
                    $('#addNoItemRow').hide();
                    $('#noItemBody').empty();

                    if (res.custom_gst && res.custom_gst.length) {
                        res.custom_gst.forEach(slot => addNoItemRow({
                            ledger: slot.ledger_id || slot.ledger_name || res.sales_ledger,
                            gst: slot.gst_rate,
                            amount: slot.taxable || slot.amount || 0
                        }));
                    } else {
                        addNoItemRow({
                            ledger: res.sales_ledger,
                            gst: res.gst_rate || 0,
                            amount: res.amount || 0
                        });
                    }

                    $('#noitem_amount').val(res.amount);
                    $('#noitem_gst_rate').val(res.gst_rate || 0);
                    
                    setSelectValueByTextOrValue($('#noitem_sales_ledger'), res.sales_ledger);
                    // Display stored GST values directly for view mode
                    $('#sum_amount').text(parseFloat(res.amount || 0).toFixed(2));
                    $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                    $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                    $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                    $('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
                    $('#noItemBody input, #noItemBody select').prop('disabled', true);
                    $('#noItemBody .receipt-del-btn').hide();
                }

                // Handle custom GST mode display
                if (res.gst_mode === 'custom' && res.custom_gst && res.custom_gst.length) {
                    let iGstLedgers = @json($iGstLedgers);
                    let cGstLedgers = @json($cGstLedgers);
                    let sGstLedgers = @json($sGstLedgers);
                    let html = '';

                    res.custom_gst.forEach(slot => {
                        let slotLedgerId = slot.ledger_id || slot.sales_ledger_id || '';
                        let slotLedgerName = slot.ledger_name || '';
                        let igstLedgerName = iGstLedgers.find(l => l.id == mappedGstLedgerId('igst', slot.igst_ledger_id, slotLedgerId, slotLedgerName))?.name || '';
                        let cgstLedgerName = cGstLedgers.find(l => l.id == mappedGstLedgerId('cgst', slot.cgst_ledger_id, slotLedgerId, slotLedgerName))?.name || '';
                        let sgstLedgerName = sGstLedgers.find(l => l.id == mappedGstLedgerId('sgst', slot.sgst_ledger_id, slotLedgerId, slotLedgerName))?.name || '';

                        html += `
                        <tr data-rate="${parseFloat(slot.gst_rate) || 0}">
                            <td>${slot.gst_rate}%</td>
                            <td class="slot-taxable view-cell-num">${parseFloat(slot.taxable || 0).toFixed(2)}</td>
                            <td>${igstLedgerName || '-'}</td>
                            <td class="view-cell-num">${parseFloat(slot.igst_amount || 0).toFixed(2)}</td>
                            <td>${cgstLedgerName || '-'}</td>
                            <td class="view-cell-num">${parseFloat(slot.cgst_amount || 0).toFixed(2)}</td>
                            <td>${sgstLedgerName || '-'}</td>
                            <td class="view-cell-num">${parseFloat(slot.sgst_amount || 0).toFixed(2)}</td>
                        </tr>`;
                    });

                    $('#customSlotsBody').html(html);

                    let totalCgst = 0;
                    let totalSgst = 0;
                    let totalIgst = 0;

                    res.custom_gst.forEach(slot => {
                        totalCgst += parseFloat(slot.cgst_amount || 0);
                        totalSgst += parseFloat(slot.sgst_amount || 0);
                        totalIgst += parseFloat(slot.igst_amount || 0);
                    });

                    $('#txt_cgst').text(totalCgst.toFixed(2));
                    $('#txt_sgst').text(totalSgst.toFixed(2));
                    $('#txt_igst').text(totalIgst.toFixed(2));

                }

                // For view mode, don't recalculate - just display stored values
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

    // ═══════════════════════════════════════════════════════════════════════
    // EDIT MODAL
    // ═══════════════════════════════════════════════════════════════════════
    // ═══════ EDIT MODAL ═══════
    $(document).on('click', '.editRow', function() {
        let btn = $(this),
            id = btn.data('id');

        $('#editModal').removeClass('view-only');
        $('#updateRow').show();
        $('#addItemRow').show();
        $('#addNoItemRow').hide();

        $('#editModal input, #editModal select, #editModal textarea')
            .prop('disabled', false)
            .css('pointer-events', 'auto');

        $('.receipt-del-btn').show();
        // Reset mode to standard
        $('#gst_calc_mode').val('standard').trigger('change');


        $('#edit_id').val(id);
        $('#edit_invoice').val(btn.data('invoice'));
        $('#edit_date').val(btn.data('date'));
        $('#edit_gst').val(btn.data('gst_no'));
        // $('#edit_voucher_type').val(btn.data('vchtype'));
        $('#edit_party').val(btn.data('party'));
        // $('#edit_place').val(btn.data('place'));
        $('#edit_ledger').val(btn.data('ledger'));

        let party = btn.data('party');
        $('#edit_party').val(party).trigger('change'); // 🔥 IMPORTANT
        // $('#edit_place').val(btn.data('place'));
        let vch = btn.data('vchtype');
        $('#edit_voucher_type option').each(function() {
            if ($(this).val().toLowerCase().trim() === String(vch).toLowerCase().trim()) {
                $(this).prop('selected', true);
            }
        });

        // Place of Supply (case-insensitive match)
        let place = btn.data('place');
        $('#edit_place option').each(function() {
            if ($(this).val().toLowerCase().trim() === String(place).toLowerCase().trim()) {
                $(this).prop('selected', true);
            }
        });
        $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">Loading…</td></tr>');
        openEditModal();

        $.ajax({
            url: "{{ route('sales.show',':id') }}".replace(':id', id),
            type: "GET",
            success: function(res) {
                $('#edit_address').val(res.address || '');
                $('#edit_pincode').val(res.pincode || '');
                $('#edit_city').val(res.city || '');
                $('#edit_remarks').val(res.Remarks || '');

                // Respect stored GST mode at edit time
                // $('#edit_is_igst').prop('checked', Number(res.is_igst) === 1);
                setIsIgstChecked(Number(res.is_igst) === 1);
                toggleGSTLedger();
                
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                $('#igst_ledger').val(mappedGstLedgerId('igst', res.igst_id, res.sales_ledger, res.sales_ledger)).trigger('change');
                $('#cgst_ledger').val(mappedGstLedgerId('cgst', res.cgst_id, res.sales_ledger, res.sales_ledger)).trigger('change');
                $('#sgst_ledger').val(mappedGstLedgerId('sgst', res.sgst_id, res.sales_ledger, res.sales_ledger)).trigger('change');

                // setSelectValueByTextOrValue($('#noitem_sales_ledger'), res.sales_ledger);
                $('#noitem_sales_ledger').val(res.sales_ledger).trigger('change.select2');
                let tbody = $('#editItemsBody').empty();
                // (res.items || []).forEach(item => tbody.append(buildItemRow(item)));
                if (res.items && res.items.length > 0) {
                    $('#standard_items_section').show();
                    $('#no_item_section').hide();
                    $('#addItemRow').show();
                    $('#addNoItemRow').hide();
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
                    $('#noItemBody').empty();

                    $('#edit_amount').val(res.amount || 0);
                    $('#edit_cgst').val(res.cgst || 0);
                    $('#edit_sgst').val(res.sgst || 0);
                    $('#edit_igst').val(res.igst || 0);
                    $('#edit_total_amount').val(res.total_amount || 0);

                    if (res.custom_gst && res.custom_gst.length) {
                        res.custom_gst.forEach(slot => addNoItemRow({
                            ledger: slot.ledger_id || slot.ledger_name || res.sales_ledger,
                            gst: slot.gst_rate,
                            amount: slot.taxable || slot.amount || 0
                        }));
                    } else {
                        addNoItemRow({
                            ledger: res.sales_ledger,
                            gst: res.gst_rate || 0,
                            amount: res.amount || 0
                        });
                    }

                    $('#noitem_amount').val(res.amount);
                    $('#noitem_gst_rate').val(res.gst_rate || 0);
                    $('#sum_cgst').html(res.cgst);
                    $('#sum_igst').html(res.igst);
                    $('#sum_sgst').html(res.sgst);

                    let amount = parseFloat(res.amount) || 0;

                    let cgst = parseFloat(res.cgst) || 0;
                    let sgst = parseFloat(res.sgst) || 0;
                    let igst = parseFloat(res.igst) || 0;
                    let total = amount + cgst + sgst + igst;

                    $('#sum_grand_total').html(total);
                    setSelectValueByTextOrValue($('#noitem_sales_ledger'), res.sales_ledger);
                    tbody.html(''); // clear table
                }
                if (res.gst_mode === 'custom' && res.custom_gst && res.custom_gst.length) {
                    let iGstLedgers = @json($iGstLedgers);
                    let cGstLedgers = @json($cGstLedgers);
                    let sGstLedgers = @json($sGstLedgers);
                    let html = '';

                    res.custom_gst.forEach(slot => {

                        html += `
                        <tr data-rate="${parseFloat(slot.gst_rate) || 0}">
                            <td>${slot.gst_rate}%</td>

                            <td class="slot-taxable">${slot.taxable}</td>

                            <td>
                                <select class="slot-igst-ledger">
                                    ${buildLedgerOptions(iGstLedgers, mappedGstLedgerId('igst', slot.igst_ledger_id, slot.ledger_id || slot.sales_ledger_id || '', slot.ledger_name || ''))}
                                </select>
                            </td>

                            <td>
                                <input type="number" class="slot-igst-amt" value="${slot.igst_amount || 0}">
                            </td>

                            <td>
                                <select class="slot-cgst-ledger">
                                    ${buildLedgerOptions(cGstLedgers, mappedGstLedgerId('cgst', slot.cgst_ledger_id, slot.ledger_id || slot.sales_ledger_id || '', slot.ledger_name || ''))}
                                </select>
                            </td>

                            <td>
                                <input type="number" class="slot-cgst-amt" value="${slot.cgst_amount || 0}">
                            </td>

                            <td>
                                <select class="slot-sgst-ledger">
                                    ${buildLedgerOptions(sGstLedgers, mappedGstLedgerId('sgst', slot.sgst_ledger_id, slot.ledger_id || slot.sales_ledger_id || '', slot.ledger_name || ''))}
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
                recalcTotals();
                // if (res.gst_mode !== 'custom') {
                //     recalcTotals();
                // }
            },
            error: () => $('#editItemsBody').html('<tr><td colspan="9" class="text-center py-3" style="color:#ef4444;">Failed to load.</td></tr>')
        });
    });  

    function buildLedgerOptions(list, selectedId) {
        let html = '<option value="">Select Ledger</option>';

        list.forEach(l => {
            let selected = (String(l.id) === String(selectedId)) ? 'selected' : '';
            html += `<option value="${l.id}" ${selected}>${l.name}</option>`;
        });

        return html;
    }

    // ═══════ ADD ITEM ROW ═══════
    $('#addItemRow').click(function () {
        // Remove "no items" placeholder if present
        if ($('#editItemsBody tr td[colspan]').length) $('#editItemsBody').empty();
        $('#editItemsBody').append(buildItemRow({}));
        recalcTotals();
    });

    $('#addNoItemRow').click(function () {
        addNoItemRow({ gst: 18 });
        recalcTotals();
    });

    $(document).on('click', '.removeNoItem', function() {
        $(this).closest('tr').remove();

        if (!$('#noItemBody tr').length) {
            addNoItemRow({ gst: 18 });
        }

        recalcTotals();
    });
    

    // ═══════ LIVE RECALC ON INPUT ═══════
    $(document).on('input', '#editItemsBody input', function () {
        recalcItemRow($(this).closest('tr'));
        recalcTotals();
    });

    $(document).on('input change', '.noitem-gst, .noitem-amount, .noitem-ledger', function() {
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
        }
        recalcTotals();
    });

    // $('#edit_is_igst').on('change', function () {
    getIsIgstInput().on('change', function () {
        // 🔥 Recalculate each row GST
        $('#editItemsBody tr').each(function () {
            recalcItemRow($(this));
        });
        // 🔥 Then update totals
        recalcTotals();
    });

    // ── Save (Update) ────────────────────────────────────────────────────
    $('#updateRow').click(function() {
        let items = [];

        if ($('#no_item_section').is(':visible')) {
            items = []; // no items case
            let amount = parseFloat($('#noitem_amount').val()) || 0;
            //let isIGST = $('#edit_is_igst').is(':checked');
            let isIGST = isIgstChecked();

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
            $('#sum_amount').text(amount.toFixed(2));
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));
            $('#sum_grand_total').text(total.toFixed(2));

            // Hidden fields (VERY IMPORTANT)
            $('#edit_amount').val(amount);
            $('#edit_cgst').val(cgst);
            $('#edit_sgst').val(sgst);
            $('#edit_igst').val(igst);
            $('#edit_total_amount').val(total);
        } else {
            $('#editItemsBody tr').each(function() {
                let row = $(this);
                items.push({
                    id: row.find('.item-id').val(),
                    hsn: row.find('.item-hsn').val(),
                    //item_name: row.find('.item-name').val(),
                    item_name: row.find('.item-name option:selected').text(),
                    gst_rate: row.find('.item-gst_rate').val(),
                    quantity: row.find('.item-qty').val(),
                    unit: row.find('.item-unit').val(),
                    rate: row.find('.item-rate').val(),
                    amount: row.find('.item-amount').val(),
                    sgst: row.find('.item-sgst').val(),
                    cgst: row.find('.item-cgst').val(),
                    igst: row.find('.item-igst').val(),
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
            url: "{{ route('sales.update') }}",
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
                sales_ledger: $('#edit_ledger').val(),
                vchType: $('#edit_voucher_type').val(),
                address: $('#edit_address').val(),
                pincode: $('#edit_pincode').val(),
                city: $('#edit_city').val(),
                //is_igst: $('#edit_is_igst').is(':checked') ? 1 : 0,
                is_igst: isIgstChecked() ? 1 : 0,

                amount: $('#edit_amount').val(),
                cgst: $('#edit_cgst').val(),
                sgst: $('#edit_sgst').val(),
                igst: $('#edit_igst').val(),
                total_amount: $('#edit_total_amount').val(),

                Remarks: $('#edit_remarks').val(),

                gst_mode: $('#gst_calc_mode').val(),

                igst_ledger: $('#igst_ledger').val(),
                cgst_ledger: $('#cgst_ledger').val(),
                sgst_ledger: $('#sgst_ledger').val(),

                noitem_amount: $('#noitem_amount').val(),
                noitem_rows: collectNoItemRows(),
                sales_ledger_id: $('#noitem_sales_ledger').val(),
                sales_ledger_name: $('#noitem_sales_ledger option:selected').text(),
                gst_rate: $('#noitem_gst_rate').val(),
                items: items,
                entry_mode: $('#no_item_section').is(':visible') ? 'noitem' : 'item',
                custom_slots: collectCustomSlots()
            },
            success: (res) => {
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
            error: () => alert('Update failed')
        });
    });

    $('#edit_place').on('change', function () {
        let place = $(this).val().toLowerCase();
        let companyState = 'gujarat'; // set dynamically

        if (place === companyState) {
            // $('#edit_is_igst').prop('checked', false);
            setIsIgstChecked(false);
        } else {
            // $('#edit_is_igst').prop('checked', true);
            setIsIgstChecked(true);
        }

        // $('#edit_is_igst').trigger('change');
        getIsIgstInput().trigger('change');
    });

    // let isIGST = $('#edit_is_igst').is(':checked') ? 1 : 0;
    // let mode = $('#gst_calc_mode').val();

    // if (mode === 'standard') {
    //     // Auto calculate based on gst_rate + isIGST
    //     let gstRate = parseFloat(row.find('.item-gst_rate').val()) || 0;
    //     let amount = qty * rate;

    //     let cgst = 0, sgst = 0, igst = 0;

    //     if (gstRate > 0) {
    //         if (isIGST) {
    //             igst = amount * gstRate / 100;
    //         } else {
    //             cgst = amount * (gstRate / 2) / 100;
    //             sgst = amount * (gstRate / 2) / 100;
    //         }
    //     }

    //     row.find('.item-cgst').val(cgst.toFixed(2));
    //     row.find('.item-sgst').val(sgst.toFixed(2));
    //     row.find('.item-igst').val(igst.toFixed(2));
    //     row.find('.item-total').val((amount + cgst + sgst + igst).toFixed(2));
    // }

    function collectCustomSlots() {
        let slots = [];

        $('#customSlotsBody tr').each(function() {
            let row = $(this);

            let rate = parseFloat(row.data('rate')) || 0;

            slots.push({
                rate: rate,
                sales_ledger_id: row.find('.slot_sales_ledger_id').val() || null,

                taxable: parseFloat(
                    row.find('.slot-taxable').text().replace('Taxable: ', '').replace(/,/g, '')
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

        let row = $(`
            <tr>
                <input type="hidden" class="item-id" value="${item.id||''}">
                <td class="td-sr">${srNo}</td>

                <td>
                    <select class="item-name itemSelect">
                        ${buildItemOptions(item.item_name || '')}
                    </select>
                </td>

                <td><input type="text" class="item-hsn" value="${item.hsn||''}"></td>
                <td><input type="number" class="item-gst_rate" value="${item.gst_rate||''}"></td>
                <td><input type="number" class="item-qty" value="${item.quantity||''}"></td>
                <td><input type="text" class="item-unit" value="${item.unit||'NOS'}"></td>
                <td><input type="number" class="item-rate" value="${item.rate||''}"></td>
                <td><input type="number" class="item-amount" value="${item.amount||''}" readonly></td>

                <td>
                    <button type="button" class="removeItemRow receipt-del-btn">✕</button>
                </td>

                <input type="hidden" class="item-sgst"  value="${item.sgst||0}">
                <input type="hidden" class="item-cgst"  value="${item.cgst||0}">
                <input type="hidden" class="item-igst"  value="${item.igst||0}">
                <input type="hidden" class="item-total" value="${item.total_amount||0}">
            </tr>
        `);

        // 🔥 APPLY SELECT2 HERE (correct place)
        row.find('.itemSelect').select2({
            dropdownParent: $('#editModal'),
            width: '100%',
            placeholder: "Search Item..."
        });

        return row;
    }

    // Recalc one item row's GST values based on mode
    function recalcItemRow(row) {
        let qty = parseFloat(row.find('.item-qty').val()) || 0;
        let rate = parseFloat(row.find('.item-rate').val()) || 0;
        let gstRate = parseFloat(row.find('.item-gst_rate').val()) || 0;
        let amount = qty * rate;
        //let isIGST = $('#edit_is_igst').is(':checked');
        let isIGST = isIgstChecked();
        let mode = $('#gst_calc_mode').val();

        let cgst = 0,
            sgst = 0,
            igst = 0;

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
    function recalcTotals() {

        // =========================
        // NO ITEM CASE
        // =========================
        if ($('#no_item_section').is(':visible')) {

            let isIGST = isIgstChecked();
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

            // Update hidden fields
            $('#edit_amount').val(amount);
            $('#edit_cgst').val(cgst.toFixed(2));
            $('#edit_sgst').val(sgst.toFixed(2));
            $('#edit_igst').val(igst.toFixed(2));
            $('#edit_total_amount').val(total.toFixed(2));

            // Update display
            $('#sum_amount').text(amount.toFixed(2));
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));
            $('#sum_grand_total').text(total.toFixed(2));

            // In custom mode (no-item flow), keep custom GST table in sync
            if ($('#gst_calc_mode').val() === 'custom') {
                renderCustomSlots(rateMap, total);
            }
            return;
        }

        let totalAmount = 0;
        let totalCGST = 0;
        let totalSGST = 0;
        let totalIGST = 0;

        let rateMap = {}; // 🔥 IMPORTANT

        $('#editItemsBody tr').each(function() {

            let row = $(this);

            let amount = parseFloat(row.find('.item-amount').val()) || 0;
            let cgst = parseFloat(row.find('.item-cgst').val()) || 0;
            let sgst = parseFloat(row.find('.item-sgst').val()) || 0;
            let igst = parseFloat(row.find('.item-igst').val()) || 0;
            let gstRate = parseFloat(row.find('.item-gst_rate').val()) || 0;

            totalAmount += amount;
            totalCGST += cgst;
            totalSGST += sgst;
            totalIGST += igst;

            // 🔥 BUILD RATE MAP
            if (!rateMap[gstRate]) {
                rateMap[gstRate] = {
                    amt: 0,
                    igst: 0,
                    cgst: 0,
                    sgst: 0
                };
            }

            rateMap[gstRate].amt += amount;
            rateMap[gstRate].igst += igst;
            rateMap[gstRate].cgst += cgst;
            rateMap[gstRate].sgst += sgst;
        });

        let grandTotal = totalAmount + totalCGST + totalSGST + totalIGST;

        // UI update
        $('#sum_amount').text(totalAmount.toFixed(2));
        $('#sum_cgst').text(totalCGST.toFixed(2));
        $('#sum_sgst').text(totalSGST.toFixed(2));
        $('#sum_igst').text(totalIGST.toFixed(2));
        $('#sum_grand_total').text(grandTotal.toFixed(2));

        // hidden
        $('#edit_amount').val(totalAmount);
        $('#edit_cgst').val(totalCGST);
        $('#edit_sgst').val(totalSGST);
        $('#edit_igst').val(totalIGST);
        $('#edit_total_amount').val(grandTotal);

        // =========================
        // 🔥 ADD THIS (MAIN FIX)
        // =========================
        let gstMode = $('#gst_calc_mode').val();

        if (gstMode === 'custom') {
            renderCustomSlots(rateMap, grandTotal);
        }

        let totalCgst = 0;
        let totalSgst = 0;
        let totalIgst = 0;
        $('#customSlotsBody tr').each(function () {
            totalCgst += parseFloat(
                $(this).find('.cgst-amount').val() || 0
            );
            totalSgst += parseFloat(
                $(this).find('.sgst-amount').val() || 0
            );
            totalIgst += parseFloat(
                $(this).find('.igst-amount').val() || 0
            );
        });

        $('#sum_cgst').text(totalCgst.toFixed(2));
        $('#sum_sgst').text(totalSgst.toFixed(2));
        $('#sum_igst').text(totalIgst.toFixed(2));
    }

    $(document).on('input', '#noitem_amount', function () {
        recalcTotals();
    });

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

        // Build the slot table body
        let slotHtml = '';
        let customSgst=0, customCgst=0, customIgst=0;

        allRates.forEach(function(rate) {
            rate = (parseFloat(rate) || 0).toString();
            let data   = rateMap[rate] || { amt:0, igst:0, cgst:0, sgst:0 };
            let halfR  = parseFloat(rate) / 2;
            // Auto-compute: use sum from item recalc (standard) or allow manual override
            let igstAmt = data.igst;
            let cgstAmt = data.cgst;
            let sgstAmt = data.sgst;
            customIgst += igstAmt;
            customCgst += cgstAmt;
            customSgst += sgstAmt;

            let isZero = data.amt === 0;

            // Build ledger options
            let iOpts = iGstLedgers.map(l => {
                let sel = String(l.id) === String(mappedGstLedgerId('igst')) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');
            let cOpts = cGstLedgers.map(l => {
                let sel = String(l.id) === String(mappedGstLedgerId('cgst')) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');
            let sOpts = sGstLedgers.map(l => {
                let sel = String(l.id) === String(mappedGstLedgerId('sgst')) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');

            slotHtml += `<tr class="${isZero ? 'zero-row' : ''}" data-rate="${rate}">
                <td><span class="rate-badge"><span class="slot-rate"></span>${rate}%</span></td>
                <td><strong>${fmt(data.amt)}</strong></td>
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
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(customIgst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(customCgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(customSgst)}</span></div>`;
        $('#custom_tax_rows').html(customSummaryHtml);

        // Update hidden fields for save
        $('#edit_igst').val(customIgst.toFixed(2));
        $('#edit_cgst').val(customCgst.toFixed(2));
        $('#edit_sgst').val(customSgst.toFixed(2));
        let total = parseFloat($('#edit_amount').val()) + customIgst + customCgst + customSgst;
        $('#edit_total_amount').val(total.toFixed(2));
        $('#sum_grand_total').text(fmt(total));
    }

    $(document).on('input', '#noitem_amount', function() {
        recalcTotals();
    });

    $(document).on('input', '#noitem_gst_rate', function() {
        recalcTotals();
    });

    function renderCustomSlots(rateMap, grandTotal) {
        let sGstLedgers = @json($sGstLedgers ?? []);
        let cGstLedgers = @json($cGstLedgers ?? []);
        let iGstLedgers = @json($iGstLedgers ?? []);

        // 🔥 PRESERVE EXISTING LEDGER SELECTIONS
        let existingSelections = {};
        $('#customSlotsBody tr').each(function() {
            let rate = (parseFloat($(this).data('rate')) || 0).toString();
            let salesLedgerId = $(this).find('.slot_sales_ledger_id').val() || '';
            let key = $(this).data('slot-key') || `${rate}|${salesLedgerId}`;

            existingSelections[key] = {
                igst_ledger: $(this).find('.slot-igst-ledger').val(),
                cgst_ledger: $(this).find('.slot-cgst-ledger').val(),
                // sgst_ledger: $(this).find('.slot-sgst-ledger').val(),
                // igst_amt: $(this).find('.slot-igst-amt').val(),
                // cgst_amt: $(this).find('.slot-cgst-amt').val(),
                // sgst_amt: $(this).find('.slot-sgst-amt').val(),
                sgst_ledger: $(this).find('.slot-sgst-ledger').val()
            };
        });

        let allRates = Object.keys(rateMap).filter(r => {
            let data = rateMap[r] || {};
            return (parseFloat(data.amt) || 0) !== 0 ||
                (parseFloat(data.igst) || 0) !== 0 ||
                (parseFloat(data.cgst) || 0) !== 0 ||
                (parseFloat(data.sgst) || 0) !== 0;
        });

        // Build the slot table body
        let slotHtml = '';
        let customSgst = 0,
            customCgst = 0,
            customIgst = 0;

        allRates.forEach(function(mapKey) {
            let data = rateMap[mapKey] || {
                amt: 0,
                igst: 0,
                cgst: 0,
                sgst: 0,
                ledgerId: $('#noitem_sales_ledger').val() || '',
                ledgerName: $('#noitem_sales_ledger option:selected').text() || ''
            };
            let rate = data.rate ?? mapKey;
            rate = (parseFloat(rate) || 0).toString();
            let halfR = parseFloat(rate) / 2;
            
            // Keep user-selected ledgers, but always refresh tax amounts from latest item data

            let existing = existingSelections[data.slotKey || mapKey] ||
                existingSelections[`${rate}|${data.ledgerId || ''}`] ||
                existingSelections[rate] ||
                {};
            // let igstAmt = existing.igst_amt !== undefined ? existing.igst_amt : data.igst;
            // let cgstAmt = existing.cgst_amt !== undefined ? existing.cgst_amt : data.cgst;
            // let sgstAmt = existing.sgst_amt !== undefined ? existing.sgst_amt : data.sgst;
            
            // customIgst += parseFloat(igstAmt) || 0;
            // customCgst += parseFloat(cgstAmt) || 0;
            // customSgst += parseFloat(sgstAmt) || 0;
            
            let igstAmt = parseFloat(data.igst) || 0;
            let cgstAmt = parseFloat(data.cgst) || 0;
            let sgstAmt = parseFloat(data.sgst) || 0;

            customIgst += igstAmt;
            customCgst += cgstAmt;
            customSgst += sgstAmt;

            let isZero = data.amt === 0;

            // Build ledger options WITH EXISTING SELECTION
            let iOpts = iGstLedgers.map(l => {
                let sel = (String(l.id) === String(mappedGstLedgerId('igst', existing.igst_ledger, data.ledgerId || '', data.ledgerName || ''))) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');
            let cOpts = cGstLedgers.map(l => {
                let sel = (String(l.id) === String(mappedGstLedgerId('cgst', existing.cgst_ledger, data.ledgerId || '', data.ledgerName || ''))) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');
            let sOpts = sGstLedgers.map(l => {
                let sel = (String(l.id) === String(mappedGstLedgerId('sgst', existing.sgst_ledger, data.ledgerId || '', data.ledgerName || ''))) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');

            slotHtml += `<tr class="${isZero ? 'zero-row' : ''}" data-slot-key="${data.slotKey || mapKey}" data-rate="${rate}">
                <td><span class="rate-badge"><span class="slot-rate"></span>${rate}%</span></td>
                <td style="color: black;"><strong>${fmt(data.amt)}</strong><input type="hidden" class="slot_sales_ledger_id" value="${data.ledgerId || ''}"></td>
                <td><select class="slot-igst-ledger" data-rate="${rate}"><option value="">— Ledger —</option>${iOpts}</select></td>
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
        $('#edit_total_amount').val(total.toFixed(2));
        $('#sum_grand_total').text(fmt(total));
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
        $('#edit_total_amount').val(total.toFixed(2));
        $('#sum_grand_total').text(fmt(total));
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
        $('#editModal').removeClass('view-only');
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
        // let isIGST = $('#edit_is_igst').is(':checked');
        let isIGST = isIgstChecked();

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

    //$('#edit_is_igst').on('change', function () {
    getIsIgstInput().on('change', function () {
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
        let html = `<option value="">Select Item</option>`;
        ITEM_MASTER.forEach(item => {
            let name = item.strItemName;
            // ✅ ESCAPE QUOTES
            let safeValue = name.replace(/"/g, '&quot;');
            let isSelected =
                name.trim().toLowerCase() === String(selected).trim().toLowerCase()
                    ? 'selected'
                    : '';
            html += `<option value="${item.iStockIdtemId}" data-name="${safeValue}" ${isSelected}>${name}</option>`;
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

</script>
@endsection
