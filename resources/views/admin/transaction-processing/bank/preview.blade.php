@extends('layouts.super_admin')
@section('content')
@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bg-red-100 text-red-800 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif
<div class="w-full px-4 h-[calc(100vh-110px)] min-h-0 flex flex-col">
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow border border-gray-200 dark:border-neutral-700 flex flex-col flex-1 min-h-0">
        <!-- HEADER -->
        <div class="flex justify-between items-center px-5 py-3 border-b border-neutral-700 shrink-0">
            <!-- <div class="flex items-center gap-3">
                <h2 class="text-white text-lg font-semibold">
                    Transactions
                </h2>
                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                    {{ $rows->count() }}
                </span>
            </div> -->
            <div class="flex items-center gap-3">
                <a href="{{ route('transaction_processing.processing_bank') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 text-lg">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>

                <h2 class="text-gray-900 dark:text-white text-lg font-semibold">
                    Bank Transactions
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
                <button onclick="openConfigModal()" class="border border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-gray-300 px-3 py-1 rounded text-sm">
                    Settings
                </button>
                <button  onclick="openLedgerModal()"
                    class="border border-blue-500 text-blue-400 px-3 py-1 rounded text-sm">
                    + Create Ledger
                </button>
                <button type="button"
                    id="saveBtn"
                    class="bg-blue-600 text-white px-4 py-1 rounded text-sm">
                    Save
                </button>
            </div>
        </div>
        <!-- FILTER BAR -->
        <div class="flex justify-between px-5 py-3 border-b border-neutral-700 text-sm shrink-0">
            <div class="flex gap-6">
                <div>
                    <label class="text-gray-700 dark:text-gray-300 text-sm">Transaction Type</label>
                    <select id="typeFilter" class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-800 dark:text-white rounded px-3 py-1 mt-1">
                        <option value="">Select Type</option>
                        @foreach($vchTypes as $vchType)
                            <option value="{{ $vchType }}">{{ $vchType }}</option>
                        @endforeach
                    </select>

                </div>
                <!-- <div>
                    <label class="text-sm">Amount</label>
                    <input type="text" id="amountFilter" 
                        class="border px-2 py-1 rounded mt-1" 
                        placeholder="Search Amount">
                </div> -->
                <div>
                    <label class="text-sm">Description</label>
                    <input type="text" id="descFilter" 
                        class="border px-2 py-1 rounded mt-1" 
                        placeholder="Search Description">
                </div>
                <div>
                    <label class="text-gray-700 dark:text-gray-300 text-sm">Ledger</label>
                    <select id="bulkLedger"
                        class="bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 text-gray-800 dark:text-white rounded px-3 py-1 mt-1">
                        <option value="">Select Ledger</option>

                        @foreach($ledgers as $ledger)
                        <option value="{{ $ledger->name }}">
                            {{ $ledger->name }}
                        </option>
                        @endforeach

                    </select>
                </div>
            </div>
            <div class="flex gap-4 mt-2 text-gray-700 dark:text-gray-300">
                <label>
                    <input type="checkbox" class="generalFilter" value="synced">Hide Tally Synced Records
                </label>
                <label>
                    <input type="checkbox" class="generalFilter" value="saved"> Saved Records
                </label>
                <label>
                    <input type="checkbox" class="generalFilter" value="blank"> Blank Records
                </label>
                <label>
                    <input type="checkbox" class="generalFilter" value="failed"> Failed
                </label>
            </div>
        </div>

        <form id="bankForm" class="flex-1 min-h-0">
            @csrf
            
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 group-block">
                <table id="bankTable" class="min-w-[900px] w-full text-sm text-left text-gray-600 dark:text-gray-200">
                    <!-- Table Header -->
                    <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 text-xs uppercase sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="px-3 py-2">Sr</th>
                            <th class="px-3 py-2">Transfer Date</th>
                            <th class="px-3 py-2">Value Date</th>
                            <th class="px-3 py-2">Description</th>
                            <th class="px-3 py-2">Type</th>
                            <th class="px-3 py-2">Amount</th>
                            <th class="px-3 py-2">Ledger</th>
                            <th class="px-3 py-2">Actions</th>
                        </tr>
                        <!-- SEARCH ROW -->
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
                                <!-- <input class="searchInput"> -->
                                <div class="flex gap-1">
                                    <input type="number" class="amountFrom searchInput" placeholder="From">
                                    <input type="number" class="amountTo searchInput" placeholder="To">
                                </div>
                            </th>
                            <th>
                                <input class="searchInput">
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                        @foreach($rows as $index=>$row)
                        <tr data-cheque="{{ $row->cheque_no }}"
                            data-ref="{{ $row->ref_no }}"
                            data-cost="{{ $row->cost_center }}"
                            data-pending-issues='@json($row->pending_issues ?? [])'  class="group border-b border-gray-200 dark:border-neutral-700 transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] [&>*]:group-hover:text-black [&_*]:group-hover:text-black {{ $row->is_suspense == 1 ? 'opacity-50 pointer-events-none' : '' }}">
                            <td class="px-3 py-2">
                                <input type="checkbox"
                                    name="selected[]"
                                    value="{{$row->id}}" {{ $row->is_suspense == 1 ? 'disabled' : '' }}>
                            </td>
                            <td class="px-3 py-2">
                                {{ $index+1 }}
                            </td>
                            <td class="px-3 py-2">
                                <input type="date"
                                    name="txn_date[{{$row->id}}]"  {{ $row->is_suspense == 1 ? 'disabled' : '' }}
                                    value="{{ \Carbon\Carbon::parse($row->txn_date)->format('Y-m-d') }}">
                            </td>
                            <td class="px-3 py-2">
                                <input type="date"
                                    name="value_date[{{$row->id}}]" {{ $row->is_suspense == 1 ? 'disabled' : '' }}
                                    value="{{ \Carbon\Carbon::parse($row->value_date)->format('Y-m-d') }}">
                            </td>
                            <td class="px-3 py-2">
                                <input type="text"
                                    name="narration[{{$row->id}}]" {{ $row->is_suspense == 1 ? 'disabled' : '' }}
                                    value="{{$row->narration}}"
                                    class="inputCell">
                            </td>
                            <td class="px-3 py-2">
                                @php($selectedVchType = trim((string) ($row->vch_type ?: ($row->debit > 0 ? 'Payment' : 'Receipt'))))
                                <select name="type[{{$row->id}}]" class="inputCell" {{ $row->is_suspense == 1 ? 'disabled' : '' }}>
                                    @foreach($vchTypes as $vchType)
                                        <option value="{{ $vchType }}" {{ strcasecmp($selectedVchType, $vchType) === 0 ? 'selected' : '' }}>
                                            {{ $vchType }}
                                        </option>
                                    @endforeach
                                    @if($selectedVchType !== '' && !collect($vchTypes)->contains(fn ($vchType) => strcasecmp($selectedVchType, $vchType) === 0))
                                        <option value="{{ $selectedVchType }}" selected>
                                            {{ $selectedVchType }}
                                        </option>
                                    @endif
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                @if($row->debit>0)
                                <span class="text-red-400">
                                    <strong>{{number_format($row->debit,2)}}</strong>
                                </span>
                                @endif
                                @if($row->credit>0)
                                <span class="text-green-400">
                                    <strong>{{number_format($row->credit,2)}}</strong>
                                </span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @php($selectedLedgerName = trim((string) ($row->ledger_name ?? '')))
                                <select name="ledger[{{$row->id}}]" {{ $row->is_suspense == 1 ? 'disabled' : '' }} class="ledgerSelect inputCell" data-selected="{{ $selectedLedgerName }}">
                                    <option value="{{ $selectedLedgerName }}" selected>{{ $selectedLedgerName ?: 'Select Ledger' }}</option>
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    @if($row->is_suspense == 1)
                                        <span class="text-yellow-400 text-xs">Suspense</span>
                                    @else
                                        <button type="button"
                                            class="text-yellow-400 suspenseBtn"  {{ $row->is_suspense == 1 ? 'disabled' : '' }}
                                            data-id="{{$row->id}}">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
                                    @endif
                                    @if($row->resolution_remark)
                                        <button type="button"
                                            class="text-blue-400 viewRemarkBtn"  {{ $row->is_suspense == 1 ? 'disabled' : '' }}
                                            data-remark="{{ $row->resolution_remark }}">
                                            <i class="fas fa-eye action-icon"></i>
                                        </button>
                                    @endif
                                    <button type="button"
                                        class="text-green-500 saveRowBtn"  {{ $row->is_suspense == 1 ? 'disabled' : '' }}
                                        data-id="{{$row->id}}">
                                        <i class="fa-solid fa-check action-icon"></i>
                                    </button>
                                    <button type="button"
                                        class="text-red-400 deleteBtn"  {{ $row->is_suspense == 1 ? 'disabled' : '' }}
                                        data-id="{{$row->id}}">
                                        <i class="fa-solid fa-trash action-icon"></i>
                                    </button>
                                </div>
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

