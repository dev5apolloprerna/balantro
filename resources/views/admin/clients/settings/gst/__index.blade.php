@extends('layouts.super_admin')

@section('content')

<div class="container mx-auto">

    <form action="{{ route('clients.GstSettingupdate') }}" method="POST">
        @csrf
        <input type="hidden" name="guid" value="{{ $user->guid }}">
        <div class="rounded-2xl p-6 shadow-sm border border-cyan-400/10 bg-white/5 backdrop-blur-xl">

            <h2 class="text-2xl font-bold mb-6 text-neutral-900 dark:text-white">
                GST Settings
                <button type="button"
                    id="btnAddMapping"
                    class="bg-cyan-600 text-white px-4 py-2 rounded">
                + Add Mapping
            </button>
            </h2>
            
            {{-- Settings --}}
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- <label class="flex items-center gap-3">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           {{ isset($setting) && $setting->IsActive ? 'checked' : '' }}>
                    <span class="text-neutral-900 dark:text-white">
                        Enable GST
                    </span>
                </label> -->
                <label class="flex items-center gap-3">
                    <input type="checkbox"
                           id="is_item_wise"
                           name="is_item_wise"
                           value="1"
                           {{ isset($setting) && $setting->IsItemWise ? 'checked' : '' }}>
                    <span class="text-neutral-900 dark:text-white">
                        With Item Wise GST
                    </span>
                </label>

            </div>
            {{-- Ledger Cards --}}
            <div class="space-y-4">
                @include('admin.clients.settings.gst.partials.card',[
                    'title'=>'Sales Accounts',
                    'ledgers'=>$salesLedgers,
                    'cgstLedgers'=>$cgstLedgers,
                    'sgstLedgers'=>$sgstLedgers,
                    'igstLedgers'=>$igstLedgers
                ])
                @include('admin.clients.settings.gst.partials.card',[
                    'title'=>'Purchase Accounts',
                    'ledgers'=>$purchaseLedgers,
                    'cgstLedgers'=>$cgstLedgers,
                    'sgstLedgers'=>$sgstLedgers,
                    'igstLedgers'=>$igstLedgers
                ])
                @include('admin.clients.settings.gst.partials.card',[
                    'title'=>'Direct Income',
                    'ledgers'=>$directIncometLedgers,
                    'cgstLedgers'=>$cgstLedgers,
                    'sgstLedgers'=>$sgstLedgers,
                    'igstLedgers'=>$igstLedgers
                ])
                @include('admin.clients.settings.gst.partials.card',[
                    'title'=>'Direct Expenses',
                    'ledgers'=>$directExpensesLedgers,
                    'cgstLedgers'=>$cgstLedgers,
                    'sgstLedgers'=>$sgstLedgers,
                    'igstLedgers'=>$igstLedgers
                ])
                @include('admin.clients.settings.gst.partials.card',[
                    'title'=>'Indirect Income',
                    'ledgers'=>$indeirectIncometLedgers,
                    'cgstLedgers'=>$cgstLedgers,
                    'sgstLedgers'=>$sgstLedgers,
                    'igstLedgers'=>$igstLedgers
                ])
                @include('admin.clients.settings.gst.partials.card',[
                    'title'=>'Indirect Expenses',
                    'ledgers'=>$indeirectExpensesLedgers,
                    'cgstLedgers'=>$cgstLedgers,
                    'sgstLedgers'=>$sgstLedgers,
                    'igstLedgers'=>$igstLedgers
                ])
            </div>
            {{-- Item Wise --}}
            <div id="itemSection"
                class="mt-8 {{ isset($setting) && $setting->IsItemWise ? '' : 'hidden' }}">
                <div class="rounded-xl border border-cyan-500/20">
                    <div class="bg-cyan-600 text-white px-4 py-3 rounded-t-xl">
                        Item Wise GST Mapping
                    </div>
                    <div class="overflow-auto">
                        <table class="w-full">
                            <thead class="bg-slate-700 text-white">
                                <tr>
                                    <th class="p-3 text-left">Item</th>
                                    <th class="p-3">CGST</th>
                                    <th class="p-3">SGST</th>
                                    <th class="p-3">IGST</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($stockItems as $item)
                                <tr class="border-b border-gray-700">
                                    <td class="p-3">
                                        {{ $item->strItemName }}
                                        <input type="hidden"
                                            name="item_ids[]"
                                            value="{{ $item->iStockIdtemId }}">
                                    </td>
                                    <td>
                                        <select name="item_cgst[{{ $item->iStockIdtemId }}]"
                                            class="w-full rounded border bg-neutral-800 text-white">
                                            <option value="">Select</option>
                                            @foreach($cgstLedgers as $gst)
                                                <option value="{{ $gst->iLedgerId }}" {{ $item->CGSTLedgerId == $gst->iLedgerId ? 'selected' : '' }}>
                                                    {{ $gst->strCustomerName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="item_sgst[{{ $item->iStockIdtemId }}]"
                                            class="w-full rounded border bg-neutral-800 text-white">
                                            <option value="">Select</option>
                                            @foreach($sgstLedgers as $gst)
                                                <option value="{{ $gst->iLedgerId }}" {{ $item->SGSTLedgerId == $gst->iLedgerId ? 'selected' : '' }}>
                                                    {{ $gst->strCustomerName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="item_igst[{{ $item->iStockIdtemId }}]"
                                            class="w-full rounded border bg-neutral-800 text-white">
                                            <option value="">Select</option>
                                            @foreach($igstLedgers as $gst)
                                                <option value="{{ $gst->iLedgerId }}" {{ $item->IGSTLedgerId == $gst->iLedgerId ? 'selected' : '' }}>
                                                    {{ $gst->strCustomerName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-8">
                <button type="submit"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg">
                    Save Settings
                </button>
            </div>
        </div>
    </form>
</div>

<div id="mappingModal" class="hidden fixed inset-0 bg-black/50 z-50">

    <div class="bg-white rounded-xl w-2/4 mx-auto mt-20 p-6">

        <h3 class="text-lg font-bold mb-4">
            Add GST Mapping
        </h3>

        <div class="grid grid-cols-2 gap-4">

            <div>
                <label>Ledger</label>

                <select name="ledger_id">
                    <option value="">Select Ledger</option>

                    @foreach($availableLedgers as $ledger)
                        <option value="{{ $ledger->iLedgerId }}">
                            {{ $ledger->strCustomerName }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div>
                <label>CGST Ledger</label>

                <select name="cgst_id">
                    <option value="">Select</option>

                    @foreach($cgstLedgers as $ledger)
                        <option value="{{ $ledger->iLedgerId }}">
                            {{ $ledger->strCustomerName }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div>
                <label>SGST Ledger</label>

                <select name="sgst_id">
                    <option value="">Select</option>

                    @foreach($sgstLedgers as $ledger)
                        <option value="{{ $ledger->iLedgerId }}">
                            {{ $ledger->strCustomerName }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div>
                <label>IGST Ledger</label>

                <select name="igst_id">
                    <option value="">Select</option>

                    @foreach($igstLedgers as $ledger)
                        <option value="{{ $ledger->iLedgerId }}">
                            {{ $ledger->strCustomerName }}
                        </option>
                    @endforeach

                </select>
            </div>

        </div>

        <div class="mt-4 text-right">

            <button type="button"
                    class="bg-gray-500 text-white px-4 py-2 rounded">
                Cancel
            </button>

            <button type="submit"
                    class="bg-cyan-600 text-white px-4 py-2 rounded">
                Add
            </button>

        </div>

    </div>

</div>
@endsection

@push('scripts')
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
</script>
@endpush