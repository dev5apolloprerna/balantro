<div class="dashboard-main-body">
    
    @if (($active_tab ?? 'financial') === 'financial')
        @include('client_dashboard.financial_dashboard')
    @else
        @include('client_dashboard.document_count')
    @endif
</div>
