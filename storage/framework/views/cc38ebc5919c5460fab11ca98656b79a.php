<?php $__env->startSection('title', 'All Ledger'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $rows = collect($rows ?? ($data['rows'] ?? ($resp['data']['rows'] ?? [])));
        $meta = $resp['meta'] ?? [];
        // Helpers
        $toFloat = function ($v) {
            if ($v === null || $v === '') {
                return 0.0;
            }
            return (float) str_replace(',', '', (string) $v);
        };
        $inr = function ($num) {
            $num = (float) $num;
            $sign = $num < 0 ? '-' : '';
            $n = abs($num);
            $str = sprintf('%.2f', $n);
            [$int, $dec] = explode('.', $str);
            if (strlen($int) > 3) {
                $last3 = substr($int, -3);
                $rest = substr($int, 0, -3);
                $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
                $int = $rest . ',' . $last3;
            }
            return $sign . $int . '.' . $dec;
        };
        
        $sideByClosing = function ($row) use ($toFloat) {
            $dr = $toFloat($row->decDr ?? 0);
            $cr = $toFloat($row->decCr ?? 0);

            if ($dr > $cr) {
                return 'Dr';
            } elseif ($cr > $dr) {
                return 'Cr';
            }

            // fallback: check closing balance
            $cl = $toFloat($row->decClBl ?? 0);
            return $cl < 0 ? 'Cr' : 'Dr';
        };

        // Totals
        // $totalDr = $rows->sum(fn($r) => $toFloat($r->decDr ?? 0));
        // $totalCr = $rows->sum(fn($r) => $toFloat($r->decCr ?? 0));
        
        $totalDr = $rows->sum(fn($r) => abs($toFloat($r->decDr ?? 0)));
        $totalCr = $rows->sum(fn($r) => abs($toFloat($r->decCr ?? 0)));

        $net = $totalDr - $totalCr;

        $totalOp = $rows->sum(fn($r) => $toFloat($r->decOpBl ?? 0));
        $totalCl = $rows->sum(fn($r) => $toFloat($r->decClBl ?? 0));
        // Grouping
        $byParent = $rows->groupBy('strParents');
        $periodText = function () use ($meta) {
            $f = date('d-m-Y', strtotime($meta['from'] ?? request('from')));
            $t = date('d-m-Y', strtotime($meta['to'] ?? request('to')));
            if ($f || $t) {
                return trim(($f ?: '—') . ' to ' . ($t ?: '—'));
            }
            return 'All time';
        };
    ?>

    <div class="container py-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">All Ledger</h1>
                <p class="text-xs text-black-500 dark:text-gray-400 mt-0.5">
                        • <?php echo e($periodText()); ?>

                </p>
            </div>
            <?php
                $queryParams = array_merge(request()->query(), [
                    'groupId' => request('groupId', $groupId ?? ''),
                    'strCustomerName' => request('strCustomerName', $strCustomerName ?? ''),
                    'from' => request('from', $from ?? ''),
                    'to' => request('to', $to ?? ''),
                    'range' => request('range', $rangeSel ?? ''),
                ]);
                
            ?>
            <div>
                <a href="<?php echo e(route('reports.ledger.export-pdf', $queryParams)); ?>" title="Export into PDF"
                    class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                    <i class="fas fa-file-pdf"></i>
                </a>
                &nbsp;
                <a href="<?php echo e(route('reports.ledger.export-excel', $queryParams)); ?>" title="Export into Excel"
                    class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#34d399]
                                hover:shadow-[0_0_15px_#34d399]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                    <i class="fas fa-file-excel"></i>
                </a>
            </div>
        </div>
        
        
        <form method="POST" action="<?php echo e(route('reports.ledger')); ?>" id="searchForm"
            class="mt-2 rounded-lg p-2 flex flex-wrap items-end gap-3">
            <?php echo csrf_field(); ?>
            <?php
                
            ?>
            
            
            <div>
                <div class="relative"
                    x-data="{
                        open: false,
                        selected: '<?php echo e(request('group_id') ?? ''); ?>',
                        label: '<?php echo e(collect($GroupMasters)->firstWhere('iGroupId', request('group_id'))->strGroupName ?? 'Select Group'); ?>'
                    }">

                    <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Group</label>

                    <!-- Hidden -->
                    <input type="hidden" name="group_id" :value="selected">

                    <!-- Button -->
                    <button type="button" @click="open = !open"
                        class="w-full text-left
                        bg-gradient-to-br from-white/60 to-white/30
                        dark:from-white/10 dark:to-transparent
                        backdrop-blur-xl
                        border border-gray-300/80 dark:border-cyan-400/20
                        text-gray-900 dark:text-white
                        rounded-xl px-3 py-2 pr-10 text-sm">

                        <span x-text="label"></span>
                    </button>

                    <!-- Arrow -->
                    <div class="mt-2 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>

                    <!-- Dropdown -->
                    <ul x-show="open" @click.outside="open = false"
                        class="absolute z-50 mt-2 w-full rounded-xl overflow-auto max-h-60
                        bg-white/10 dark:bg-white/5 backdrop-blur-2xl border border-white/20">

                        <!-- Default -->
                        <li>
                            <!-- @click="selected=''; label='Select Group'; open=false" -->
                            <button type="button"
                               @click="selected='';
                                    label='Select Group';
                                    open=false;
                                    setTimeout(() => {
                                        autoSubmitIfPresetRange();
                                    }, 100);"
                                class="w-full text-left px-4 py-2 text-sm hover:text-[#22d3ee]">
                                Select Group
                            </button>
                        </li>

                        <?php $__currentLoopData = $GroupMasters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <!-- @click="selected='<?php echo e($Group->iGroupId); ?>'; label='<?php echo e($Group->strGroupName); ?>'; open=false" -->
                                <button type="button"
                                    @click="
                                        selected='<?php echo e($Group->iGroupId); ?>';
                                        label='<?php echo e($Group->strGroupName); ?>';
                                        open=false;
                                        setTimeout(() => {
                                            autoSubmitIfPresetRange();
                                        }, 100);"
                                    class="w-full text-left px-4 py-2 text-sm hover:text-[#22d3ee]">
                                    <?php echo e($Group->strGroupName); ?>

                                </button>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </ul>
                </div>
            </div>
            
            
            <div>
                <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">Ledger</label>
                <input name="strCustomerName" id="strCustomerName" oninput="
                        clearTimeout(window.ledgerTimer);
                        const range = document.querySelector('input[name=\'range\']').value;
                        if(range !== 'custom') {
                            window.ledgerTimer = setTimeout(() => {
                                document.getElementById('searchForm').submit();
                            }, 600);
                        }" value="<?php echo e(request('strCustomerName')); ?>"
                    placeholder="Search Ledger..."
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>
            
            
            <div>
                <div class="relative"
                    x-data="{
                        open: false,
                        selected: '<?php echo e($rangeSel); ?>',
                        options: <?php echo \Illuminate\Support\Js::from(collect($financialYears ?? [])->mapWithKeys(fn ($year) => [(string) $year->iYearId => $year->strYear])->put('custom', 'Custom Date')->all())->toHtml() ?>,
                        init() {
                            this.$watch('selected', value => {
                                handleRangeChange(value);
                            });
                        }
                    }">
                    <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Date Range</label>
                    <input type="hidden" name="range" :value="selected">
                    <!-- Button -->
                    <button type="button" @click="open = !open"
                        class="w-full text-left
                        bg-gradient-to-br from-white/60 to-white/30
                        dark:from-white/10 dark:to-transparent
                        backdrop-blur-xl
                        border border-gray-300/80 dark:border-cyan-400/20
                        text-gray-900 dark:text-white
                        rounded-xl px-3 py-2 pr-10 text-sm">

                        <span x-text="options[selected] ?? 'Select Range'"></span>
                    </button>

                    <!-- Arrow -->
                    <div class="mt-2 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>

                    <!-- Dropdown -->
                    <ul x-show="open" @click.outside="open = false"
                        class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden
                        bg-white/10 dark:bg-white/5 backdrop-blur-2xl border border-white/20">

                        <?php $__empty_1 = true; $__currentLoopData = $financialYears ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $financialYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li>
                                <button type="button"
                                    @click="selected='<?php echo e($financialYear->iYearId); ?>'; open=false"
                                    class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                    <?php echo e($financialYear->strYear); ?>

                                </button>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li>
                                <span class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-300">No financial years found</span>
                            </li>
                        <?php endif; ?>
                        <li>
                            <button type="button"
                                @click="selected='custom'; open=false"
                                class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                Custom Date
                            </button>
                        </li>

                    </ul>
                </div>
                
            </div>

            
            <div id="customFromWrap" class="<?php echo e($rangeSel === 'custom' ? '' : 'hidden'); ?>">
                <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from_custom" id="from_custom" value="<?php echo e(request('from')); ?>" min="1900-01-01"
                    max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>
            <div id="customToLabel"
                class="pb-2 text-black-500 dark:text-gray-400 <?php echo e($rangeSel === 'custom' ? '' : 'hidden'); ?>">TO</div>
            <div id="customToWrap" class="<?php echo e($rangeSel === 'custom' ? '' : 'hidden'); ?>">
                <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to_custom" id="to_custom" value="<?php echo e(request('to')); ?>" min="1900-01-01"
                    max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>

            
            <input type="hidden" name="from" id="from" value="<?php echo e(request('from')); ?>">
            <input type="hidden" name="to" id="to" value="<?php echo e(request('to')); ?>">

            
            <div id="customSearchButtons" class="<?php echo e($rangeSel === 'custom' ? '' : 'hidden'); ?> flex gap-2">
                <button type="submit"
                    class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Search</button>
                <a href="<?php echo e(route('reports.ledger')); ?>"
                    class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Reset</a>
            </div>
        </form>

        
        

        
        <div class="mt-2 space-y-5" id="ledgerGroups">
            <?php $__empty_1 = true; $__currentLoopData = $byParent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                
                <!-- <?php
                    $gDr = $list->sum(fn($r) => $toFloat($r->decDr ?? 0));
                    $gCr = $list->sum(fn($r) => $toFloat($r->decCr ?? 0));
                ?> -->
                <?php
                    // ✅ Remove zero-balance rows
                    $filteredList = $list->filter(function ($r) use ($toFloat) {
                        $op = $toFloat($r->decOpBl ?? 0);
                        $dr = $toFloat($r->decDr ?? 0);
                        $cr = $toFloat($r->decCr ?? 0);
                        $cl = $toFloat($r->decClBl ?? 0);

                        return !($op == 0 && $dr == 0 && $cr == 0 && $cl == 0);
                    });

                    // Recalculate totals after filter
                    // $gDr = $filteredList->sum(fn($r) => $toFloat($r->decDr ?? 0));
                    // $gCr = $filteredList->sum(fn($r) => $toFloat($r->decCr ?? 0));
                    
                    $gDr = $filteredList->sum(fn($r) => abs($toFloat($r->decDr ?? 0)));
                    $gCr = $filteredList->sum(fn($r) => abs($toFloat($r->decCr ?? 0)));
                ?>
                <?php if($filteredList->count() > 0): ?>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden group-block">
                    <div
                        class="px-4 py-3  border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="text-sm font-semibold text-black-700 dark:text-gray-200">
                            <?php echo e($parent ?: 'Ungrouped'); ?> 
                            <!-- — <span class="font-normal text-gray-500 dark:text-gray-400"><?php echo e($list->count()); ?> ledgers</span> -->
                        </div>
                        <div class="text-xs md:text-sm text-black-600 dark:text-gray-300">
                            <!-- <span class="mr-4">Dr: <strong
                                    class="text-gray-600 dark:text-gray-300"><?php echo e($inr($gDr)); ?></strong></span>
                            <span>Cr: <strong class="text-gray-600 dark:text-gray-300"><?php echo e($inr($gCr)); ?></strong></span> -->
                            <?php
                                $gOp = $filteredList->sum(fn($r) => $toFloat($r->decOpBl ?? 0));
                                $gCl = $filteredList->sum(fn($r) => $toFloat($r->decClBl ?? 0));
                            ?>

                            <!-- <span>Op:
                                <strong>
                                    <?php echo e($inr(abs($gOp))); ?> <?php echo e($gOp < 0 ? 'Cr' : 'Dr'); ?>

                                </strong>
                            </span> |

                            <span>Dr:
                                <strong><?php echo e($inr($gDr)); ?></strong>
                            </span> |

                            <span>Cr:
                                <strong><?php echo e($inr($gCr)); ?></strong>
                            </span> |

                            <span>Cl:
                                <strong>
                                    <?php echo e($inr(abs($gCl))); ?> <?php echo e($gCl < 0 ? 'Cr' : 'Dr'); ?>

                                </strong>
                            </span> -->
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="sticky top-0 z-10 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md border-b border-cyan-500/20">
                                <tr class="text-black-600 dark:text-gray-300">
                                    <th class="px-4 py-2 font-bold">Ledger</th>
                                    <th class="px-4 py-2 font-bold">Parent</th>
                                    
                                    <th class="px-4 py-2 font-bold text-right">Opening</th>
                                    <th class="px-4 py-2 font-bold text-right">Debit</th>
                                    <th class="px-4 py-2 font-bold text-right">Credit</th>
                                    <th class="px-4 py-2 font-bold text-right">Closing</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                                <?php $__currentLoopData = $filteredList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $op = $toFloat($r->decOpBl ?? 0);
                                        $cl = $toFloat($r->decClBl ?? 0);
                                        $rb = $toFloat($r->decRunningBalance ?? 0);
                                        $dr = $toFloat($r->decDr ?? 0);
                                        $cr = $toFloat($r->decCr ?? 0);
                                        $side = $sideByClosing($r);
                                    ?>
                                    <!-- hover:bg-transparent  -->
                                    <tr
                                        class="group  hover:backdrop-blur-md hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80">
                                        <td class="px-4 py-2 group-hover:text-black">
                                            
                                            <a href="<?php echo e(route('reports.voucher_history', ['ledger_id' => $r->iLedgerId ?? null, 'from' => $queryParams['from'], 'to' => $queryParams['to']])); ?>"
                                                class="text-blue-600 hover:underline group-hover:text-black">
                                                <div class="text-gray-900 dark:text-gray-100 group-hover:text-black">
                                                    <?php echo e($r->strCustomerName ?? 'Ledger'); ?></div>
                                                
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black"><?php echo e($r->strParents ?? '-'); ?>

                                        </td>
                                        
                                        <td
                                            class="px-4 py-2 group-hover:text-black  text-right <?php echo e($op < 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 '); ?>">
                                            <!-- <?php echo e($inr($op)); ?> -->
                                            <?php
                                                $opSide = $op <= 0 ? 'Dr' : 'Cr';
                                            ?>
                                            <?php if(abs($op) > 0): ?>
                                                <?php echo e($inr(abs($op))); ?> <?php echo e($op < 0 ? 'Dr' : 'Cr'); ?>

                                            <?php else: ?>
                                                0.00
                                            <?php endif; ?>

                                        </td>
                                        <td
                                            class="px-4 py-2 group-hover:text-black text-right <?php echo e($dr > 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 '); ?>">
                                            <?php echo e($inr($dr)); ?>

                                        </td>
                                        <td
                                            class="px-4 py-2 group-hover:text-black text-right <?php echo e($cr > 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 '); ?>">
                                            <?php echo e($inr($cr)); ?>

                                        </td>
                                        <td
                                            class="px-4 py-2 group-hover:text-black text-right <?php echo e($cl < 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 '); ?>">
                                            <!-- <?php echo e($inr($cl)); ?> -->
                                            <?php
                                                $side = $cl <= 0 ? 'Dr' : 'Cr';
                                            ?>
                                            
                                            <?php if(abs($cl) > 0): ?>
                                                <?php echo e($inr(abs($cl))); ?> <?php echo e($cl < 0 ? 'Dr' : 'Cr'); ?>

                                            <?php else: ?>
                                                0.00
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class=" border-t border-gray-600">
                                <tr>
                                    <td colspan="2" class="px-4 py-3">
                                        <!-- <div class="flex flex-wrap items-center justify-end gap-6 text-sm">
                                            <span class="text-gray-700 dark:text-gray-300">Group Dr:
                                                <strong
                                                    class="text-gray-700 dark:text-gray-300"><?php echo e($inr($gDr)); ?></strong></span>
                                            <span class="text-gray-700 dark:text-gray-300">Group Cr:
                                                <strong
                                                    class="text-gray-600 dark:text-gray-300"><?php echo e($inr($gCr)); ?></strong></span>
                                        </div> -->
                                        <div class="flex flex-wrap items-center justify-end gap-6 text-sm">
                                            <span class="text-gray-700 dark:text-gray-300"><strong
                                                    class="text-black-700 dark:text-gray-300">Total</strong>
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-2 group-hover:text-black  text-right <?php echo e($op < 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 '); ?>">
                                        <!-- <?php echo e($inr($op)); ?> -->
                                        <strong>
                                            <?php if(abs($gOp) > 0): ?>
                                                <?php echo e($inr(abs($gOp))); ?> <?php echo e($gOp < 0 ? 'Dr' : 'Cr'); ?>

                                            <?php else: ?>
                                                0.00
                                            <?php endif; ?></strong>
                                    </td>
                                    <td
                                        class="px-4 py-2 group-hover:text-black text-right <?php echo e($dr > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300 '); ?>">
                                        <strong><?php echo e($inr($gDr)); ?></strong>
                                    </td>
                                    <td
                                        class="px-4 py-2 group-hover:text-black text-right <?php echo e($cr > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300 '); ?>">
                                        <strong><?php echo e($inr($gCr)); ?></strong>
                                    </td>
                                    
                                    <td
                                        class="px-4 py-2 group-hover:text-black text-right <?php echo e($cl < 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300 '); ?>">
                                        <!-- <?php echo e($inr($cl)); ?> -->
                                        <strong> 
                                            <?php if(abs($gCl) > 0): ?>
                                                <?php echo e($inr(abs($gCl))); ?> <?php echo e($gCl < 0 ? 'Dr' : 'Cr'); ?>

                                            <?php else: ?>
                                                0.00
                                            <?php endif; ?>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                 <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div
                    class="mt-4 rounded-md bg-yellow-50 dark:bg-yellow-900/20 px-4 py-3 text-sm text-yellow-800 dark:text-yellow-300">
                    No ledgers found for the selected period.
                </div>
            <?php endif; ?>
        </div>

        
        <div
            class="mt-2 rounded-md border border-gray-200 dark:border-gray-700  p-4 flex flex-wrap items-center justify-between">
            <!-- <div class="text-sm text-gray-600 dark:text-gray-300">
                <strong>Total Dr:</strong> <span
                    class="text-gray-700 dark:text-gray-300"><?php echo e($inr($totalDr)); ?></span>
                &nbsp;|&nbsp;
                <strong>Total Cr:</strong> <span class="text-gray-700 dark:text-gray-300"><?php echo e($inr($totalCr)); ?></span>
            </div> -->
            <div class="flex flex-wrap justify-end gap-6 text-sm text-right w-full">
                <span>Opening: <strong>
                    <?php if(abs($totalOp) > 0): ?>
                        <?php echo e($inr(abs($totalOp))); ?> <?php echo e($totalOp < 0 ? 'Dr' : 'Cr'); ?>

                    <?php else: ?>
                        0.00
                    <?php endif; ?>
                </strong></span> |
                <span>Debit: <strong><?php echo e($inr($totalDr)); ?></strong></span> |
                <span>Credit: <strong><?php echo e($inr($totalCr)); ?></strong></span> |
                <span>Closing: <strong>
                    <?php if(abs($totalCl) > 0): ?>
                        <?php echo e($inr(abs($totalCl))); ?> <?php echo e($totalCl < 0 ? 'Dr' : 'Cr'); ?>

                    <?php else: ?>
                        0.00
                    <?php endif; ?>
                </strong></span>
            </div>
            <!-- <div class="text-sm">
                <?php if(abs($net) < 0.005): ?>
                    <span class="text-emerald-700 dark:text-emerald-300 font-semibold">Balanced (Dr = Cr)</span>
                <?php elseif($net > 0): ?>
                    <span class="text-gray-900 dark:text-gray-100">Net: <strong
                            class="text-gray-700 dark:text-gray-300"><?php echo e($inr($net)); ?> Dr</strong></span>
                <?php else: ?>
                    <span class="text-gray-900 dark:text-gray-100">Net: <strong
                            class="text-gray-700 dark:text-gray-300"><?php echo e($inr(abs($net))); ?> Cr</strong></span>
                <?php endif; ?>
            </div> -->
        </div>
    </div>

    <script>
        window.financialYearOptions = <?php echo json_encode(collect($financialYears ?? [])->mapWithKeys(fn ($year) => [(string) $year->iYearId => $year->strYear])->all(), 15, 512) ?>;

        function getRangeValue() {
            return document.querySelector('input[name="range"]').value;
        }

        function applyPresetRange(kind) {
            const pad = n => String(n).padStart(2, '0');
            const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

            function firstDayOfMonth(y, m) {
                return new Date(y, m, 1);
            }

            function lastDayOfMonth(y, m) {
                return new Date(y, m + 1, 0);
            }

            function fyStartYearFor(date) {
                return date.getMonth() >= 3 ? date.getFullYear() : date.getFullYear() - 1;
            }

            function fyRange(startYr) {
                const from = new Date(startYr, 3, 1);
                const to = new Date(startYr + 1, 2, 31);
                return { from, to };
            }

            function fyQuarterIndex(date) {
                const shifted = (date.getMonth() + 12 - 3) % 12;
                return Math.floor(shifted / 3) + 1;
            }

            function fyQuarterRange(fyStartYr, q) {
                let from, to;
                if (q === 4) {
                    from = new Date(fyStartYr + 1, 0, 1);
                    to = lastDayOfMonth(fyStartYr + 1, 2);
                } else {
                    const startM = 3 + (q - 1) * 3;
                    from = new Date(fyStartYr, startM, 1);
                    to = lastDayOfMonth(fyStartYr, startM + 2);
                }
                return { from, to };
            }

            function financialYearRange(kind) {
                const match = /^(\d{4})-(\d{4})$/.exec(window.financialYearOptions?.[kind] || '');

                if (!match) return null;

                return {
                    from: new Date(Number(match[1]), 3, 1),
                    to: new Date(Number(match[2]), 2, 31)
                };
            }

            function computeRange(kind) {
                const selectedFinancialYear = financialYearRange(kind);
                if (selectedFinancialYear) return selectedFinancialYear;

                const now = new Date();
                const y = now.getFullYear();
                const m = now.getMonth();

                if (kind === 'this_month') return {
                    from: firstDayOfMonth(y, m),
                    to: lastDayOfMonth(y, m)
                };
                if (kind === 'last_month') {
                    const prevY = m === 0 ? y - 1 : y;
                    const prevM = m === 0 ? 11 : m - 1;
                    return {
                        from: firstDayOfMonth(prevY, prevM),
                        to: lastDayOfMonth(prevY, prevM)
                    };
                }

                const fyStartYr = fyStartYearFor(now);
                if (kind === 'current_year') return fyRange(fyStartYr);
                if (kind === 'last_year') return fyRange(fyStartYr - 1);

                if (kind === 'current_quarter') {
                    const q = fyQuarterIndex(now);
                    return fyQuarterRange(fyStartYr, q);
                }
                if (kind === 'last_quarter') {
                    let q = fyQuarterIndex(now) - 1;
                    let startYr = fyStartYr;
                    if (q === 0) {
                        q = 4;
                        startYr = fyStartYr - 1;
                    }
                    return fyQuarterRange(startYr, q);
                }

                return null;
            }

            const r = computeRange(kind);
            if (r) {
                document.getElementById('from').value = fmt(r.from);
                document.getElementById('to').value = fmt(r.to);
            }
        }

        function autoSubmitIfPresetRange() {
            const form = document.getElementById('searchForm');
            const range = document.querySelector('input[name="range"]').value;
            // ONLY AUTO SEARCH FOR NON CUSTOM
            if (range !== 'custom') {
                applyPresetRange(range);
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('searchForm');
            //const rangeSel = document.getElementById('rangeSel');
            
            const customSearchButtons = document.getElementById('customSearchButtons');
            const autoSearchElements = document.querySelectorAll('.auto-search');
            
            let autoSearchTimeout;
            //let isCustomRange = rangeSel.value === 'custom';
            let isCustomRange = getRangeValue() === 'custom';

            // Toggle custom date fields visibility
            function toggleCustomFields(show) {
                document.getElementById('customFromWrap').classList.toggle('hidden', !show);
                document.getElementById('customToLabel').classList.toggle('hidden', !show);
                document.getElementById('customToWrap').classList.toggle('hidden', !show);
                customSearchButtons.classList.toggle('hidden', !show);
                isCustomRange = show;
            }

            // Initialize
            toggleCustomFields(isCustomRange);

            // Range selector change handler
            // rangeSel.addEventListener('change', function() {
            //     const isCustom = this.value === 'custom';
            //     toggleCustomFields(isCustom);
                
            //     // Auto-submit for non-custom ranges
            //     if (!isCustom) {
            //         setTimeout(submitForm, 100);
            //     }
            // });

            // Auto-search functionality
            autoSearchElements.forEach(element => {
                element.addEventListener('change', function() {
                    if (!isCustomRange) {
                        debouncedSubmit();
                    }
                });
                
                // For input fields (ledger name), also trigger on input with debouncing
                if (element.type === 'text') {
                    element.addEventListener('input', function() {
                        if (!isCustomRange) {
                            debouncedSubmit();
                        }
                    });
                }
            });

            // Debounced form submission
            function debouncedSubmit() {
                clearTimeout(autoSearchTimeout);
                autoSearchTimeout = setTimeout(submitForm, 500); // 500ms delay
            }

            // Form submission function
            // function submitForm() {
            //     // Update hidden date fields before submission
            //     //if (rangeSel.value === 'custom') {
            //     if (getRangeValue() === 'custom') {
            //         document.getElementById('from').value = document.getElementById('from_custom').value;
            //         document.getElementById('to').value = document.getElementById('to_custom').value;
            //     } else {
            //         // For preset ranges, compute dates (using your existing logic)
            //         applyPresetRange(rangeSel.value);
            //     }
                
            //     // Show loading indicator (optional)
            //     showLoading();
                
            //     // Submit the form
            //     form.submit();
            // }

            // Your existing date range computation logic
            

            // Optional: Loading indicator
            function showLoading() {
                // You can add a loading spinner here if desired
                console.log('Searching...');
            }
        });

        document.getElementById('searchForm').addEventListener('submit', function(e) {

            const range = document.querySelector('input[name="range"]').value;

            if (range === 'custom') {

                const fromCustom = document.getElementById('from_custom').value;
                const toCustom   = document.getElementById('to_custom').value;

                // validation
                if (!toCustom) {
                    e.preventDefault();
                    alert('Please select To Date');
                    return;
                }

                // 🔥 SET VALUES HERE (THIS IS YOUR MAIN FIX)
                document.getElementById('from').value = fromCustom;
                document.getElementById('to').value   = toCustom;

            } else {
                // preset range
                applyPresetRange(range);
            }
        });

        function handleRangeChange(value) {

            const fromWrap = document.getElementById('customFromWrap');
            const toWrap   = document.getElementById('customToWrap');
            const toLabel  = document.getElementById('customToLabel');
            const btnWrap  = document.getElementById('customSearchButtons');

            const isCustom = value === 'custom';

            // 🔥 safe toggle
            fromWrap && fromWrap.classList.toggle('hidden', !isCustom);
            toWrap && toWrap.classList.toggle('hidden', !isCustom);
            toLabel && toLabel.classList.toggle('hidden', !isCustom);
            btnWrap && btnWrap.classList.toggle('hidden', !isCustom);

            // auto submit for preset
            if (!isCustom) {
                document.getElementById('searchForm').submit();
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\reports\ledger.blade.php ENDPATH**/ ?>