<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-neutral-900 w-[520px] rounded-lg shadow-lg p-6">
        <h2 class="text-lg text-white mb-4">Edit Transaction</h2>
        <div id="pendingIssueAlert" class="pending-issue-alert" style="display:none;">
            <div class="pending-issue-title">
                <i class="fa-solid fa-triangle-exclamation"></i> This bank transaction is still pending
            </div>
            <ul id="pendingIssueList" class="pending-issue-list"></ul>
        </div>
        <form id="editForm">
            @csrf
            <input type="hidden" id="edit_id">
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Transfer Date</label>
                <input type="date" id="edit_txn_date" class="inputCell">
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Value Date</label>
                <input type="date" id="edit_value_date" class="inputCell">
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Description</label>
                <input type="text" id="edit_narration" class="inputCell">
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Type</label>
                <select id="edit_type" class="inputCell">
                    <option value="Payment">Payment</option>
                    <option value="Receipt">Receipt</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Amount</label>
                <input type="number" id="edit_amount" class="inputCell">
            </div>
            <div class="mb-3">
                <label class="text-gray-300 text-sm">Ledger</label>
                <select id="edit_ledger" class="inputCell">
                    <option value="">Select Ledger</option>
                    @foreach($ledgers as $ledger)
                    <option value="{{$ledger->name}}">
                        {{$ledger->name}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" id="closeModal" class="px-4 py-1 bg-gray-600 text-white rounded">
                    Cancel
                </button>
                <button type="button" id="updateBtn" class="px-4 py-1 bg-blue-600 text-white rounded">
                    Update
                </button>
            </div>
        </form>
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
                <input type="hidden" name="ledger_action" id="ledger_action" value="submit">
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
                        <select id="State" name="State" class="inputCell">
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
            <!-- <button type="submit" form="ledgerForm" class="submit-btn">Save Ledger</button> -->
            <button type="button" id="ledgerSaveBtn" class="submit-btn ledger-save-btn">Save</button>
            <button type="button" id="ledgerSubmitBtn" class="submit-btn ledger-submit-btn">Submit</button>
        </div>
    </div>
</div>

<div id="configModal" class="modal" style="display: none;">
    <div class="config-box">

        <!-- HEADER -->
        <div class="config-header">
            <h3>Configuration</h3>
            <button onclick="closeConfigModal()">✕</button>
        </div>

        <!-- BODY -->
        <div class="config-body">
            <!-- <div class="config-item">
                <input type="checkbox" class="configCheck" value="bank_allocation" id="bank_allocation">
                <label for="bank_allocation">Bank Allocation</label>
            </div> -->
            <div class="config-item">
                <input type="checkbox" class="configCheck" value="cheque_no" id="cheque_no">
                <label for="cheque_no">Cheque / Instrument No</label>
            </div>
            <div class="config-item">
                <input type="checkbox" class="configCheck" value="reference_no" id="reference_no">
                <label for="reference_no">Supplier Reference</label>
            </div>
            <div class="config-item">
                <input type="checkbox" class="configCheck" value="cost_center" id="cost_center">
                <label for="cost_center">Cost Centre</label>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="config-footer">
            <button class="btn-cancel" onclick="closeConfigModal()">Cancel</button>
            <button class="btn-ok" onclick="applyConfig()">OK</button>
        </div>
    </div>
</div>

@include('admin.transaction-processing.bank.suspense_modal')


<style>
    /* =========================
    GLOBAL RESET
    ========================= */
    html,
    body {
        height: 100%;
        margin: 0;
        overflow: hidden;
        font-family: system-ui;
    }

    /* =========================
    PAGE LAYOUT
    ========================= */
    .page-wrapper {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .card-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* =========================
    FIXED HEADER + FILTER
    ========================= */
    .header-fixed {
        flex-shrink: 0;
    }

    .filter-fixed {
        flex-shrink: 0;
    }

    /* =========================
    TABLE CONTAINER (ONLY SCROLL)
    ========================= */
    .table-container {
        height: calc(100vh - 180px);
        /* 🔥 adjust if needed */
        overflow-y: auto;
        /* overflow-x: hidden; */
        overflow-x: auto;
    }

    /* =========================
    TABLE STYLE
    ========================= */
    #bankTable {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        /* 🔥 IMPORTANT */
        font-size: 11px;
    }

    /* HEADER */
    #bankTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        padding: 8px;
    }

    /* LIGHT MODE */
    #bankTable thead th {
        background: #f3f4f6; /* gray-100 */
        color: #374151;
    }

    /* DARK MODE */
    .dark #bankTable thead th {
        background: #1f2937; /* neutral-800 */
        color: #d1d5db;
    }

    /* CELLS */
    #bankTable th,
    #bankTable td {
        padding: 6px;
        white-space: normal;
        /* ✅ wrap text */
        border-bottom: 1px solid #374151;
    }

    #bankTable td {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* #bankTable th,
    #bankTable td {
        max-width: 150px;
    } */

    #bankTable td input,
    #bankTable td select {
        width: 100%;
    }

    /* INPUTS */
    .inputCell {
        width: 100%;
        padding: 4px 6px;
        font-size: 11px;
        border-radius: 4px;
        border: 1px solid #374151;
        background: #020617;
        color: #e2e8f0;
    }

    /* LIGHT MODE */
    .inputCell {
        background: #fff;
        color: #000;
        border: 1px solid #ccc;
    }

    /* =========================
    DYNAMIC COLUMN FIX
    ========================= */
    .dynamic-col {
        max-width: 150px;
    }

    .dynamic-col input {
        width: 100%;
    }

    /* =========================
    COLUMN WIDTH (OPTIMIZED)
    ========================= */
    /* CHECKBOX */
    #bankTable th:nth-child(1),
    #bankTable td:nth-child(1) {
        width: 30px;
        min-width: 30px;
        max-width: 30px;
        text-align: center;
    }

    /* SR NO */
    #bankTable th:nth-child(2),
    #bankTable td:nth-child(2) {
        width: 40px;
        min-width: 40px;
        max-width: 40px;
        text-align: center;
    }

    #bankTable th:nth-child(3),
    #bankTable td:nth-child(3),
    #bankTable th:nth-child(4),
    #bankTable td:nth-child(4) {
        min-width: 110px;
    }

    #bankTable th:nth-child(5),
    #bankTable td:nth-child(5) {
        min-width: 220px;
    }

    #bankTable th:nth-child(6),
    #bankTable td:nth-child(6) {
        min-width: 120px;
    }

    #bankTable th:nth-child(7),
    #bankTable td:nth-child(7) {
        min-width: 160px !important;
    }

    #bankTable th:nth-child(8),
    #bankTable td:nth-child(8) {
        min-width: 180px;
    }

    #bankTable th:nth-child(9),
    #bankTable td:nth-child(9) {
        min-width: 100px;
    }

    /* =========================
    SCROLLBAR CLEAN
    ========================= */
    .table-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #64748b;
        border-radius: 10px;
    }

    .modal {
        display: none;
        position: fixed;
        /* 🔥 FIX */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        /* 🔥 ABOVE EVERYTHING */
        align-items: center;
        justify-content: center;

        /* inset: 0; */
        /* display: flex; */
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: #111827;
        width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        border-radius: 10px;
        padding: 20px;
    }

    /* =========================
    CONFIG MODAL DESIGN
    ========================= */

    .config-box {
        background: #111827;
        width: 400px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.2s ease-in-out;
    }

    /* HEADER */
    .config-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background: #1f2937;
        border-bottom: 1px solid #374151;
    }

    .config-header h3 {
        color: #fff;
        font-size: 16px;
    }

    .config-header button {
        background: none;
        border: none;
        color: #ccc;
        font-size: 18px;
        cursor: pointer;
    }

    /* BODY */
    .config-body {
        padding: 15px;
    }

    .config-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        color: #e5e7eb;
    }

    /* FOOTER */
    .config-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 12px 15px;
        border-top: 1px solid #374151;
    }

    /* BUTTONS */
    .btn-cancel {
        background: #374151;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
    }

    .btn-ok {
        background: #2563eb;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
    }

    /* ANIMATION */
    @keyframes fadeIn {
        from {
            transform: scale(0.95);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .config-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #2563eb;
    }

    .amount-filter {
        display: flex;
        gap: 4px;
    }

    .amount-filter input {
        width: 50%;
        min-width: 60px;
        padding: 3px;
        font-size: 10px;
    }

    /* 🔥 SELECT2 DROPDOWN FIX */
    .select2-container--default .select2-results__option {
        background-color: #1f2937 !important;
        color: #e5e7eb !important;
    }

    /* hover */
    .select2-container--default .select2-results__option--highlighted {
        background-color: #2563eb !important;
        color: #fff !important;
    }

    /* selected */
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #374151 !important;
        color: #fff !important;
    }

    /* search box */
    .select2-container--default .select2-search--dropdown .select2-search__field {
        background: #111827 !important;
        color: #fff !important;
        border: 1px solid #374151 !important;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .animate-fadeIn {
        animation: fadeIn 0.2s ease-in-out;
    }

    .pending-issue-alert { margin:0 0 12px; padding:8px 10px; border:1px solid #fca5a5; border-left:4px solid #dc2626; border-radius:6px; background:#fef2f2; color:#991b1b; font-size:12px; }
    .pending-issue-title { font-weight:700; margin-bottom:4px; }
    .pending-issue-list { margin:0; padding-left:18px; }
    #editModal .pending-field-error,
    #editModal .pending-field-error + .select2 .select2-selection { border-color:#ef4444!important; background:#fff7f7!important; box-shadow:0 0 0 2px rgba(239,68,68,.12)!important; }
    #bankTable .pending-field-error,
    #bankTable .pending-field-error + .select2 .select2-selection { border-color:#f87171!important; background:#fffafa!important; box-shadow:inset 0 0 0 1px rgba(248,113,113,.35)!important; }
    #bankTable tr.pending-field-error-row { background:#fff7f7; }

    /* Professional bank preview table polish */
    /* #bankTable tbody tr {
        background: #ffffff;
    } */

    .dark #bankTable tbody tr {
        background: #0f172a;
    }

    #bankTable tr.pending-field-error-row {
        background: #fffafa;
    }

    /* .dark #bankTable tr.pending-field-error-row {
        background: rgba(127, 29, 29, 0.18);
    } */

    #bankTable input,
    #bankTable select,
    #bankTable .inputCell,
    #bankTable .searchInput,
    #bankTable .select2-container--default .select2-selection--single {
        min-height: 30px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #111827;
        font-size: 12px;
        line-height: 1.25;
    }

    #bankTable input,
    #bankTable select,
    #bankTable .inputCell,
    #bankTable .searchInput {
        padding: 5px 8px;
    }

    .dark #bankTable input,
    .dark #bankTable select,
    .dark #bankTable .inputCell,
    .dark #bankTable .searchInput,
    .dark #bankTable .select2-container--default .select2-selection--single {
        border-color: #475569;
        background: #020617;
        color: #f8fafc;
    }

    #bankTable input[type="date"] {
        color-scheme: light;
    }

    .dark #bankTable input[type="date"] {
        color-scheme: dark;
    }

    #bankTable .pending-field-error,
    #bankTable .pending-field-error + .select2 .select2-selection {
        border-color: #f87171 !important;
        background: #fffafa !important;
        box-shadow: 0 0 0 2px rgba(248, 113, 113, .14) !important;
    }

    .dark #bankTable .pending-field-error,
    .dark #bankTable .pending-field-error + .select2 .select2-selection {
        background: #1f1215 !important;
        color: #f8fafc !important;
    }

    #bankTable .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: inherit !important;
        line-height: 28px;
    }

    #bankTable .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 28px;
    }
