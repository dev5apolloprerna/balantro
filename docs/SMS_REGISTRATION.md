# Sign-up with mobile OTP

## How it works

1. **Sign-up form** (`/register`): Full Name, Email, Mobile. There is no password field.
2. **On submit** (`RegisterController@register` → `SelfRegistrationService::start`):
   - The email and mobile are checked against `users` (including deleted accounts) and `profiles.mobile_no`.
   - A 6-digit OTP is generated, **stored only as a hash**, valid for 10 minutes, and sent by SMS.
   - **No row in `users` and no password exist yet.** Nothing is emailed at this step.
3. **OTP screen** (`/register/verify`):
   - Shows the masked mobile (`+91 98******10`).
   - Allows 5 wrong attempts per code.
   - "Resend code" has a 60-second cooldown and a limit of 5 codes; after that the request is locked for 30 minutes.
4. **Correct OTP**:
   - The user is created (role client, active) along with the profile (`mobile_no`, `mobile_verified_at`).
   - A random 12-character password is generated, saved only as a bcrypt hash on the user, and **emailed once** (`RegistrationPasswordMail`). It is never stored in plain text, so it cannot be sent again later.
   - The pending request is deleted, so the code cannot be used again.
   - The user is signed in (session + JWT cookie) and sent to `/home`.

### Failure handling

- **SMS fails**: the OTP screen shows an error, and "Resend code" works immediately.
- **Password email fails** (after verification): the account is still created and the user is signed in. The dashboard tells them the email failed and to set a password under Profile > Change password or with "Forgot password". The API returns `password_emailed: false`.
- **Request expiry**: an abandoned request expires after 24 hours.

### Never logged or returned

The password, the OTP, and the SMS gateway URL (which contains credentials). The activity log also strips `otp`, `token`, `registration_token` and the password fields.

### Mobile app API

| Endpoint | Body | Returns |
|---|---|---|
| `POST /api/v1/sign_up/start` | name, email, mobile | `registration_token`, `mobile_masked`, `sms_sent`, `resend_after` |
| `POST /api/v1/sign_up/verify` | registration_token, otp | user + JWT `token`, `password_emailed` |
| `POST /api/v1/sign_up/resend` | registration_token | `resend_after` |

The old `POST /api/v1/sign_up` (name, email, password, no verification) still works while `REGISTRATION_LEGACY_API_SIGNUP=true`. Set it to `false` once the app uses the new endpoints. The old endpoint then returns 410.

## Deployment

1. **Copy the files** from the zip into the project, keeping the folders.
2. **Create the table.** Use one of these:
   - `php artisan migrate --path=database/migrations/2026_10_27_000001_create_registration_requests_table.php`
   - or run `database/sql/registration_requests.sql` in SSMS.

   If you already ran the first version, run it again: it removes the two old password columns from `registration_requests`.
3. **Add the `SMS_*` and `REGISTRATION_*` settings** to `.env` (see `.env.example`).
4. **Check the mail settings.** `MAIL_*` in `.env` must be able to send. Without email, nobody can sign up.
5. **Clear caches:**
   ```
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

## OnlySMS settings

1. In the OnlySMS panel, open the **API / HTTP API** section and copy the "send SMS" URL.
2. Replace your real values in that URL with placeholders. Example (the parameter names must match the ones in your panel):
   ```
   SMS_DRIVER=http
   SMS_HTTP_URL="http://onlysms.in/api/sendhttp.php?user={username}&password={password}&sender={sender}&mobile={mobile}&message={message}&templateid={template_id}"
   SMS_USERNAME=your_login
   SMS_PASSWORD=your_password
   SMS_SENDER_ID=BALNTR
   SMS_DLT_TEMPLATE_ID=1207xxxxxxxxxxxxxxx
   SMS_HTTP_FAILURE_CONTAINS=error
   ```
3. If the panel gives an API key instead of a password, use `{api_key}` and `SMS_API_KEY`.
4. If the gateway needs a country code, set `SMS_MOBILE_PREFIX=91`.

**DLT (India).** The SMS text must match your approved template exactly. If the approved template differs from the default, set `SMS_OTP_TEMPLATE`, keeping `{otp}` and `{minutes}` where the variables go. Registered example:

```
{#var#} is your Balantro verification code. It is valid for {#var#} minutes. Do not share it with anyone.
```

**Success check.**
- If the gateway reply contains the word "error" on failure, the default `SMS_HTTP_FAILURE_CONTAINS=error` catches it.
- If it returns a fixed word on success (for example a message ID prefix), set `SMS_HTTP_SUCCESS_CONTAINS` to that word.

**Testing locally without sending SMS.** Set `SMS_DRIVER=log`; the message is written to `storage/logs/laravel.log`. This driver refuses to run when `APP_ENV=production`.

## Tests

```
DB_CONNECTION=sqlite DB_DATABASE=:memory: php vendor/bin/phpunit tests/Feature/SmsOtpRegistrationTest.php
```

The tests use an in-memory database and a fake SMS gateway. They never touch SQL Server or send real messages.

## Known limitation

The OTP proves the mobile number, not the email address. Someone could sign up with another person's email using their own mobile. That person would then receive a "your account details" email they did not ask for, and the account would be tied to their email. To close this, require the emailed password for the first sign-in instead of signing in automatically, or add an email link check.
