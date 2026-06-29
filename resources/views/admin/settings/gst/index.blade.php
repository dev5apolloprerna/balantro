@extends('layouts.super_admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="rounded-3xl border border-cyan-400/20 bg-white shadow-xl shadow-cyan-950/5 dark:bg-neutral-900/90 dark:shadow-black/20">
        <div class="rounded-t-3xl border-b border-neutral-200 bg-gradient-to-r from-cyan-50 via-white to-blue-50 px-6 py-7 dark:border-neutral-800 dark:from-cyan-950/40 dark:via-neutral-900 dark:to-blue-950/30">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-cyan-600 dark:text-cyan-300">
                        Tax Configuration
                    </p>
                    <h2 class="mt-2 text-2xl font-bold text-neutral-950 dark:text-white">
                        GST Ledger Mapping
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm text-neutral-600 dark:text-neutral-300">
                        Select the default ledgers used for CGST, SGST and IGST posting during transaction processing.
                    </p>
                </div>
                <div class="rounded-2xl border border-cyan-200 bg-white/80 px-4 py-3 text-sm font-semibold text-cyan-700 shadow-sm dark:border-cyan-500/30 dark:bg-neutral-900/70 dark:text-cyan-200">
                    <span class="mr-2 inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                    Mapping Model
                </div>
            </div>
        </div>

        <form action="{{ route('gst.setting.update') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                {{-- CGST --}}
                <div class="rounded-2xl border border-neutral-200 bg-neutral-50/80 p-5 transition hover:-translate-y-1 hover:border-cyan-300 hover:shadow-lg hover:shadow-cyan-950/10 dark:border-neutral-800 dark:bg-neutral-950/50 dark:hover:border-cyan-500/50">
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            <label for="cgst_ledger_id" class="block text-base font-semibold text-neutral-900 dark:text-white">
                                CGST Ledger
                            </label>
                            <p class="mt-1 text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                                Central GST collections
                            </p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-xs font-bold text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-200">
                            CGST
                        </div>
                    </div>

                    <select id="cgst_ledger_id" name="cgst_ledger_id"
                        class="gst-ledger-select w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-neutral-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                        data-placeholder="Select CGST Ledger">
                        <option value="">Select CGST Ledger</option>
                        @foreach($cgstLedgers as $ledger)
                            <option value="{{ $ledger->iLedgerId }}" {{ isset($setting) && $setting->CGSTLedgerId == $ledger->iLedgerId ? 'selected' : '' }}>
                                {{ $ledger->strCustomerName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SGST --}}
                <div class="rounded-2xl border border-neutral-200 bg-neutral-50/80 p-5 transition hover:-translate-y-1 hover:border-cyan-300 hover:shadow-lg hover:shadow-cyan-950/10 dark:border-neutral-800 dark:bg-neutral-950/50 dark:hover:border-cyan-500/50">
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            <label for="sgst_ledger_id" class="block text-base font-semibold text-neutral-900 dark:text-white">
                                SGST Ledger
                            </label>
                            <p class="mt-1 text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                                State GST collections
                            </p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-xs font-bold text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-200">
                            SGST
                        </div>
                    </div>

                    <select id="sgst_ledger_id" name="sgst_ledger_id"
                        class="gst-ledger-select w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-neutral-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                        data-placeholder="Select SGST Ledger">
                        <option value="">Select SGST Ledger</option>
                        @foreach($sgstLedgers as $ledger)
                            <option value="{{ $ledger->iLedgerId }}" {{ isset($setting) && $setting->SGSTLedgerId == $ledger->iLedgerId ? 'selected' : '' }}>
                                {{ $ledger->strCustomerName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- IGST --}}
                <div class="rounded-2xl border border-neutral-200 bg-neutral-50/80 p-5 transition hover:-translate-y-1 hover:border-cyan-300 hover:shadow-lg hover:shadow-cyan-950/10 dark:border-neutral-800 dark:bg-neutral-950/50 dark:hover:border-cyan-500/50">
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            <label for="igst_ledger_id" class="block text-base font-semibold text-neutral-900 dark:text-white">
                                IGST Ledger
                            </label>
                            <p class="mt-1 text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                                Integrated GST collections
                            </p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-xs font-bold text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-200">
                            IGST
                        </div>
                    </div>

                    <select id="igst_ledger_id" name="igst_ledger_id"
                        class="gst-ledger-select w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-neutral-900 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                        data-placeholder="Select IGST Ledger">
                        <option value="">Select IGST Ledger</option>
                        @foreach($igstLedgers as $ledger)
                            <option value="{{ $ledger->iLedgerId }}" {{ isset($setting) && $setting->IGSTLedgerId == $ledger->iLedgerId ? 'selected' : '' }}>
                                {{ $ledger->strCustomerName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-4 rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-950/60 md:flex-row md:items-center md:justify-between">
                <label class="inline-flex items-center gap-3">
                    <input type="checkbox"
                        name="is_active"
                        value="1"
                        {{ isset($setting) && $setting->IsActive ? 'checked' : '' }}
                         class="h-5 w-5 rounded border-neutral-300 text-cyan-600 focus:ring-cyan-500 dark:border-neutral-600">

                    <span>
                        <span class="block font-semibold text-neutral-900 dark:text-white">
                            Enable GST
                        </span>
                        <span class="block text-sm text-neutral-500 dark:text-neutral-400">
                            Apply this GST mapping during transaction processing.
                        </span>
                    </span>
                </label>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-cyan-600/20 transition hover:-translate-y-0.5 hover:bg-cyan-500 hover:shadow-cyan-500/30 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2 dark:focus:ring-offset-neutral-950">
                    Save GST Mapping
                </button>
            </div>

        </form>

    </div>

</div>

@endsection