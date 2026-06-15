@extends('layouts.super_admin')
@section('content')

<div class="container mx-auto">
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow border border-gray-200 dark:border-neutral-700">

        <!-- HEADER -->
        <div class="flex justify-between items-center px-5 py-3 border-b border-neutral-700">

            <div class="flex items-center gap-3">
                <button onclick="window.history.back()"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 text-lg">
                    ←
                </button>

                <h2 class="text-gray-900 dark:text-white text-lg font-semibold">
                    Journal Transactions
                </h2>

                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                    {{ count($rows) }}
                </span>
            </div>

            <div class="flex gap-2">
                <button onclick="saveSelected()" class="bg-green-600 text-white px-3 py-1 rounded text-sm">
                    Save
                </button>

                <!-- <button onclick="submitSelected()" class="bg-purple-600 text-white px-3 py-1 rounded text-sm">
                    Submit
                </button> -->
            </div>
        </div>

        <!-- FILTER -->
        <div class="px-5 py-3 border-b border-neutral-700 flex gap-4 text-sm text-gray-700 dark:text-gray-300">
            <label><input type="checkbox" class="filterStatus" value="saved"> Saved</label>
            <label><input type="checkbox" class="filterStatus" value="pending"> Pending</label>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700 dark:text-gray-300 border-collapse">
                <thead class="bg-gray-100 dark:bg-neutral-800 text-xs text-gray-700 dark:text-gray-400 uppercase">
                    <tr>
                        <th class="px-3 py-2 w-8"><input type="checkbox" id="selectAll"></th>
                        <th class="px-3 py-2 w-8">SR</th>
                        <th class="px-3 py-2 w-8">Journal No</th>
                        <th class="px-3 py-2 w-8">Date</th>
                        <th class="px-3 py-2 w-8">Total Debit</th>
                        <th class="px-3 py-2 w-8">Total Credit</th>
                        <th class="px-3 py-2 w-8">Status</th>
                        <th class="px-3 py-2 w-8">Action</th>
                    </tr>

                    <!-- SEARCH ROW -->
                    <tr class="bg-white dark:bg-neutral-900">
                        <th></th>
                        <th></th>
                        <th><input class="searchInput"></th>
                        <th><input class="searchInput"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($rows as $index => $row)
                    <tr class="border-b border-neutral-700 hover:bg-neutral-800">

                        <td class="px-3 py-2">
                            <input type="checkbox" class="rowCheckbox" value="{{ $row->id }}">
                        </td>

                        <td class="px-3 py-2">{{ $index+1 }}</td>

                        <td class="px-3 py-2">
                            <input type="text"
                                value="{{ $row->journal_no }}"
                                class="inputCell">
                        </td>

                        <td class="px-3 py-2">
                            <input type="date"
                                value="{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}"
                                class="inputCell">
                        </td>

                        <td class="text-green-400 px-3 py-2">
                            {{ number_format($row->total_debit,2) }}
                        </td>

                        <td class="text-blue-400 px-3 py-2">
                            {{ number_format($row->total_credit,2) }}
                        </td>

                        <td class="px-3 py-2">
                            <span class="{{ $row->status=='saved'?'text-green-400':'text-yellow-400' }}">
                                {{ $row->status }}
                            </span>
                        </td>

                        <td class="flex gap-2">
                            <button type="button"
                                onclick="editRow({{ $row->id }})"
                                class="text-blue-400 hover:text-blue-300"
                                title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <button type="button"
                                class="text-red-500"
                                onclick="deleteRow({{ $row->id }})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <!-- <button type="button" onclick="editRow({{ $row->id }})"
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

                            <button class="text-red-500 deleteRow" onclick="deleteRow({{ $row->id }})" data-id="{{$row->id}}">
                                <i class="fa-solid fa-trash"></i>
                            </button> -->
                        </td>

                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>

