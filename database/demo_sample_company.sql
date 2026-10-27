/*
  Sample company: SQL Server objects (alternative to the Laravel migration).

  1. Adds users.is_demo.
  2. Read-only checks to run after the sample Tally company has been synced.

  The sample company's report data (LedgerMaster, GroupMaster, YearMaster,
  VchHistory, StockItemMaster, ClosingStock, BalanceSheetData) is NOT inserted
  here. Load it through the normal Tally sync from the fictional company so
  running balances and stock are computed exactly as for real clients.
  Never insert sample rows under a real client's iPartyId.
*/

IF COL_LENGTH('dbo.users', 'is_demo') IS NULL
BEGIN
    ALTER TABLE dbo.users ADD is_demo BIT NOT NULL CONSTRAINT DF_users_is_demo DEFAULT (0);
END
GO

/* Replace with your DEMO_PARTY_ID / DEMO_PARTY_GUID before running the checks. */
DECLARE @DemoPartyId INT = 0;
DECLARE @DemoGuid NVARCHAR(64) = N'00000000-0000-0000-0000-000000000000';

/* Must return 0 rows: sample GUID paired with another party. */
SELECT TOP 10 iPartyId, PartyGUID FROM dbo.VchHistory
WHERE PartyGUID = @DemoGuid AND iPartyId <> @DemoPartyId;

/* Must return 0 rows: sample company assigned to staff. */
SELECT 'clients_managers' AS pivot_table, COUNT(*) AS n FROM dbo.clients_managers WHERE client_id = @DemoPartyId
UNION ALL SELECT 'clients_supervisors', COUNT(*) FROM dbo.clients_supervisors WHERE client_id = @DemoPartyId
UNION ALL SELECT 'clients_data_entry_operators', COUNT(*) FROM dbo.clients_data_entry_operators WHERE client_id = @DemoPartyId;

/* Row counts loaded for the sample company. */
SELECT
    (SELECT COUNT(*) FROM dbo.YearMaster   WHERE iPartyId = @DemoPartyId) AS years,
    (SELECT COUNT(*) FROM dbo.GroupMaster  WHERE iPartyId = @DemoPartyId) AS groups,
    (SELECT COUNT(*) FROM dbo.LedgerMaster WHERE iPartyId = @DemoPartyId) AS ledgers,
    (SELECT COUNT(*) FROM dbo.VchHistory   WHERE iPartyId = @DemoPartyId) AS vouchers;
GO
