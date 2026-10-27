<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use App\Services\Demo\ReportPartyResolver;
use App\Support\Demo\ReportContext;
use App\Support\Demo\ReportMode;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Sample company isolation and mode selection.
 *
 * Runs on an in-memory SQLite database with only the tables these classes
 * touch, so it never needs (or reaches) the real SQL Server.
 */
class DemoSampleDataTest extends TestCase
{
    private const DEMO_GUID = '11111111-2222-3333-4444-555555555555';

    private User $demo;
    private User $newClient;
    private User $clientWithData;
    private User $otherClient;
    private User $manager;
    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'cache.default' => 'array',
        ]);
        DB::purge('sqlite');
        Cache::flush();

        $this->createSchema();

        $this->demo = $this->makeUser('Sample Traders Pvt Ltd', 'sample@balantro.invalid', User::ROLES['client'], self::DEMO_GUID, false);
        $this->newClient = $this->makeUser('New Client', 'new@example.test', User::ROLES['client'], null);
        $this->clientWithData = $this->makeUser('Has Data Ltd', 'data@example.test', User::ROLES['client'], 'aaaaaaaa-0000-0000-0000-000000000001');
        $this->otherClient = $this->makeUser('Other Ltd', 'other@example.test', User::ROLES['client'], 'aaaaaaaa-0000-0000-0000-000000000002');
        $this->manager = $this->makeUser('Manager', 'manager@example.test', User::ROLES['manager'], null);
        $this->superAdmin = $this->makeUser('Admin', 'admin@example.test', User::ROLES['super_admin'], null);

        DB::table('YearMaster')->insert(['iPartyId' => $this->clientWithData->id, 'strYear' => '2025-2026']);
        DB::table('clients_managers')->insert(['manager_id' => $this->manager->id, 'client_id' => $this->clientWithData->id]);

        config([
            'demo.enabled' => true,
            'demo.party_id' => $this->demo->id,
            'demo.party_guid' => self::DEMO_GUID,
            'demo.email' => 'sample@balantro.invalid',
        ]);
    }

    public function test_new_client_on_the_web_sees_the_sample_company(): void
    {
        $party = $this->resolveFor($this->newClient, web: true);

        $this->assertTrue($party->isDemo);
        $this->assertSame($this->demo->id, $party->id);
        $this->assertSame(self::DEMO_GUID, $party->guid);
    }

    public function test_client_with_own_data_sees_their_own_books_by_default(): void
    {
        $party = $this->resolveFor($this->clientWithData, web: true);

        $this->assertFalse($party->isDemo);
        $this->assertSame($this->clientWithData->id, $party->id);
    }

    public function test_saved_choice_overrides_the_default(): void
    {
        $party = $this->resolveFor($this->clientWithData, web: true, session: [ReportMode::SESSION_KEY => ReportMode::DEMO]);
        $this->assertTrue($party->isDemo);

        $party = $this->resolveFor($this->newClient, web: true, session: [ReportMode::SESSION_KEY => ReportMode::OWN]);
        $this->assertFalse($party->isDemo);
        $this->assertSame($this->newClient->id, $party->id);
    }

    public function test_api_returns_own_data_unless_the_app_asks_for_the_sample(): void
    {
        $this->assertFalse($this->resolveFor($this->newClient)->isDemo);

        $party = $this->resolveFor($this->newClient, headers: [ReportMode::HEADER => 'demo']);
        $this->assertTrue($party->isDemo);
    }

    public function test_feature_flag_off_restores_previous_behaviour(): void
    {
        config(['demo.enabled' => false]);

        $party = $this->resolveFor($this->newClient, web: true);

        $this->assertFalse($party->isDemo);
        $this->assertSame($this->newClient->id, $party->id);
    }

    public function test_client_cannot_read_another_clients_books_by_guid(): void
    {
        $this->expectException(HttpException::class);

        $this->resolveFor($this->clientWithData, guid: $this->otherClient->guid);
    }

    public function test_client_may_ask_for_the_sample_guid_explicitly(): void
    {
        $party = $this->resolveFor($this->clientWithData, guid: self::DEMO_GUID);

        $this->assertTrue($party->isDemo);
    }

    public function test_staff_read_only_assigned_clients(): void
    {
        $party = $this->resolveFor($this->manager, guid: $this->clientWithData->guid);
        $this->assertSame($this->clientWithData->id, $party->id);
        $this->assertFalse($party->isDemo);

        try {
            $this->resolveFor($this->manager, guid: $this->otherClient->guid);
            $this->fail('Manager read an unassigned client.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_only_super_admin_staff_can_open_the_sample_company(): void
    {
        try {
            $this->resolveFor($this->manager, guid: self::DEMO_GUID);
            $this->fail('Manager opened the sample company.');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }

        $this->assertTrue($this->resolveFor($this->superAdmin, guid: self::DEMO_GUID)->isDemo);
    }

    public function test_sample_company_is_hidden_from_client_queries(): void
    {
        $ids = Client::query()->pluck('id')->all();

        $this->assertNotContains($this->demo->id, $ids);
        $this->assertContains($this->newClient->id, $ids);
        $this->assertNull(Client::query()->find($this->demo->id));
        $this->assertNotContains($this->demo->id, User::query()->excludingDemoParty()->pluck('id')->all());
    }

    public function test_report_session_keys_are_separate_in_sample_mode(): void
    {
        $this->resolveFor($this->newClient, web: true);
        $this->assertSame('demo_year', ReportContext::sessionKey('year'));

        $this->resolveFor($this->clientWithData, web: true);
        $this->assertSame('year', ReportContext::sessionKey('year'));
    }

    public function test_report_middleware_overwrites_partyguid_and_labels_the_response(): void
    {
        Route::post('/_test/report', fn (Request $r) => response()->json(['guid' => $r->input('partyguid')]))
            ->middleware('report.party');

        $this->actingAs($this->clientWithData)
            ->postJson('/_test/report', ['partyguid' => $this->otherClient->guid], [ReportMode::HEADER => 'demo'])
            ->assertStatus(403);

        $this->app['request']->attributes->replace([]);

        $this->actingAs($this->newClient)
            ->postJson('/_test/report', ['partyguid' => 'ffffffff-0000-0000-0000-000000000000'], [ReportMode::HEADER => 'demo'])
            ->assertStatus(403);
    }

    public function test_report_middleware_returns_the_sample_guid_in_demo_mode(): void
    {
        Route::post('/_test/report', fn (Request $r) => response()->json(['guid' => $r->input('partyguid')]))
            ->middleware('report.party');

        $this->actingAs($this->newClient)
            ->postJson('/_test/report', [], [ReportMode::HEADER => 'demo'])
            ->assertOk()
            ->assertJsonPath('guid', self::DEMO_GUID)
            ->assertJsonPath('report_context.is_demo', true)
            ->assertJsonPath('report_context.company_name', 'Sample Traders Pvt Ltd (sample)');
    }

    public function test_writes_targeting_the_sample_company_are_refused(): void
    {
        Route::middleware('api')->post('/_test/write', fn () => response()->json(['written' => true]));
        Route::middleware('api')->get('/_test/select/{id}', fn () => response()->json(['selected' => true]))
            ->name('sales.select.company');

        $this->postJson('/_test/write', ['iPartyId' => $this->demo->id])->assertStatus(403);
        $this->postJson('/_test/write', ['guid' => self::DEMO_GUID])->assertStatus(403);
        $this->postJson('/_test/write', ['client_id' => (string) $this->demo->id])->assertStatus(403);
        $this->getJson('/_test/select/' . $this->demo->id)->assertStatus(403);

        $this->postJson('/_test/write', ['iPartyId' => $this->newClient->id])->assertOk()->assertJson(['written' => true]);
        $this->getJson('/_test/select/' . $this->newClient->id)->assertOk();
    }

    public function test_sample_company_cannot_log_in_on_the_web(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\LogActivity::class);

        $this->post('/login', ['email' => 'sample@balantro.invalid', 'password' => 'secret-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_real_client_with_the_same_password_can_log_in(): void
    {
        // Control for the test above: same password, real client, login works.
        $this->withoutMiddleware(\App\Http\Middleware\LogActivity::class);

        $this->post('/login', ['email' => 'new@example.test', 'password' => 'secret-password']);

        $this->assertAuthenticatedAs($this->newClient);
    }

    private function resolveFor(User $user, bool $web = false, array $session = [], array $headers = [], ?string $guid = null)
    {
        $request = Request::create($web ? '/home' : '/api/v1/ledger', 'POST');
        foreach ($headers as $name => $value) {
            $request->headers->set($name, $value);
        }

        if ($web) {
            $store = $this->app['session']->driver('array');
            $store->flush();
            foreach ($session as $key => $value) {
                $store->put($key, $value);
            }
            $request->setLaravelSession($store);
        }

        // Bind first: rebinding 'request' resets the user resolver.
        $this->app->instance('request', $request);
        $request->setUserResolver(fn () => $user);

        return ReportContext::party($guid);
    }

    private function makeUser(string $name, string $email, int $role, ?string $guid, bool $active = true): User
    {
        return User::withoutEvents(fn () => tap(new User())->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('secret-password'),
            'role' => $role,
            'type' => array_search($role, User::ROLES, true),
            'guid' => $guid,
            'is_active' => $active,
        ])->save() ? User::query()->where('email', $email)->firstOrFail() : null);
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
            $t->boolean('is_active')->default(true);
            $t->boolean('is_demo')->default(false);
            $t->rememberToken();
            $t->softDeletes();
            $t->timestamps();
        });
        Schema::create('YearMaster', function (Blueprint $t) {
            $t->id('iYearId');
            $t->integer('iPartyId');
            $t->string('strYear');
        });
        Schema::create('VchHistory', function (Blueprint $t) {
            $t->id('iVchId');
            $t->integer('iPartyId');
            $t->string('PartyGUID')->nullable();
        });
        // Written by the login/logout listeners.
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
        foreach (['clients_managers' => 'manager_id', 'clients_supervisors' => 'supervisor_id', 'clients_data_entry_operators' => 'data_entry_operator_id'] as $table => $column) {
            Schema::create($table, function (Blueprint $t) use ($column) {
                $t->integer($column);
                $t->integer('client_id');
            });
        }
    }
}
