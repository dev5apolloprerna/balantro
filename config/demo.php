<?php

/*
|--------------------------------------------------------------------------
| Sample (demo) company shown to new clients
|--------------------------------------------------------------------------
|
| The sample company is ONE real, read-only party: an inactive `users` row
| whose id / guid key its own rows in LedgerMaster, GroupMaster, YearMaster,
| VchHistory, StockItemMaster, ClosingStock and BalanceSheetData. Those rows
| are loaded by the normal Tally sync from a fictional Tally company.
|
| Because the demo rows live under their own iPartyId / PartyGUID, every
| existing stored procedure and query keeps excluding them from real
| clients without any extra filter. Nothing here ever copies demo rows into
| a real client's party.
|
| Provision the row with:  php artisan demo:provision
| Check the figures with:  php artisan demo:verify
*/

return [

    // Master switch. When false the application behaves exactly as before.
    'enabled' => (bool) env('DEMO_DATA_ENABLED', false),

    // users.id of the sample company (printed by demo:provision).
    'party_id' => (int) env('DEMO_PARTY_ID', 0),

    // users.guid of the sample company. Must be a UUID because the report
    // APIs validate `partyguid` as `uuid`, and must equal the PartyGUID the
    // Tally sync writes for the sample company.
    'party_guid' => env('DEMO_PARTY_GUID'),

    // Name shown in banners, report titles and exports.
    'company_name' => env('DEMO_COMPANY_NAME', 'Sample Traders Pvt Ltd'),

    // Login e-mail of the inactive demo row. Uses the reserved .invalid TLD
    // so no mail can ever be delivered for it.
    'email' => env('DEMO_PARTY_EMAIL', 'sample-company@balantro.invalid'),

    // How long "does this client have their own data yet?" is cached.
    'own_data_cache_seconds' => (int) env('DEMO_OWN_DATA_CACHE_SECONDS', 300),

    /*
    | Figures the sample vouchers must produce (used by `demo:verify`).
    | Amounts in rupees. Update these whenever the sample company changes.
    */
    'reconciliation' => [
        'from' => env('DEMO_VERIFY_FROM', '2025-04-01'),
        'to' => env('DEMO_VERIFY_TO', '2026-03-31'),
        'tolerance' => 1.0,
        'targets' => [
            'sales' => 4800000,
            'purchases' => 3360000,
            'opening_stock' => 400000,
            'closing_stock' => 520000,
            'direct_expenses' => 180000,
            'indirect_incomes' => 30000,
            'indirect_expenses' => 840000,
            'gross_profit' => 1380000,
            'net_profit' => 570000,
        ],
    ],
];
