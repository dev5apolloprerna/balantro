<div class="accordion-card rounded-xl border border-cyan-500/20 overflow-hidden">
    <div class="accordion-btn cursor-pointer bg-cyan-600 text-white px-4 py-3 flex justify-between items-center">
        <span class="font-semibold">
            {{ $title }}
        </span>
        <span>▼</span>
    </div>
    <div class="accordion-body hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-700 text-white">
                    <tr>
                        <th class="p-3 text-left">Ledger</th>
                        <th class="p-3 text-center">CGST</th>
                        <th class="p-3 text-center">SGST</th>
                        <th class="p-3 text-center">IGST</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($ledgers as $ledger)
                    <tr class="border-b border-gray-700 hover:bg-cyan-500/5">
                        <td class="p-3 text-neutral-900 dark:text-white">
                            {{ $ledger->strCustomerName }}
                            <input type="hidden"
                                name="ledger_ids[]"
                                value="{{ $ledger->iLedgerId }}">
                        </td>
                        <td class="p-2">
                            <select
                                name="ledger_cgst[{{ $ledger->iLedgerId }}]"
                                class="w-full rounded border border-gray-600 bg-white dark:bg-neutral-800 dark:text-white">
                                <option value="">Select</option>
                                @foreach($cgstLedgers as $gst)
                                    <option value="{{ $gst->iLedgerId }}" {{ $ledger->CGSTLedgerId == $gst->iLedgerId ? 'selected' : '' }}>
                                        {{ $gst->strCustomerName }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-2">
                            <select
                                name="ledger_sgst[{{ $ledger->iLedgerId }}]"
                                class="w-full rounded border border-gray-600 bg-white dark:bg-neutral-800 dark:text-white">
                                <option value="">Select</option>
                                @foreach($sgstLedgers as $gst)
                                    <option value="{{ $gst->iLedgerId }}" {{ $ledger->SGSTLedgerId == $gst->iLedgerId ? 'selected' : '' }}>
                                        {{ $gst->strCustomerName }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-2">
                            <select
                                name="ledger_igst[{{ $ledger->iLedgerId }}]"
                                class="w-full rounded border border-gray-600 bg-white dark:bg-neutral-800 dark:text-white">
                                <option value="">Select</option>
                                @foreach($igstLedgers as $gst)
                                    <option value="{{ $gst->iLedgerId }}" {{ $ledger->IGSTLedgerId == $gst->iLedgerId ? 'selected' : '' }}>
                                        {{ $gst->strCustomerName }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-400">
                            No Ledger Found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>