</style>
@endsection
@section('scripts')
<script>
let ALL_LEDGERS = @json($allLedgers);
let BANK_LEDGERS = @json($bankLedgers);
let LEDGER_OPTIONS_CACHE = {};

function escapeLedgerOption(value) {
    return $('<div>').text(value || '').html();
}

function getLedgerList(type) {
    return type === 'contra' ? BANK_LEDGERS : ALL_LEDGERS;
}

function getLedgerOptions(type, selectedLedger = '') {
    type = normalizePreviewFilterText(type);
    selectedLedger = (selectedLedger || '').toString();

    if (!LEDGER_OPTIONS_CACHE[type]) {
        let html = '<option value="">Select Ledger</option>';
        getLedgerList(type).forEach(l => {
            const name = escapeLedgerOption(l.name);
            html += `<option value="${name}">${name}</option>`;
        });
        LEDGER_OPTIONS_CACHE[type] = html;
    }
    if (selectedLedger && !getLedgerList(type).some(l => l.name === selectedLedger)) {
        const safeSelectedLedger = escapeLedgerOption(selectedLedger);
        return LEDGER_OPTIONS_CACHE[type] + `<option value="${safeSelectedLedger}">${safeSelectedLedger}</option>`;
    }

    return LEDGER_OPTIONS_CACHE[type];
}