@include('admin.bulkupload.journal.edit_modal')
<style>
    .inputCell {
        background: white;
        border: 1px solid #d1d5db;
        color: #111827;
        padding: 4px;
        border-radius: 4px;
        width: 100%;
    }

    .dark .inputCell {
        background: #020617;
        border: 1px solid #374151;
        color: white;
    }

    .searchInput {
        width: 100%;
        background: white;
        border: 1px solid #d1d5db;
    }

    .dark .searchInput {
        background: #020617;
        color: white;
    }
</style>
<style>
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    /* MAIN CARD */
    .receipt-wrapper {
        width: 780px;
        max-width: 95%;
        background: #ffffff;
        color: #111;
        border-radius: 14px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    /* HEADER */
    .receipt-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 5px;
        background: #ffffff;
        /* dark blue ERP style */
        color: #000;
    }

    .receipt-company {
        font-size: 16px;
        font-weight: 600;
    }

    .receipt-subtitle {
        font-size: 12px;
        opacity: 0.7;
    }

    /* FORM */
    .receipt-meta-grid {
        padding: 3px 10px;
    }

    .receipt-field-row {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .receipt-field-row label {
        width: 130px;
        font-size: 13px;
        color: #374151;
    }

    /* INPUT */
    .receipt-input {
        flex: 1;
        padding: 4px 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #f9fafb;
        font-size: 13px;
    }

    /* ITEMS HEADER */
    .receipt-items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px 10px;
        font-weight: 500;
    }

    /* TABLE */
    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .receipt-table th {
        background: #f3f4f6;
        padding: 2px;
        text-align: left;
        color: #374151;
    }

    .receipt-table td {
        padding: 2px;
        border-top: 1px solid #e5e7eb;
    }

    /* ROW INPUT */
    .receipt-table input {
        width: 100%;
        padding: 4px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
    }

    /* TOTAL ROW */
    tfoot td {
        font-weight: 600;
        background: #f9fafb;
    }

    /* ADD BUTTON */
    .receipt-add-btn {
        background: #2563eb;
        color: white;
        padding: 2px 5px;
        border-radius: 6px;
        font-size: 12px;
    }

    /* FOOTER */
    .receipt-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid #e5e7eb;
    }

    .submit-btn {
        background: #16a34a;
        color: white;
        padding: 7px 16px;
        border-radius: 6px;
    }

    .btn-cancel {
        background: #6b7280;
        color: white;
        padding: 7px 16px;
        border-radius: 6px;
    }

    /* 🔥 SELECT BOX */
    .select2-container--default .select2-selection--single {
        height: 34px !important;
        background-color: #f9fafb !important;
        border: 1px solid #d1d5db !important;
        display: flex !important;
        align-items: center !important;
    }

    /* 🔥 SELECTED VALUE */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #000 !important;
        opacity: 1 !important;
    }

    /* 🔥 DROPDOWN OPTIONS FIX (MAIN ISSUE) */
    .select2-results__option {
        color: #000 !important;
        background: #fff !important;
    }

    /* 🔥 HOVER FIX */
    .select2-results__option--highlighted {
        background: #2563eb !important;
        color: #fff !important;
    }

    /* 🔥 DARK MODE FIX */
    .dark .select2-results__option {
        color: #fff !important;
        background: #020617 !important;
    }

    .dark .select2-results__option--highlighted {
        background: #2563eb !important;
        color: #fff !important;
    }

    /* 🔥 DROPDOWN BOX */
    .select2-dropdown {
        z-index: 99999 !important;
    }

    .auto-row input {
        background: #fef9c3;
    }
</style>
@endsection
@section('scripts')
<script>
    let ledgers = @json($ledgers);
