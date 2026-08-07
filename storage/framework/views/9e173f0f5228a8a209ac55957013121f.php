function validateBulkInvoiceFrontend(options = {}) {
    const isNoItem = $('#no_item_section').is(':visible') ||
        $('input[name="entry_mode"]:checked').val() === 'noitem';
    const fields = [
        ['#edit_party', 'Please select Party Name'],
        ['#edit_gst', 'Please enter GSTIN / UIN'],
        ['#edit_address', 'Please enter Address'],
        ['#edit_pincode', 'Please enter Pincode'],
        ['#edit_city', 'Please enter City'],
        ['#edit_invoice', `Please enter ${options.documentName || 'Invoice'} Number`],
        ['#edit_date', `Please select ${options.documentName || 'Invoice'} Date`],
        ['#edit_place', 'Please select Place Of Supply']
    ];

    if (!isNoItem && options.headerLedger) {
        fields.splice(5, 0, [options.headerLedger, `Please select ${options.ledgerName || 'Ledger'}`]);
    }

    for (const [selector, message] of fields) {
        const field = $(selector);
        if (!String(field.val() || '').trim()) {
            showToast(message, 'error');
            field.trigger('focus');
            return false;
        }
    }

    const rows = isNoItem ? $('#noItemBody tr') : $('#editItemsBody tr');
    if (!rows.length) {
        showToast(isNoItem ? `Please add at least one ${options.ledgerName || 'ledger'} row` : 'Please add at least one item row', 'error');
        return false;
    }

    let error = null;
    rows.each(function(index) {
        const row = $(this);
        const checks = isNoItem ? [
            ['.noitem-ledger', `Please select ${options.ledgerName || 'Ledger'} in row ${index + 1}`],
            ['.noitem-gst', `Please select GST % in row ${index + 1}`],
            ['.noitem-amount', `Please enter Amount in row ${index + 1}`]
        ] : [
            [options.itemSelector || '.item_name', `Please select Item / Particulars in row ${index + 1}`],
            [options.gstSelector || '.gst', `Please select GST % in row ${index + 1}`],
            [options.qtySelector || '.qty', `Please enter Quantity in row ${index + 1}`],
            [options.unitSelector || '.unit', `Please enter Unit in row ${index + 1}`],
            [options.rateSelector || '.rate', `Please enter Rate in row ${index + 1}`]
        ];

        for (const [selector, message] of checks) {
            const field = row.find(selector);
            if (!String(field.val() || '').trim()) {
                error = { field, message };
                return false;
            }
        }

        const amountField = row.find(isNoItem ? '.noitem-amount' : (options.amountSelector || '.amount'));
        if ((parseFloat(amountField.val()) || 0) <= 0) {
            error = {
                field: isNoItem ? amountField : row.find(options.rateSelector || '.rate'),
                message: `Amount in row ${index + 1} must be greater than 0`
            };
            return false;
        }

        if (!isNoItem) {
            const hsnField = row.find(options.hsnSelector || '.hsn');
            const hsn = String(hsnField.val() || '').trim();
            if (hsn && !/^\d{4}(?:\d{2})?(?:\d{2})?$/.test(hsn)) {
                error = {
                    field: hsnField,
                    message: `HSN Code in row ${index + 1} must contain exactly 4, 6, or 8 digits`
                };
                return false;
            }
        }
    });

    if (error) {
        showToast(error.message, 'error');
        error.field.trigger('focus');
        return false;
    }

    return true;
}<?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/bulkupload/shared/invoiceFrontendValidation.blade.php ENDPATH**/ ?>