/*
  Self-registration with SMS OTP: SQL Server objects
  (same as migration 2026_10_27_000001_create_registration_requests_table.php).
*/

IF OBJECT_ID('dbo.registration_requests', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.registration_requests (
        id                  BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        name                NVARCHAR(255) NOT NULL,
        email               NVARCHAR(255) NOT NULL,
        mobile              NVARCHAR(10)  NOT NULL,
        token_hash          NVARCHAR(64)  NOT NULL,
        otp_hash            NVARCHAR(255) NULL,
        otp_expires_at      DATETIME NULL,
        otp_attempts        SMALLINT NOT NULL CONSTRAINT DF_regreq_attempts DEFAULT (0),
        otp_sends           SMALLINT NOT NULL CONSTRAINT DF_regreq_sends DEFAULT (0),
        last_sent_at        DATETIME NULL,
        locked_until        DATETIME NULL,
        ip                  NVARCHAR(45) NULL,
        created_at          DATETIME NULL,
        updated_at          DATETIME NULL,
        CONSTRAINT UQ_registration_requests_email UNIQUE (email)
    );
    CREATE INDEX IX_registration_requests_mobile ON dbo.registration_requests (mobile);
END
GO

-- Tables created by the first version of this script had password columns.
IF COL_LENGTH('dbo.registration_requests', 'password_hash') IS NOT NULL
    ALTER TABLE dbo.registration_requests DROP COLUMN password_hash;
IF COL_LENGTH('dbo.registration_requests', 'password_emailed_at') IS NOT NULL
    ALTER TABLE dbo.registration_requests DROP COLUMN password_emailed_at;
GO

IF COL_LENGTH('dbo.profiles', 'mobile_verified_at') IS NULL
    ALTER TABLE dbo.profiles ADD mobile_verified_at DATETIME NULL;
GO