</script>
<script>
    function normalize(str) {
        return (str || '')
            .replace(/["']/g, '')       // remove quotes
            .replace(/\s+/g, ' ')       // fix multiple spaces
            .trim()
            .toLowerCase();
    }

    function editRow(id) {
        let url = "{{ route('journal.show', ':id') }}";
        url = url.replace(':id', id);
        $('#editModal').addClass('show'); // ⚡ instant open
        $.get(url, function(res) {

            //$('#editModal').addClass('show'); // IMPORTANT FIX

            $('#edit_id').val(res.id);
            $('#edit_journal_no').val(res.journal_no);

            let d = new Date(res.date);

            let formatted =
                d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');

            $('#edit_date').val(formatted);

            $('#edit_narration').val(res.narration);

            let tbody = $('#itemsBody').empty();

            res.items.forEach((item, index) => {
                tbody.append(rowHtml(item, index + 1));
            });

            initLedgerSelect();
            
            // $('#itemsBody tr').each(function(i){
            //     let item = res.items[i];

            //     $(this).find('.ledger').val(item.ledger_id).trigger('change');
            //     $(this).find('.debit').val(item.debit);
            //     $(this).find('.credit').val(item.credit);
            // });
            setTimeout(() => {
                $('#itemsBody tr').each(function(i){
                    let item = res.items[i];

                    let select = $(this).find('.ledger');

                    // 🔥 FORCE OPTION MATCH
                    select.val(String(item.ledger_id)).trigger('change.select2');
                });
            }, 100);

            recalc();
        });
    }

    // =====================
    // ROW HTML
    // =====================
    function clean(str) {
        return (str || '')
            .replace(/"/g, '')
            .replace(/'/g, '')
            .trim()
            .toLowerCase();
    }

    function rowHtml(item = {}, index = '') {
        let options = `<option value="">Select Ledger</option>`;

        ledgers.forEach(l => {
            let selected = (l.id.trim() === (item.ledger_id || '').trim()) ? 'selected' : '';
            // let selected = clean(l.name) === clean(item.ledger_name) ? 'selected' : '';
            // options += `<option value="${l.name}" ${selected}>${l.name}</option>`;
            //options += `<option value="${l.name}">${l.name}</option>`;
            options += `<option value="${l.id}">${l.name}</option>`;
        });

        return `
        <tr>
            <td>${index}</td>

            <td>
                <select class="receipt-input ledger">
                    ${options}
                </select>
            </td>

            <td>
                <input type="number" value="${item.debit || ''}" 
                class="receipt-input debit" placeholder="0">
            </td>

            <td>
                <input type="number" value="${item.credit || ''}" 
                class="receipt-input credit" placeholder="0">
            </td>

            <td>
                <button style="color:red;font-weight:bold"
                onclick="$(this).closest('tr').remove(); recalc()">✕</button>
            </td>
        </tr>
        `;
    }

    // =====================
    // ADD ROW
    // =====================
    function addRow() {
        let count = $('#itemsBody tr').length + 1;
        $('#itemsBody').append(rowHtml({}, count));
        initLedgerSelect();
    }

    function initLedgerSelect() {
        $('.ledger').select2({
            dropdownParent: $('#editModal'),
            width: '100%',
            placeholder: 'Select Ledger',
            allowClear: true
        });


    }
    // =====================
    // DR / CR AUTO LOGIC
    // =====================
    $(document).on('input', '.debit, .credit', function() {

        let row = $(this).closest('tr');

        if ($(this).hasClass('debit')) {
            row.find('.credit').val(0);
        } else {
            row.find('.debit').val(0);
        }

        recalc();
    });

    

    function recalc() {

        let dr = 0,
            cr = 0;

        // ✅ FIRST calculate ONLY manual rows
        $('#itemsBody tr:not(.auto-row)').each(function() {
            dr += parseFloat($(this).find('.debit').val()) || 0;
            cr += parseFloat($(this).find('.credit').val()) || 0;
        });

        let diff = dr - cr;
        let autoRow = $('#itemsBody tr.auto-row');

        // ✅ HANDLE AUTO ROW
        if (diff === 0) {
            autoRow.remove();
        } else {
            let type = diff > 0 ? 'credit' : 'debit';
            let amount = Math.abs(diff);

            if (autoRow.length) {
                autoRow.find('.debit').val(type == 'debit' ? amount : 0);
                autoRow.find('.credit').val(type == 'credit' ? amount : 0);
            } else {
                let count = $('#itemsBody tr').length + 1;
                let options = `<option value="">Select Ledger</option>`;
                ledgers.forEach(l => {
                    // options += `<option value="${l.name}">${l.name}</option>`;
                    options += `<option value="${l.id}">${l.name}</option>`;
                });
                let row = `
            <tr class="auto-row bg-yellow-50">
                <td>${count}</td>
                <td>
                    <select class="receipt-input ledger">
                        ${options}
                    </select>
                </td>
                <td>
                    <input type="number" class="receipt-input debit" value="${type=='debit'?amount:0}">
                </td>
                <td>
                    <input type="number" class="receipt-input credit" value="${type=='credit'?amount:0}">
                </td>
                <td>
                <button style="color:red;font-weight:bold" onclick="removeAutoRow(this)">✕</button>
                </td>
            </tr>
            `;
                $('#itemsBody').append(row);
                initLedgerSelect();
            }
        }

        // ✅ FINAL TOTAL (INCLUDING AUTO ROW)
        let finalDr = 0,
            finalCr = 0;

        $('#itemsBody tr').each(function() {
            finalDr += parseFloat($(this).find('.debit').val()) || 0;
            finalCr += parseFloat($(this).find('.credit').val()) || 0;
        });

        $('#totalDr').text(finalDr.toFixed(2));
        $('#totalCr').text(finalCr.toFixed(2));
    }

    function removeAutoRow(btn) {
        $(btn).closest('tr').remove();
        recalc(); // recalculate after delete
    }

    // =====================
    // UPDATE JOURNAL
    // =====================
    function updateJournal() {
        let items = [];
        $('#itemsBody tr').each(function() {
            items.push({
                //ledger_name: $(this).find('.ledger').val(),
                ledger_id: $(this).find('.ledger').val(),
                debit: parseFloat($(this).find('.debit').val()) || 0,
                credit: parseFloat($(this).find('.credit').val()) || 0,
            });
        });

        $.post("{{ route('journal.update') }}", {
            _token: '{{ csrf_token() }}',
            id: $('#edit_id').val(),
            journal_no: $('#edit_journal_no').val(),
            date: $('#edit_date').val(),
            narration: $('#edit_narration').val(),
            items: items
        }, function(res) {
            alert(res.message || 'Updated Successfully');
            $('#editModal').removeClass('show');
            location.reload();
        });
    }

    // =====================
    // CLOSE MODAL
    // =====================
    function closeModal() {
        $('#editModal').removeClass('show');
    }

    function saveSelected() {
        let selected = $('.rowCheckbox:checked').map(function() {
            return this.value;
        }).get();
        $.post("{{ route('journal.save') }}", {
            _token: '{{ csrf_token() }}',
            selected: selected
        }, function(res) {
            alert(res.message);
            location.reload();
        });
    }

    function submitSelected() {
        let selected = $('.rowCheckbox:checked').map(function() {
            return this.value;
        }).get();
        $.post("{{ route('journal.submit') }}", {
            _token: '{{ csrf_token() }}',
            selected: selected
        }, function(res) {
            alert(res.message);
            location.reload();
        });
    }

    function deleteRow(id) {

        if (confirm('Are you sure you want to delete?')) {
            let url = "{{ route('journal.delete', ':id') }}";
            url = url.replace(':id', id);

            $.get(url, function() {
                location.reload();
            });
        }
    }

    // SELECT ALL
    $('#selectAll').click(function() {
        $('.rowCheckbox').prop('checked', this.checked);
    });

    // SEARCH
    $('.searchInput').on('keyup', function() {
        let column = $(this).closest('th').index();
        let value = $(this).val().toLowerCase();

        $('tbody tr').each(function() {
            let text = $(this).find('td').eq(column).text().toLowerCase();
            $(this).toggle(text.includes(value));
        });
    });

    // FILTER
    $('.filterStatus').on('change', function() {
        let filters = $('.filterStatus:checked').map(function() {
            return this.value;
        }).get();

        $('tbody tr').each(function() {
            let status = $(this).find('td:eq(6)').text().trim().toLowerCase();

            if (filters.length === 0) {
                $(this).show();
            } else {
                $(this).toggle(filters.includes(status));
            }
        });
    });
</script>

@endsection