# Sample company for new clients

New clients see a fictional company, **Sample Traders Pvt Ltd**, on the dashboard
and in every financial report until their own books are synced. A
**Sample data / My business** switch lets them move between the two.

## How it stays separate from real data

- The sample company is one inactive `users` row (`DEMO_PARTY_ID`, `DEMO_PARTY_GUID`).
  Its report rows live under that id/GUID in `LedgerMaster`, `GroupMaster`,
  `YearMaster`, `VchHistory`, `StockItemMaster`, `ClosingStock`, `BalanceSheetData`.
  Every stored procedure already filters by party, so real reports never include it.
- `App\Services\Demo\ReportPartyResolver` is the only place that picks the party a
  report reads. Request input can never point a report at another company:
  clients get their own books or the sample; staff get only clients assigned to
  them (super admins: any client).
- The sample company is read-only: `BlockDemoPartyWrites` (web and api groups)
  refuses writes that target it, `Client`'s global scope hides it from staff
  lists and route binding, and it cannot log in or reset a password.
- Report filters (financial year, dates, selected cards) use separate session
  keys in sample mode, so switching never changes a client's real selections.
- Nothing is copied, moved or deleted when a client's own data arrives; the
  default simply changes to *My business*.

## Setup

1. Deploy the code with `DEMO_DATA_ENABLED=false` (no behaviour change).
2. Optional: add the `users.is_demo` column
   (`php artisan migrate --path=database/migrations/2026_10_22_000001_add_is_demo_to_users_table.php`
   or `database/sql/demo_sample_company.sql`).
3. `php artisan demo:provision --guid=<PartyGUID of the sample Tally company>`
   and copy the printed `DEMO_*` lines into `.env`; `php artisan config:clear`.
4. Create the fictional company in Tally (vouchers per the recommendation doc) and
   sync it with the normal sync agent under that party id and GUID.
5. `php artisan demo:verify` until every check passes.
6. Set `DEMO_DATA_ENABLED=true`.

## Behaviour

| Who | Default | Switch |
| --- | --- | --- |
| Client without own data (web) | Sample data | Yes |
| Client with own data (web) | My business | Yes |
| Mobile app | My business | App sends `report_mode=demo` (or header `X-Report-Mode: demo`) |
| Staff | Own / assigned client | No |

API report responses gain `report_context: { is_demo, company_name }`.
Sample exports are named `sample-…`, sheet titles start with `SAMPLE -`, the
company name reads "Sample Traders Pvt Ltd (sample)", and PDFs carry a
"SAMPLE DATA" watermark and notice.

## Tests

`tests/Feature/DemoSampleDataTest.php` runs on in-memory SQLite (no SQL Server needed).