function initLedgerSelect(ledgerDropdown) {
    if (!$.fn.select2 || !ledgerDropdown.length || ledgerDropdown.hasClass('select2-hidden-accessible')) {
        return;
    }

    const row = ledgerDropdown.closest('tr');
    const type = row.find('select[name^="type"]').val()?.toLowerCase() || '';
    const selectedLedger = ledgerDropdown.val() || ledgerDropdown.data('selected') || '';
    if (ledgerDropdown.data('optionsHydrated') !== 1) {
        ledgerDropdown.html(getLedgerOptions(type, selectedLedger));
        ledgerDropdown.val(selectedLedger || '');
        ledgerDropdown.data('optionsHydrated', 1);
    }

    ledgerDropdown.select2({
        width: '100%',
        placeholder: "Search Ledger...",
        allowClear: true
    });
}

$('#bankTable').on('change', 'select[name^="type"]', function () {
    let type = $(this).val().toLowerCase();
    let row = $(this).closest('tr');
    let ledgerDropdown = row.find('select[name^="ledger"]');
    const selectedLedger = ledgerDropdown.val() || ledgerDropdown.data('selected') || '';
    ledgerDropdown.html(getLedgerOptions(type, selectedLedger));
    ledgerDropdown.val(selectedLedger);
    ledgerDropdown.trigger('change.select2');
});

// $('#bankTable').on('change', 'select[name^="type"]', function () {
//     let type = $(this).val().toLowerCase();
//     let row = $(this).closest('tr');
//     let ledgerDropdown = row.find('select[name^="ledger"]');
//     ledgerDropdown.html(getLedgerOptions(type));

//     // 🔥 clear old value
//     // ledgerDropdown.val('').trigger('change');

//     // 🔥 reinitialize select2 properly
//     if (ledgerDropdown.hasClass("select2-hidden-accessible")) {
//         ledgerDropdown.select2('destroy');
//     }

//     ledgerDropdown.select2({
//         width: '100%',
//         placeholder: "Search Ledger...",
//         allowClear: true
//     });
// });

