
<?php $__env->startSection('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="container mx-auto px-2 sm:px-4">
    <div class="bg-white dark:bg-neutral-900 rounded-lg shadow border border-gray-200 dark:border-neutral-700">
        <!-- HEADER -->
                <div class="flex flex-col gap-3 px-4 py-3 border-b border-neutral-700 lg:flex-row lg:items-center lg:justify-between lg:px-5">
            <!-- <div class="flex items-center gap-3">
                <h2 class="text-white text-lg font-semibold">
                    Sales Transactions
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
                    Sales Transactions
                </h2>

                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                    <?php echo e($rows->count()); ?>

                </span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
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
        <div class="flex flex-col gap-4 px-4 py-3 text-sm border-b border-neutral-700 xl:flex-row xl:items-end xl:gap-10 lg:px-5">
            <div>
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
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
                <label class="flex gap-4 mt-2 text-gray-700 dark:text-gray-300">General Filters</label>
                <div class="flex flex-wrap gap-4 mt-2 text-gray-700 dark:text-gray-300">
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
        <!-- <form id="salesForm" method="POST" action="<?php echo e(route('sales.save')); ?>"> -->
        <form id="salesForm">
            <?php echo csrf_field(); ?>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 group-block">
                <table id="salesTable" class="sales-preview-table min-w-[1120px] xl:min-w-0 w-full text-sm text-gray-700 dark:text-gray-300 border-collapse">
                    <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-gray-900/40 text-xs text-gray-700 dark:text-gray-300 uppercase sticky top-0 z-10">
                        <tr>
                            <th class=" w-8">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="w-10">SR</th>
                            <th class="col-date">DATE</th>
                            <th class="col-reference">REFERENCE</th>
                            <th class="col-voucher">VOUCHER</th>
                            <th class="col-party">PARTY A/C NAME</th>
                            <th class="col-gstin">GSTIN/UIN</th>
                            <th class="col-place">PLACE</th>
                            <!-- <th class="">PARTICULARS</th> -->
                            <th class="col-amount text-right">AMOUNT</th>
                            <th class="col-status">STATUS</th>
                            <th class="col-action">ACTION</th>
                        </tr>
                        <tr class="bg-white dark:bg-neutral-900">
                            <th></th>
                            <th></th>
                            <th>
                                <input class="searchInput" type="search" inputmode="numeric" placeholder="Search date">
                            </th>
                            <th>
                                <input class="searchInput" type="search" placeholder="Search reference">
                            </th>
                            <th>
                                <input class="searchInput" type="search" placeholder="Search voucher">
                            </th>
                            <th>
                                <input class="searchInput" type="search" placeholder="Search party">
                            </th>
                            <th>
                                <input class="searchInput" type="search" placeholder="Search GSTIN">
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
                            <td class="">
                                <input type="checkbox" name="selected[]" value="<?php echo e($row->id); ?>">
                            </td>
                            <td class="">
                                <?php echo e($index+1); ?>

                            </td>
                            <td class="">
                                <input type="date"
                                    name="date[<?php echo e($row->id); ?>]"
                                    value="<?php echo e(\Carbon\Carbon::parse($row->date)->format('Y-m-d')); ?>"
                                    class="inputCell">
                            </td>
                            <td class="">
                                <input type="text"
                                    name="invoice_no[<?php echo e($row->id); ?>]"
                                    value="<?php echo e($row->invoice_no); ?>"
                                    class="inputCell">
                            </td>
                            <td class="">
                                <select name="voucher_type[<?php echo e($row->id); ?>]" class="inputCell voucherSelect">
                                    <?php $__currentLoopData = $vchTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vchType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($vchType); ?>"
                                        <?php echo e(strtolower(trim($vchType)) == strtolower(trim($row->vchType))  ? 'selected' : ''); ?>><?php echo e($vchType); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="">
                                <!-- Party Name -->
                                <input type="text"
                                    name="party_name[<?php echo e($row->id); ?>]"
                                    value="<?php echo e($row->party_name); ?>"
                                    class="inputCell mb-1">
                                </br >
                                <!-- Ledger -->
                                <select name="ledger[<?php echo e($row->id); ?>]"
                                    class="ledgerSelect inputCell">
                                    <option value="">Select Ledger</option>
                                    <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ledger->name); ?>"
                                        <?php echo e($row->party_name==$ledger->name?'selected':''); ?>>
                                        <?php echo e($ledger->name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="">
                                <?php echo e($row->gst_no); ?>

                            </td>
                            <td class="">
                                <select name="place_of_supply[<?php echo e($row->id); ?>]"
                                    class="placeSelect inputCell">
                                    <option value="">Select State</option>
                                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($state); ?>"
                                        <?php echo e(strtolower(trim($state)) == strtolower(trim($row->place_of_supply)) ? 'selected':''); ?>>
                                        <?php echo e($state); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class=" text-right">
                                <?php echo e(number_format((float) ($row->total_amount ?? 0), 2)); ?>

                            </td>
                            <td class="">
                                <span class="text-yellow-400">
                                    <?php echo e($row->status); ?>

                                </span>
                            </td>
                            <td class="">
                                
                                <button type="button" class="viewRow text-green-400 hover:text-green-300"
                                    title="View" data-id="<?php echo e($row->id); ?>">
                                    <i class="fa-solid fa-eye action-icon"></i>
                                </button>
                                <button type="button"
                                    class="text-blue-400 hover:text-blue-300 editRow"
                                    title="Edit"
                                    data-id="<?php echo e($row->id); ?>"
                                    data-invoice="<?php echo e($row->invoice_no); ?>"
                                    data-date="<?php echo e(($timestamp = strtotime($row->date ?? '')) ? date('Y-m-d', $timestamp) : ''); ?>"
                                    data-gst_no="<?php echo e($row->gst_no); ?>"
                                    data-vchtype="<?php echo e($row->vchType); ?>"
                                    data-party="<?php echo e($row->party_name); ?>"
                                    data-place="<?php echo e($row->place_of_supply); ?>"
                                    data-ledger="<?php echo e($row->sales_ledger); ?>"
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
<input type="hidden"
       class="slot_sales_ledger_id"
       value="${slot.ledger_id || slot.sales_ledger_id || ''}">
<?php echo $__env->make('admin.bulkupload.sales.previewEditModel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('admin.bulkupload.sales.previewVewandLedger', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('admin.bulkupload.sales.previewStyle', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#selectAll').click(function() {
            $('tbody input[type=checkbox]').prop('checked', this.checked);
        });
        function applySalesTableFilters() {
            const filters = $('.searchInput').map(function() {
                const value = $(this).val().trim().toLowerCase();
                return value ? { column: $(this).closest('th').index(), value } : null;
            }).get();
            $('#salesTable tbody tr').each(function() {
                const row = $(this);
                const matches = filters.every(function(filter) {
                    const cell = row.find('td').eq(filter.column);
                    let text = cell.text().toLowerCase();
                    const input = cell.find('input,select').map(function() {
                        const value = $(this).val();
                        return Array.isArray(value) ? value.join(' ') : value;
                    }).get().join(' ');

                    if (input) {
                        text += ' ' + String(input).toLowerCase();
                    }
                    return text.indexOf(filter.value) > -1;
                });
                row.toggle(matches);
            });
        }

        $('.searchInput').on('input change', applySalesTableFilters);

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

    function resetSalesModalState() {
        $('#editItemsBody').empty();
        $('#noItemBody').empty();
        $('#customSlotsBody').empty();
        $('#custom_tax_rows').empty();
        // $('#sum_amount, #sum_cgst, #sum_sgst, #sum_igst, #sum_grand_total, #foot_amount, #foot_total').text('0.00');
        // $('#edit_amount, #edit_cgst, #edit_sgst, #edit_igst, #edit_total_amount, #noitem_amount, #noitem_gst_rate').val(0);
        //$('#sum_amount, #sum_cgst, #sum_sgst, #sum_igst, #sum_roundoff, #sum_grand_total, #foot_amount, #foot_total').text('0.00');
        $('#sum_amount, #sum_cgst, #sum_sgst, #sum_igst, #sum_grand_total, #foot_amount, #foot_total').text('0.00');
        $('#sum_roundoff').val('0.00');
        $('#edit_amount, #edit_cgst, #edit_sgst, #edit_igst, #edit_roundoff, #edit_total_amount, #noitem_amount, #noitem_gst_rate').val(0);
        $('#standard_items_section').show();
        $('#no_item_section').hide();
        $('#custom_slots_section').hide();
        $('#standard_tax_rows').show();
        $('#custom_tax_rows').hide();
        $('#addItemRow').show();
        $('#addNoItemRow').hide();
        $('#invoice_sales_ledger_wrap').show();
        $('#igst_toggle_wrap').show();
        $('#gst_calc_mode').prop('disabled', false).val('standard');
        $('input[name="entry_mode"][value="item"]').prop('checked', true);
    }

    function fillPartyDetailsFromLedger() {
        const selected = $('#edit_party option:selected');

        $('#edit_gst').val(selected.data('gst') || '');
        $('#edit_address').val(selected.data('address') || '');
        $('#edit_pincode').val(selected.data('pincode') || '');
        $('#edit_city').val(selected.data('city') || '');

        const state = String(selected.data('state') || '').trim();
        if (!state) {
            $('#edit_place').val('').trigger('change');
            return;
        }

        const matchingState = $('#edit_place option').filter(function() {
            return String($(this).val()).trim().toLowerCase() === state.toLowerCase();
        }).first();

        $('#edit_place').val(matchingState.length ? matchingState.val() : state).trigger('change');
    }

    $(document).on('change', '#edit_party', function() {
        fillPartyDetailsFromLedger();
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
            url: "<?php echo e(route('sales.ledger.store')); ?>",
            type: "POST",
            data: formData,
            success: function(response) {
                closeLedgerModal();
                let name = $('input[name="Name"]').val();

                // ✅ Add into EDIT MODAL dropdown
                let newOption = new Option(name, name, true, true);
                $('#edit_party').append(newOption).trigger('change');

                // ✅ ALSO update table dropdowns (VERY IMPORTANT)
                $('.ledgerSelect').each(function() {
                    $(this).append(new Option(name, name));
                });

                // ✅ Refresh Select2 UI
                $('#edit_party').trigger('change');
                $('.ledgerSelect').trigger('change');

                // ✅ Clear form
                $('#ledgerForm')[0].reset();

            },
            error: function(xhr) {
                showToast('Error saving ledger','error');
                console.log(xhr.responseText);
            }
        });
    });

    const ledgers = <?php echo json_encode(collect($ledgers)->pluck('name'), 15, 512) ?>;
    const states = <?php echo json_encode($states, 15, 512) ?>;
    const vouchers = <?php echo json_encode($vchTypes, 15, 512) ?>;
    const ITEM_MASTER = <?php echo json_encode($stockItems, 15, 512) ?>;
    const SALES_LEDGERS = <?php echo json_encode($salesLedgers, 15, 512) ?>;
    const SALES_GST_MAPPINGS = <?php echo json_encode($salesGstMappings ?? [], 15, 512) ?>;

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
        const selectedValue = select.val();
        const selectedText = select.find('option:selected').text();

        return findSalesLedgerMapping(selectedValue, selectedText);
    }

    function findGstMappingForLedgerOrItem(ledgerValue = '', ledgerText = '') {
        const salesMapping = findSalesLedgerMapping(ledgerValue, ledgerText);
        if (salesMapping) {
            return salesMapping;
        }

        const item = findItemGstMapping(ledgerValue) || findItemGstMapping(ledgerText);
        if (!item) {
            return null;
        }

        return {
            igst_id: item.IGSTLedgerId || null,
            cgst_id: item.CGSTLedgerId || null,
            sgst_id: item.SGSTLedgerId || null,
        };
    }

    function applyGstLedgerMapping(force = false) {
        const mapping = getSelectedSalesLedgerMapping();

        if (!mapping) {
            return;
        }

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

    function mappedGstLedgerId(type, existing = null, ledgerValue = '', ledgerText = '') {
        if (existing) {
            return existing;
        }

        const mapping = ledgerValue || ledgerText
            ? findGstMappingForLedgerOrItem(ledgerValue, ledgerText)
            : getSelectedSalesLedgerMapping();

        return mapping ? mapping[`${type}_id`] : null;
    }
    function roundCurrency(value) {
        return Math.round(((parseFloat(value) || 0) + Number.EPSILON) * 100) / 100;
    }

    const ROUND_OFF_SIDE = <?php echo json_encode($roundOffSide ?? 'normal', 15, 512) ?>;

    function calculateRoundOffAmountForSummary(total) {
        total = roundCurrency(total);
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
        total = roundCurrency(total);
        // let roundOff = roundOffAmount === null || roundOffAmount === undefined
        //     ? calculateRoundOffAmountForSummary(total)
        //     : (parseFloat(roundOffAmount) || 0);
        roundOff = roundCurrency(roundOff);
        let roundedTotal = roundCurrency(total + roundOff);

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
        total = roundCurrency(total);
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

    function findItemGstMapping(itemId = '') {
        if (!itemId) return null;
        return ITEM_MASTER.find(item => String(item.iStockIdtemId) === String(itemId)) || null;
    }

    function applyItemGstMapping(itemId = '', force = false) {

        const item = findItemGstMapping(itemId);

        if (!item) {
            return;
        }

        // IGST
        if (item.IGSTLedgerId) {
            $('#igst_ledger').val(item.IGSTLedgerId).trigger('change');
        } else {
            $('#igst_ledger').val('').trigger('change');
        }

        // CGST
        if (item.CGSTLedgerId) {
            $('#cgst_ledger').val(item.CGSTLedgerId).trigger('change');
        } else {
            $('#cgst_ledger').val('').trigger('change');
        }

        // SGST
        if (item.SGSTLedgerId) {
            $('#sgst_ledger').val(item.SGSTLedgerId).trigger('change');
        } else {
            $('#sgst_ledger').val('').trigger('change');
        }
    }

    $(document).on('change', '#noitem_sales_ledger', function() {
        applyGstLedgerMapping(true);
        recalcTotals();
    });

    $(document).on('change', '.item-name', function() {
        let selectedValue = $(this).val();
        recalcTotals();
        applyItemGstMapping(selectedValue, true);
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

    function setEntryMode(mode) {
        $(`input[name="entry_mode"][value="${mode}"]`).prop('checked', true);

        if (mode === 'noitem') {
            $('#gst_calc_mode').val('custom').prop('disabled', true);
            $('#gst_calc_mode').closest('.receipt-field-row').hide();
            $('#standard_items_section').hide();
            $('#no_item_section').show();
            $('#addItemRow').hide();
            $('#addNoItemRow').show();
            $('#invoice_sales_ledger_wrap').hide();
            $('#igst_toggle_wrap').hide();
            $('#standard_tax_rows').hide();
            $('#custom_slots_section').show();
            $('#custom_tax_rows').show();

            if (!$('#noItemBody tr').length) {
                addNoItemRow({ gst: $('#noitem_gst_rate').val() || 0, amount: $('#noitem_amount').val() || 0 });
            }
        } else {
            $('#gst_calc_mode').prop('disabled', false);
            $('#gst_calc_mode').closest('.receipt-field-row').show();
            $('#standard_items_section').show();
            $('#no_item_section').hide();
            $('#addItemRow').show();
            $('#addNoItemRow').hide();
            $('#invoice_sales_ledger_wrap').show();
            $('#igst_toggle_wrap').toggle($('#gst_calc_mode').val() === 'standard');
        }

        recalcTotals();
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
                // row.find('select[name^="place_of_supply"]').val(value);
                row.find('select[name^="place_of_supply"]').val(value).trigger('change');
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
            url: "<?php echo e(route('sales.save')); ?>",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response && response.status === false) {
                    showToast(response.message || 'Unable to save selected records.','error');
                    return;
                }
                showToast(response.message || 'Saved Successfully','success');
                location.reload(); // reload page and refresh table
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Error saving data';
                showToast(message,'error');
            }
        });
    });

    $(document).on('click', '.deleteRow', function() {
        let id = $(this).data('id');
        if (!confirm('Delete this row?')) return;
        $.ajax({
            url: "<?php echo e(route('sales.delete', ':id')); ?>".replace(':id', id),
            type: "POST",
            data: {
                _token: "<?php echo e(csrf_token()); ?>"
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

    $('.generalFilter').on('change', function() {
        let filters = [];
        $('.generalFilter:checked').each(function() {
            filters.push($(this).val());
        });
        $('#salesTable tbody tr').each(function() {
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

    $(document).on('change', 'select[name^="ledger"]', function() {
        let value = $(this).val();
        let row = $(this).closest('tr');

        row.find('input[name^="party_name"]').val(value);
    });

    // ═══════════════════════════════════════════════════════════════════════
    // VIEW MODAL
    // ═══════════════════════════════════════════════════════════════════════
    $(document).on('click', '.viewRow', function() {
        let id = $(this).data('id');

        resetSalesModalState();

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
            url: "<?php echo e(route('sales.show', ':id')); ?>".replace(':id', id),
            type: "GET",
            success: function(res) {
                // console.log(res);

                // Fill header fields
                $('#edit_id').val(res.id);
                $('#edit_invoice').val(res.invoice_no);
                $('#edit_date').val(res.date);
                $('#edit_gst').val(res.gst_no);
                // $('#edit_party').val(res.party_name);
                //setSelectValueByTextOrValue($('#edit_party'), res.party_name);
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

                // Respect stored GST mode at edit time
                $('#edit_is_igst').prop('checked', Number(res.is_igst) === 1);
                toggleGSTLedger();

                // $('#edit_is_igst').val(res.is_igst);
                $('#edit_is_igst').prop('checked', res.is_igst == 1);
                $('#edit_remarks').val(res.Remarks);
                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');

                $('#noitem_sales_ledger').val(res.sales_ledger).trigger('change.select2');
                applyGstLedgerMapping(false);
                // $('#igst_ledger').val(mappedGstLedgerId('igst', res.igst_id)).trigger('change');
                // $('#cgst_ledger').val(mappedGstLedgerId('cgst', res.cgst_id)).trigger('change');
                // $('#sgst_ledger').val(mappedGstLedgerId('sgst', res.sgst_id)).trigger('change');
                $('#igst_ledger').val(mappedGstLedgerId('igst', res.igst_id)).trigger('change');
                $('#cgst_ledger').val(mappedGstLedgerId('cgst', res.cgst_id)).trigger('change');
                $('#sgst_ledger').val(mappedGstLedgerId('sgst', res.sgst_id)).trigger('change');

                $('#igst_ledger').val(res.igst_id).trigger('change');
                $('#cgst_ledger').val(res.cgst_id).trigger('change');
                $('#sgst_ledger').val(res.sgst_id).trigger('change');
                $('#sum_cgst').text(parseFloat(res.cgst || 0).toFixed(2));
                $('#sum_sgst').text(parseFloat(res.sgst || 0).toFixed(2));
                $('#sum_igst').text(parseFloat(res.igst || 0).toFixed(2));
                //$('#sum_grand_total').text(parseFloat(res.total_amount || 0).toFixed(2));
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
                        recalcItemRow(row);   // ADD THIS
                    });
                    recalcTotals();
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
                } else {
                    $('#editItemsBody').empty();
                    setEntryMode('noitem');
                    $('#standard_items_section').hide();
                    $('#no_item_section').show();
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

                    //$('#noitem_amount').val(res.total_amount);
                    $('#noitem_amount').val(res.amount);
                    $('#noitem_gst_rate').val(res.gst_rate || 0);
                    $('#noitem_sales_ledger').val(res.sales_ledger).trigger('change.select2');

                    recalcTotals();

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
                    let iGstLedgers = <?php echo json_encode($iGstLedgers, 15, 512) ?>;
                    let cGstLedgers = <?php echo json_encode($cGstLedgers, 15, 512) ?>;
                    let sGstLedgers = <?php echo json_encode($sGstLedgers, 15, 512) ?>;
                    let html = '';

                    res.custom_gst.forEach(slot => {
                        // let igstLedgerName = iGstLedgers.find(l => l.id == slot.igst_ledger_id)?.name || '';
                        // let cgstLedgerName = cGstLedgers.find(l => l.id == slot.cgst_ledger_id)?.name || '';
                        // let sgstLedgerName = sGstLedgers.find(l => l.id == slot.sgst_ledger_id)?.name || '';
                        let slotLedgerId = slot.ledger_id || slot.sales_ledger_id || '';
                        let slotLedgerName = slot.ledger_name || '';
                        let igstLedgerName = iGstLedgers.find(l => l.id == mappedGstLedgerId('igst', slot.igst_ledger_id, slotLedgerId, slotLedgerName))?.name || '';
                        let cgstLedgerName = cGstLedgers.find(l => l.id == mappedGstLedgerId('cgst', slot.cgst_ledger_id, slotLedgerId, slotLedgerName))?.name || '';
                        let sgstLedgerName = sGstLedgers.find(l => l.id == mappedGstLedgerId('sgst', slot.sgst_ledger_id, slotLedgerId, slotLedgerName))?.name || '';


                        html += `
                        <tr data-rate="${slot.gst_rate}">
                            <td>${slot.gst_rate}%</td>
                            <td class="slot-taxable">
                                ${slot.taxable || 0}
                                <input type="hidden" class="slot_sales_ledger_id" value="${slot.ledger_id || slot.sales_ledger_id || ''}">
                            </td>
                            <td>${igstLedgerName}</td>
                            <td>${slot.igst_amount || 0}</td>
                            <td>${cgstLedgerName}</td>
                            <td>${slot.cgst_amount || 0}</td>
                            <td>${sgstLedgerName}</td>
                            <td>${slot.sgst_amount || 0}</td>
                        </tr>`;
                    });

                    $('#customSlotsBody').html(html);
                    refreshCustomSummaryFromRows();
                }
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                $('#editModal input, #editModal select, #editModal textarea')
                    .prop('disabled', true)
                    .css('pointer-events', 'none');

                // For view mode, don't recalculate - just display stored values
                // recalcTotals();
            }
        });
    });

    function openViewModal() {
        document.getElementById('viewModal').classList.add('show');
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('show');
    }

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
            $select.append(new Option(value, value, true, true));
        }

        $select.trigger('change.select2');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // EDIT MODAL
    // ═══════════════════════════════════════════════════════════════════════
    // ═══════ EDIT MODAL ═══════
    $(document).on('click', '.editRow', function() {
        let btn = $(this),
            id = btn.data('id');
        resetSalesModalState();

        $('#updateRow').show();
        $('#addItemRow').show();

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
        // $('#edit_party').val(btn.data('party'));
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
            url: "<?php echo e(route('sales.show',':id')); ?>".replace(':id', id),
            type: "GET",
            success: function(res) {
                $('#edit_address').val(res.address || '');
                $('#edit_pincode').val(res.pincode || '');
                $('#edit_city').val(res.city || '');
                $('#edit_remarks').val(res.Remarks || '');
                
                // Respect stored GST mode at edit time
                $('#edit_is_igst').prop('checked', Number(res.is_igst) === 1);
                toggleGSTLedger();

                $('#gst_calc_mode').val(res.gst_mode || 'standard').trigger('change');
                 $('#noitem_sales_ledger').val(res.sales_ledger).trigger('change.select2');
                applyGstLedgerMapping(false);
                $('#igst_ledger').val(mappedGstLedgerId('igst', res.igst_id)).trigger('change');
                $('#cgst_ledger').val(mappedGstLedgerId('cgst', res.cgst_id)).trigger('change');
                $('#sgst_ledger').val(mappedGstLedgerId('sgst', res.sgst_id)).trigger('change');
                let tbody = $('#editItemsBody').empty();
                // (res.items || []).forEach(item => tbody.append(buildItemRow(item)));
                if (res.items && res.items.length > 0) {
                    setEntryMode('item');
                    // res.items.forEach(item => tbody.append(buildItemRow(item)));
                    res.items.forEach(item => {
                        let row = buildItemRow(item);
                        tbody.append(row);
                        recalcItemRow(row);   // ADD THIS
                    });
                    recalcTotals();
                } else {
                    $('#editItemsBody').empty();
                    setEntryMode('noitem');
                    $('#standard_items_section').hide();
                    $('#no_item_section').show();
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

                    $('#edit_amount').val(res.amount || 0);
                    $('#edit_cgst').val(res.cgst || 0);
                    $('#edit_sgst').val(res.sgst || 0);
                    $('#edit_igst').val(res.igst || 0);
                    //$('#edit_total_amount').val(res.total_amount || 0);
                    setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);

                    $('#noitem_amount').val(res.amount);
                    $('#noitem_gst_rate').val(res.gst_rate || 0);
                    $('#sum_cgst').html(res.cgst);
                    $('#sum_igst').html(res.igst);
                    $('#sum_sgst').html(res.sgst);

                    let amount = parseFloat(res.amount) || 0;

                    let cgst = parseFloat(res.cgst) || 0;
                    let sgst = parseFloat(res.sgst) || 0;
                    let igst = parseFloat(res.igst) || 0;
                    let total = roundCurrency(amount + cgst + sgst + igst);

                    //$('#sum_grand_total').html(total);
                    setRoundOffSummary(res.total_amount || total, res.roundoff || 0);
                    $('#noitem_sales_ledger').val(res.sales_ledger).trigger('change.select2');
                    recalcTotals();
                    tbody.html(''); // clear table
                }
                if (res.gst_mode === 'custom' && res.custom_gst && res.custom_gst.length) {
                    let iGstLedgers = <?php echo json_encode($iGstLedgers, 15, 512) ?>;
                    let cGstLedgers = <?php echo json_encode($cGstLedgers, 15, 512) ?>;
                    let sGstLedgers = <?php echo json_encode($sGstLedgers, 15, 512) ?>;
                    let html = '';

                    res.custom_gst.forEach(slot => {

                        html += `
                        <tr data-rate="${slot.gst_rate}">
                            <td>${slot.gst_rate}%</td>

                            <td class="slot-taxable">
                                ${slot.taxable}
                                <input type="hidden" class="slot_sales_ledger_id" value="${slot.ledger_id || slot.sales_ledger_id || ''}">
                            </td>
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
                    refreshCustomSummaryFromRows();
                }
                // if (!res.items || !res.items.length) {
                //     tbody.html('<tr><td colspan="9" class="text-center py-4" style="color:#94a3b8;font-size:12px;">No items — click Add Row</td></tr>');
                // }
                recalcTotals();
                setRoundOffSummary(res.total_amount || 0, res.roundoff || 0);
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

    // function buildLedgerOptions(list, selectedId) {

    //     selectedId = selectedId ? selectedId.toString().trim() : '';

    //     let html = '<option value="">Select Ledger</option>';

    //     list.forEach(l => {
    //         let id = l.id ? l.id.toString().trim() : '';
    //         let selected = (id === selectedId) ? 'selected' : '';
    //         html += `<option value="${l.id}" ${selected}>${l.name}</option>`;
    //     });

    //     return html;
    // }

    // ═══════ ADD ITEM ROW ═══════
    $('#addItemRow').click(function() {
        // Remove "no items" placeholder if present
        if ($('#editItemsBody tr td[colspan]').length) $('#editItemsBody').empty();
        $('#editItemsBody').append(buildItemRow({}));
        recalcTotals();
    });

    $('#addNoItemRow').click(function() {
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

    $(document).on('input change', '.noitem-gst, .noitem-amount, .noitem-ledger', function() {
        recalcTotals();
    });

    $(document).on('change', 'input[name="entry_mode"]', function() {
        setEntryMode($(this).val());
    });

    // ═══════ LIVE RECALC ON INPUT ═══════
    $(document).on('input', '#editItemsBody input', function() {
        recalcItemRow($(this).closest('tr'));
        recalcTotals();
    });

    // ═══════ REMOVE ROW ═══════
    $(document).on('click', '.removeItemRow', function() {
        $(this).closest('tr').remove();
        recalcTotals();
    });

    // ═══════ GST MODE SWITCH ═══════
    $('#gst_calc_mode').on('change', function() {
        let mode = $(this).val();
        if (mode === 'standard') {
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
        recalcTotals();
    });

    $('#edit_is_igst').on('change', function() {

        // 🔥 Recalculate each row GST
        $('#editItemsBody tr').each(function() {
            recalcItemRow($(this));
        });

        // 🔥 Also recalculate totals for no-item case
        recalcTotals();
    });
    //     // 🔥 Then update totals
    //     recalcTotals();
    // });

    // ── Save (Update) ────────────────────────────────────────────────────
    $('#updateRow').click(function() {
        let items = [];
        if ($('#gst_calc_mode').val() === 'standard') {

            let useIGST = $('#edit_is_igst').is(':checked');

            if (useIGST) {

                if (!$('#igst_ledger').val()) {
                    showToast('Please select IGST Ledger','error');
                    return;
                }

            } else {

                if (!$('#cgst_ledger').val()) {
                    showToast('Please select CGST Ledger','error');
                    return;
                }

                if (!$('#sgst_ledger').val()) {
                    showToast('Please select SGST Ledger','error');
                    return;
                }
            }
        }
        if ($('#no_item_section').is(':visible')) {
            items = []; // no items case
            let noItemRows = collectNoItemRows();
            let amount = noItemRows.reduce((total, row) => total + (parseFloat(row.amount) || 0), 0);
            let isIGST = $('#edit_is_igst').is(':checked');

            //let cgst = 0, sgst = 0, igst = 0;

            // Default GST % (you can make dynamic later)
            // let gstRate = 18;

            // if (isIGST) {
            //     igst = roundCurrency(amount * gstRate / 100);
            // } else {
            //     cgst = roundCurrency(amount * (gstRate / 2) / 100);
            //     sgst = roundCurrency(amount * (gstRate / 2) / 100);
            // }

            // let total = roundCurrency(amount + cgst + sgst + igst);
            let cgst = parseFloat($('#edit_cgst').val()) || 0;
            let sgst = parseFloat($('#edit_sgst').val()) || 0;
            let igst = parseFloat($('#edit_igst').val()) || 0;
            let total = roundCurrency(amount + cgst + sgst + igst);
            // Update UI
            $('#sum_amount').text(amount.toFixed(2));
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));
            // $('#sum_grand_total').text(total.toFixed(2));
            setRoundOffSummary(total);

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
            url: "<?php echo e(route('sales.update')); ?>",
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
                noitem_rows: collectNoItemRows(),
                sales_ledger_id: $('#noitem_sales_ledger').val(),
                sales_ledger_name: $('#noitem_sales_ledger option:selected').text(),
                // gst_rate: $('#noitem_gst_rate').val(),
                gst_rate: $('#no_item_section').is(':visible') ? $('#noitem_gst_rate').val() : null,
                items: items,
                entry_mode: $('#no_item_section').is(':visible') ? 'noitem' : 'item',
                custom_slots: collectCustomSlots()
            },
            success: (res) => {
                if (res.status) {
                    showToast(res.message || 'Inserted successfully', 'success');
                    //closeEditModal();
                    location.reload();
                } else {
                    showToast(res.message || 'Something went wrong', 'error');
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
        return parseFloat(v || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // function buildItemRow(item) {
    //     let srNo = $('#editItemsBody tr').length + 1;
    //     return `<tr>
    //         <input type="hidden" class="item-id" value="${item.id||''}">
    //         <td class="td-sr" style="width:28px;text-align:center;font-size:11px;color:#9ca3af;padding-left:6px;">${srNo}</td>
    //         <td style="min-width:180px;"><select class="item-name itemSelect">
    //             ${buildItemOptions()}
    //         </select></td>
    //         <td style="width:80px;"><input type="text" class="item-hsn" value="${item.hsn||''}" placeholder="HSN" style="text-align:center;"></td>
    //         <td style="width:65px;"><input type="number" class="item-gst_rate" value="${item.gst_rate||''}" placeholder="%" step="any" style="text-align:right;"></td>
    //         <td style="width:65px;"><input type="number" class="item-qty" value="${item.quantity||''}" placeholder="0" step="any" style="text-align:right;"></td>
    //         <td style="width:55px;"><input type="text" class="item-unit" value="${item.unit||'NOS'}" style="text-align:center;"></td>
    //         <td style="width:85px;"><input type="number" class="item-rate" value="${item.rate||''}" placeholder="0.00" step="any" style="text-align:right;"></td>
    //         <td style="width:85px;"><input type="number" class="item-amount" value="${item.amount||''}" readonly style="text-align:right;"></td>
    //         <td style="width:30px;text-align:center;">
    //             <button type="button" class="removeItemRow receipt-del-btn" title="Remove">
    //                 <i class="fa-solid fa-times" style="font-size:11px;"></i>
    //             </button>
    //         </td>
    //         <input type="hidden" class="item-sgst"  value="${item.sgst||0}">
    //         <input type="hidden" class="item-cgst"  value="${item.cgst||0}">
    //         <input type="hidden" class="item-igst"  value="${item.igst||0}">
    //         <input type="hidden" class="item-total" value="${item.total_amount||0}">
    //     </tr>`;

    //     // 🔥 Apply Select2 AFTER append
    //     $('.itemSelect').last().select2({
    //         width: '100%',
    //         placeholder: "Search Item...",
    //         allowClear: true
    //     });
    // }

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
        let amount = roundCurrency(qty * rate);
        let isIGST = $('#edit_is_igst').is(':checked');
        let mode = $('#gst_calc_mode').val();

        let cgst = 0,
            sgst = 0,
            igst = 0;

        if (gstRate > 0) {
            if (isIGST) {
                igst = roundCurrency(amount * gstRate / 100);
            } else {
                cgst = roundCurrency(amount * (gstRate / 2) / 100);
                sgst = roundCurrency(amount * (gstRate / 2) / 100);
            }
        }
        // In custom mode GST comes from slot ledger selection — item rows just store amount
        let total = roundCurrency(amount + cgst + sgst + igst);

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

    //         // 👇 DB values use karo
    //         let cgst = parseFloat($('#edit_cgst').val()) || 0;
    //         let sgst = parseFloat($('#edit_sgst').val()) || 0;
    //         let igst = parseFloat($('#edit_igst').val()) || 0;

    //         let total = roundCurrency(amount + cgst + sgst + igst);

    //         // UI update
    //         $('#sum_amount').text(amount.toFixed(2));
    //         $('#sum_cgst').text(cgst.toFixed(2));
    //         $('#sum_sgst').text(sgst.toFixed(2));
    //         $('#sum_igst').text(igst.toFixed(2));
    //         $('#sum_grand_total').text(total.toFixed(2));

    //         // hidden fields sync
    //         $('#edit_amount').val(amount);
    //         $('#edit_cgst').val(cgst);
    //         $('#edit_sgst').val(sgst);
    //         $('#edit_igst').val(igst);
    //         $('#edit_total_amount').val(total);

    //         return; // 🔥 IMPORTANT
    //     }
    //     let mode = $('#gst_calc_mode').val();
    //     let sumAmt = 0,
    //         sumSgst = 0,
    //         sumCgst = 0,
    //         sumIgst = 0,
    //         sumTotal = 0;

    //     // Collect per-rate data for custom mode
    //     let rateMap = {}; // { '5': {amt,igst,cgst,sgst}, '18': {...}, ... }

    //     $('#editItemsBody tr').each(function() {
    //         let row = $(this);
    //         let amt = parseFloat(row.find('.item-amount').val()) || 0;
    //         let sgst = parseFloat(row.find('.item-sgst').val()) || 0;
    //         let cgst = parseFloat(row.find('.item-cgst').val()) || 0;
    //         let igst = parseFloat(row.find('.item-igst').val()) || 0;
    //         let total = parseFloat(row.find('.item-total').val()) || 0;
    //         let gstRate = row.find('.item-gst_rate').val() || '0';

    //         sumAmt += amt;
    //         sumSgst += sgst;
    //         sumCgst += cgst;
    //         sumIgst += igst;
    //         sumTotal += total;

    //         // Accumulate into rate bucket for custom mode
    //         if (!rateMap[gstRate]) rateMap[gstRate] = {
    //             amt: 0,
    //             igst: 0,
    //             cgst: 0,
    //             sgst: 0
    //         };
    //         rateMap[gstRate].amt += amt;
    //         rateMap[gstRate].igst = roundCurrency(rateMap[gstRate].igst + igst);
    //         rateMap[gstRate].cgst = roundCurrency(rateMap[gstRate].cgst + cgst);
    //         rateMap[gstRate].sgst = roundCurrency(rateMap[gstRate].sgst + sgst);
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
    //     $('#editItemsBody tr').each(function(i) {
    //         $(this).find('.td-sr').text(i + 1);
    //     });

    //     if (mode === 'standard') {
    //         $('#sum_sgst').text(fmt(sumSgst));
    //         $('#sum_cgst').text(fmt(sumCgst));
    //         $('#sum_igst').text(fmt(sumIgst));
    //     } else {
    //         // CUSTOM MODE: render rate-wise slots
    //         renderCustomSlots(rateMap, sumTotal);
    //     }
    // }

    function recalcTotals() {
        applyGstLedgerMapping(false);

        // =========================
        // NO ITEM CASE
        // =========================
        if ($('#no_item_section').is(':visible')) {

            let isIGST = $('#edit_is_igst').is(':checked');
            let gstMode = $('#gst_calc_mode').val();
            let amount = 0;
            let cgst = 0;
            let sgst = 0;
            let igst = 0;
            let rateMap = {};

            $('#noItemBody tr').each(function(index) {
                let rowAmount = parseFloat($(this).find('.noitem-amount').val()) || 0;
                let gstRate = parseFloat($(this).find('.noitem-gst').val()) || 0;
                let gstAmount = roundCurrency(rowAmount * gstRate / 100);
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
                    igst = roundCurrency(igst + gstAmount);
                    rateMap[rateKey].igst = roundCurrency(rateMap[rateKey].igst + gstAmount);
                } else {
                    let halfGstAmount = roundCurrency(rowAmount * (gstRate / 2) / 100);
                    cgst = roundCurrency(cgst + halfGstAmount);
                    sgst = roundCurrency(sgst + halfGstAmount);
                    rateMap[rateKey].cgst = roundCurrency(rateMap[rateKey].cgst + halfGstAmount);
                    rateMap[rateKey].sgst = roundCurrency(rateMap[rateKey].sgst + halfGstAmount);
                }
            });
            
            // Update base amount first (used by custom slot rendering)
            $('#noitem_amount').val(amount.toFixed(2));
            $('#edit_amount').val(amount.toFixed(2));
            $('#sum_amount').text(amount.toFixed(2));
            $('#foot_amount').text(amount.toFixed(2));

            if (gstMode === 'custom') {
                renderCustomSlots(rateMap, amount + cgst + sgst + igst);
                return;
            }

            let total = roundCurrency(amount + cgst + sgst + igst);

            // Update hidden fields
            //$('#edit_amount').val(amount);
            $('#edit_cgst').val(cgst.toFixed(2));
            $('#edit_sgst').val(sgst.toFixed(2));
            $('#edit_igst').val(igst.toFixed(2));
            $('#edit_total_amount').val(total.toFixed(2));

            // Update display
            //$('#sum_amount').text(amount.toFixed(2));
            $('#sum_cgst').text(cgst.toFixed(2));
            $('#sum_sgst').text(sgst.toFixed(2));
            $('#sum_igst').text(igst.toFixed(2));
            // $('#sum_grand_total').text(total.toFixed(2));
            $('#foot_total').text(total.toFixed(2));
            const roundedTotal = setRoundOffSummary(total);
            $('#foot_total').text(roundedTotal.toFixed(2));

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

            totalAmount = roundCurrency(totalAmount + amount);
            totalCGST = roundCurrency(totalCGST + cgst);
            totalSGST = roundCurrency(totalSGST + sgst);
            totalIGST = roundCurrency(totalIGST + igst);

            // 🔥 BUILD RATE MAP
            if (!rateMap[gstRate]) {
                rateMap[gstRate] = {
                    amt: 0,
                    igst: 0,
                    cgst: 0,
                    sgst: 0
                };
            }

            rateMap[gstRate].amt = roundCurrency(rateMap[gstRate].amt + amount);
            rateMap[gstRate].igst = roundCurrency(rateMap[gstRate].igst + igst);
            rateMap[gstRate].cgst = roundCurrency(rateMap[gstRate].cgst + cgst);
            rateMap[gstRate].sgst = roundCurrency(rateMap[gstRate].sgst + sgst);
        });

        let grandTotal = roundCurrency(totalAmount + totalCGST + totalSGST + totalIGST);

        // UI update
        $('#sum_amount').text(totalAmount.toFixed(2));
        $('#sum_cgst').text(totalCGST.toFixed(2));
        $('#sum_sgst').text(totalSGST.toFixed(2));
        $('#sum_igst').text(totalIGST.toFixed(2));
        // $('#sum_grand_total').text(grandTotal.toFixed(2));
        const roundedGrandTotal = setRoundOffSummary(grandTotal);
        $('#foot_amount').text(totalAmount.toFixed(2));
        // $('#foot_total').text(grandTotal.toFixed(2));
        $('#foot_total').text(roundedGrandTotal.toFixed(2));

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
    }

    $(document).on('input', '#noitem_amount', function() {
        recalcTotals();
    });

    $(document).on('input', '#noitem_gst_rate', function() {
        recalcTotals();
    });

    // ═══════════════════════════════════════════════════════════════
    // CUSTOM MODE — render rate-wise slots
    // Each unique GST% from items gets one row with IGST/CGST/SGST
    // ledger dropdowns and auto-computed tax amounts.
    // ═══════════════════════════════════════════════════════════════
    function renderCustomSlots(rateMap, grandTotal) {
        let sGstLedgers = <?php echo json_encode($sGstLedgers ?? [], 15, 512) ?>;
        let cGstLedgers = <?php echo json_encode($cGstLedgers ?? [], 15, 512) ?>;
        let iGstLedgers = <?php echo json_encode($iGstLedgers ?? [], 15, 512) ?>;

        // 🔥 PRESERVE EXISTING LEDGER SELECTIONS
        let existingSelections = {};
        $('#customSlotsBody tr').each(function () {
            let rate = $(this).data('rate');
            let salesLedgerId = $(this).find('.slot_sales_ledger_id').val() || '';
            let key = $(this).data('slot-key') || `${rate}|${salesLedgerId}`;

            existingSelections[key] = {
                igst_ledger: $(this).find('.slot-igst-ledger').val(),
                cgst_ledger: $(this).find('.slot-cgst-ledger').val(),
                sgst_ledger: $(this).find('.slot-sgst-ledger').val(),
                igst_amt: $(this).find('.slot-igst-amt').val(),
                cgst_amt: $(this).find('.slot-cgst-amt').val(),
                sgst_amt: $(this).find('.slot-sgst-amt').val()
            };

            if (!existingSelections[String(rate)]) {
                existingSelections[String(rate)] = existingSelections[key];
            }
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
                sgst: 0
            };
            let rate = data.rate ?? mapKey;
            let halfR = parseFloat(rate) / 2;
            
            // 🔥 USE EXISTING SELECTION IF AVAILABLE
            let existing = existingSelections[data.slotKey || mapKey] ||
                existingSelections[`${rate}|${data.ledgerId || ''}`] ||
                existingSelections[rate] ||
                {};
            // GST amounts are derived from the current taxable amount and GST rate.
            // Preserve only ledger selections here; otherwise editing a no-item row amount
            // would keep stale tax amounts from the previously rendered slot row.
            let igstAmt = roundCurrency(data.igst);
            let cgstAmt = roundCurrency(data.cgst);
            let sgstAmt = roundCurrency(data.sgst);
            customIgst = roundCurrency(customIgst + igstAmt);
            customCgst = roundCurrency(customCgst + cgstAmt);
            customSgst = roundCurrency(customSgst + sgstAmt);

            let isZero = data.amt === 0;

            // Build ledger options WITH EXISTING SELECTION
            let iOpts = iGstLedgers.map(l => {
                // let sel = (String(l.id) === String(existing.igst_ledger)) ? 'selected' : '';
                let sel = (String(l.id) === String(mappedGstLedgerId('igst', existing.igst_ledger, data.ledgerId || '', data.ledgerName || ''))) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');
            let cOpts = cGstLedgers.map(l => {
                // let sel = (String(l.id) === String(existing.cgst_ledger)) ? 'selected' : '';
                let sel = (String(l.id) === String(mappedGstLedgerId('cgst', existing.cgst_ledger, data.ledgerId || '', data.ledgerName || ''))) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');
            let sOpts = sGstLedgers.map(l => {
                // let sel = (String(l.id) === String(existing.sgst_ledger)) ? 'selected' : '';
                let sel = (String(l.id) === String(mappedGstLedgerId('sgst', existing.sgst_ledger, data.ledgerId || '', data.ledgerName || ''))) ? 'selected' : '';
                return `<option value="${l.id}" ${sel}>${l.name}</option>`;
            }).join('');

            slotHtml += `<tr class="${isZero ? 'zero-row' : ''}" data-slot-key="${data.slotKey || mapKey}" data-rate="${rate}">
                <td><span class="rate-badge"><span class="slot-rate"></span>${rate}%</span></td>
                <td class="slot-taxable"><strong>${fmt(data.amt)}</strong><input type="hidden" class="slot_sales_ledger_id" value="${data.ledgerId || ''}"></td>
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
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(customIgst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(customCgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(customSgst)}</span></div>`;
        $('#custom_tax_rows').html(customSummaryHtml);

        // Update hidden fields for save
        $('#edit_igst').val(customIgst.toFixed(2));
        $('#edit_cgst').val(customCgst.toFixed(2));
        $('#edit_sgst').val(customSgst.toFixed(2));
        let total = roundCurrency((parseFloat($('#edit_amount').val()) || 0) + customIgst + customCgst + customSgst);
        $('#edit_total_amount').val(total.toFixed(2));
        //$('#sum_grand_total').text(fmt(total));
        const roundedTotal = setRoundOffSummary(total);
        $('#foot_amount').text(fmt(parseFloat($('#edit_amount').val()) || 0));
        //$('#foot_total').text(fmt(total));
        $('#foot_total').text(fmt(roundedTotal));
    }

    // When user manually edits a slot amount → recalc grand total
    function refreshCustomSummaryFromRows() {
        let igst = 0,
            cgst = 0,
            sgst = 0;

        $('#customSlotsBody tr').each(function() {
            let row = $(this);
            igst += parseFloat(row.find('.slot-igst-amt').val() ?? row.find('td:eq(3)').text()) || 0;
            cgst += parseFloat(row.find('.slot-cgst-amt').val() ?? row.find('td:eq(5)').text()) || 0;
            sgst += parseFloat(row.find('.slot-sgst-amt').val() ?? row.find('td:eq(7)').text()) || 0;
        });

        let base = parseFloat($('#edit_amount').val()) || parseFloat($('#sum_amount').text().replace(/,/g, '')) || 0;
        let total = base + igst + cgst + sgst;

        $('#edit_igst').val(igst.toFixed(2));
        $('#edit_cgst').val(cgst.toFixed(2));
        $('#edit_sgst').val(sgst.toFixed(2));
        $('#edit_total_amount').val(total.toFixed(2));
        //$('#sum_grand_total').text(fmt(total));
        const roundedTotal = setRoundOffSummary(total);
        $('#foot_amount').text(fmt(base));
        //$('#foot_total').text(fmt(total));
        $('#foot_total').text(fmt(roundedTotal));

        let customSummaryHtml = `
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(igst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(cgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(sgst)}</span></div>`;
        $('#custom_tax_rows').html(customSummaryHtml);
    }

    $(document).on('input', '.slot-igst-amt, .slot-cgst-amt, .slot-sgst-amt', function() {
        let igst = 0,
            cgst = 0,
            sgst = 0;
        $('.slot-igst-amt').each(function() {
            igst += parseFloat($(this).val()) || 0;
        });
        $('.slot-cgst-amt').each(function() {
            cgst += parseFloat($(this).val()) || 0;
        });
        $('.slot-sgst-amt').each(function() {
            sgst += parseFloat($(this).val()) || 0;
        });
        let base = parseFloat($('#edit_amount').val()) || 0;
        let total = base + igst + cgst + sgst;
        $('#edit_igst').val(igst.toFixed(2));
        $('#edit_cgst').val(cgst.toFixed(2));
        $('#edit_sgst').val(sgst.toFixed(2));
        $('#edit_total_amount').val(total.toFixed(2));
        //$('#sum_grand_total').text(fmt(total));
        const roundedTotal = setRoundOffSummary(total);
        $('#foot_amount').text(fmt(base));
        //$('#foot_total').text(fmt(total));
        $('#foot_total').text(fmt(roundedTotal));
        let customSummaryHtml = `
            <div class="tax-row"><span class="tax-label">IGST (Total)</span><span class="tax-value">${fmt(igst)}</span></div>
            <div class="tax-row"><span class="tax-label">CGST (Total)</span><span class="tax-value">${fmt(cgst)}</span></div>
            <div class="tax-row"><span class="tax-label">SGST (Total)</span><span class="tax-value">${fmt(sgst)}</span></div>`;
        $('#custom_tax_rows').html(customSummaryHtml);
    });

    // Get slot ledger selections for save payload
    function getCustomSlotData() {
        let slots = [];
        $('#customSlotsBody tr').each(function() {
            let r = $(this).data('rate');
            slots.push({
                rate: r,
                igst_ledger: $(this).find('.slot-igst-ledger').val(),
                igst_amount: $(this).find('.slot-igst-amt').val(),
                cgst_ledger: $(this).find('.slot-cgst-ledger').val(),
                cgst_amount: $(this).find('.slot-cgst-amt').val(),
                sgst_ledger: $(this).find('.slot-sgst-ledger').val(),
                sgst_amount: $(this).find('.slot-sgst-amt').val(),
            });
        });
        return slots;
    }

    function openEditModal() {
        document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show'); // 🔥 RESET STATE
        $('#updateRow').show();
        $('#addItemRow').show();

        $('#editModal input, #editModal select, #editModal textarea')
            .prop('disabled', false)
            .css('pointer-events', 'auto');

        $('.receipt-del-btn').show();
    }

    function openViewModal() {
        document.getElementById('viewModal').classList.add('show');
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('show');
    }

    function openLedgerModal() {
        document.getElementById('ledgerModal').classList.add('show');
    }

    function closeLedgerModal() {
        document.getElementById('ledgerModal').classList.remove('show');
    }

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

    $('#edit_is_igst').on('change', function() {
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


    $(document).on('change', '#noitem_sales_ledger, #edit_sales_ledger',function () {
        applyGstLedgerMapping(true);
    });

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

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/bulkupload/sales/preview.blade.php ENDPATH**/ ?>