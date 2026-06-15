
<div id="ledgerModal" class="modal">
    <div class="modal-content">
        <!-- HEADER -->
        <div class="modal-header">
            <h3>Create Ledger</h3>
            <button type="button" class="close-btn" onclick="closeLedgerModal()">✕</button>
        </div>
        
        <!-- BODY -->
        <div class="modal-body">
            <form id="ledgerForm">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="Name">
                    </div>
                    <div class="form-group">
                        <label>Parent</label>
                        <select name="Parent">
                            <option>Select Parent</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->strParents }}">{{ $parent->strParents }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mailing Name</label>
                        <input type="text" name="MailingName">
                    </div>
                    <div class="form-group">
                        <label>Address Line 1</label>
                        <input type="text" name="AddressLine1">
                    </div>
                    <div class="form-group">
                        <label>Address Line 2</label>
                        <input type="text" name="AddressLine2">
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="City">
                    </div>
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="Pincode">
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <select id="State" class="inputCell">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                            <option value="{{$state}}">{{$state}}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="Country">
                    </div>
                    <div class="form-group">
                        <label>GST No</label>
                        <input type="text" name="GstNo">
                    </div>
                    <div class="form-group">
                        <label>GST Registration Type</label>
                        <select name="GstRegistrationType">
                            <option value="">Select</option>
                            <option value="Regular">Regular</option>
                            <option value="Composition">Composition</option>
                            <option value="Unregistered">Unregistered</option>
                            <option value="Casual Taxable">Casual Taxable</option>
                            <option value="Non-resident Taxable">Non-resident Taxable</option>
                            <option value="Input Service Distributor">Input Service Distributor</option>
                            <option value="Special Economic Zone">Special Economic Zone</option>
                            <option value="E-commerce Operators">E-commerce Operators</option>
                            <option value="Tax Deduction at Source">Tax Deduction at Source</option>
                            <option value="TCS Collector">TCS Collector</option>
                            <option value="Voluntary Registration">Voluntary Registration</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button onclick="closeLedgerModal()" class="btn-cancel">Cancel</button>
            <button type="submit" form="ledgerForm" class="submit-btn">Save Ledger</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     VIEW MODAL  (read-only)
══════════════════════════════════════════════════════ --}}
<div id="viewModal" class="modal">
    <div class="modal-content" style="width:780px;">
        <div class="modal-header">
            <h3>View Sales</h3>
            <button type="button" class="close-btn" onclick="closeViewModal()">✕</button>
        </div>
        <div class="modal-body">

            <!-- HEADER SUMMARY -->
            <div class="view-card">
                <div class="view-grid">
                    <div><label>Invoice No</label>
                        <p id="v_invoice"></p>
                    </div>
                    <div><label>Date</label>
                        <p id="v_date"></p>
                    </div>
                    <div><label>Voucher Type</label>
                        <p id="v_voucher"></p>
                    </div>
                    <div><label>Party Name</label>
                        <p id="v_party"></p>
                    </div>
                    <div><label>GST No</label>
                        <p id="v_gst"></p>
                    </div>
                    <div><label>Place of Supply</label>
                        <p id="v_place"></p>
                    </div>
                    <div><label>Sales Ledger</label>
                        <p id="v_ledger"></p>
                    </div>
                    <div><label>Status</label>
                        <p id="v_status" class="status-badge"></p>
                    </div>
                </div>
            </div>

            <!-- TOTALS -->
            <div class="view-totals">
                <div class="box">
                    <span>Amount</span>
                    <strong id="v_amount"></strong>
                </div>
                <div class="box">
                    <span>SGST</span>
                    <strong id="v_sgst"></strong>
                </div>
                <div class="box">
                    <span>CGST</span>
                    <strong id="v_cgst"></strong>
                </div>
                <div class="box">
                    <span>IGST</span>
                    <strong id="v_igst"></strong>
                </div>
                <div class="box highlight">
                    <span>Total</span>
                    <strong id="v_total"></strong>
                </div>
            </div>

            <!-- ITEMS -->
            <div id="v_items_section" class="mt-4">
                <div class="section-title">Item Details</div>

                <div class="table-wrapper">
                    <table class="view-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>SGST</th>
                                <th>CGST</th>
                                <th>IGST</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="v_items_body"></tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button onclick="closeViewModal()" class="btn-cancel">Close</button>
        </div>
    </div>
</div>