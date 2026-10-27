{{-- Printed on every PDF page when the report is for the sample company. --}}
@if (!empty($isDemoReport))
    <div style="position: fixed; top: 35%; left: 0; right: 0; text-align: center; font-size: 64px; font-weight: bold; color: #f59e0b; opacity: 0.15; transform: rotate(-30deg); z-index: -1;">
        SAMPLE DATA
    </div>
    <div style="border: 1px solid #f59e0b; background: #fffbeb; color: #92400e; padding: 6px 10px; margin-bottom: 10px; font-size: 11px; font-weight: bold;">
        SAMPLE DATA &ndash; NOT REAL FIGURES. {{ \App\Support\Demo\DemoParty::name() }} is a fictional company.
    </div>
@endif