$(document).ready(function () {
    applyBankPendingIssuesToRows();
    if (!$.fn.select2) {
        console.error('Select2 not loaded');
        return;
    }

    let type = $('#typeFilter').val()?.toLowerCase() || '';
    let bulkLedger = $('#bulkLedger');
    bulkLedger.html(getLedgerOptions(type));
    bulkLedger.select2({
        width: '200px',
        placeholder: "Search Ledger...",
        allowClear: true
    });

    // 🔥 INITIAL LOAD FIX (MAIN SOLUTION)
    $('#bankTable tbody tr').each(function () {

        let row = $(this);
        let type = row.find('select[name^="type"]').val().toLowerCase();
        let ledgerDropdown = row.find('select[name^="ledger"]');

        // set options based on type
        ledgerDropdown.html(getLedgerOptions(type));

        let selectedLedger = ledgerDropdown.data('selected') || ledgerDropdown.val();
        // Keep the saved ledger selected even when the type-specific ledger list changes.
        ledgerDropdown.html(getLedgerOptions(type, selectedLedger));
        ledgerDropdown.val(selectedLedger || '');

    });

});


$('#descFilter').on('keyup', function () {
    let value = $(this).val().toLowerCase();
    $('#bankTable tbody tr').each(function () {
        let text = $(this).find('input[name^="narration"]').val().toLowerCase();
        $(this).toggle(text.includes(value));
    });
});

function applyFilters() {
    let type = $('#typeFilter').val().toLowerCase();
    //let amount = $('#amountFilter').val().toLowerCase();
    let desc = $('#descFilter').val().toLowerCase();
    $('#bankTable tbody tr').each(function () {
        let row = $(this);
        let rowType = row.find('select[name^="type"]').val().toLowerCase();
        let rowAmount = row.find('td:eq(6)').text().toLowerCase();
        let rowDesc = row.find('input[name^="narration"]').val().toLowerCase();
        let show = true;
        if (type && rowType !== type) show = false;
        // if (amount && !rowAmount.includes(amount)) show = false;
        if (desc && !rowDesc.includes(desc)) show = false;
        row.toggle(show);
    });
}
// $('#typeFilter, #amountFilter, #descFilter').on('change keyup', applyFilters);

function openConfigModal() {
    $('#configModal').addClass('show');
}
function closeConfigModal() {
    $('#configModal').removeClass('show');
}

function applyConfig() {

    $('.dynamic-col').remove();

    let selected = [];

    $('.configCheck:checked').each(function () {
        selected.push($(this).val());
    });

    let insertIndex = 2; // after Transfer Date

    let headerRow = $('#bankTable thead tr').eq(0);
    let searchRow = $('#bankTable thead tr').eq(1);

    selected.forEach(col => {

        let header = '';
        let searchField = '';

        if (col === 'cheque_no') {
            header = 'Cheque No';
            searchField = `<input class="searchInput">`;
        }

        if (col === 'reference_no') {
            header = 'Reference';
            searchField = `<input class="searchInput">`;
        }

        if (col === 'cost_center') {
            header = 'Cost Center';
            searchField = `<input class="searchInput">`;
        }

        // 🔥 HEADER INSERT
        headerRow.find('th').eq(insertIndex).after(
            `<th class="px-3 py-2 dynamic-col">${header}</th>`
        );

        // 🔥 SEARCH ROW INSERT (IMPORTANT 🔥)
        searchRow.find('th').eq(insertIndex).after(
            `<th class="dynamic-col">${searchField}</th>`
        );

        // 🔥 BODY INSERT
        $('#bankTable tbody tr').each(function () {

            let row = $(this);
            let rowId = row.find('input[name="selected[]"]').val();

            let chequeVal = row.data('cheque') || '';
            let refVal = row.data('ref') || '';
            let costVal = row.data('cost') || '';

            let field = '';

            if (col === 'cheque_no') {
                field = `<input type="text" class="inputCell" name="cheque_no[${rowId}]" value="${chequeVal}">`;
            }

            if (col === 'reference_no') {
                field = `<input type="text" class="inputCell" name="ref_no[${rowId}]" value="${refVal}">`;
            }

            if (col === 'cost_center') {
                field = `<input type="text" class="inputCell" name="cost_center[${rowId}]" value="${costVal}">`;
            }

            row.find('td').eq(insertIndex).after(
                `<td class="dynamic-col">${field}</td>`
            );
        });

        insertIndex++;
    });

    closeConfigModal();
}

