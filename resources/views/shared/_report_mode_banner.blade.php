{{--
    Sample data banner and Sample / My business switch.
    Shown only to clients, only when the sample company is enabled, and only on
    pages that resolved a report party (dashboard and reports).
--}}
@php
    $bannerUser = auth()->user();
    $showSwitch = \App\Support\Demo\ReportMode::switchVisibleFor($bannerUser) && isset($reportParty);
    $ownDataReady = $showSwitch && ($isDemoReport ?? false)
        ? app(\App\Services\Demo\ReportPartyResolver::class)->hasOwnData($bannerUser)
        : false;
@endphp

@if ($showSwitch)
    @if ($isDemoReport ?? false)
        <div class="mb-4 rounded-md border border-amber-400 bg-amber-50 px-4 py-3 text-amber-900 dark:border-amber-600 dark:bg-amber-950 dark:text-amber-100"
             role="status" aria-live="polite">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold">
                        <span class="mr-2 inline-block rounded bg-amber-500 px-2 py-0.5 text-xs font-bold uppercase tracking-wide text-white">Sample data</span>
                        You're viewing {{ \App\Support\Demo\DemoParty::name() }}, a fictional company.
                    </p>
                    <p class="mt-1 text-sm">
                        @if ($ownDataReady)
                            Your books are connected. Switch to My business to see your own figures.
                        @else
                            These figures are not yours. Your own reports appear here once your books are connected.
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('report-mode.update') }}">
                    @csrf
                    <input type="hidden" name="report_mode" value="{{ \App\Support\Demo\ReportMode::OWN }}">
                    <button type="submit"
                            class="rounded-md bg-amber-600 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                        Show my business
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="mb-3 flex justify-end">
            <form method="POST" action="{{ route('report-mode.update') }}">
                @csrf
                <input type="hidden" name="report_mode" value="{{ \App\Support\Demo\ReportMode::DEMO }}">
                <button type="submit"
                        class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                    View sample reports
                </button>
            </form>
        </div>
    @endif
@endif
