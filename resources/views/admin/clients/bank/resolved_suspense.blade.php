{{-- resources/views/admin/clients/index.blade.php --}}
@extends('layouts.super_admin')

@section('content')
<div class="container py-3">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Bank Suspense Entries</h1>
        </div>
        <a href="{{ route('clients.suspense') }}"
            class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#f472b6]
                                hover:shadow-[0_0_15px_#f472b6]
                                hover:scale-105
                                hover:-translate-y-1">
            <i class="fa-solid fa-arrow-left mr-1"></i>
        </a>
    </div>
    
    <form method="POST" action="{{ route('clients.suspense') }}" class="p-4 rounded-lg mb-4 flex gap-4 items-end">
        @csrf
        <div id="customFromWrap" class="">
            <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">From Date</label>
            <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" min="1900-01-01"
       max="2099-12-31" class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
        </div>
        <div id="customToWrap" class="">
            <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">To Date</label>
            <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" min="1900-01-01"
       max="2099-12-31" class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
        </div>
        <!-- <div>
            <label class="text-sm">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
        </div> -->
        <!-- <div>
            <label class="text-sm">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
        </div> -->
        <div class="flex gap-2">
            <button class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1">Search</button>
            <a href="{{ route('clients.suspense') }}" class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1">Reset</a>
        </div>

        <button type="button" id="bulkResolveBtn"
            class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1">
            Resolve Selected
        </button>

        
    </form>

    

    <!-- Table Card -->
    <div class="mt-5 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden group-block">
        <table class="min-w-full text-sm text-left">
            <!-- Header -->
            <thead class="sticky top-0 z-10 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md border-b border-cyan-500/20">
                <tr class="text-black-600 dark:text-gray-300">
                    <th class="px-4 py-2">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th class="px-4 py-2 font-bold">Date</th>
                    <th class="px-4 py-2 font-bold">Narration</th>
                    <th class="px-4 py-2 font-bold">Amount</th>
                    <th class="px-4 py-2 font-bold">Type</th>
                    <th class="px-4 py-2 font-bold">Reason</th>
                    <th class="px-4 py-2 font-bold">Remark</th>
                    <th class="px-4 py-2 font-bold">Resolved At</th>
                    <th class="px-4 py-2 font-bold">Action</th>
                </tr>
                <tr class="dark:bg-neutral-900">
                    <th></th>
                    <th><input class="searchInput w-full px-1 py-1"></th>
                    <th><input class="searchInput w-full px-1 py-1"></th>
                    <th>
                        <div class="flex gap-1">
                            <input type="number" class="amountFrom w-1/2 px-1 py-1 text-xs" placeholder="From">
                            <input type="number" class="amountTo w-1/2 px-1 py-1 text-xs" placeholder="To">
                        </div>
                    </th>
                    <th><input class="searchInput w-full px-1 py-1"></th>
                    <th><input class="searchInput w-full px-1 py-1"></th>
                    <th><input class="searchInput w-full px-1 py-1"></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <!-- Body -->
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                
                @forelse($transactions as $row)
                <tr class="group  hover:backdrop-blur-md hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80">
                    <td class="px-4 py-2 text-center">
                        <input type="checkbox" class="rowCheckbox" value="{{ $row->id }}">
                    </td>
                    <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black">{{ $row->txn_date }}</td>
                    <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black">
                        {{ $row->narration }}
                    </td>
                    <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black">
                        {{ number_format($row->amount, 2) }}
                    </td>
                    <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black text-center">
                        <span class="px-2 py-1 rounded text-xs
                                {{ $row->txn_type == 'Debit' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                            {{ $row->txn_type }}
                        </span>
                    </td>
                    
                    <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black text-yellow-500 text-xs font-medium">
                        ⚠ {{ $row->suspense_reason }}
                    </td>
                    <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black text-green-500">
                            {{ $row->resolution_remark }}
                    </td>
                    <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black">
                        {{ optional($row->resolved_at)->format('d-m-Y h:i A') }}
                    </td>
                    <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black text-center">
                        <button
                            class="text-green-600 hover:underline openResolveModal"
                            data-id="{{ $row->id }}">
                            Resolve
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center p-4 text-gray-500">
                        No suspense entries found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>

    </div>
</div>

<div id="resolveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-900 rounded-lg p-6 w-96">
        <h3 class="text-lg font-semibold mb-4">Resolve Suspense</h3>
        <input type="hidden" id="resolve_id">
        <div>
            <label class="text-sm">Remarks</label>
            <textarea id="resolve_remark" class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300 form-control w-full mt-1" rows="3"></textarea>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button id="closeModal" class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1">Cancel</button>
            <button id="submitResolve" class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1">Submit</button>
        </div>
    </div>
</div>

<style>
    .form-control {
        background: #fff;
        border: 1px solid #ccc;
        padding: 6px 10px;
        border-radius: 6px;
    }

    .dark .form-control {
        background: #1f2937;
        border-color: #374151;
        color: #fff;
    }
</style>
@push('scripts')
<script>
    // Open modal
    $(document).on('click', '.openResolveModal', function() {
        let id = $(this).data('id');
        $('#resolve_id').val(id);
        $('#resolve_remark').val('');
        $('#resolveModal').removeClass('hidden').addClass('flex');
    });

    // Close modal
    $('#closeModal').click(function() {
        $('#resolveModal').addClass('hidden').removeClass('flex');
    });

    // Submit resolve
    // $('#submitResolve').click(function() {
    //     let id = $('#resolve_id').val();
    //     let remark = $('#resolve_remark').val();
    //     if (!remark) {
    //         alert('Please enter remark');
    //         return;
    //     }
    //     $.post("{{ route('clients.resolveSuspense') }}", {
    //         _token: "{{ csrf_token() }}",
    //         txn_id: id,
    //         remark: remark
    //     }, function(res) {
    //         alert('Resolved successfully');
    //         location.reload();
    //     });
    // });

    $(document).on('keyup change', '.searchInput, .amountFrom, .amountTo', function () {
        let from = parseFloat($('.amountFrom').val()) || 0;
        let to = parseFloat($('.amountTo').val()) || Infinity;
        $('tbody tr').each(function () {
            let row = $(this);
            let show = true;
            // 🔍 Column text filter
            row.find('td').each(function (index) {
                let input = $('thead tr:eq(1) th').eq(index).find('.searchInput');
                if (input.length) {
                    let val = input.val().toLowerCase();
                    if (val) {
                        let text = $(this).text().toLowerCase();
                        if (!text.includes(val)) {
                            show = false;
                        }
                    }
                }
            });
            // 💰 Amount filter (column index 3)
            let amountText = row.find('td:eq(3)').text().replace(/,/g, '').trim();
            let amount = parseFloat(amountText) || 0;
            if (amount < from || amount > to) {
                show = false;
            }
            row.toggle(show);
        });
    });


    $('#selectAll').click(function () {
        $('.rowCheckbox').prop('checked', this.checked);
    });

    let selectedIds = [];
    let isBulk = false;

    // SINGLE resolve
    $(document).on('click', '.openResolveModal', function() {
        let id = $(this).data('id');

        selectedIds = [id];   // ✅ store as array
        isBulk = false;

        $('#resolve_remark').val('');
        $('#resolveModal').removeClass('hidden').addClass('flex');
    });

    // BULK resolve
    $('#bulkResolveBtn').click(function (e) {
        e.preventDefault(); // ⚠️ IMPORTANT (form submit stop)

        selectedIds = [];

        $('.rowCheckbox:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one row');
            return;
        }

        isBulk = true;

        $('#resolve_remark').val('');
        $('#resolveModal').removeClass('hidden').addClass('flex');
    });

    // FINAL submit (COMMON)
    $('#submitResolve').click(function () {

        let remark = $('#resolve_remark').val();

        if (!remark) {
            alert('Please enter remark');
            return;
        }

        $.post("{{ route('clients.updateRemark') }}", {
            _token: "{{ csrf_token() }}",
            txn_ids: selectedIds, // ✅ ALWAYS ARRAY
            remark: remark
        }, function (res) {
            alert('Resolved successfully');
            location.reload();
        });
    });

    $('.amountFrom, .amountTo').on('keyup change', function () {
        applyAmountFilter();
    });
    
    function applyAmountFilter() {
        let from = parseFloat($('.amountFrom').val()) || 0;
        let to = parseFloat($('.amountTo').val()) || Infinity;

        $('tbody tr').each(function () {

            let row = $(this);

            // get amount column (index 3)
            let amountText = row.find('td:eq(3)').text().replace(/,/g, '').trim();
            let amount = parseFloat(amountText) || 0;

            let show = true;

            if (amount < from || amount > to) {
                show = false;
            }

            // 🔥 also apply other search filters (important)
            row.toggle(show);
        });
    }
</script>
@endpush
@endsection