$(document).on('keyup change', '.searchInput', function () {

    let input = $(this);
    let value = input.val().toLowerCase();

    let columnIndex = input.closest('th').index();

    $('#bankTable tbody tr').each(function () {

        let row = $(this);
        let cell = row.find('td').eq(columnIndex);

        let text = '';

        // text
        text += cell.text().toLowerCase();

        // input value
        let inputVal = cell.find('input').val();
        if (inputVal) text += inputVal.toLowerCase();

        // select
        let selectVal = cell.find('select option:selected').text();
        if (selectVal) text += selectVal.toLowerCase();

        row.toggle(text.includes(value));
    });
});
</script>
<script>
    $('#bankTable').on('click', '.saveRowBtn', function () {

        let row = $(this).closest('tr');
        let debit = row.find('.text-red-400').text().replace(/,/g, '').trim();
        let credit = row.find('.text-green-400').text().replace(/,/g, '').trim();

        let amount = 0;

        if (debit !== '' && !isNaN(debit)) {
            amount = parseFloat(debit);
        } else if (credit !== '' && !isNaN(credit)) {
            amount = parseFloat(credit);
        }
        let data = {
            _token: "{{ csrf_token() }}",
            id: row.find('input[name^="selected"]').val(),
            txn_date: row.find('input[name^="txn_date"]').val(),
            value_date: row.find('input[name^="value_date"]').val(),
            narration: row.find('input[name^="narration"]').val(),
            type: row.find('select[name^="type"]').val(),
            ledger: row.find('select[name^="ledger"]').val(),
            amount: amount,
            cheque_no : row.find('input[name^="cheque_no"]').val(),
            reference : row.find('input[name^="ref_no"]').val(),
            cost_center : row.find('input[name^="cost_center"]').val(),
        };
        console.log('DEBIT:', debit);
        console.log('CREDIT:', credit);
        console.log('AMOUNT:', amount);

        $.ajax({
            url: "{{ route('bank.update') }}",
            type: "POST",
            data: data,
            success: function (res) {
                showToast(res.message || (res.status ? 'Saved successfully' : 'Unable to save row'),(res.status !== false) ? 'success' : 'error');
                if (res.status !== false) {
                    location.reload();
                }

            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Error saving bank row','error');
            }
        });
    });

    function bankPendingIssueTargets(field, context) {
        const selectors = {
            ledger: ['#edit_ledger', 'select[name^="ledger"]'],
            type: ['#edit_type', 'select[name^="type"]'],
            date: ['#edit_txn_date', 'input[name^="txn_date"]'],
            amount: ['#edit_amount'],
        };

        return (selectors[field] || []).map(selector => context.find(selector)).filter(field => field.length);
    }

    function clearPendingIssueHighlights(context = $(document)) {
        $('#pendingIssueAlert').hide();
        $('#pendingIssueList').empty();
        context.find('.pending-field-error').removeClass('pending-field-error');
        context.find('.pending-field-error-row').removeClass('pending-field-error-row');
    }

    function applyPendingIssueHighlights(issues, context = $('#editModal')) {
        clearPendingIssueHighlights(context);
        const issueList = Array.isArray(issues) ? issues : [];

        if (!issueList.length) {
            return;
        }

        const list = issueList.map(issue => `<li>${$('<div>').text(issue.message || 'Please review this field.').html()}</li>`).join('');
        $('#pendingIssueList').html(list);
        $('#pendingIssueAlert').show();

        issueList.forEach(issue => {
            bankPendingIssueTargets(issue.field, context).forEach(field => {
                field.addClass('pending-field-error');
                field.closest('tr').addClass('pending-field-error-row');
            });
        });
    }

    function applyBankPendingIssuesToRows() {
        $('#bankTable tbody tr').each(function() {
            const row = $(this);
            const issues = row.data('pendingIssues') || [];
            if (!Array.isArray(issues) || !issues.length) {
                return;
            }
            issues.forEach(issue => {
                bankPendingIssueTargets(issue.field, row).forEach(field => {
                    field.addClass('pending-field-error');
                    row.addClass('pending-field-error-row');
                });
            });
        });
    }

    $('#saveBtn').click(function() {
        let missingLedgerRows = [];
        const selectedRows = $('#bankForm input[name="selected[]"]:checked');

        if (!selectedRows.length) {
            showToast('Please select at least one bank transaction', 'error');
            return;
        }

        selectedRows.each(function() {
            let row = $(this).closest('tr');
            let ledgerSelect = row.find('select[name^="ledger"]');

            if (!ledgerSelect.val()) {
                missingLedgerRows.push(row.find('td:eq(1)').text().trim() || $(this).val());
                ledgerSelect.css('border', '1px solid red');
            } else {
                ledgerSelect.css('border', '');
            }
        });

        if (missingLedgerRows.length) {
            showToast('Please select ledger for all selected bank rows before submitting. Missing ledger on row(s): ' + missingLedgerRows.join(', '),'error');
            return;
        }
        //let formData = $('#bankForm').serialize();
        let hiddenFields = $('#bankTable tbody tr:hidden').find('input,select');
        hiddenFields.prop('disabled', true);
        let formData = $('#bankForm').serialize();
        hiddenFields.prop('disabled', false);

        $.ajax({
            url: "{{ route('bank.save') }}",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function(response) {
                showToast(response.message || (response.status ? 'Submitted successfully' : 'Unable to submit selected bank rows'),(response.status ? 'success' : 'error'));
                if (response.status) {
                    location.reload();
                }

            },
            error: function(xhr) {
                console.log(xhr.responseText);
                showToast(xhr.responseJSON?.message || 'Error submitting bank data','error');
            }

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

    const LEDGER_PROFIT_AND_LOSS_GROUPS = [
        'sales accounts',
        'purchase accounts',
        'direct incomes',
        'direct expenses',
        'indirect incomes',
        'indirect expenses'
    ];

    function getLedgerFormValue(fieldName) {
        return String($('#ledgerForm [name="' + fieldName + '"]').val() || '').trim();
    }

    function isLedgerProfitAndLossGroup() {
        return LEDGER_PROFIT_AND_LOSS_GROUPS.includes(getLedgerFormValue('Parent').toLowerCase());
    }

    function updateLedgerActionButtons() {
        const isProfitAndLoss = isLedgerProfitAndLossGroup();
        $('#ledgerSaveBtn').toggle(!isProfitAndLoss);
    }

    function validateLedgerForm() {
        const isProfitAndLoss = isLedgerProfitAndLossGroup();
        const missing = [];

        if (!getLedgerFormValue('Name')) {
            missing.push('Name');
        }

        if (!getLedgerFormValue('Parent') || getLedgerFormValue('Parent').toLowerCase() === 'select parent') {
            missing.push('Group');
        }

        if (!isProfitAndLoss && !getLedgerFormValue('State')) {
            missing.push('State');
        }

        if (missing.length) {
            showToast('Please fill required field(s): ' + missing.join(', '), 'error');
            return false;
        }

        return true;
    }

    $(document).on('change', '#ledgerForm [name="Parent"]', updateLedgerActionButtons);

    $(document).on('click', '#ledgerSaveBtn', function() {
        if (!validateLedgerForm()) {
            return;
        }

        if (!isLedgerProfitAndLossGroup() && !getLedgerFormValue('GstNo')) {
            alert('GST No is empty. Please fill the GST No if you have it, else press Submit. Click OK to stay on the ledger form.');
            return;
        }

        $('#ledger_action').val('save');
        $('#ledgerForm').trigger('submit');
    });

    $(document).on('click', '#ledgerSubmitBtn', function() {
        if (!validateLedgerForm()) {
            return;
        }

        $('#ledger_action').val('submit');
        $('#ledgerForm').trigger('submit');
    });

    // Optional: handle form submit
    $('#ledgerForm').on('submit', function(e) {
        e.preventDefault();
        if (typeof validateLedgerForm === 'function' && !validateLedgerForm()) {
            return;
        }
        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('purchase.ledger.store') }}",
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

    $('#selectAll').click(function() {
        $('tbody input[type=checkbox]').prop('checked', this.checked);
    });

    
    $(document).on('keyup change', '.amountFrom, .amountTo', function () {

        let from = parseFloat($('.amountFrom').val()) || 0;
        let to = parseFloat($('.amountTo').val()) || Infinity;

        $('#bankTable tbody tr').each(function () {

            let row = $(this);

            let amountText = row.find('td:eq(6)').text().replace(/,/g, '').trim();
            let amount = parseFloat(amountText) || 0;

            if (amount >= from && amount <= to) {
                row.show();
            } else {
                row.hide();
            }

        });
    });

    function applyColumnFilters() {

        let from = parseFloat($('.amountFrom').val()) || 0;
        let to = parseFloat($('.amountTo').val()) || Infinity;

        $('#bankTable tbody tr').each(function () {

            let row = $(this);

            // amount
            let amountText = row.find('td:eq(6)').text().replace(/,/g, '').trim();
            let amount = parseFloat(amountText) || 0;

            // description
            let descFilter = $('th:eq(4) .searchInput').val()?.toLowerCase() || '';
            let desc = row.find('input[name^="narration"]').val().toLowerCase();

            let show = true;

            if (amount < from || amount > to) show = false;
            if (descFilter && !desc.includes(descFilter)) show = false;

            row.toggle(show);
        });
    }

    $('.searchInput, .amountFrom, .amountTo').on('keyup change', applyColumnFilters);

    
    $('.searchInput').on('keyup change', function () {
        let column = $(this).closest('th').index();
        let value = $(this).val().toLowerCase();
        $('#bankTable tbody tr').each(function () {
            let cell = $(this).find('td').eq(column);
            let text = '';
            text += cell.text().toLowerCase();
            let input = cell.find('input').val();
            if (input) text += input.toLowerCase();
            let select = cell.find('select option:selected').text();
            if (select) text += select.toLowerCase();
            $(this).toggle(text.includes(value));
        });
    });

    $('.deleteBtn').click(function() {
        if (!confirm('Are you sure you want to delete this transaction?')) {
            return;
        }
        let row = $(this).closest('tr');
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('bank.delete', ':id') }}".replace(':id', id),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.status) {
                    row.remove();
                    showToast(response.message,'success');
                    location.reload();
                }
            },
            error: function() {
                showToast('Delete failed','error');
            }
        });
    });

    $('#closeModal').click(function() {
        $('#editModal').addClass('hidden');
        clearPendingIssueHighlights($('#editModal'));
    });

    $('.editBtn').click(function() {
        const row = $(this).closest('tr');
        const issues = row.data('pendingIssues') || [];
        $('#edit_id').val($(this).data('id'));
        $('#edit_txn_date').val($(this).data('txn'));
        $('#edit_value_date').val($(this).data('value'));
        $('#edit_narration').val($(this).data('narration'));
        $('#edit_type').val($(this).data('type'));
        $('#edit_ledger').val($(this).data('ledger'));

        let debit = $(this).data('debit');
        let credit = $(this).data('credit');

        if (debit > 0) {
            $('#edit_amount').val(debit);
        } else {
            $('#edit_amount').val(credit);
        }
        $('#editModal').removeClass('hidden').addClass('flex');
        applyPendingIssueHighlights(issues);
    });

    $('#updateBtn').click(function() {
        const requiredFields = [
            ['#edit_txn_date', 'Please select Transaction Date'],
            ['#edit_value_date', 'Please select Value Date'],
            ['#edit_type', 'Please select Transaction Type'],
            ['#edit_ledger', 'Please select Ledger'],
            ['#edit_amount', 'Please enter Amount']
        ];
        for (const [selector, message] of requiredFields) {
            const field = $(selector);
            if (!String(field.val() || '').trim()) {
                showToast(message, 'error');
                field.trigger('focus');
                return;
            }
        }
        if ((parseFloat($('#edit_amount').val()) || 0) <= 0) {
            showToast('Amount must be greater than 0', 'error');
            $('#edit_amount').trigger('focus');
            return;
        }
        
        $.ajax({
            url: "{{ route('bank.update') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: $('#edit_id').val(),
                txn_date: $('#edit_txn_date').val(),
                value_date: $('#edit_value_date').val(),
                narration: $('#edit_narration').val(),
                type: $('#edit_type').val(),
                amount: $('#edit_amount').val(),
                ledger: $('#edit_ledger').val()
            },
            success: function(response) {
                if (response.status) {
                    showToast('Updated Successfully','success');
                    location.reload();
                }
            }
        });
    });

    // BULK TYPE UPDATE
    $('#typeFilter').on('change', function () {
        let type = $(this).val().toLowerCase();
        let bulkLedger = $('#bulkLedger');

        // 🔥 set options
        bulkLedger.html(getLedgerOptions(type));
        // 🔥 clear value
        bulkLedger.val('');
        // 🔥 destroy old select2
        if (bulkLedger.hasClass("select2-hidden-accessible")) {
            bulkLedger.select2('destroy');
        }
        // 🔥 re-init select2
        bulkLedger.select2({
            width: '200px',
            placeholder: "Search Ledger...",
            allowClear: true
        });
    });

    $('#bulkLedger').change(function () {
        let ledger = $(this).val();
        $('#bankTable tbody tr:visible').each(function () {
            let checkbox = $(this).find('input[type="checkbox"]');
            if (checkbox.is(':checked')) {
                let row = $(this);
                let type = row.find('select[name^="type"]').val().toLowerCase();
                let ledgerDropdown = row.find('select[name^="ledger"]');
                // 🔥 rebind correct options based on type
                ledgerDropdown.html(getLedgerOptions(type));
                // 🔥 check if selected ledger exists
                let exists = ledgerDropdown.find(`option[value="${ledger}"]`).length;
                if (exists) {
                    ledgerDropdown.val(ledger).trigger('change');
                } else {
                    ledgerDropdown.val('').trigger('change');
                }
                // 🔥 reinit select2
                if (ledgerDropdown.hasClass("select2-hidden-accessible")) {
                    ledgerDropdown.select2('destroy');
                }
                ledgerDropdown.select2({
                    width: '100%',
                    placeholder: "Search Ledger...",
                    allowClear: true
                });
            }
        });
    });

    $('.generalFilter').on('change', function () {
        let filters = [];
        $('.generalFilter:checked').each(function () {
            filters.push($(this).val());
        });
        $('#bankTable tbody tr').each(function () {
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

    $(document).ready(function () {
        if (!$.fn.select2) {
            console.error('Select2 not loaded');
            // return;
        }
        // applySelect2(); // ✅ important
    });

    // function applySelect2() {
    //     $('#bankTable tbody tr:visible .ledgerSelect').slice(0, 25).each(function () {
    //         initLedgerSelect($(this));
    //     });
    // }

    $('#bankTable').on('focus mouseenter', '.ledgerSelect', function() {
        initLedgerSelect($(this));
    });

    $(document).on('select2:open', function() {
        setTimeout(function() {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });

    
    $(document).on('click', '.suspenseBtn', function() {
        let id = $(this).data('id');

        $('#suspense_id').val(id);
        $('#suspense_remark').val('');

        $('#suspenseModal').removeClass('hidden').addClass('flex');
    });
    function closeSuspenseModal() {
        $('#suspenseModal').addClass('hidden').removeClass('flex');
    }

    $('#submitSuspense').click(function() {

        let id = $('#suspense_id').val();
        let remark = $('#suspense_remark').val().trim();

        if (!remark) {
            showToast('Please enter reason','error');
            return;
        }

        $.ajax({
            url: "{{ route('bank.markSuspense') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                remark: remark
            },
            success: function(res) {
                showToast('Marked as Suspense','success');
                location.reload();
            }
        });
    });

    $(document).on('click', '.viewRemarkBtn', function (e) {
        e.preventDefault(); // 🔥 STOP FORM SUBMIT

        let remark = $(this).data('remark');

        $('#remarkText').text(remark);
        $('#remarkModal').removeClass('hidden').addClass('flex');
    });

    function closeRemarkModal() {
        $('#remarkModal').removeClass('flex').addClass('hidden');
    }

    function normalizePreviewFilterText(value) {
        return (value || '').toString().toLowerCase().trim();
    }

    function getPreviewCellText(cell) {
        let parts = [];
        parts.push(cell.text());
        cell.find('input, textarea').each(function() {
            const value = $(this).val();
            parts.push(value);

            if ($(this).attr('type') === 'date' && value) {
                const dateParts = value.split('-');
                if (dateParts.length === 3) {
                    parts.push(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`);
                    parts.push(`${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`);
                }
            }
        });
        cell.find('select').each(function() {
            parts.push($(this).val());
            parts.push($(this).find('option:selected').text());
        });
        return normalizePreviewFilterText(parts.join(' '));
    }

    function getPreviewAmount(row) {
        let amountText = row.find('td').eq(6).text().replace(/,/g, '').trim();
        let amount = parseFloat(amountText);
        return Number.isNaN(amount) ? 0 : amount;
    }

    function applyBankPreviewFilters() {
        const typeFilter = normalizePreviewFilterText($('#typeFilter').val());
        const descriptionFilter = normalizePreviewFilterText($('#descFilter').val());
        const amountFromValue = parseFloat($('.amountFrom').val());
        const amountToValue = parseFloat($('.amountTo').val());
        const amountFrom = Number.isNaN(amountFromValue) ? -Infinity : amountFromValue;
        const amountTo = Number.isNaN(amountToValue) ? Infinity : amountToValue;
        const generalFilters = $('.generalFilter:checked').map(function() {
            return $(this).val();
        }).get();

        const columnFilters = [];
        $('#bankTable thead tr').eq(1).find('th').each(function(index) {
            const inputs = $(this).find('.searchInput').not('.amountFrom, .amountTo');
            const value = normalizePreviewFilterText(inputs.map(function() {
                return $(this).val();
            }).get().join(' '));
            if (value) {
                columnFilters.push({ index, value });
            }
        });

        $('#bankTable tbody tr').each(function() {
            const row = $(this);
            let show = true;

            if (typeFilter) {
                const rowType = normalizePreviewFilterText(row.find('select[name^="type"]').val());
                show = rowType === typeFilter;
            }

            if (show && descriptionFilter) {
                const rowDescription = normalizePreviewFilterText(row.find('input[name^="narration"]').val());
                show = rowDescription.includes(descriptionFilter);
            }

            if (show && (amountFrom !== -Infinity || amountTo !== Infinity)) {
                const amount = getPreviewAmount(row);
                show = amount >= amountFrom && amount <= amountTo;
            }

            if (show) {
                for (const filter of columnFilters) {
                    const cellText = getPreviewCellText(row.find('td').eq(filter.index));
                    if (!cellText.includes(filter.value)) {
                        show = false;
                        break;
                    }
                }
            }

            if (show && generalFilters.length) {
                const status = normalizePreviewFilterText(row.find('td').eq(10).text());
                if (generalFilters.includes('synced') && status === 'synced') {
                    show = false;
                }
                if (generalFilters.includes('saved') && status !== 'saved') {
                    show = false;
                }
                if (generalFilters.includes('failed') && status !== 'failed') {
                    show = false;
                }
                if (generalFilters.includes('blank')) {
                    const party = row.find('input[name^="party_name"]').val();
                    const ledger = row.find('select[name^="ledger"]').val();
                    if (party && ledger) {
                        show = false;
                    }
                }
            }

            row.toggle(show);
        });
    }

    let bankPreviewFilterTimer;
    function scheduleBankPreviewFilters() {
        clearTimeout(bankPreviewFilterTimer);
        bankPreviewFilterTimer = setTimeout(applyBankPreviewFilters, 150);
    }

    $(function() {
        $('#bankTable').attr('id', 'bankTable');
        $('#descFilter').off('keyup');
        $('.searchInput, .amountFrom, .amountTo').off('keyup change');
        $('.generalFilter').off('change');
        $(document).off('keyup change', '.searchInput');
        $(document).off('keyup change', '.amountFrom, .amountTo, #descFilter');
        $(document).off('keyup change', '.amountFrom, .amountTo');

        $(document).on('keyup change', '#descFilter, #bankTable .searchInput, #bankTable .amountFrom, #bankTable .amountTo, .generalFilter', scheduleBankPreviewFilters);
        $('#typeFilter').on('change', scheduleBankPreviewFilters);
        $('#bankTable').on('change keyup', 'tbody input, tbody select, tbody textarea', scheduleBankPreviewFilters);
    });
</script>
@endsection