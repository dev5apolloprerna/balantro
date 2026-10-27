<?php

namespace Tests\Feature;

use App\Mail\RegistrationPasswordMail;
use App\Models\RegistrationRequest;
use App\Models\User;
use App\Services\Registration\SelfRegistrationService;
use App\Services\Sms\HttpSmsSender;
use App\Services\Sms\SmsDeliveryException;
use App\Services\Sms\SmsSender;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Sign-up with Full Name + Email + Mobile, OTP by SMS, and a generated
 * password e-mailed after the OTP is verified. Runs on in-memory SQLite with a fake SMS gateway.
 */
class SmsOtpRegistrationTest extends TestCase
{
    private FakeSms $sms;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'cache.default' => 'array',
            'session.driver' => 'array',
            'registration.otp_length' => 6,
            'registration.otp_ttl_minutes' => 10,
            'registration.otp_max_attempts' => 5,
            'registration.resend_cooldown_seconds' => 60,
            'registration.max_sends' => 5,
            'registration.lock_minutes' => 30,
            'registration.start_per_ip' => 50,
            'registration.legacy_api_signup' => true,
            'demo.enabled' => false,
        ]);
        DB::purge('sqlite');
        Cache::flush();
        $this->createSchema();

        $this->sms = new FakeSms();
        $this->app->instance(SmsSender::class, $this->sms);
        Mail::fake();
    }

    // ---- Web flow --------------------------------------------------------

    public function test_form_has_no_password_fields_and_asks_for_mobile(): void
    {
        $html = $this->get('/register')->assertOk()->getContent();

        $this->assertStringContainsString('name="mobile"', $html);
        $this->assertStringNotContainsString('name="password"', $html);
        $this->assertStringNotContainsString('name="password_confirmation"', $html);
    }

    public function test_successful_registration_sends_otp_then_emails_password_and_logs_in(): void
    {
        $this->post('/register', $this->form())
            ->assertRedirect(route('registration.otp.show'));

        // Nothing is created before verification.
        $this->assertSame(0, User::query()->count());
        $pending = RegistrationRequest::query()->sole();
        $this->assertSame('9876543210', $pending->mobile);

        // No password e-mail before the OTP is verified.
        Mail::assertNothingSent();

        // OTP: sent by SMS, stored only as a hash.
        $this->assertCount(1, $this->sms->sent);
        $this->assertSame('9876543210', $this->sms->sent[0]['mobile']);
        $otp = $this->sms->lastOtp();
        $this->assertNotSame($otp, $pending->otp_hash);
        $this->assertTrue(Hash::check($otp, $pending->otp_hash));

        // OTP screen shows a masked number, not the OTP.
        $page = $this->get(route('registration.otp.show'))->assertOk()->getContent();
        $this->assertStringContainsString('+91 98******10', $page);
        $this->assertStringNotContainsString('9876543210', $page);
        $this->assertStringNotContainsString($otp, $page);

        $this->post(route('registration.otp.verify'), ['otp' => $otp])
            ->assertRedirect('/home')
            ->assertSessionHas('notice', fn ($notice) => str_contains($notice, 'emailed to asha@example.test'));

        // Password: generated now, e-mailed once, stored only as a hash.
        $password = null;
        Mail::assertSent(RegistrationPasswordMail::class, 1);
        Mail::assertSent(RegistrationPasswordMail::class, function (RegistrationPasswordMail $mail) use (&$password) {
            $password = $mail->password;
            return $mail->hasTo('asha@example.test');
        });
        $this->assertNotNull($password);
        $this->assertGreaterThanOrEqual(12, strlen($password));
        foreach ($this->sms->sent as $sms) {
            $this->assertStringNotContainsString($password, $sms['message']);
        }

        $user = User::query()->where('email', 'asha@example.test')->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(User::ROLES['client'], (int) $user->role);
        $this->assertTrue((bool) $user->is_active);
        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertSame('9876543210', DB::table('profiles')->where('user_id', $user->id)->value('mobile_no'));
        $this->assertNotNull(DB::table('profiles')->where('user_id', $user->id)->value('mobile_verified_at'));
        $this->assertSame(0, RegistrationRequest::query()->count());
    }

    public function test_otp_cannot_be_used_twice(): void
    {
        $this->post('/register', $this->form());
        $otp = $this->sms->lastOtp();
        $handle = session('registration_request');

        $this->post(route('registration.otp.verify'), ['otp' => $otp])->assertRedirect('/home');
        $this->post('/logout');
        auth()->logout();

        // Replay with the same handle and code.
        $this->withSession(['registration_request' => $handle])
            ->post(route('registration.otp.verify'), ['otp' => $otp])
            ->assertRedirect(route('register'));
        $this->assertSame(1, User::query()->count());
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $this->makeUser('asha@example.test');

        $this->from('/register')->post('/register', $this->form())
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertSame(0, RegistrationRequest::query()->count());
        $this->assertCount(0, $this->sms->sent);
        Mail::assertNothingSent();
    }

    public function test_email_check_ignores_case_and_includes_deleted_accounts(): void
    {
        $this->makeUser('asha@example.test')->delete();

        $this->post('/register', $this->form(['email' => 'ASHA@Example.test']))
            ->assertSessionHasErrors('email');
    }

    public function test_duplicate_mobile_is_rejected_in_any_format(): void
    {
        $user = $this->makeUser('someone@example.test');
        DB::table('profiles')->insert(['user_id' => $user->id, 'mobile_no' => '9876543210']);

        $this->post('/register', $this->form(['mobile' => '+91 98765 43210']))
            ->assertSessionHasErrors('mobile');
        $this->assertCount(0, $this->sms->sent);
    }

    public function test_invalid_mobile_and_missing_fields_are_rejected(): void
    {
        $this->post('/register', $this->form(['mobile' => '12345']))->assertSessionHasErrors('mobile');
        $this->post('/register', $this->form(['mobile' => '5876543210']))->assertSessionHasErrors('mobile');
        $this->post('/register', ['email' => 'x@example.test', 'mobile' => '9876543210'])->assertSessionHasErrors('name');
        $this->post('/register', $this->form(['email' => 'not-an-email']))->assertSessionHasErrors('email');
        $this->assertSame(0, RegistrationRequest::query()->count());
    }

    public function test_wrong_otp_counts_attempts_then_blocks(): void
    {
        $this->post('/register', $this->form());
        $otp = $this->sms->lastOtp();
        $wrong = $otp === '000000' ? '111111' : '000000';

        $this->post(route('registration.otp.verify'), ['otp' => $wrong])
            ->assertSessionHasErrors(['otp' => 'The code is incorrect. 4 attempts left.']);

        for ($i = 0; $i < 4; $i++) {
            $this->post(route('registration.otp.verify'), ['otp' => $wrong]);
        }

        // The right code no longer works once the attempts are used up.
        $this->post(route('registration.otp.verify'), ['otp' => $otp])->assertSessionHasErrors('otp');
        $this->assertGuest();
        $this->assertSame(0, User::query()->count());
    }

    public function test_expired_otp_is_rejected(): void
    {
        $this->post('/register', $this->form());
        $otp = $this->sms->lastOtp();

        $this->travel(11)->minutes();

        $this->post(route('registration.otp.verify'), ['otp' => $otp])
            ->assertSessionHasErrors(['otp' => 'This code has expired. Please request a new code.']);
        $this->assertGuest();
    }

    public function test_resend_respects_cooldown_and_replaces_the_old_code(): void
    {
        $this->post('/register', $this->form());
        $first = $this->sms->lastOtp();

        $this->post(route('registration.otp.resend'))->assertSessionHasErrors('otp');
        $this->assertCount(1, $this->sms->sent);

        $this->travel(61)->seconds();
        $this->post(route('registration.otp.resend'))->assertSessionHas('status');
        $this->assertCount(2, $this->sms->sent);
        $second = $this->sms->lastOtp();

        // Resending a code never e-mails a password.
        Mail::assertNothingSent();

        if ($first !== $second) {
            $this->post(route('registration.otp.verify'), ['otp' => $first])->assertSessionHasErrors('otp');
        }
        $this->post(route('registration.otp.verify'), ['otp' => $second])->assertRedirect('/home');
        Mail::assertSent(RegistrationPasswordMail::class, 1);
    }

    public function test_too_many_resends_locks_the_request(): void
    {
        $this->post('/register', $this->form());

        for ($i = 0; $i < 4; $i++) {
            $this->travel(61)->seconds();
            $this->post(route('registration.otp.resend'))->assertSessionHas('status');
        }
        $this->assertCount(5, $this->sms->sent);

        $this->travel(61)->seconds();
        $this->post(route('registration.otp.resend'))->assertSessionHasErrors('otp');
        $this->assertCount(5, $this->sms->sent);
        $this->assertNotNull(RegistrationRequest::query()->sole()->locked_until);

        // Starting again with the same e-mail is blocked too.
        $this->post('/register', $this->form())->assertSessionHasErrors();
        $this->assertCount(5, $this->sms->sent);
    }

    public function test_sms_failure_keeps_request_and_allows_immediate_resend(): void
    {
        $this->sms->fail = true;

        $this->post('/register', $this->form())
            ->assertRedirect(route('registration.otp.show'))
            ->assertSessionHasErrors('otp');
        $this->assertSame(0, User::query()->count());

        $this->sms->fail = false;
        $this->post(route('registration.otp.resend'))->assertSessionHas('status');
        $this->post(route('registration.otp.verify'), ['otp' => $this->sms->lastOtp()])->assertRedirect('/home');
        $this->assertAuthenticated();
    }

    public function test_email_failure_after_verification_still_creates_account_and_tells_user(): void
    {
        $this->post('/register', $this->form());
        $otp = $this->sms->lastOtp();

        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->post(route('registration.otp.verify'), ['otp' => $otp])
            ->assertRedirect('/home')
            ->assertSessionHas('notice', fn ($notice) => str_contains($notice, 'could not email your password'));

        $this->assertAuthenticated();
        $this->assertSame(1, User::query()->count());
        $this->assertSame(0, RegistrationRequest::query()->count());
    }

    public function test_unverified_user_cannot_sign_in_or_reach_dashboard(): void
    {
        $this->post('/register', $this->form());

        // No account and no password exist until the OTP is verified.
        Mail::assertNothingSent();
        $this->post('/login', ['email' => 'asha@example.test', 'password' => 'anything']);
        $this->assertGuest();
        $this->get('/home')->assertRedirect();
        $this->assertGuest();
    }

    public function test_otp_page_without_a_pending_registration_redirects(): void
    {
        $this->get(route('registration.otp.show'))->assertRedirect(route('register'));
        $this->post(route('registration.otp.verify'), ['otp' => '123456'])->assertRedirect(route('register'));
    }

    public function test_secrets_are_not_written_to_the_activity_log(): void
    {
        $this->post('/register', $this->form());
        $otp = $this->sms->lastOtp();
        $this->post(route('registration.otp.verify'), ['otp' => $otp]);

        $logged = DB::table('activity_logs')->pluck('changes')->implode(' ');
        $this->assertStringNotContainsString($otp, $logged);
    }

    // ---- API flow --------------------------------------------------------

    public function test_api_start_verify_returns_token_and_never_the_password(): void
    {
        $start = $this->postJson('/api/v1/sign_up/start', $this->form())
            ->assertOk()
            ->assertJsonPath('sms_sent', true)
            ->assertJsonPath('mobile_masked', '+91 98******10');

        $this->assertArrayNotHasKey('password', $start->json());
        $this->assertArrayNotHasKey('otp', $start->json());
        $token = $start->json('registration_token');

        $this->postJson('/api/v1/sign_up/verify', ['registration_token' => $token, 'otp' => '000000x'])
            ->assertStatus(422);

        $verify = $this->postJson('/api/v1/sign_up/verify', ['registration_token' => $token, 'otp' => $this->sms->lastOtp()])
            ->assertOk()
            ->assertJsonPath('user.email', 'asha@example.test')
            ->assertJsonPath('password_emailed', true);
        Mail::assertSent(RegistrationPasswordMail::class, 1);
        $this->assertNotEmpty($verify->json('token'));
        $this->assertArrayNotHasKey('password', $verify->json('user'));

        // Single use.
        $this->postJson('/api/v1/sign_up/verify', ['registration_token' => $token, 'otp' => $this->sms->lastOtp()])
            ->assertStatus(410);
    }

    public function test_api_rejects_a_forged_registration_token(): void
    {
        $token = $this->postJson('/api/v1/sign_up/start', $this->form())->json('registration_token');
        [$id] = explode('.', $token);

        $this->postJson('/api/v1/sign_up/verify', ['registration_token' => $id . '.forged', 'otp' => $this->sms->lastOtp()])
            ->assertStatus(410);
        $this->assertSame(0, User::query()->count());
    }

    public function test_api_duplicate_mobile_returns_field_error(): void
    {
        $user = $this->makeUser('someone@example.test');
        DB::table('profiles')->insert(['user_id' => $user->id, 'mobile_no' => '9876543210']);

        $this->postJson('/api/v1/sign_up/start', $this->form())
            ->assertStatus(422)
            ->assertJsonPath('reason', 'mobile_taken');
    }

    public function test_legacy_api_signup_can_be_switched_off(): void
    {
        config(['registration.legacy_api_signup' => false]);

        $this->postJson('/api/v1/sign_up', ['name' => 'Old App', 'email' => 'old@example.test', 'password' => 'Secret123!'])
            ->assertStatus(410);
        $this->assertSame(0, User::query()->count());
    }

    // ---- Unit-level checks -------------------------------------------------

    public function test_mobile_normalisation(): void
    {
        $this->assertSame('9876543210', SelfRegistrationService::normaliseMobile('+91 98765-43210'));
        $this->assertSame('9876543210', SelfRegistrationService::normaliseMobile('09876543210'));
        $this->assertSame('9876543210', SelfRegistrationService::normaliseMobile('919876543210'));
        $this->assertNull(SelfRegistrationService::normaliseMobile('1234567890'));
        $this->assertNull(SelfRegistrationService::normaliseMobile('98765'));
    }

    public function test_http_driver_fails_safely_without_configuration(): void
    {
        config(['sms.http.url' => '']);

        $this->expectException(SmsDeliveryException::class);
        (new HttpSmsSender(['url' => '']))->send('9876543210', '123456 is your code');
    }

    public function test_http_driver_builds_the_gateway_url_and_detects_errors(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'gw.example.test/*' => \Illuminate\Support\Facades\Http::sequence()
                ->push('MsgID: 12345', 200)
                ->push('Error: invalid template', 200),
        ]);
        $sender = new HttpSmsSender([
            'url' => 'https://gw.example.test/send?user={username}&pass={password}&to={mobile}&from={sender}&text={message}&tid={template_id}',
            'username' => 'demo user', 'password' => 'p&ss', 'sender' => 'BALNTR', 'template_id' => '1207',
            'mobile_prefix' => '91', 'failure_contains' => 'error',
        ]);

        $sender->send('9876543210', '123456 is your code & more');
        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $q);
            return $q['to'] === '919876543210' && $q['pass'] === 'p&ss'
                && $q['user'] === 'demo user' && $q['text'] === '123456 is your code & more';
        });

        $this->expectException(SmsDeliveryException::class);
        $sender->send('9876543210', '654321 is your code');
    }

    public function test_log_driver_refuses_to_run_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->expectException(SmsDeliveryException::class);
        (new \App\Services\Sms\LogSmsSender())->send('9876543210', '123456 is your code');
    }

    // ---------------------------------------------------------------------

    private function form(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Asha Verma',
            'email' => 'asha@example.test',
            'mobile' => '9876543210',
        ], $overrides);
    }

    private function makeUser(string $email): User
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Existing',
            'email' => $email,
            'password' => Hash::make('Existing123!'),
            'role' => User::ROLES['client'],
            'is_active' => true,
        ])->save();

        return $user;
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->unique();
            $t->string('password');
            $t->integer('role')->default(0);
            $t->string('type')->nullable();
            $t->string('guid')->nullable();
            $t->string('confirmation_token')->nullable();
            $t->boolean('is_active')->default(true);
            $t->boolean('is_demo')->default(false);
            $t->rememberToken();
            $t->softDeletes();
            $t->timestamps();
        });
        Schema::create('profiles', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('mobile_no')->nullable();
            $t->timestamp('mobile_verified_at')->nullable();
            $t->timestamps();
        });
        Schema::create('groups', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });
        Schema::create('group_users', function (Blueprint $t) {
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('user_id');
        });
        DB::table('groups')->insert(['name' => 'Clients']);
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('user_name')->nullable();
            $t->string('action')->nullable();
            $t->string('subject_type')->nullable();
            $t->string('subject_id')->nullable();
            $t->text('changes')->nullable();
            $t->string('route')->nullable();
            $t->string('method')->nullable();
            $t->string('ip')->nullable();
            $t->text('user_agent')->nullable();
            $t->timestamps();
        });

        // Same shape as the real migration.
        (require base_path('database/migrations/2026_10_27_000001_create_registration_requests_table.php'))->up();
    }
}

/** Records messages instead of calling a gateway. */
class FakeSms implements SmsSender
{
    public array $sent = [];
    public bool $fail = false;

    public function send(string $mobile, string $message): void
    {
        if ($this->fail) {
            throw new SmsDeliveryException('Gateway unavailable');
        }
        $this->sent[] = ['mobile' => $mobile, 'message' => $message];
    }

    public function lastOtp(): string
    {
        $last = end($this->sent);
        preg_match('/\b(\d{6})\b/', $last['message'] ?? '', $m);

        return $m[1] ?? '';
    }
}
