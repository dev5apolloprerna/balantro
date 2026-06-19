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
                    Save
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
                            <option value="ledger">Ledger</option>
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
                            <th class="px-3 py-2">PARTICULARS</th>
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
                            <td class="px-3 py-2">
                                <select name="ledger[{{$row->id}}]" class="ledgerSelect inputCell">
                                    <option value="">Select Ledger</option>
                                    @foreach($ledgers as $ledger)
                                    <option value="{{$ledger->name}}"
                                        {{ $row->sales_ledger==$ledger->name?'selected':'' }}>
                                        {{$ledger->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2 text-right">
                                {{ number_format($row->total_amount,2) }}
                            </td>
                            <td class="px-3 py-2">
                                <span class="text-yellow-400">
                                    {{$row->status}}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <button
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

<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-neutral-900 p-6 rounded-lg w-[650px]">
        <h3 class="text-white text-lg mb-4">Edit Purchase</h3>
        <input type="hidden" id="edit_id">
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <label class="text-gray-300">Date</label>
                <input type="date" id="edit_date" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">Invoice No</label>
                <input type="text" id="edit_invoice" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">Voucher Type</label>
                <select id="edit_voucher_type" class="inputCell">
                    @foreach($vchTypes as $vchType)
                    <option value="{{$vchType}}">{{$vchType}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-gray-300">Party Name</label>
                <select id="edit_party" class="inputCell">
                    @foreach($ledgers as $ledger)
                    <option value="{{$ledger->name}}">{{$ledger->name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-gray-300">GST No</label>
                <input type="text" id="edit_gst" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">Place Of Supply</label>
                <select id="edit_place" class="inputCell">
                    @foreach($states as $state)
                    <option value="{{$state}}">{{$state}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-gray-300">Ledger</label>
                <select id="edit_ledger" class="inputCell">
                    @foreach($ledgers as $ledger)
                    <option value="{{$ledger->name}}">{{$ledger->name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-gray-300">Item Name</label>
                <input type="text" id="edit_item" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">Quantity</label>
                <input type="number" id="edit_qty" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">Rate</label>
                <input type="number" id="edit_rate" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">Amount</label>
                <input type="number" id="edit_amount" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">CGST</label>
                <input type="number" id="edit_cgst" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">SGST</label>
                <input type="number" id="edit_sgst" class="inputCell">
            </div>
            <div>
                <label class="text-gray-300">IGST</label>
                <input type="number" id="edit_igst" class="inputCell">
            </div>
        </div>
        <div class="flex justify-end mt-5 gap-2">
            <button id="closeModal" class="px-3 py-1 bg-gray-600 text-white rounded">
                Cancel
            </button>
            <button id="updateRow" class="px-3 py-1 bg-blue-600 text-white rounded">
                Update
            </button>
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

<style>
    
    .select2-container--default .select2-selection--single {
        background: #020617;
        border: 1px solid #374151;
        height: 28px;
        color: white;
    }

    .select2-dropdown {
        background: #020617;
        color: white;
    }

    .dark .select2-container--default .select2-selection--single {
        background: #020617;
        border: 1px solid #374151;
        color: white;
    }

    .dark .select2-dropdown {
        background: #020617;
        color: white;
    }

    .inputCell{
        background:white;
        border:1px solid #d1d5db;
        color:#111827;
        padding:6px 8px;
        font-size:12px;
        width:100%;
        border-radius:4px;
    }

    .dark .inputCell{
        background:#020617;
        border:1px solid #374151;
        color:white;
    }
    /* LIGHT MODE */   
    .select2-container--default .select2-selection--single {
        background: white;
        border: 1px solid #d1d5db;
        color: #111827;
    }

    .select2-dropdown {
        background: white;
        color: #111827;
    }

    .dark #salesTable tbody tr:hover {
        background: #1f2937;
    }

    #salesTable tbody tr:hover {
        background: #f3f4f6;
    }

    .select2-container--default .select2-selection--single{
        background:white;
        border:1px solid #d1d5db;
        height:28px;
    }

    .dark .select2-container--default .select2-selection--single{
        background:#020617;
        border:1px solid #374151;
        color:white;
    }

    .select2-dropdown{
        background:white;
        color:#111827;
    }

    .dark .select2-dropdown{
        background:#020617;
        color:white;
    }
    #salesTable tbody tr:hover{
        background:#f3f4f6;
    }

    .dark #salesTable tbody tr:hover{
        background:#1f2937;
    }
    .searchInput{
        background:white;
        border:1px solid #d1d5db;
        color:#111827;
        width:100%;
        padding:4px;
        font-size:12px;
        border-radius:4px;
    }

    .dark .searchInput{
        background:#020617;
        border:1px solid #374151;
        color:white;
    }

    .modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 999;
        background: rgba(0, 0, 0, 0.6);
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }


    /* DARK THEME FIX */
    /* LIGHT MODE (default) */
    /* MODAL BASE */
    .modal-content {
        width: 650px;
        max-height: 85vh;
        background: #ffffff;
        color: #111827;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* DARK MODE */
    .dark .modal-content {
        background: #1e293b;
        color: #e2e8f0;
    }
  

    .modal-footer {
        padding: 12px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* DARK BORDER */
    .dark .modal-header,
    .dark .modal-footer {
        border-color: #334155;
    }

    /* BODY SCROLL */
    .modal-body {
        padding: 16px;
        overflow-y: auto;
        flex: 1;
    }

    /* INPUTS - LIGHT */
    .modal-content input,
    .modal-content select {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #111827;
        font-size: 13px;
    }

    /* INPUTS - DARK */
    .dark .modal-content input,
    .dark .modal-content select {
        background: #020617;
        border: 1px solid #334155;
        color: #e2e8f0;
    }

    /* FOCUS */
    .modal-content input:focus,
    .modal-content select:focus {
        border-color: #3b82f6;
        outline: none;
    }

    /* LABEL */
    .form-group label {
        font-size: 12px;
        margin-bottom: 4px;
        display: block;
    }

    /* DARK LABEL */
    .dark .form-group label {
        color: #cbd5f5;
    }

    /* SPACING */
    .form-group {
        margin-bottom: 14px;
    }

    /* BUTTONS */
    .submit-btn {
        background: #3b82f6;
        padding: 8px 16px;
        border-radius: 6px;
        color: white;
    }

    .btn-cancel {
        background: #374151;
        padding: 8px 16px;
        border-radius: 6px;
        color: white;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #64748b;
        border-radius: 10px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    /* DARK MODE BORDER */
    .dark .modal-header {
        border-color: #334155;
    }

    /* TITLE */
    .modal-header h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    /* CLOSE BUTTON */
    .close-btn {
        font-size: 18px;
        background: transparent;
        border: none;
        cursor: pointer;
        color: #6b7280;
    }

    /* HOVER */
    .close-btn:hover {
        color: #ef4444;
    }

    /* DARK MODE */
    .dark .close-btn {
        color: #94a3b8;
    }

    .dark .close-btn:hover {
        color: #f87171;
    }
</style>

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
                showToast(response.message,'success');

                closeLedgerModal();
                location.reload();
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
            rows = $('#salesTable tbody tr');
        }
        rows.each(function() {
            let row = $(this);
            if (column === 'party') {
                row.find('input[name^="party_name"]').val(value);
            }
            if (column === 'ledger') {
                row.find('select[name^="ledger"]').val(value).trigger('change');
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
            url: "{{ route('sales.save') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                showToast('Saved Successfully','success');
                location.reload(); // reload page and refresh table
            },
            error: function(xhr) {
                showToast('Error saving data','error');
            }
        });
    });

    $(document).on('click', '.deleteRow', function() {
        let id = $(this).data('id');
        if (!confirm('Delete this row?')) return;
        $.ajax({
            url: "{{ route('sales.delete', ':id') }}".replace(':id', id),
            type: "DELETE",
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

    $(document).on('click', '.editRow', function() {

        $('#editModal').removeClass('hidden');
        $('#edit_id').val($(this).data('id'));
        $('#edit_invoice').val($(this).data('invoice'));
        $('#edit_date').val($(this).data('date'));
        $('#edit_party').val($(this).data('party'));
        $('#edit_place').val($(this).data('place'));
        $('#edit_ledger').val($(this).data('ledger'));
        $('#edit_item').val($(this).data('item'));
        $('#edit_qty').val($(this).data('qty'));
        $('#edit_rate').val($(this).data('rate'));
        $('#edit_cgst').val($(this).data('cgst'));
        $('#edit_sgst').val($(this).data('sgst'));
        $('#edit_igst').val($(this).data('igst'));
        $('#edit_amount').val($(this).data('amount'));
        $('#edit_gst').val($(this).data('gst_no'));
        $('#edit_voucher_type').val($(this).data('vchtype'));

    });
    $('#closeModal').click(function() {
        $('#editModal').addClass('hidden');
    });

    $('#updateRow').click(function() {
        let id = $('#edit_id').val();
        $.ajax({
            url: "{{ route('sales.update') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: $('#edit_id').val(),
                invoice_no: $('#edit_invoice').val(),
                date: $('#edit_date').val(),
                party_name: $('#edit_party').val(),
                gst_no: $('#edit_gst').val(),
                place_of_supply: $('#edit_place').val(),
                sales_ledger: $('#edit_ledger').val(),
                item_name: $('#edit_item').val(),
                quantity: $('#edit_qty').val(),
                rate: $('#edit_rate').val(),
                cgst: $('#edit_cgst').val(),
                sgst: $('#edit_sgst').val(),
                igst: $('#edit_igst').val(),
                vchType: $('#edit_voucher_type').val(),
                total_amount: $('#edit_amount').val()
            },
            success: function() {
                showToast('Updated Successfully','success');
                location.reload();
            }
        });
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

</script>
@endsection