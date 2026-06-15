@extends('layouts.super_admin')
@section('title', 'Voucher History')

@section('content')
    @php
        // ---------- normalize ----------
        $payload = $data ?? ($resp['data'] ?? []);
        $rows = collect($rows ?? ($payload['rows'] ?? []));
        $meta = $resp['meta'] ?? [];
        $ledgerId = request('ledger_id');

        // ---------- helpers ----------
        $toFloat = function ($v) {
            if ($v === null || $v === '') {
                return 0.0;
            }
            return (float) str_replace(',', '', (string) $v);
        };

        // Indian format ##,##,###.00
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

        // Use values calculated by the service
        $totalDr = $payload['raw_total_dr'] ?? 0.0;
        $totalCr = $payload['raw_total_cr'] ?? 0.0;
        $openingBalance = $payload['raw_opening'] ?? 0.0;
        $closingBalance = $payload['raw_closing'] ?? 0.0;
        $diff = $totalDr - $totalCr;

        $openingSide = $openingBalance >= 0 ? 'Dr' : 'Cr';
        $closingSide = $closingBalance >= 0 ? 'Dr' : 'Cr';

        // header subtitle
        $periodText = function () use ($meta) {
            $f = $meta['from'] ?? request('from');
            $t = $meta['to'] ?? request('to');
            if ($f || $t) {
                return trim(($f ?: '—') . ' to ' . ($t ?: '—'));
            }
            return 'All time';
        };
    @endphp

    <div class="container py-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Voucher History</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Ledger #{{ $ledgerId ?: '—' }} • {{ $periodText() }}
                </p>
            </div>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium
                  bg-gray-200 text-gray-700 hover:bg-gray-300
                  dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Go Back
            </a>
        </div>

        {{-- Filters (GET for shareable URLs) --}}
        <form method="GET" action="{{ route('reports.voucher_history') }}"
            class="mt-4 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-4 flex flex-wrap items-end gap-3">
            <div style="display: none">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Party GUID</label>
                <input type="text" name="partyguid" value="{{ request('partyguid') }}"
                    class="w-64 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
            </div>

            <div style="display: none">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Ledger ID</label>
                <input type="number" name="ledger_id" value="{{ request('ledger_id') }}"
                    class="w-32 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
            </div>
            @php
                // pick current if provided; fall back to 'current_year'
                $rangeSel = request('range');
                if (!$rangeSel) {
                    $rangeSel = request('from') || request('to') ? 'custom' : 'current_year';
                }
            @endphp
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Date Range</label>
                <select id="rangeSel" name="range"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="current_year" {{ $rangeSel === 'current_year' ? 'selected' : '' }}>Current Year</option>
                    <option value="this_month" {{ $rangeSel === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $rangeSel === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="current_quarter"{{ $rangeSel === 'current_quarter' ? 'selected' : '' }}>Current Quarter
                    </option>
                    <option value="last_quarter" {{ $rangeSel === 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                    <option value="last_year" {{ $rangeSel === 'last_year' ? 'selected' : '' }}>Last Year</option>
                    <option value="custom" {{ $rangeSel === 'custom' ? 'selected' : '' }}>Custom Date</option>
                </select>
            </div>

            {{-- Custom date inputs (shown only when range=custom) --}}
            <div id="customFromWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from_custom" id="from_custom" value="{{ request('from') }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div id="customToLabel"
                class="pb-2 text-gray-500 dark:text-gray-400 {{ $rangeSel === 'custom' ? '' : 'hidden' }}">TO</div>
            <div id="customToWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to_custom" id="to_custom" value="{{ request('to') }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>

            {{-- Hidden fields actually submitted for presets (and also for custom via JS before submit) --}}
            <input type="hidden" name="from" id="from" value="{{ request('from') }}">
            <input type="hidden" name="to" id="to" value="{{ request('to') }}">

            <div class="ml-auto" style="display: none">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Search</label>
                <input id="vhSearch" type="text" placeholder="Voucher no / account / type…"
                    class="w-64 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
            </div>

            <div class="flex gap-2">
                <button class="rounded-md bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">Apply</button>
                <a href="{{ route('reports.voucher_history', ['ledger_id' => request('ledger_id'), 'partyguid' => request('partyguid')]) }}"
                    class="rounded-md bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">Reset</a>
            </div>
        </form>

        {{-- Balance Summary Cards - All in one row --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-3">
            {{-- Opening Balance --}}
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Opening Balance</div>
                <div
                    class="text-xl font-semibold {{ $openingSide === 'Dr' ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-600 dark:text-red-400' }}">
                    {{ $inr(abs($openingBalance)) }}
                </div>
                <div class="text-xs mt-1">
                    <span
                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                    {{ $openingSide === 'Dr' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                        {{ $openingSide }}
                    </span>
                </div>
            </div>

            {{-- Transaction Count --}}
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Entries</div>
                <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $rows->count() }}</div>
            </div>

            {{-- Total Debit --}}
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Debit</div>
                <div class="text-xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $inr($totalDr) }}</div>
            </div>

            {{-- Total Credit --}}
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Credit</div>
                <div class="text-xl font-semibold text-red-600 dark:text-red-400">{{ $inr($totalCr) }}</div>
            </div>

            {{-- Closing Balance --}}
            <div
                class="rounded-lg border-2 {{ $closingSide === 'Dr' ? 'border-emerald-200 dark:border-emerald-800' : 'border-red-200 dark:border-red-800' }} p-4 bg-gradient-to-r {{ $closingSide === 'Dr' ? 'from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20' : 'from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20' }}">
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Closing Balance</div>
                    <div
                        class="text-xl font-bold {{ $closingSide === 'Dr' ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-600 dark:text-red-400' }}">
                        {{ $inr(abs($closingBalance)) }}
                    </div>
                    <div class="text-xs mt-1">
                        <span
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $closingSide === 'Dr' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                            {{ $closingSide }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/40 sticky top-0 z-10">
                    <tr class="text-gray-600 dark:text-gray-300">
                        <th class="px-4 py-2 font-semibold">Date</th>
                        <th class="px-4 py-2 font-semibold">Voucher No</th>
                        <th class="px-4 py-2 font-semibold">Type</th>
                        <th class="px-4 py-2 font-semibold">Account</th>
                        <th class="px-4 py-2 font-semibold text-right">Dr</th>
                        <th class="px-4 py-2 font-semibold text-right">Cr</th>
                        <th class="px-4 py-2 font-semibold text-right">Running</th>
                        <th class="px-4 py-2 font-semibold text-center">Side</th>
                    </tr>
                </thead>
                <tbody id="vhBody" class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                    @forelse ($rows as $r)
                        @php
                            $date = \Carbon\Carbon::parse($r->strVchDate ?? ($r->VchDate ?? now()))->format('d-m-Y');
                            $vno = $r->vchNo ?? ($r->VoucherNo ?? '-');
                            $type = $r->vchType ?? ($r->VoucherType ?? '-');
                            $acc = $r->trnAccount ?? ($r->Particulars ?? '-');

                            $drRaw = $toFloat($r->DRAmount ?? ($r->DrAmount ?? 0));
                            $crRaw = $toFloat($r->CRAmount ?? ($r->CrAmount ?? 0));

                            // Use the calculated running balance from service
                            $run = $toFloat($r->calculatedRunningBalance ?? ($r->decRunningBalance ?? 0));

                            // For display, show absolute values
                            $drDisp = abs($drRaw);
                            $crDisp = abs($crRaw);

                            // Determine side based on running balance
                            $side = $run >= 0 ? 'Dr' : 'Cr';
                        @endphp
                        <tr
                            class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-900/40 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $date }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100 voucher-no">{{ $vno }}</td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300 voucher-type">{{ $type }}</td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300 voucher-acc">{{ $acc }}</td>

                            <td
                                class="px-4 py-2 text-right {{ $drDisp > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $drDisp > 0 ? $inr($drDisp) : '0.00' }}
                            </td>
                            <td
                                class="px-4 py-2 text-right {{ $crDisp > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $crDisp > 0 ? $inr($crDisp) : '0.00' }}
                            </td>
                            <td
                                class="px-4 py-2 text-right {{ $r->decRunningBalance < 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-700 dark:text-emerald-300' }}">
                                {{ $inr($r->decRunningBalance) }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if ($side === 'Dr')
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Dr</span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Cr</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">No vouchers
                                found.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-900/40">
                    <tr>
                        <td colspan="8" class="px-4 py-3">
                            <div class="flex flex-wrap items-center justify-end gap-6 text-sm">
                                <span class="text-gray-700 dark:text-gray-300">Total Dr:
                                    <strong
                                        class="text-emerald-700 dark:text-emerald-300">{{ $inr($totalDr) }}</strong></span>
                                <span class="text-gray-700 dark:text-gray-300">Total Cr:
                                    <strong class="text-red-600 dark:text-red-400">{{ $inr($totalCr) }}</strong></span>
                                <span class="text-gray-700 dark:text-gray-300">Diff:
                                    <strong
                                        class="{{ $diff >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $inr($diff) }}
                                    </strong></span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Rest of your JavaScript code remains the same --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const q = document.getElementById('vhSearch');
            if (!q) return;
            q.addEventListener('input', function() {
                const needle = (q.value || '').toLowerCase();
                document.querySelectorAll('#vhBody tr').forEach(tr => {
                    const txt = (
                        (tr.querySelector('.voucher-no')?.textContent || '') + ' ' +
                        (tr.querySelector('.voucher-type')?.textContent || '') + ' ' +
                        (tr.querySelector('.voucher-acc')?.textContent || '')
                    ).toLowerCase();
                    tr.style.display = txt.includes(needle) ? '' : 'none';
                });
            });
        });
    </script>

    {{-- Your existing date range JavaScript code --}}
    <script>
        // ... your existing JavaScript code for date ranges ...
    </script>
@endsection
