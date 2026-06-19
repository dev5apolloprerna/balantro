<div id="editModal" class="modal" style="display: none;">
    <div class="receipt-wrapper">

        <input type="hidden" id="edit_id">

        <!-- HEADER -->
        <div class="receipt-head">
            <div>
                <div class="receipt-company">Journal Entry</div>
                <div class="receipt-subtitle">Voucher</div>
            </div>
            <button class="receipt-close-btn" onclick="closeModal()">✕</button>
        </div>

        <!-- FORM -->
        <div class="receipt-meta-grid">
            <div class="flex gap-4">

                <div class="flex items-center w-1/2">
                    <label class="w-32">Journal No</label>
                    <input type="text" id="edit_journal_no" class="receipt-input">
                </div>

                <div class="flex items-center w-1/2">
                    <label class="w-20">Date</label>
                    <input type="date" id="edit_date" class="receipt-input">
                </div>

            </div>
        </div>

        <!-- ITEMS -->
        <div>

            <div class="receipt-items-header">
                <!-- <span>Journal Entries</span> -->
                 <span></span>
                <button onclick="addRow()" class="receipt-add-btn">+ Add Row</button>
            </div>

            <div class="receipt-table-wrap">
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ledger</th>
                            <th>Dr</th>
                            <th>Cr</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody id="itemsBody"></tbody>

                    <tfoot>
                        <tr>
                            <td colspan="2">Total</td>
                            <td id="totalDr">0.00</td>
                            <td id="totalCr">0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="receipt-meta-grid">
                <div class="receipt-meta-block">
                    <div class="receipt-field-row">
                        <label>Narration</label>
                        <!-- <input type="text" id="edit_narration" class="receipt-input"> -->
                        <textarea id="edit_narration" class="receipt-input" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div style="padding:10px 18px; font-size:12px; color:#6b7280;">
                Note: Debit & Credit must be equal
            </div>
        </div>

        <!-- FOOTER -->
        <div class="receipt-footer">
            <button onclick="closeModal()" class="btn-cancel">Close</button>
            <button onclick="updateJournal()" class="submit-btn">Update</button>
        </div>

    </div>
</div>