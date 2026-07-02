<style>
    
    /* ── BASE ── */
    .inputCell {
        background: white;
        border: 1px solid #d1d5db;
        color: #111827;
        padding: 6px 8px;
        font-size: 12px;
        width: 100%;
        min-width: 0;
        border-radius: 4px;
    }

    .dark .inputCell {
        background: #020617;
        border: 1px solid #374151;
        color: white;
    }

    .searchInput {
        background: white;
        border: 1px solid #d1d5db;
        color: #111827;
    }

    .dark .searchInput {
        background: #020617;
        border: 1px solid #374151;
        color: white;
    }

    #purchaseTable tbody tr:hover {
        background: #f3f4f6;
    }
    .sales-preview-table {
        table-layout: fixed;
    }

    .sales-preview-table th,
    .sales-preview-table td {
        vertical-align: middle;
        white-space: nowrap;
    }
    
    .sales-preview-table .col-date { width: 105px; }
    .sales-preview-table .col-reference { width: 100px; }
    .sales-preview-table .col-voucher { width: 100px; }
    .sales-preview-table .col-party { width: 210px; }
    .sales-preview-table .col-gstin { width: 138px; }
    .sales-preview-table .col-place { width: 140px; }
    .sales-preview-table .col-amount { width: 100px; }
    .sales-preview-table .col-status { width: 65px; }
    .sales-preview-table .col-action { width: 92px; }

    .sales-preview-table .searchInput {
        width: 100%;
        min-width: 0;
        padding: 5px 7px;
        border-radius: 4px;
        font-size: 12px;
    }

    .sales-preview-table .select2-container {
        min-width: 0;
        width: 100% !important;
    }

    .sales-preview-table .select2-container--default .select2-selection--single {
        min-height: 34px;
        height: 34px;
        display: flex;
        align-items: center;
    }

    .sales-preview-table .select2-container--default .select2-selection--single .select2-selection__rendered {
        width: 100%;
        padding-right: 28px;
        line-height: 32px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sales-preview-table .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px;
    }

    @media (max-width: 640px) {
        .sales-preview-table {
            font-size: 12px;
        }

        .sales-preview-table th,
        .sales-preview-table td {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }
    .dark #purchaseTable tbody tr:hover {
        background: #1f2937;
    }

    /* SELECT2 */
    .select2-container--default .select2-selection--single {
        background: #fff;
        border: 1px solid #d1d5db;
        color: #111827;
        height: 30px;
    }

    .select2-container--default .select2-selection__rendered {
        color: #111827;
    }

    .select2-container--default .select2-results__option {
        color: #111827;
        background: white;
    }

    .select2-container--default .select2-results__option--highlighted {
        background: #2563eb;
        color: white;
    }

    .select2-dropdown {
        background: white;
        border: 1px solid #d1d5db;
    }

    .dark .select2-container--default .select2-selection--single {
        background: #020617;
        border: 1px solid #374151;
        color: white;
    }

    .dark .select2-container--default .select2-results__option {
        background: #020617;
        color: white;
    }

    .dark .select2-container--default .select2-results__option--highlighted {
        background: #2563eb;
        color: white;
    }

    .dark .select2-dropdown {
        background: #020617;
        border: 1px solid #374151;
        color: white;
    }

    /* MODAL BASE */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 999;
        background: rgba(0, 0, 0, .65);
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        width: 95%;
        max-width: 1100px;
        max-height: 95vh;
        background: #fff;
        color: #111827;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .dark .modal-content {
        background: #1e293b;
        color: #e2e8f0;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .modal-header {
        border-color: #334155;
    }

    .modal-header h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .modal-body {
        padding: 16px;
        overflow-y: auto;
        flex: 1;
        max-height: calc(95vh - 120px);
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #64748b;
        border-radius: 10px;
    }

    .modal-footer {
        padding: 12px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .dark .modal-header,
    .dark .modal-footer {
        border-color: #334155;
    }

    .modal-content input,
    .modal-content select {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #111827;
        font-size: 13px;
    }

    .dark .modal-content input,
    .dark .modal-content select {
        background: #020617;
        border: 1px solid #334155;
        color: #e2e8f0;
    }

    .modal-content input:focus,
    .modal-content select:focus {
        border-color: #3b82f6;
        outline: none;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-group label {
        font-size: 12px;
        margin-bottom: 4px;
        display: block;
    }

    .dark .form-group label {
        color: #cbd5f5;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .close-btn {
        font-size: 18px;
        background: transparent;
        border: none;
        cursor: pointer;
        color: #6b7280;
    }

    .close-btn:hover {
        color: #ef4444;
    }

    .dark .close-btn {
        color: #94a3b8;
    }

    .submit-btn {
        background: #3b82f6;
        padding: 4px 12px;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        border: none;
    }

    .btn-cancel {
        background: #374151;
        padding: 4px 12px;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        border: none;
    }

    /* ══ RECEIPT MODAL ══ */
    #editModal.modal.show {
        align-items: flex-start;
        padding: 16px;
        overflow-y: hidden;
    }

    .receipt-wrapper {
        width: 95%;
        max-width: 1100px;
        max-height: 95vh;
        background: #fff;
        border-radius: 8px;
        overflow: auto;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .4);
        border: 1px solid #e2e8f0;
    }

    .dark #editModal input,
    .dark #editModal select,
    .dark #editModal textarea,
    .dark #editModal .receipt-input {
        background: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
    }

    .pending-issue-alert {
        margin: 0 8px 8px;
        padding: 8px 10px;
        border: 1px solid #fca5a5;
        border-left: 4px solid #dc2626;
        border-radius: 6px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 12px;
    }

    .pending-issue-title {
        font-weight: 700;
        margin-bottom: 4px;
    }

    .pending-issue-list {
        margin: 0;
        padding-left: 18px;
    }

    #editModal .pending-field-error,
    #editModal .pending-field-error + .select2 .select2-selection,
    #editModal .pending-field-error.select2-hidden-accessible + .select2 .select2-selection {
        border-color: #ef4444 !important;
        background: #fff7f7 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, .12) !important;
    }

    #editModal .receipt-table .pending-field-error,
    #editModal .receipt-table .pending-field-error + .select2 .select2-selection,
    #editModal .receipt-table .pending-field-error.select2-hidden-accessible + .select2 .select2-selection,
    #editModal .custom-slots-table .pending-field-error {
        border-color: #f87171 !important;
        background: #fffafa !important;
        box-shadow: inset 0 0 0 1px rgba(248, 113, 113, .35) !important;
    }


    #editModal .pending-field-error-row {
        background: #fff7f7;
    }

    .receipt-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 4px 8px;
        background: #fff;
    }

    .receipt-company {
        font-size: 12px;
        font-weight: 700;
        color: #000;
    }

    .receipt-subtitle {
        font-size: 8px;
        color: #000;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .receipt-close-btn {
        background: rgba(0, 0, 0, .1);
        border: none;
        color: #000;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .receipt-close-btn:hover {
        background: rgba(239, 68, 68, .15);
        color: #dc2626;
    }

    .receipt-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-bottom: 2px solid #e2e8f0;
    }

    .receipt-meta-block {
        padding: 4px 9px;
    }

    .receipt-meta-block:first-child {
        border-right: 1px solid #e2e8f0;
    }

    .receipt-block-title {
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #000;
        border-bottom: 1px dashed #e2e8f0;
    }

    .receipt-field-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 2px;
    }

    .receipt-field-row label {
        font-size: 11px;
        color: #000;
        width: 115px;
        flex-shrink: 0;
        text-align: right;
        padding-right: 6px;
    }

    .receipt-input {
        flex: 1;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 12px;
        color: #111827;
        width: 100%;
    }

    .receipt-input:focus {
        border-color: #3b82f6;
        background: #eff6ff;
        outline: none;
    }

    .receipt-items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e2e8f0;
    }

    .receipt-add-btn {
        font-size: 11px;
        background: #059669;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .receipt-add-btn:hover {
        background: #047857;
    }

    .receipt-table-wrap {
        max-height: 160px;
        overflow-y: auto;
    }

    .receipt-table-wrap::-webkit-scrollbar {
        width: 4px;
    }

    .receipt-table-wrap::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .receipt-table thead tr {
        background: #f1f5f9;
        border-bottom: 2px solid #e2e8f0;
        position: sticky;
        top: 0;
    }

    .receipt-table th {
        padding: 6px 8px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #000;
        white-space: nowrap;
    }

    .receipt-table th.col-num {
        text-align: right;
    }

    .receipt-table th.col-sr {
        width: 30px;
    }

    .receipt-table th.col-item {
        min-width: 180px;
    }

    .receipt-table th.col-num {
        width: 85px;
    }

    .receipt-table th.col-action {
        width: 36px;
    }

    .receipt-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .receipt-table tbody tr:hover {
        background: #f8fafc;
    }

    .receipt-table td {
        vertical-align: middle;
    }

    .receipt-table td.td-sr {
        text-align: center;
        font-size: 11px;
        color: #9ca3af;
        padding-left: 8px;
    }

    .receipt-table td input[type="text"],
    .receipt-table td input[type="number"] {
        width: 100%;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 3px;
        padding: 3px 5px;
        font-size: 12px;
        color: #111827;
    }

    .receipt-table td input:focus {
        border-color: #3b82f6;
        background: #eff6ff;
        outline: none;
    }

    .receipt-table td input[readonly] {
        background: #f8fafc;
        color: #374151;
        border-color: #e2e8f0;
        font-weight: 600;
    }

    .receipt-table td input[type="number"] {
        text-align: right;
    }

    .receipt-table td:last-child {
        text-align: center;
    }

    .receipt-subtotal-row td {
        padding: 5px 4px;
        font-size: 12px;
        color: #000;
        background: #f8fafc;
        border-top: 2px solid #e2e8f0;
    }

    .receipt-del-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #ef4444;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .receipt-del-btn:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .receipt-tax-summary {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 6px 9px;
        border-top: 2px dashed #e2e8f0;
        background: #f8fafc;
    }

    .tax-note {
        font-size: 10px;
        color: #000;
        font-style: italic;
        margin-top: 4px;
    }

    .tax-summary-right {
        min-width: 320px;
    }

    .tax-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2px 0;
        border-bottom: 1px solid #e2e8f0;
        font-size: 12px;
        color: #000;
        gap: 6px;
    }

    .tax-label {
        font-size: 11px;
        flex: 1;
    }

    .tax-value {
        font-weight: 600;
        font-size: 12px;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .grand-total-row {
        background: #1e40af;
        border-radius: 4px;
        padding: 2px 8px !important;
        margin-top: 4px;
        border-bottom: none !important;
    }

    .grand-total-row .tax-label,
    .grand-total-row .tax-value {
        color: #ffffff !important;
        font-size: 13px !important;
        font-weight: 700 !important;
    }

    .receipt-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 9px;
        border-top: 1px solid #e2e8f0;
        background: #fff;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }

    .receipt-footer-note {
        font-size: 10px;
        color: #000;
        font-style: italic;
    }

    .receipt-footer-actions {
        display: flex;
        gap: 10px;
    }

    /* ══ CUSTOM GST SLOTS TABLE ══ */
    .custom-slots-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .custom-slots-table th {
        background: #e0e7ff;
        color: #1e40af;
        padding: 0px 8px;
        text-align: left;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10px;
        border: 1px solid #c7d2fe;
    }

    .custom-slots-table td {
        padding: 0px 6px;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
    }

    .custom-slots-table .rate-badge {
        display: inline-block;
        background: #1e40af;
        color: #fff;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .custom-slots-table select {
        width: 100%;
        font-size: 11px;
        padding: 2px 4px;
        border: 1px solid #d1d5db;
        border-radius: 3px;
        background: #fff;
        color: #111827;
    }

    .custom-slots-table input[type="number"] {
        width: 100%;
        font-size: 11px;
        padding: 2px 4px;
        border: 1px solid #e2e8f0;
        border-radius: 3px;
        background: #f8fafc;
        color: #374151;
        font-weight: 600;
        text-align: right;
    }

    .custom-slots-table .zero-row {
        opacity: .4;
    }

    /* ══ VIEW MODAL STYLES ══ */
    .view-card {
        background: #1e293b;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 16px;
    }

    .view-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px 20px;
    }

    .view-grid label {
        font-size: 11px;
        color: #94a3b8;
    }

    .view-grid p {
        font-size: 13px;
        font-weight: 500;
        margin: 2px 0 0;
        color: #e2e8f0;
    }

    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        background: #f59e0b;
        color: white;
    }

    .view-totals {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }

    .view-totals .box {
        background: #020617;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }

    .view-totals span {
        font-size: 11px;
        color: #94a3b8;
    }

    .view-totals strong {
        display: block;
        font-size: 14px;
        margin-top: 3px;
    }

    .view-totals .highlight {
        background: #2563eb;
        color: white;
    }

    .section-title {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #cbd5f5;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .view-table {
        width: 100%;
        border-collapse: collapse;
    }

    .view-table th {
        font-size: 11px;
        background: #020617;
        padding: 8px;
        color: #94a3b8;
        text-align: left;
    }

    .view-table td {
        padding: 8px;
        border-bottom: 1px solid #1e293b;
        font-size: 12px;
    }

    .view-table td:nth-child(n+4) {
        text-align: right;
    }

    .view-table tbody tr:hover {
        background: #1e293b;
    }

    #no_item_section {
        border-top: 1px dashed #e2e8f0;
        margin-top: 10px;
        padding-top: 10px;
    }

    #no_item_section .receipt-field-row {
        max-width: 400px;
    }

    /* Select2 dropdown background fix */
    .select2-container--default .select2-results__option {
        background: #ffffff !important;
        color: #000000 !important;
    }

    .select2-container--default .select2-results__option--highlighted {
        background: #2563eb !important;
        /* blue highlight */
        color: #ffffff !important;
    }

    /* Selected item (top input box) */
    .select2-container--default .select2-selection--single {
        background: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #d1d5db !important;
    }

    /* Dropdown box */
    .select2-dropdown {
        background: #ffffff !important;
        color: #000000 !important;
    }

    /* FIX CUSTOM GST TABLE TEXT VISIBILITY */
.custom-slots-table td,
.custom-slots-table th {
    color: #111827 !important; /* dark text */
}

/* input fields */
.custom-slots-table input {
    color: #000 !important;
    background: #fff !important;
}

/* select dropdown */
.custom-slots-table select {
    color: #000 !important;
    background: #fff !important;
}

/* VERY IMPORTANT for plain text cells */
.custom-slots-table .slot-taxable {
    color: #000 !important;
    font-weight: 500;
}

#no_item_section th:nth-child(2),
#no_item_section td:nth-child(2){
    width:120px;
}

#no_item_section th:nth-child(3),
#no_item_section td:nth-child(3){
    width:180px;
}

#addNoItemRow{
    margin-top:10px;
    float:right;
}
.removeNoItem{
    background:none;
    border:none;
    color:#dc2626;
    cursor:pointer;
}
</style>