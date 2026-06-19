

<?php $__env->startSection('content'); ?>
<style>
.select2-container--default .select2-selection--single{
    height:42px !important;
    border-radius:8px !important;
    border:1px solid #cbd5e1 !important;
}

.dark .select2-container--default .select2-selection--single{
    background:#1e293b !important;
    color:#fff !important;
    border:1px solid #334155 !important;
}

.dark .select2-dropdown{
    background:#1e293b !important;
    color:#fff !important;
}

.dark .select2-results__option{
    color:#fff !important;
}
/* Multi Select Container */
.dark .select2-container--default .select2-selection--multiple{
    background:#1e293b !important;
    border:1px solid #334155 !important;
    min-height:42px !important;
}

/* Selected Tag */
.dark .select2-container--default .select2-selection--multiple .select2-selection__choice{
    background:#334155 !important;
    border:1px solid #475569 !important;
    color:#fff !important;
    padding:2px 8px !important;
}

/* Selected Tag Text */
.dark .select2-container--default .select2-selection__choice__display{
    color:#fff !important;
}

/* Remove Icon */
.dark .select2-container--default .select2-selection__choice__remove{
    color:#cbd5e1 !important;
    border-right:none !important;
}

.dark .select2-container--default .select2-selection__choice__remove:hover{
    color:#ef4444 !important;
    background:transparent !important;
}

/* Search Input */
.dark .select2-search__field{
    color:#fff !important;
}

/* Dropdown */
.dark .select2-dropdown{
    background:#0f172a !important;
    border:1px solid #334155 !important;
}

/* Options */
.dark .select2-results__option{
    color:#fff !important;
}

/* Hover */
.dark .select2-results__option--highlighted{
    background:#0284c7 !important;
    color:#fff !important;
}

/* Placeholder */
.dark .select2-selection__placeholder{
    color:#94a3b8 !important;
}

#mappingModal .gst-modal-panel,
#itemMappingModal .gst-modal-panel {
    max-height: min(88vh, 760px);
}

#mappingModal .gst-modal-body,
#itemMappingModal .gst-modal-body {
    max-height: calc(88vh - 140px);
    overflow-y: auto;
}

#mappingModal .select2-container--default .select2-selection--multiple,
#itemMappingModal .select2-container--default .select2-selection--multiple {
    max-height: 132px;
    overflow-y: auto;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<div class="container mx-auto">

    <!-- <form action="<?php echo e(route('clients.GstSettingupdate')); ?>" method="POST">
        <?php echo csrf_field(); ?> -->
        <input type="hidden" name="guid" value="<?php echo e($user->guid); ?>">
        <div class="rounded-2xl p-2 shadow-sm border border-cyan-400/10 bg-white/5 backdrop-blur-xl">
             <div class="flex justify-between items-center mb-3">
                <div>
                    <h2 class="text-2xl font-bold mb-3 text-neutral-900 dark:text-white">
                        GST Settings
                    </h2>
                </div>
                <div>
                    <span class="text-xs font-semibold text-green-600 whitespace-nowrap" style="font-size: 1.0rem;font-variant-caps: small-caps;">
                        <?php echo e($user->name ?? ''); ?>

                    </span>
                </div>
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <div class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white/80 px-2 py-1 dark:border-slate-700 dark:bg-slate-900/80">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Roundoff</span>
                        <select id="roundoff_side"
                            class="rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="normal" <?php echo e(($user->profile?->roundoff_side ?? 'normal') === 'normal' ? 'selected' : ''); ?>>Normal</option>
                            <option value="upper_side" <?php echo e(($user->profile?->roundoff_side ?? '') === 'upper_side' ? 'selected' : ''); ?>>Upper Side</option>
                            <option value="lower_side" <?php echo e(($user->profile?->roundoff_side ?? '') === 'lower_side' ? 'selected' : ''); ?>>Lower Side</option>
                        </select>
                        <select id="roundoff_ledger_id"
                            class="rounded border border-slate-300 bg-white px-2 py-1 text-xs text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="">Select Roundoff</option>
                            <?php $__currentLoopData = $roundOffLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ledger->iLedgerId); ?>" <?php echo e((string)($user->profile?->roundoff_ledger_id ?? '') === (string)$ledger->iLedgerId ? 'selected' : ''); ?>>
                                    <?php echo e($ledger->strCustomerName); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="button" id="saveRoundoffSetting"
                            class="rounded bg-slate-700 px-2 py-1 text-xs font-semibold text-white hover:bg-slate-800">
                            Save
                        </button>
                    </div>

                    <button type="button" id="btnAddMapping" class="bg-cyan-600 text-white px-2 py-1 rounded">
                        + Add Mapping
                    </button>

                    <button type="button"
                        id="btnAddItemMapping"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-2 py-1 rounded">
                        + Item GST Mapping
                    </button>

                    <!-- <a href="<?php echo e(url()->previous()); ?>" title="Go Back" class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                        hover:border-[#f472b6] hover:shadow-[0_0_15px_#f472b6] hover:scale-105 hover:-translate-y-1">
                        <i class="fa-solid fa-arrow-left"></i>                    
                    </a> -->
                    <a href="javascript:void(0);" onclick="history.back();" title="Go Back"
                        class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700 hover:border-[#f472b6] hover:shadow-[0_0_15px_#f472b6] hover:scale-105 hover:-translate-y-1">
                            <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-700 text-white">
                        <tr>
                            <th class="p-1 text-left">Ledger Name</th>
                            <th class="p-1">CGST Ledger</th>
                            <th class="p-1">SGST Ledger</th>
                            <th class="p-1">IGST Ledger</th>
                            <th class="p-1">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $mappedLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-slate-700">
                            <td class="p-1">
                                <?php echo e($ledger->strCustomerName); ?>

                            </td>
                            <td class="p-1">
                                <?php echo e($ledger->CGSTLedgerName); ?>

                            </td>
                            <td class="p-1">
                                <?php echo e($ledger->SGSTLedgerName); ?>

                            </td>
                            <td class="p-1">
                                <?php echo e($ledger->IGSTLedgerName); ?>

                            </td>
                            <td class="p-1">
                                <button data-ledger="<?php echo e($ledger->iLedgerId); ?>" data-cgst="<?php echo e($ledger->CGSTLedgerId); ?>" data-sgst="<?php echo e($ledger->SGSTLedgerId); ?>" data-igst="<?php echo e($ledger->IGSTLedgerId); ?>"
                                    class="editMapping rounded-full bg-blue-100 p-1.5 sm:p-2 text-blue-700 ring-1 ring-inset ring-blue-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800"
                                    data-id="<?php echo e($ledger->iLedgerId); ?>" title="Edit Mapping">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2">
                                        <path
                                            d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path
                                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                                    </g>
                                </svg>
                                </button>
                                <button
                                    class="deleteMapping rounded-full bg-rose-100 p-1.5 sm:p-2 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800"
                                    data-id="<?php echo e($ledger->iLedgerId); ?>" title="Delete Mapping">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                        height="1em" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M10 5h4a2 2 0 1 0-4 0M8.5 5a3.5 3.5 0 1 1 7 0h5.75a.75.75 0 0 1 0 1.5h-1.32l-1.17 12.111A3.75 3.75 0 0 1 15.026 22H8.974a3.75 3.75 0 0 1-3.733-3.389L4.07 6.5H2.75a.75.75 0 0 1 0-1.5zm2 4.75a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0zM14.25 9a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-1.5 0v-7.5a.75.75 0 0 1 .75-.75m-7.516 9.467a2.25 2.25 0 0 0 2.24 2.033h6.052a2.25 2.25 0 0 0 2.24-2.033L18.424 6.5H5.576z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5"
                                class="text-center p-8 text-gray-400">
                                No GST Mapping Found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            

            
        </div>
    <!-- </form> -->
</div>
<div class="rounded-2xl p-2 mt-6 shadow-sm border border-emerald-400/10 bg-white/5 backdrop-blur-xl">
    <h3 class="text-xl font-semibold mb-3 text-neutral-900 dark:text-white">
        Item GST Mapping
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-700 text-white">
                <tr>
                    <th class="p-1 text-left">Item Name</th>
                    <th class="p-1">CGST Ledger</th>
                    <th class="p-1">SGST Ledger</th>
                    <th class="p-1">IGST Ledger</th>
                    <th class="p-1">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $mappedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                <tr class="border-b border-slate-700">
                    <td class="p-1"><?php echo e($item->strItemName); ?></td>
                    <td class="p-1"><?php echo e($item->CGSTLedgerName); ?></td>
                    <td class="p-1"><?php echo e($item->SGSTLedgerName); ?></td>
                    <td class="p-1"><?php echo e($item->IGSTLedgerName); ?></td>

                    <td class="p-1">

                        <button
                            class="editItemMapping rounded-full bg-blue-100 p-1.5 sm:p-2 text-blue-700 ring-1 ring-inset ring-blue-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800" title="Edit"
                            data-item="<?php echo e($item->iStockIdtemId); ?>"
                            data-itemname="<?php echo e($item->strItemName); ?>"
                            data-cgst="<?php echo e($item->CGSTLedgerId); ?>"
                            data-sgst="<?php echo e($item->SGSTLedgerId); ?>"
                            data-igst="<?php echo e($item->IGSTLedgerId); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2">
                                    <path
                                        d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path
                                        d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                                </g>
                            </svg>
                        </button>

                        <button
                            class="deleteItemMapping rounded-full bg-rose-100 p-1.5 sm:p-2 text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800" title="Delete"
                            data-id="<?php echo e($item->iStockIdtemId); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                        height="1em" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M10 5h4a2 2 0 1 0-4 0M8.5 5a3.5 3.5 0 1 1 7 0h5.75a.75.75 0 0 1 0 1.5h-1.32l-1.17 12.111A3.75 3.75 0 0 1 15.026 22H8.974a3.75 3.75 0 0 1-3.733-3.389L4.07 6.5H2.75a.75.75 0 0 1 0-1.5zm2 4.75a.75.75 0 0 0-1.5 0v7.5a.75.75 0 0 0 1.5 0zM14.25 9a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-1.5 0v-7.5a.75.75 0 0 1 .75-.75m-7.516 9.467a2.25 2.25 0 0 0 2.24 2.033h6.052a2.25 2.25 0 0 0 2.24-2.033L18.424 6.5H5.576z" />
                                    </svg>
                        </button>

                    </td>

                </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                <tr>
                    <td colspan="5" class="text-center p-4">
                        No Item Mapping Found
                    </td>
                </tr>

                <?php endif; ?>
            </tbody>

        </table>
    </div>

</div>

<div id="mappingModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-3">
    <div class="gst-modal-panel flex w-full max-w-3xl flex-col rounded-2xl bg-white dark:bg-slate-900 shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-xl font-semibold text-slate-800 dark:text-white">
                GST Ledger Mapping
            </h3>

            <button type="button"
                class="closeModal text-2xl text-slate-500 hover:text-red-500">
                ×
            </button>
        </div>
        <form id="mappingForm">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="guid" value="<?php echo e($user->guid); ?>">
            <!-- Body -->
            <div class="gst-modal-body p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Ledger -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                            Ledger Name
                        </label>

                        <select id="ledger_id" name="ledger_id[]"  multiple="multiple"
                            class="select2-ledger w-full rounded-lg border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-800 dark:text-white px-3 py-2">
                            <!-- <option value="">Select Ledger</option> -->

                            <?php $__currentLoopData = $availableLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>" data-available="true">
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <!-- CGST -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                            CGST Ledger
                        </label>

                        <select id="cgst_id" name="cgst_id"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-800 dark:text-white px-3 py-2">
                            <option value="">Select CGST</option>

                            <?php $__currentLoopData = $cgstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>">
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <!-- SGST -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                            SGST Ledger
                        </label>

                        <select id="sgst_id" name="sgst_id"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-800 dark:text-white px-3 py-2">
                            <option value="">Select SGST</option>

                            <?php $__currentLoopData = $sgstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>">
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <!-- IGST -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                            IGST Ledger
                        </label>

                        <select id="igst_id" name="igst_id"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-800 dark:text-white px-3 py-2">
                            <option value="">Select IGST</option>

                            <?php $__currentLoopData = $igstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>">
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="flex shrink-0 justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 rounded-b-2xl">

                <button type="button"
                    class="closeModal px-5 py-2 rounded-lg bg-slate-500 text-white hover:bg-slate-600">
                    Cancel
                </button>

                <button type="button"
                    id="saveMapping"
                    class="px-5 py-2 rounded-lg bg-cyan-600 text-white hover:bg-cyan-700">
                    Save Mapping
                </button>

            </div>
        </form>
    </div>
</div>

<div id="itemMappingModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-3">
    <div class="gst-modal-panel flex w-full max-w-3xl flex-col rounded-2xl bg-white dark:bg-slate-900 shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-xl font-semibold text-slate-800 dark:text-white">
                Item GST Mapping
            </h3>
            <button type="button" class="closeItemModal text-2xl text-slate-500 hover:text-red-500">
                ×
            </button>
        </div>
        <div class="gst-modal-body p-6">
            <input type="hidden" id="item_mapping_id">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label>Item</label>
                    <select id="item_id" name="item_id[]" multiple="multiple" class="select2-item w-full rounded-lg border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-800 dark:text-white px-3 py-2">
                        <!-- <option value="">Select Item</option> -->
                        <?php $__currentLoopData = $availableItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->iStockIdtemId); ?>" data-available="true">
                                <?php echo e($item->strItemName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label>CGST</label>
                    <select id="item_cgst_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-800 dark:text-white px-3 py-2">
                        <option value="">Select</option>
                        <?php $__currentLoopData = $cgstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>">
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label>SGST</label>
                    <select id="item_sgst_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-800 dark:text-white px-3 py-2">
                        <option value="">Select</option>
                        <?php $__currentLoopData = $sgstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>">
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label>IGST</label>
                    <select id="item_igst_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-700
                                bg-white dark:bg-slate-800
                                text-slate-800 dark:text-white px-3 py-2">
                        <option value="">Select</option>
                        <?php $__currentLoopData = $igstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>">
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4 dark:border-slate-700 dark:bg-slate-900 rounded-b-2xl">
            <button type="button" class="closeItemModal bg-gray-500 text-white px-4 py-2 rounded">
                Cancel
            </button>
            <button type="button" id="saveItemMapping" class="bg-emerald-600 text-white px-4 py-2 rounded">
                Save
            </button>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('#is_item_wise').change(function(){
    if($(this).is(':checked'))
    {
        $('#itemSection').slideDown();
    }
    else
    {
        $('#itemSection').slideUp();
    }
});

$(document).on('click','.accordion-btn',function(){
    $(this)
        .closest('.accordion-card')
        .find('.accordion-body')
        .slideToggle();

});

$('#ledger_id').select2({
    dropdownParent: $('#mappingModal'),
    placeholder: 'Search Ledger...',
    allowClear: true,
    width:'100%'
});

$('#item_id').select2({
    dropdownParent: $('#itemMappingModal'),
    placeholder: 'Search Item...',
    allowClear: true,
    width: '100%'
});

$('#saveRoundoffSetting').click(function () {
    $.ajax({
        url: "<?php echo e(route('clients.updateRoundoffSetting')); ?>",
        type: "POST",
        data: {
            _token: "<?php echo e(csrf_token()); ?>",
            guid: "<?php echo e($user->guid); ?>",
            roundoff_side: $('#roundoff_side').val(),
            roundoff_ledger_id: $('#roundoff_ledger_id').val()
        },
        success: function(res) {
            if (res.success) {
                showToast(res.message || 'Roundoff setting saved successfully.','success');
            }
        },
        error: function(xhr) {
            showToast(xhr.responseJSON?.message || 'Unable to save roundoff setting.','error');
        }
    });
});

// $(document).ready(function () {
//     $('#btnAddMapping').click(function () {
//         $('#mappingModal').removeClass('hidden');
//         setTimeout(function () {
//             $('#ledger_id').select2({
//                 dropdownParent: $('#mappingModal'),
//                 // width: '100%',
//                 placeholder: 'Search Ledger...',
//                 allowClear: true
//             });
//         }, 100);
//     });
// });

// $(document).on('click','.editMapping',function(){
//     let ledgerId = $(this).data('ledger');
//     $('#ledger_id').val([ledgerId]).trigger('change');
//     $('#cgst_id').val($(this).data('cgst'));
//     $('#sgst_id').val($(this).data('sgst'));
//     $('#igst_id').val($(this).data('igst'));
//     $('#mappingModal').removeClass('hidden');
// });

$(document).on('click','.editMapping',function(){
    let ledgerId   = $(this).data('ledger');
    let ledgerName = $(this).closest('tr').find('td:eq(0)').text().trim();
    if ($("#ledger_id option[value='"+ledgerId+"']").length == 0)
    {
        // $('#ledger_id').append(
        //     new Option(ledgerName, ledgerId, true, true)
        // );
        const editOption = new Option(ledgerName, ledgerId, true, true);
        $(editOption).attr('data-edit-only', 'true');
        $('#ledger_id').append(editOption);
    }

    $('#ledger_id').val([ledgerId]).trigger('change');
    $('#cgst_id').val($(this).data('cgst'));
    $('#sgst_id').val($(this).data('sgst'));
    $('#igst_id').val($(this).data('igst'));
    $('#mappingModal').removeClass('hidden');
});

$(document).ready(function () {
    $('#btnAddMapping').click(function () {
        $('#ledger_id option[data-edit-only="true"]').remove();
        $('#ledger_id').val('').trigger('change');
        $('#cgst_id').val('');
        $('#sgst_id').val('');
        $('#igst_id').val('');
        $('#mappingModal').removeClass('hidden');
    });
    $(document).on('click', '.closeModal', function () {
        $('#mappingModal').addClass('hidden');
    });
    $('#mappingModal').click(function(e){
        if(e.target === this){
            $(this).addClass('hidden');
        }
    });
});

$('#saveMapping').click(function () {

    $.ajax({
        url: "<?php echo e(route('clients.saveGstMapping')); ?>",
        type: "POST",
        data: {
            _token: "<?php echo e(csrf_token()); ?>",
            guid: "<?php echo e($user->guid); ?>",
            ledger_ids: $('#ledger_id').val(),
            cgst_id: $('#cgst_id').val(),
            sgst_id: $('#sgst_id').val(),
            igst_id: $('#igst_id').val()
        },
        success: function(res){

            if(res.success){
                location.reload();
            }
        }
    });

});

$(document).on('click','.deleteMapping',function(){
    if(!confirm('Delete GST Mapping?')){
        return;
    }
    let ledgerId = $(this).data('id');
    $.ajax({
        url: "<?php echo e(route('clients.deleteGstMapping', ':id')); ?>".replace(':id', ledgerId),
        type: 'DELETE',
        data: {
            _token: '<?php echo e(csrf_token()); ?>'
        },
        success: function(res){
            if(res.success){
                location.reload();
            }
        },
        error: function(xhr){
            showToast('Something went wrong.','error');
        }
    });
});

$(document).ready(function () {

    $('#btnAddItemMapping').click(function () {

        $('#item_id').val('').trigger('change');
        $('#item_cgst_id').val('');
        $('#item_sgst_id').val('');
        $('#item_igst_id').val('');

        $('#itemMappingModal').removeClass('hidden');
    });

    $(document).on('click', '.closeItemModal', function () {
        $('#itemMappingModal').addClass('hidden');
    });

    $('#itemMappingModal').click(function(e){
        if(e.target === this){
            $(this).addClass('hidden');
        }
    });

});

$('#saveItemMapping').click(function(){

    $.ajax({
        url: "<?php echo e(route('clients.saveItemGstMapping')); ?>",
        type: "POST",
        data: {
            _token: "<?php echo e(csrf_token()); ?>",
            guid: "<?php echo e($user->guid); ?>",
            item_ids: $('#item_id').val(),
            cgst_id: $('#item_cgst_id').val(),
            sgst_id: $('#item_sgst_id').val(),
            igst_id: $('#item_igst_id').val()
        },
        success: function(res){
            if(res.success){
                location.reload();
            }
        }
    });

});

$(document).on('click','.editItemMapping',function(){
    let itemId   = $(this).data('item');
    let itemName = $(this).data('itemname');
    if ($("#item_id option[value='"+itemId+"']").length == 0)
    {
        $('#item_id').append(
            new Option(itemName, itemId, true, true)
        );
    }
    $('#item_id').val([itemId]).trigger('change');
    $('#item_cgst_id').val($(this).data('cgst'));
    $('#item_sgst_id').val($(this).data('sgst'));
    $('#item_igst_id').val($(this).data('igst'));
    $('#itemMappingModal').removeClass('hidden');
});
$(document).on('click','.deleteItemMapping',function(){
    if(!confirm('Delete Item GST Mapping?')){
        return;
    }
    let itemId = $(this).data('id');
    $.ajax({
        url: "<?php echo e(route('clients.deleteItemGstMapping', ':id')); ?>"
                .replace(':id', itemId),
        type: 'DELETE',
        data: {
            _token: '<?php echo e(csrf_token()); ?>'
        },
        success: function(res){
            if(res.success){
                location.reload();
            }
        },
        error: function(xhr){
            console.log(xhr.responseText);
            showToast('Delete failed.','error');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/settings/gst/index.blade.php ENDPATH**/ ?>