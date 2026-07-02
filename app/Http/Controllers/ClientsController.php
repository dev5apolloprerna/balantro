<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Group;
use App\Models\Manager;
use App\Models\Supervisor;
use App\Models\DataEntryOperator;
use App\Models\Permission;
use App\Services\ClientAssignmentService;
use App\Services\FilterService;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\ReportsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Support\ReportCache;
use Throwable;
use App\Models\UserDevice;
use App\Models\BankTransaction;
use App\Models\GstSetting;
use App\Models\GSTLedgerMapping;

class ClientsController extends Controller
{
    protected $client;
    protected $clients;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->has('id') || $request->has('client_id')) {
                $this->setClient($request);
            }
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (in_array($request->route()->getActionMethod(), ['update', 'destroy', 'assignUsers', 'assignGroups', 'assignPermissions'])) {
                $this->setClients($request);
            }
            return $next($request);
        });
    }

    private function cachedPandl(ReportsService $svc, int $partyId, ?string $from, ?string $to): array
    {
        return Cache::remember(ReportCache::key('clients', $partyId, 'pandl:' . md5(($from ?? '') . '|' . ($to ?? ''))), ReportCache::ttl(), function () use ($svc, $partyId, $from, $to) {
            return $svc->pandl($partyId, $from, $to);
        });
    }

    private function cachedBalanceSheet(ReportsService $svc, ?string $partyguid, int $partyId, ?string $from, ?string $to): array
    {
        return Cache::remember(ReportCache::key('clients', $partyId, 'balance_sheet:' . md5(($partyguid ?? '') . '|' . ($from ?? '') . '|' . ($to ?? ''))), ReportCache::ttl(), function () use ($svc, $partyguid, $partyId, $from, $to) {
            return $svc->balanceSheet($partyguid, $partyId, $from, $to);
        });
    }

    private function cachedMonthlyGraph(ReportsService $svc, int $partyId, ?string $from, ?string $to, int $type, array $opts): array
    {
        return Cache::remember(ReportCache::key('clients', $partyId, 'monthly_graph:' . md5($type . '|' . ($from ?? '') . '|' . ($to ?? '') . '|' . json_encode($opts))), ReportCache::ttl(), function () use ($svc, $partyId, $from, $to, $type, $opts) {
            return $svc->monthlyGraph($partyId, $from, $to, $type, $opts);
        });
    }

    private function cachedGroupsWithBalances(ReportsService $svc, int $partyId, ?string $from, ?string $to): array
    {
                return Cache::remember(ReportCache::key('clients', $partyId, 'groups_with_balances:' . md5(($from ?? '') . '|' . ($to ?? ''))), ReportCache::ttl(), function () use ($svc, $partyId, $from, $to) {
            return $svc->getAllGroupsWithBalances($partyId, $from, $to);
        });
    }

    private function cachedDocumentSummary(int $partyId): array
    {
        return Cache::remember(ReportCache::key('clients', $partyId, 'document_summary'), ReportCache::ttl(), function () use ($partyId) {
            return DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$partyId]);
        });
    }

    public function index(Request $request)
    {
        $auth = auth()->user();

        $clientsQuery = Client::query()
            ->with(['groups', 'managers', 'supervisors', 'dataEntryOperators', 'profile'])
            ->orderByDesc('created_at');

        /* ── ROLE SCOPING ─────────────────────────────────────────── */
        if ($auth->role === \App\Models\User::ROLES['manager']) {
            // Only clients of this manager
            $clientsQuery->whereHas('managers', fn($q) => $q->whereKey($auth->id));
        } elseif ($auth->role === \App\Models\User::ROLES['supervisor']) {
            // Only clients of this supervisor
            $clientsQuery->whereHas('supervisors', fn($q) => $q->whereKey($auth->id));
        } elseif ($auth->role === \App\Models\User::ROLES['data_entry_operator']) {
            // Only clients assigned to this DEO (optional)
            $clientsQuery->whereHas('dataEntryOperators', fn($q) => $q->whereKey($auth->id));
        } elseif ($auth->role !== \App\Models\User::ROLES['super_admin']) {
            // lock others out (e.g., clients viewing admin area)
            // $clientsQuery->whereRaw('1=0');
        }

        /* ── SEARCH ───────────────────────────────────────────────── */
        if ($request->filled('query')) {
            $term = trim((string) $request->query('query'));
            $clientsQuery->where(function ($sub) use ($term) {
                $sub->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('guid', 'like', "%{$term}%");
            });
        }

        /* ── FILTERS (kept inside already-scoped results) ─────────── */
        // Manager filter: allow only Super Admin to set arbitrary manager
        if (
            $auth->role === \App\Models\User::ROLES['super_admin']
            && $request->filled('manager_id') && ctype_digit((string)$request->manager_id)
        ) {
            $mid = (int) $request->query('manager_id');
            $clientsQuery->whereHas('managers', fn($q) => $q->whereKey($mid));
        }

        // Supervisor filter: allowed for Super Admin; Managers can also filter (still within their own scope)
        if (
            in_array($auth->role, [
                \App\Models\User::ROLES['super_admin'],
                \App\Models\User::ROLES['manager'],
            ], true)
            && $request->filled('supervisor_id') && ctype_digit((string)$request->supervisor_id)
        ) {
            $sid = (int) $request->query('supervisor_id');
            $clientsQuery->whereHas('supervisors', fn($q) => $q->whereKey($sid));
        }

        // DEO filter: allowed for Super Admin; Managers/Supervisors optional (kept here)
        if (
            in_array($auth->role, [
                \App\Models\User::ROLES['super_admin'],
                \App\Models\User::ROLES['manager'],
                \App\Models\User::ROLES['supervisor'],
            ], true)
            && $request->filled('data_entry_operator_id') && ctype_digit((string)$request->data_entry_operator_id)
        ) {
            $did = (int) $request->query('data_entry_operator_id');
            $clientsQuery->whereHas('dataEntryOperators', fn($q) => $q->whereKey($did));
        }

        // (Optional) Group filter, since you load $groups below
        if ($request->filled('group_id') && ctype_digit((string)$request->group_id)) {
            $gid = (int) $request->query('group_id');
            $clientsQuery->whereHas('groups', fn($q) => $q->where('groups.id', $gid));
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'], true)) {
            $clientsQuery->where('is_active', $request->status === 'active');
        }
        $clients = $clientsQuery->paginate(10)->appends($request->query());

        /* ── DROPDOWNS (role-aware) ───────────────────────────────── */
        if ($auth->role === \App\Models\User::ROLES['super_admin']) {
            $managers          = \App\Models\Manager::select('id', 'name')->orderBy('name')->get();
            $supervisors       = \App\Models\Supervisor::select('id', 'name')->orderBy('name')->get();
            $dataEntryOperators = \App\Models\DataEntryOperator::select('id', 'name')->orderBy('name')->get();

            $mgrSupMap = \App\Models\Manager::with(['supervisors:id,name'])
                ->get(['id', 'name'])
                ->mapWithKeys(fn($m) => [$m->id => $m->supervisors->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name
                ])->values()])
                ->toArray();
        } elseif ($auth->role === \App\Models\User::ROLES['manager']) {
            // Manager sees only self in Manager dropdown
            $managers = \App\Models\Manager::whereKey($auth->id)->get(['id', 'name']);
            // Supervisors under this manager
            $supervisors = \App\Models\Supervisor::whereHas('managers', fn($q) => $q->whereKey($auth->id))
                ->select('id', 'name')->orderBy('name')->get();
            // DEOs under this manager (if relation exists through DEO->managers)
            $dataEntryOperators = \App\Models\DataEntryOperator::whereHas('managers', fn($q) => $q->whereKey($auth->id))
                ->select('id', 'name')->orderBy('name')->get();

            $mgrSupMap = [
                $auth->id => $supervisors->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()->all(),
            ];
        } elseif ($auth->role === \App\Models\User::ROLES['supervisor']) {
            // Managers connected to this supervisor (optional)
            $managers = \App\Models\Manager::whereHas('supervisors', fn($q) => $q->whereKey($auth->id))
                ->select('id', 'name')->orderBy('name')->get();
            // Only self in Supervisor dropdown
            $supervisors = \App\Models\Supervisor::whereKey($auth->id)->get(['id', 'name']);
            // DEOs connected to this supervisor
            $dataEntryOperators = \App\Models\DataEntryOperator::whereHas('supervisors', fn($q) => $q->whereKey($auth->id))
                ->select('id', 'name')->orderBy('name')->get();

            $mgrSupMap = \App\Models\Manager::with(['supervisors' => fn($q) => $q->select('users.id', 'users.name')->whereKey($auth->id)])
                ->whereHas('supervisors', fn($q) => $q->whereKey($auth->id))
                ->get(['id', 'name'])
                ->mapWithKeys(fn($m) => [$m->id => $m->supervisors->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name
                ])->values()])
                ->toArray();
        } elseif ($auth->role === \App\Models\User::ROLES['data_entry_operator']) {
            // DEO: keep dropdowns limited to their network (optional)
            $managers = \App\Models\Manager::whereHas('dataEntryOperators', fn($q) => $q->whereKey($auth->id))
                ->select('id', 'name')->orderBy('name')->get();
            $supervisors = \App\Models\Supervisor::whereHas('dataEntryOperators', fn($q) => $q->whereKey($auth->id))
                ->select('id', 'name')->orderBy('name')->get();
            $dataEntryOperators = \App\Models\DataEntryOperator::whereKey($auth->id)->get(['id', 'name']);

            // $mgrSupMap = \App\Models\Manager::with(['supervisors' => fn($q) => $q->select('users.id', 'users.name')])
            //     ->whereHas('dataEntryOperators', fn($q) => $q->whereKey($auth->id))
            //     ->get(['id', 'name'])
            //     ->mapWithKeys(fn($m) => [$m->id => $m->supervisors->map(fn($s) => ['id' => $s->id, 'name' => $s->name])])
            //     ->toArray();
            $mgrSupMap = \App\Models\Supervisor::whereHas('managers', fn($q) => $q->whereKey($auth->id))
                ->select('id', 'name')   // ✅ no prefix
                ->orderBy('name')
                ->get();
        } else {
            $managers = collect();
            $supervisors = collect();
            $dataEntryOperators = collect();
            $mgrSupMap = [];
        }

        $groups = \App\Models\Group::withCount('permissions')->orderBy('name')->get();
        $permissions = \App\Models\Permission::orderBy('name')->get(['id', 'name', 'action', 'subject'])
            ->whereNotIn('subject', ['managers','supervisors','user_devices','themes','data_entry_operators','clients']);

        /* ── VIEW FLAGS (for buttons) ──────────────────────────────── */
        $canAdd = $auth->role === \App\Models\User::ROLES['super_admin']; // show “Add”
        $canAllocateSupervisor = in_array($auth->role, [
            \App\Models\User::ROLES['manager'],
            \App\Models\User::ROLES['supervisor'],
        ], true); // show “Allocate Supervisor”
        $user = $auth;
        return view('admin.clients.index', compact(
            'clients',
            'managers',
            'supervisors',
            'dataEntryOperators',
            'groups',
            'permissions',
            'mgrSupMap',
            'canAdd',
            'canAllocateSupervisor',
            'user'
        ));
    }

    public function create(Request $request)
    {
        $generatedPassword = $this->generateStrongPassword();

        $client = new Client($request->only(['name', 'email', 'guid']));
        $client->password = $generatedPassword;
        $client->password_confirmation = $generatedPassword;

        if (!$client->profile) {
            $client->profile()->create([]);
        }

        if ($client->save()) {
            $this->sendWelcomeEmail($client, $generatedPassword, true);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => __('admin.clients.flash.client_update_msg')], 422);
            }
            //return redirect()->route('clients.index', ['page' => $request->get('page')])->with('notice', __('admin.clients.flash.client_update_msg'));
            return redirect()->route('clients.index', ['page' => $request->get('page')])->with('notice', __('admin.clients.flash.client_update_msg'));
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'client' => $client->errors(),
                        'profile' => $client->profile ? $client->profile->errors() : []
                    ]
                ], 422);
            }
            //return redirect()->route('clients.index', ['page' => $request->get('page')])->with('notice', $client->errors());
            return redirect()->route('clients.index', ['page' => $request->get('page')])->with('alert', __($client->errors()));
        }
    }

    public function store(Request $request)
    {
        // Validate flat + nested profile fields
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'guid'  => ['required', 'string', 'max:255', 'unique:users,guid'],
            'isStockManagement' => ['nullable', 'integer', 'in:0,1'],

            'profile.business_type' => ['nullable', 'string', 'max:50'],
            'profile.mobile_no'     => ['nullable', 'string', 'max:20'],
            'profile.whatsapp_no'   => ['nullable', 'string', 'max:20'],
            'profile.pan_no'        => ['nullable', 'string', 'max:20'],
            'profile.gst_no'        => ['nullable', 'string', 'max:30'],
            'profile.address'       => ['nullable', 'string', 'max:2000'],
            
            
            // Optional relation inputs (arrays of IDs)
            // 'manager_ids'                => ['array'],
            // 'manager_ids.*'              => ['integer', 'exists:users,id'],
            // 'supervisor_ids'             => ['array'],
            // 'supervisor_ids.*'           => ['integer', 'exists:users,id'],
            // 'data_entry_operator_ids'    => ['array'],
            // 'data_entry_operator_ids.*'  => ['integer', 'exists:users,id'],
            // 'group_ids'                  => ['array'],
            // 'group_ids.*'                => ['integer', 'exists:groups,id'],
        ]);

        // Create client
        $plainPassword = "Client1!"; // $this->generateStrongPassword(); // see helper below
        $client = new Client();
        $client->name  = $validated['name'];
        $client->email = $validated['email'];
        $client->guid  = $validated['guid'];
        $client->role  = 0;            // your default role id
        $client->type  = User::ROLES['client'];    // or whatever value fits your schema
        $client->password = Hash::make($plainPassword);
        $client->is_active = true;
        $client->isStockManagement = $validated['isStockManagement'];
        $client->confirmation_token = Str::random(60); // ✅ Add this
        $client->save(); // must save BEFORE creating hasOne profile

        // if ($request->filled('fcm_token')) {
        //     UserDevice::updateOrCreate(
        //         [
        //             'device_token' => $request->fcm_token,
        //         ],
        //         [
        //             'user_id'      => $client->id,
        //             'device_type'  => $request->device_type ?? 'pc',
        //             'browser_name' => $request->browser_name,
        //             'os_name'      => $request->os_name,
        //             'is_active'    => true,
        //         ]
        //     );
        // }
        // Create profile (if provided)
        if (!empty($validated['profile'])) {
            $client->profile()->create($validated['profile']);
        }

        // // Optional: sync relations if provided
        // if (!empty($validated['manager_ids'])) {
        //     $client->managers()->sync($validated['manager_ids']);
        // }
        // if (!empty($validated['supervisor_ids'])) {
        //     $client->supervisors()->sync($validated['supervisor_ids']);
        // }
        // if (!empty($validated['data_entry_operator_ids'])) {
        //     $client->dataEntryOperators()->sync($validated['data_entry_operator_ids']);
        // }
        // if (!empty($validated['group_ids'])) {
        //     $client->groups()->sync($validated['group_ids']);
        // }

        // Optional: send welcome email & reset link (wrap to avoid hard-fails)
        $welcomeEmailSent = $this->sendWelcomeEmail($client, $plainPassword, true);
        
        // Respond
        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => __('admin.clients.flash.client_update_msg'),
                'client'  => $client->load(['profile', 'groups', 'managers', 'supervisors', 'dataEntryOperators']),
                'mail_queued' => $welcomeEmailSent,
                'mail_warning' => $welcomeEmailSent ? null : __('Welcome email could not be queued. Please verify queue/mail settings.'),
            ]);
        }

        $redirect = redirect()->route('clients.index')->with('notice', __('admin.clients.flash.client_update_msg'));

        if (!$welcomeEmailSent) {
            $redirect->with('alert', __('Client was saved, but the welcome email could not be queued. Please verify queue/mail settings.'));
        }

        return $redirect;
    }


    /**
     * Send or queue a client welcome email without letting mail transport failures break the request.
     */
    protected function sendWelcomeEmail(Client $client, ?string $plainPassword = null, bool $queue = false): bool
    {
        try {
            $pendingMail = Mail::to($client->email);
            $welcomeMail = new WelcomeMail($client, $plainPassword);

            $queue ? $pendingMail->queue($welcomeMail) : $pendingMail->send($welcomeMail);

            return true;
        } catch (Throwable $e) {
            report($e);

            Log::warning('Unable to send client welcome email.', [
                'client_id' => $client->id,
                'email' => $client->email,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function edit(Request $request, $id)
    {
        $client = Client::with('profile')->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($client);
        }

        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        // (optional) validate
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $client->id],
            'guid'  => ['required', 'string', 'max:255', 'unique:users,guid,' . $client->id],
            'isStockManagement' => ['sometimes', 'integer', 'in:0,1'],
            // profile fields
            'profile.business_type' => ['nullable', 'string', 'max:255'],
            'profile.mobile_no'     => ['nullable', 'string', 'max:20'],
            'profile.whatsapp_no'   => ['nullable', 'string', 'max:20'],
            'profile.pan_no'        => ['nullable', 'string', 'max:20'],
            'profile.gst_no'        => ['nullable', 'string', 'max:20'],
            'profile.address'       => ['nullable', 'string', 'max:500'],
        ]);
        
        $client->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'guid'  => $data['guid'],
            'isStockManagement' => (int) ($data['isStockManagement'] ?? 0),
        ]);

        // if ($request->filled('fcm_token')) {
        //     UserDevice::where([
        //         'user_id' => $client->id,
        //         'device_type' => $request->device_type,
        //         'browser_name' => $request->browser_name
        //     ])->updateOrCreate(
        //         [
        //             'device_token' => $request->fcm_token,
        //         ],
        //         [
        //             'user_id'      => $client->id,
        //             'device_type'  => $request->device_type ?? 'pc',
        //             'browser_name' => $request->browser_name,
        //             'os_name'      => $request->os_name,
        //             'is_active'    => true,
        //         ]
        //     );
        // }

        //$client->update($request->only(['name', 'email', 'guid']));
        $profile = $data['profile'] ?? [];
        $client->profile()->updateOrCreate(
            ['user_id' => $client->id],
            [
                'business_type'    => $profile['business_type'] ?? null,
                'mobile_no'        => $profile['mobile_no'] ?? null,
                'whatsapp_no'      => $profile['whatsapp_no'] ?? null,
                'pan_no'           => $profile['pan_no'] ?? null,
                'gst_no'           => $profile['gst_no'] ?? null,
                'address'          => $profile['address'] ?? null,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => __('admin.clients.flash.client_update_msg'),
                'client'  => $client->load(['groups', 'managers', 'supervisors', 'dataEntryOperators', 'profile']),
            ]);
        }
        return redirect()->route('clients.index', ['page' => $request->get('page')])->with('notice', __('admin.clients.flash.client_update_msg'));
    }

    public function destroy(Request $request, Client $client)
    {
        // Delete related records that reference the client
        DB::table('managers_supervisors')->where('supervisor_id', $client->id)->delete();
        DB::table('clients_managers')->where('client_id', $client->id)->delete();
        DB::table('clients_supervisors')->where('client_id', $client->id)->delete();  // Delete related supervisors
        DB::table('group_users')->where('user_id', $client->id)->delete();
        DB::table('user_permissions')->where('user_id', $client->id)->delete();  // Ensure this is deleted first
        DB::table('clients_data_entry_operators')->where('client_id', $client->id)->delete();  // Delete related records
        DB::table('documents')->where('user_id', $client->id)->delete();  // Delete related documents

        // Delete the client-related data
        $client->profile()->delete();  // Delete associated profile (if applicable)

        // Now delete the client (user)
        $client->delete();

        // Return success response (JSON or redirect)
        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => __('admin.clients.flash.client_delete_msg'),
            ]);
        }

        return redirect()->route('clients.index', ['page' => $request->get('page')])->with('notice', __('admin.clients.flash.client_delete_msg'));
    }

    public function assignUsers(Request $request, Client $client)
    {
        $currentPage = $request->page ?? 1;

        $assignmentService = new ClientAssignmentService(auth()->user(), $client, $request->all());
        $assignmentService->call();

        if ($request->has('manager_ids')) {
            $client->managers()->sync($request->manager_ids);
        }
        if ($request->has('supervisor_ids')) {
            $client->supervisors()->sync($request->supervisor_ids);
        }
        if ($request->has('data_entry_operator_ids')) {
            $client->dataEntryOperators()->sync($request->data_entry_operator_ids);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => __('admin.clients.flash.users_assigned_msg'),
            ]);
        }

        return redirect()->route('clients.index', ['page' => $currentPage])->with('notice', __('admin.clients.flash.users_assigned_msg'));
    }

    public function managerSupervisors(Request $request, Manager $manager)
    {
        return response()->json($manager->supervisors);
    }

    public function supervisorDataEntryOperators(Request $request, Supervisor $supervisor)
    {
        return response()->json($supervisor->dataEntryOperators);
    }

    public function assignGroups(Request $request, Client $client)
    {
        $client->groups()->sync($request->group_ids ?? []);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => __('admin.assign_groups.messages.update_success')]);
        }

        //return redirect()->route('clients.index')->with('notice', __('admin.assign_groups.messages.update_success'));
        return redirect()->route('clients.index', ['page' => $request->get('page')])->with('notice', __('admin.assign_groups.messages.update_success'));
    }

    public function getGroups(Request $request, Client $client)
    {
        $groups = Group::all();

        return response()->json([
            'client_id'           => $client->id,
            'groups'              => $groups->map(fn($g) => [
                'id' => $g->id,
                'name' => $g->name,
                'permissions_count' => $g->permissions->count(),
            ]),
            'assigned_group_ids'  => $client->groups->pluck('id'),
        ]);
    }

    public function assignPermissions(Request $request, Client $client)
    {
        $assignedPermissionIds = collect($request->permission_ids)->map(fn($id) => (int) $id)->toArray();
        $deniedPermissionIds   = collect($request->denied_permission_ids)->map(fn($id) => (int) $id)->toArray();

        $groupPermissionIds = Permission::whereHas('groups.users', fn($q) => $q->where('users.id', $client->id))
            ->distinct()->pluck('id')->toArray();

        $client->userPermissions()->where('is_negative', false)->whereNotIn('permission_id', $assignedPermissionIds)->delete();

        foreach ($assignedPermissionIds as $permissionId) {
            if (!in_array($permissionId, $groupPermissionIds)) {
                $client->assignPermission($permissionId, false);
            }
        }

        $client->userPermissions()->whereIn('permission_id', $groupPermissionIds)->where('is_negative', true)
            ->whereNotIn('permission_id', $deniedPermissionIds)->delete();

        foreach ($groupPermissionIds as $permissionId) {
            $client->assignPermission($permissionId, true, in_array($permissionId, $deniedPermissionIds));
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => __('admin.assign_permissions.messages.update_success')]);
        }

        //return redirect()->route('clients.index')->with('notice', __('admin.assign_permissions.messages.update_success'));
        return redirect()->route('clients.index', ['page' => $request->get('page')])->with('notice', __('admin.assign_permissions.messages.update_success'));
    }

    public function getPermissions(Request $request, Client $client)
    {
        $groupPermissionIds = $client->groups
            ->flatMap(fn($g) => $g->permissions->pluck('id'))
            ->unique()
            ->values()
            ->toArray();

        $assignedPermissions = $client->userPermissions()
            ->where('is_negative', false)
            ->pluck('permission_id')
            ->toArray();

        $deniedPermissions = $client->userPermissions()
            ->where('is_negative', true)
            ->pluck('permission_id')
            ->toArray();

        // merge group-based permissions as "assigned"
        $allAssigned = array_unique(array_merge($groupPermissionIds, $assignedPermissions));

        return response()->json([
            'client_id'            => $client->id,
            'permissions'          => Permission::select(['id', 'name', 'action', 'subject'])->whereNotIn('subject', ['managers','supervisors','user_devices','themes','data_entry_operators','clients','groups'])->get()->toArray(),
            'group_permission_ids' => $groupPermissionIds,
            'assigned_permissions' => $allAssigned,
            'denied_permissions'   => $deniedPermissions,
        ]);
    }

    protected function setClient($request)
    {
        $this->client = Client::findOrFail($request->id ?? $request->client_id);
    }

    protected function setClients($request)
    {
        $this->clients = Client::with(['groups', 'managers', 'supervisors', 'dataEntryOperators', 'profile'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $request->page);
    }

    protected function generateStrongPassword()
    {
        $uppercase = Str::upper(Str::random(1));
        $lowercase = Str::lower(Str::random(5));
        $digits = Str::random(2, '0123456789');
        $special = collect(['!', '@', '#', '$', '%', '^', '&', '*'])->random();

        return Str::shuffle($lowercase . $uppercase . $digits . $special);
    }

    public function assignManager(Request $request, Client $client)
    {
        $request->validate([
            'manager_ids' => 'array',
            'manager_ids.*' => 'exists:users,id'
        ]);

        // Sync managers for client
        $client->managers()->sync($request->manager_ids);

        return back()->with('notice', 'Managers updated successfully!');
    }

    public function toggleStatus(Request $request, Client $client)
    {
        // (Optional) authz check here (e.g., super_admin only)
        // abort_unless(auth()->user()->role === \App\Models\User::ROLES['super_admin'], 403);

        $client->is_active = !$client->is_active;
        $client->save();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $client->is_active ? 'Client activated' : 'Client deactivated',
                'is_active' => $client->is_active,
                'client_id' => $client->id,
            ]);
        }

        return redirect()
            ->route('clients.index', ['page' => $request->get('page')])
            ->with('notice', $client->is_active ? 'Client activated' : 'Client deactivated');
    }

    public function pnl(Request $r, $guid = null, \App\Services\ReportsService $svc)
    {
        //try {
            // 🔹 Check if GUID is provided
            if (!$guid) {
                return redirect()
                    ->route('clients.index')
                    ->with('error', 'Invalid request. Client GUID is missing.');
            }
            // $rangeSel = $r->input('range');
            // if ($rangeSel) {
            //     session([
            //         'selectedRange' => $rangeSel,
            //         'selectedFrom'  => $r->input('from'),
            //         'selectedTo'    => $r->input('to'),
            //     ]);
            // }
            $user = Client::where('guid', $guid)->first();
            $toDMY = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '';
            // $rangeSel = $rangeSel ?: session('selectedRange', 'current_year');
            // // Restore from session
            // $from = $r->input('from', session('selectedFrom'));
            // $to   = $r->input('to', session('selectedTo'));
            // // ✅ Auto set from/to if not provided
            // if (!$from || !$to) {
            //     $today = now();

            //     if ($rangeSel === 'current_year') {
            //         $startYear = $today->month >= 4 ? $today->year : $today->year - 1;
            //         $from = date('Y-m-d', strtotime("$startYear-04-01"));
            //         $to   = date('Y-m-d', strtotime(($startYear + 1) . "-03-31"));
            //     }

            //     if ($rangeSel === 'last_year') {
            //         $startYear = $today->month >= 4 ? $today->year - 1 : $today->year - 2;
            //         $from = date('Y-m-d', strtotime("$startYear-04-01"));
            //         $to   = date('Y-m-d', strtotime(($startYear + 1) . "-03-31"));
            //     }
            // }
            
            // $resp = $svc->pandl($user->id, $toDMY($r->input('from')), $toDMY($r->input('to')));
            [$financialYears, $rangeSel, $from, $to] = $this->resolveClientFinancialYear($r, $user);
            // $resp = $svc->pandl($user->id, $toDMY($from), $toDMY($to));
            $resp = $this->cachedPandl($svc, $user->id, $toDMY($from), $toDMY($to));
            
            // $from = $r->input('from') ? date('d-m-Y',strtotime($r->input('from'))) : '';
            // $to = $r->input('to') ? date('d-m-Y',strtotime($r->input('to'))) : '';
            $pl = data_get($resp, 'data', []);
            // if (!$from && !$to) {

            //     $today = now();

            //     if ($today->month < 4) {
            //         // Jan, Feb, Mar → FY started last year
            //         $fyStart = now()->subYear()->startOfYear()->setMonth(4)->setDay(1);
            //         $fyEnd   = now()->startOfYear()->setMonth(3)->setDay(31);
            //     } else {
            //         // Apr onwards → FY started this year
            //         $fyStart = now()->startOfYear()->setMonth(4)->setDay(1);
            //         $fyEnd   = now()->addYear()->startOfYear()->setMonth(3)->setDay(31);
            //     }

            //     $from = $fyStart->format('Y-m-d');
            //     $to   = $fyEnd->format('Y-m-d');
            // }
            //return view('admin.clients.reports.pl', compact('resp', 'from', 'to', 'pl', 'guid','user','rangeSel'));
            return view('admin.clients.reports.pl', compact('resp', 'from', 'to', 'pl', 'guid','user','rangeSel', 'financialYears'));
        // } catch (\Throwable $e) {
        //     // 🔹 Log error and redirect with friendly message
        //     Log::error("Dashboard error for GUID {$guid}: " . $e->getMessage(), [
        //         'trace' => $e->getTraceAsString(),
        //     ]);

        //     return redirect()
        //         ->route('clients.index')
        //         ->with('error', 'Something went wrong while loading the dashboard. Please try again.');
        // }
    }

    public function balanceSheet(Request $r, $guid = null, ReportsService $svc)
    {
        try {
            // 🔹 Check if GUID is provided
            if (!$guid) {
                return redirect()
                    ->route('clients.index')
                    ->with('error', 'Invalid request. Client GUID is missing.');
            }
            $user = Client::where('guid', $guid)->first();
            // inputs
            // $rangeSel = $r->input('range');
            // $from = $r->input('from') ? date('d-m-Y',strtotime($r->input('from'))) : '';
            // $to = $r->input('to') ? date('d-m-Y',strtotime($r->input('to'))) : '';
            [$financialYears, $rangeSel, $from, $to] = $this->resolveClientFinancialYear($r, $user);
            $partyguid = $guid; // provide via UI/session
            $partyId   = $user->id;

            // $from = $r->input('from', session('selectedFrom'));
            // $to   = $r->input('to', session('selectedTo'));
            // $rangeSel = $rangeSel ?: session('selectedRange', 'current_year');
            // // ✅ Auto set from/to if not provided
            // if (!$from || !$to) {
            //     $today = now();

            //     if ($rangeSel === 'current_year') {
            //         $startYear = $today->month >= 4 ? $today->year : $today->year - 1;
            //         $from = date('Y-m-d', strtotime("$startYear-04-01"));
            //         $to   = date('Y-m-d', strtotime(($startYear + 1) . "-03-31"));
            //     }

            //     if ($rangeSel === 'last_year') {
            //         $startYear = $today->month >= 4 ? $today->year - 1 : $today->year - 2;
            //         $from = date('Y-m-d', strtotime("$startYear-04-01"));
            //         $to   = date('Y-m-d', strtotime(($startYear + 1) . "-03-31"));
            //     }
            // }
            // $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);
            $resp = $this->cachedBalanceSheet($svc, $partyguid, $partyId, $from, $to);

            $data = data_get($resp, 'data', []);
            // if (!$from && !$to) {

            //     $today = now();

            //     if ($today->month < 4) {
            //         // Jan, Feb, Mar → FY started last year
            //         $fyStart = now()->subYear()->startOfYear()->setMonth(4)->setDay(1);
            //         $fyEnd   = now()->startOfYear()->setMonth(3)->setDay(31);
            //     } else {
            //         // Apr onwards → FY started this year
            //         $fyStart = now()->startOfYear()->setMonth(4)->setDay(1);
            //         $fyEnd   = now()->addYear()->startOfYear()->setMonth(3)->setDay(31);
            //     }

            //     $from = $fyStart->format('Y-m-d');
            //     $to   = $fyEnd->format('Y-m-d');
            // }
            
            // return view('admin.clients.reports.balance_sheet', compact('resp', 'from', 'to', 'data', 'partyguid', 'guid','user','rangeSel'));
            return view('admin.clients.reports.balance_sheet', compact('resp', 'from', 'to', 'data', 'partyguid', 'guid','user','rangeSel', 'financialYears'));
        } catch (\Throwable $e) {
            // 🔹 Log error and redirect with friendly message
            Log::error("Dashboard error for GUID {$guid}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('clients.index')
                ->with('error', 'Something went wrong while loading the dashboard. Please try again.');
        }
    }

    public function ledger(Request $r, $guid = null, ReportsService $svc)
    {
        try {
            // 🔹 Check if GUID is provided
            if (!$guid) {
                return redirect()
                    ->route('clients.index')
                    ->with('error', 'Invalid request. Client GUID is missing.');
            }
            $user = Client::where('guid', $guid)->first();
            // $from = $r->input('from');
            // $to   = $r->input('to');
            // $rangeSel = $r->input('range');
            [$financialYears, $rangeSel, $from, $to] = $this->resolveClientFinancialYear($r, $user);
            $groupId = (int) $r->input('group_id'); // iGroupId
            $strCustomerName =  $r->input('strCustomerName');

            // if ($rangeSel) {
            //     session([
            //         'selectedRange' => $rangeSel,
            //         'selectedFrom'  => $r->input('from'),
            //         'selectedTo'    => $r->input('to'),
            //     ]);
            // }
            // $from = $r->input('from', session('selectedFrom'));
            // $to   = $r->input('to', session('selectedTo'));
            // $rangeSel = $rangeSel ?: session('selectedRange', 'current_year');

            $partyId = $user->id;
            $partyguid = $guid; // provide via UI/session
            // ✅ Auto set from/to if not provided
            // if (!$from || !$to) {
            //     $today = now();
            //     if ($rangeSel === 'current_year') {
            //         $startYear = $today->month >= 4 ? $today->year : $today->year - 1;
            //         $from = date('Y-m-d', strtotime("$startYear-04-01"));
            //         $to   = date('Y-m-d', strtotime(($startYear + 1) . "-03-31"));
            //     }
            //     if ($rangeSel === 'last_year') {
            //         $startYear = $today->month >= 4 ? $today->year - 1 : $today->year - 2;
            //         $from = date('Y-m-d', strtotime("$startYear-04-01"));
            //         $to   = date('Y-m-d', strtotime(($startYear + 1) . "-03-31"));
            //     }
            // }
            
            $resp = $svc->ledger($partyId, $groupId, $from, $to, $strCustomerName);
            $data = data_get($resp, 'data', []);
            $GroupMasters = DB::table('GroupMaster')
                //->where('PartyGUID', $partyguid)
                ->where('iPartyId', $partyId)
                ->get();
            
			// $from = $r->input('from') ? date('d-m-Y',strtotime($r->input('from'))) : '';
            // $to = $r->input('to') ? date('d-m-Y',strtotime($r->input('to'))) : '';
            //return view('admin.clients.reports.ledger', compact('resp', 'from', 'to', 'strCustomerName', 'data', 'groupId', 'GroupMasters', 'guid','user','rangeSel'));
            return view('admin.clients.reports.ledger', compact('resp', 'from', 'to', 'strCustomerName', 'data', 'groupId', 'GroupMasters', 'guid','user','rangeSel', 'financialYears'));
        } catch (\Throwable $e) {
            // 🔹 Log error and redirect with friendly message
            Log::error("Dashboard error for GUID {$guid}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('clients.index')
                ->with('error', 'Something went wrong while loading the dashboard. Please try again.');
        }
    }

    public function voucherHistory(Request $r,  ReportsService $svc)
    {
        try {
            $guid = $r->input('guid');
            // $rangeSel = $r->input('range');
            // 🔹 Check if GUID is provided
            if (!$guid) {
                return redirect()
                    ->route('clients.index')
                    ->with('error', 'Invalid request. Client GUID is missing.');
            }
            // $from = $r->input('from');
            // $to   = $r->input('to');
            // if ($r->input('range') === 'custom') {

            //     $from = $r->input('from_custom');
            //     $to   = $r->input('to_custom');

            // } else {

            //     $from = $r->input('from', session('selectedFrom'));
            //     $to   = $r->input('to', session('selectedTo'));
            // }
            $user = Client::where('guid', $guid)->first();
            [$financialYears, $rangeSel, $from, $to] = $this->resolveClientFinancialYear($r, $user);
            $ledgerId  = (int) $r->input('ledger_id'); // iledgerid
            $partyguid = $guid; // provide via UI/session
            // if ($rangeSel) {
            //     session([
            //         'selectedRange' => $rangeSel,
            //         'selectedFrom'  => $r->input('from'),
            //         'selectedTo'    => $r->input('to'),
            //     ]);
            // }
            // $rangeSel = $rangeSel ?: session('selectedRange', 'current_year');

            // // ✅ Auto set from/to if not provided
            // if (!$from || !$to) {
            //     $today = now();

            //     if ($rangeSel === 'current_year') {
            //         $startYear = $today->month >= 4 ? $today->year : $today->year - 1;
            //         $from = date('Y-m-d', strtotime("$startYear-04-01"));
            //         $to   = date('Y-m-d', strtotime(($startYear + 1) . "-03-31"));
            //     }

            //     if ($rangeSel === 'last_year') {
            //         $startYear = $today->month >= 4 ? $today->year - 1 : $today->year - 2;
            //         $from = date('Y-m-d', strtotime("$startYear-04-01"));
            //         $to   = date('Y-m-d', strtotime(($startYear + 1) . "-03-31"));
            //     }
            // }
            $resp = $svc->voucherHistory($partyguid, $ledgerId, $from, $to);
            $data = data_get($resp, 'data', []);
			$ledgerName = '';
            if ($ledgerId) {
                $ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
                $ledgerName = $ledger->strCustomerName ?? '';
            }
			// $from = $r->input('from') ? date('d-m-Y',strtotime($r->input('from'))) : '';
            // $to = $r->input('to') ? date('d-m-Y',strtotime($r->input('to'))) : '';
            // return view('admin.clients.reports.voucher_history', compact('resp', 'from', 'to', 'data', 'ledgerId', 'partyguid', 'guid','user','ledgerName','rangeSel'));
            return view('admin.clients.reports.voucher_history', compact('resp', 'from', 'to', 'data', 'ledgerId', 'partyguid', 'guid','user','ledgerName','rangeSel', 'financialYears'));
        } catch (\Throwable $e) {
            // 🔹 Log error and redirect with friendly message
            Log::error("Dashboard error for GUID {$guid}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('clients.index')
                ->with('error', 'Something went wrong while loading the dashboard. Please try again.');
        }
    }

    private function resolveClientFinancialYear(Request $request, Client $client): array
    {
        // $financialYears = DB::table('YearMaster')
        //     ->where('iPartyId', $client->id)
        //     ->orderBy('iYearId', 'desc')
        //     ->get();
        $financialYears = Cache::remember(ReportCache::key('clients', (int) $client->id, 'financial_years'), ReportCache::ttl(), function () use ($client) {
            return DB::table('YearMaster')
                ->where('iPartyId', $client->id)
                ->orderBy('iYearId', 'desc')
                ->get();
        });

        $defaultRange = $financialYears->first()->strYear ?? null;
        $sessionPrefix = "client_{$client->id}";
        $rangeSel = $request->input('range', session("{$sessionPrefix}_selectedRange", $defaultRange));
        $from = $request->input('from', session("{$sessionPrefix}_selectedFrom"));
        $to = $request->input('to', session("{$sessionPrefix}_selectedTo"));

        if ($rangeSel === 'custom') {
            $from = $request->input('from_custom', $from);
            $to = $request->input('to_custom', $to);
        }

        if ((! $from || ! $to) && preg_match('/^(\d{4})-(\d{4})$/', (string) $rangeSel, $matches)) {
            $from = $matches[1] . '-04-01';
            $to = $matches[2] . '-03-31';
        }

        if (! $rangeSel) {
            $today = now();
            $startYear = $today->month >= 4 ? $today->year : $today->year - 1;
            $rangeSel = $startYear . '-' . ($startYear + 1);
            $from = $from ?: $startYear . '-04-01';
            $to = $to ?: ($startYear + 1) . '-03-31';
        }

        session([
            "{$sessionPrefix}_selectedRange" => $rangeSel,
            "{$sessionPrefix}_selectedFrom" => $from,
            "{$sessionPrefix}_selectedTo" => $to,
        ]);

        return [$financialYears, $rangeSel, $from, $to];
    }

    public function viewVoucher($guid,$strGUID, $vchType)
    {
        $guid = $guid;
        $user = Client::where('guid', $guid)->first();
        $svc = new ReportsService();
        $resp = $svc->voucherDetails($guid, $strGUID, $vchType);
        
        // $voucher = DB::select(
        //     "EXEC GetVoucherDetails ?, ?, ?",
        //     [$guid, $vchNo, $vchType]
        // );
        //$voucher = collect(data_get($resp, 'data.rows', []));
        $voucher = collect($resp);
        
        if ($voucher->isEmpty()) {
            abort(404);
        }
        // ✅ HEADER
        $header = $voucher->first();
        
        // ✅ TOTALS
        $totalDr = $voucher->sum(function ($r) {
            return (float) ($r->DRAmount ?? 0);
        });

        $totalCr = $voucher->sum(function ($r) {
            return (float) ($r->CRAmount ?? 0);
        });

        return view('admin.clients.reports.voucher_view', compact(
            'voucher',
            'header',
            'totalDr',
            'totalCr',
            'user',
            'guid'
        ));
    }

    public function dashboard(Request $r, $guid = null, ReportsService $svc)
    {
        try {
            // 🔹 Check if GUID is provided
            if (!$guid) {
                return redirect()
                    ->route('clients.index')
                    ->with('error', 'Invalid request. Client GUID is missing.');
            }
            $user = Client::where('guid', $guid)->first();

            if (!$user) {
                return redirect()
                    ->route('clients.index')
                    ->with('error', 'Client not found.');
            }

            $summary = [
                ['key' => '1', 'value' => "Sale & Purchase"],
                ['key' => '2', 'value' => "Credit & Debit"],
                ['key' => '3', 'value' => "Recepit & Payment"],
                ['key' => '4', 'value' => "Cash & Bank balance"]
            ];

            $type = (int) $r->input('type', 1);
            // $from = $r->input('from');
            // $to   = $r->input('to');
            // $financialYears = DB::table('YearMaster')
            //     ->where('iPartyId', $user->id)
            //     ->orderBy('iYearId', 'desc')
            //     ->get();
            $financialYears = Cache::remember(ReportCache::key('clients', (int) $user->id, 'financial_years'), ReportCache::ttl(), function () use ($user) {
                return DB::table('YearMaster')
                    ->where('iPartyId', $user->id)
                    ->orderBy('iYearId', 'desc')
                    ->get();
            });

            $currentFinancialYear = $financialYears->first();
            $defaultRange = $currentFinancialYear->strYear ?? null;
            $selectedRangeLabel = $r->input("range", session("client_{$user->id}_selectedRange", $defaultRange));
            $from = $r->input("from", session("client_{$user->id}_selectedFrom"));
            $to   = $r->input("to", session("client_{$user->id}_selectedTo"));

            if ((! $from || ! $to) && preg_match('/^(\d{4})-(\d{4})$/', (string) $selectedRangeLabel, $matches)) {
                $from = $matches[1] . '-04-01';
                $to = $matches[2] . '-03-31';
            }

            if ($selectedRangeLabel || $from || $to) {
                session([
                    "client_{$user->id}_selectedRange" => $selectedRangeLabel,
                    "client_{$user->id}_selectedFrom" => $from,
                    "client_{$user->id}_selectedTo" => $to,
                ]);
            }

            // Titles per type (for tabs / card title)
            $titles = [
                1 => 'Sales vs Purchase',
                2 => 'Creditors vs Debtors',
                3 => 'Receipt vs Payment',
                4 => 'Cash & Bank Flow',
                5 => 'Income & Expense',
            ];

            // Get monthly series for ALL four types for the selected FY
            $charts      = [];
            $selectedRes = null;
            $sum         = fn($arr) => array_sum(array_map('floatval', $arr ?? []));

            for ($t = 1; $t <= 5; $t++) {
                $groups = null;
                $res = [];
                if ($t == 4)
                {
                    $metric = $r->input('metric', 'cash');

                    if ($metric == 'cash') {
                        $groups = 'Cash-in-Hand';
                    }
                    elseif ($metric == 'bank') {
                        $groups = 'Bank Accounts';
                    }
                } elseif ($t == 5) {

                    // $res = [
                    //     'months' => [],
                    //     'directIncome' => [],
                    //     'directExpense' => [],
                    //     'indirectIncome' => [],
                    //     'indirectExpense' => [],
                    // ];

                    $metrics = [
                        'Direct Incomes',
                        'Direct Expenses',
                        'Indirect Incomes',
                        'Indirect Expenses'
                    ];

                    foreach ($metrics as $metric) {

                        // $tmp = $svc->monthlyGraph(
                        $tmp = $this->cachedMonthlyGraph(
                            $svc,
                            $user->id,
                            $from,
                            $to,
                            5,
                            [
                                'metricType' => $metric
                            ]
                        );
                        
                        $res['months'] = $tmp['months'] ?? [];

                        if ($metric == 'Direct Incomes') {
                            //$res['directIncome'] = $tmp['closingBalance'] ?? [];
                            $res['directIncome'] = $tmp['cashIn'] ?? [];
                        } elseif ($metric == 'Direct Expenses') {
                            //$res['directExpense'] = $tmp['closingBalance'] ?? [];
                            $res['directExpense'] = $tmp['cashIn'] ?? [];
                        } elseif ($metric == 'Indirect Incomes') {
                            //$res['indirectIncome'] = $tmp['closingBalance'] ?? [];
                            $res['indirectIncome'] = $tmp['cashIn'] ?? [];
                        } elseif ($metric == 'Indirect Expenses') {
                            // $res['indirectExpense'] = $tmp['closingBalance'] ?? [];
                            $res['indirectExpense'] = $tmp['cashIn'] ?? [];
                        }
                        
                    }
                }
                
                if ($t != 5) {
                    // $res = $svc->monthlyGraph($user->id, $from, $to, $t, [
                    $res = $this->cachedMonthlyGraph($svc, $user->id, $from, $to, $t, [
                        'outflow_negative' => false,
                        'groups'           => $groups,
                        'exclude_types'    => null,
                        'date_style'       => null,
                    ]);
                }

                if ($t === $type) {
                    $selectedRes = $res;
                }

                $charts[] = [
                    'key'            => $t,
                    'title'          => $titles[$t],
                    'months'         => $res['months'] ?? [],
                    'in'             => $res['cashIn']  ?? [],
                    'out'            => $res['cashOut'] ?? [],
                    'cash'           => $res['closingBalance'] ?? [],
                    'bank'           => $res['closingBalance'] ?? [],
                    // FIXED: Pass the monthly arrays, not the totals
                    'prevMonthIn'    => $res['prevMonthIn'] ?? [],
                    'prevMonthOut'   => $res['prevMonthOut'] ?? [],
                    'prevQuarterIn'  => $res['prevQuarterIn'] ?? [],
                    'prevQuarterOut' => $res['prevQuarterOut'] ?? [],
                    'prevYearIn'     => $res['prevYearIn'] ?? [],
                    'prevYearOut'    => $res['prevYearOut'] ?? [],
                    'budgetIn'       => $res['budgetIn'] ?? [],
                    'budgetOut'      => $res['budgetOut'] ?? [],
                    'forecastIn'     => $res['forecastIn'] ?? [],
                    'forecastOut'    => $res['forecastOut'] ?? [],
                    'cashflowIn'     => $res['cashflowIn'] ?? [],
                    'cashflowOut'    => $res['cashflowOut'] ?? [],
                    'plIn'           => $res['plIn'] ?? [],
                    'plOut'          => $res['plOut'] ?? [],
                    'sumIn'          => $sum($res['cashIn'] ?? []),
                    'sumOut'         => $sum($res['cashOut'] ?? []),
                    'quarterLabels'  => $res['quarterLabels'] ?? [],
                    'quarterIn'      => $res['quarterIn'] ?? [],
                    'quarterOut'     => $res['quarterOut'] ?? [],
                    'quarterCompare' => $res['quarterCompare'] ?? [],
                    'directIncome'    => $res['directIncome'] ?? [],
                    'directExpense'   => $res['directExpense'] ?? [],
                    'indirectIncome'  => $res['indirectIncome'] ?? [],
                    'indirectExpense' => $res['indirectExpense'] ?? [],
                    // 'prevQuarterIn'  => $res['prevQuarterIn'] ?? [],
                    // 'prevQuarterOut' => $res['prevQuarterOut'] ?? [],
                ];
            }

            // Use selected type (or type 1's) for header FY label / range / tiles
            $basis     = $selectedRes ?: ($charts[$type - 1] ?? []);
            $labelFY   = $selectedRes['fy_label']   ?? ($basis['fy_label']   ?? '');
            $range     = $selectedRes['range']      ?? ($basis['range']      ?? ['from' => $from, 'to' => $to]);
            $allTotals = $selectedRes['allTotals']  ?? ($basis['allTotals']  ?? []);
            $fySel = $r->input('fySel');
            $fyRangeSel = $selectedRangeLabel;
            $activeType = $type;

            // Get groups data for the client - IMPORTANT for cards
            $defaultGroupNames = [
                'Sales Accounts',
                'Purchase Accounts',
                'Sundry Creditors',
                'Sundry Debtors',
                'Cash-in-Hand',
                'Bank Accounts',
                'Direct Incomes',
                'Direct Expenses'
            ];

            try {
                // $allGroupsWithBalances = $svc->getAllGroupsWithBalances($user->id, $from, $to);
                $allGroupsWithBalances = $this->cachedGroupsWithBalances($svc, $user->id, $from, $to);
                $allGroups = collect($allGroupsWithBalances);

                if ($allGroups->isEmpty()) {
                    \Log::warning('No groups with balances found for client ' . $user->id . ', creating demo groups');
                    $allGroups = collect([
                        (object)['iGroupId' => 1, 'strGroupName' => 'Sales Accounts', 'Closing' => 100000, 'Opening' => 80000],
                        (object)['iGroupId' => 2, 'strGroupName' => 'Purchase Accounts', 'Closing' => 75000, 'Opening' => 60000],
                        (object)['iGroupId' => 3, 'strGroupName' => 'Sundry Creditors', 'Closing' => 50000, 'Opening' => 45000],
                        (object)['iGroupId' => 4, 'strGroupName' => 'Sundry Debtors', 'Closing' => 60000, 'Opening' => 55000],
                        (object)['iGroupId' => 5, 'strGroupName' => 'Cash-in-Hand', 'Closing' => 25000, 'Opening' => 20000],
                        (object)['iGroupId' => 6, 'strGroupName' => 'Bank Accounts', 'Closing' => 150000, 'Opening' => 120000],
                        (object)['iGroupId' => 7, 'strGroupName' => 'Direct Incomes', 'Closing' => 30000, 'Opening' => 25000],
                        (object)['iGroupId' => 8, 'strGroupName' => 'Direct Expenses', 'Closing' => 45000, 'Opening' => 40000],
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error fetching groups with balances for client ' . $user->id . ': ' . $e->getMessage());

                // Fallback
                $allGroups = DB::table('GroupMaster')
                    ->where('iPartyId', $user->id)
                    ->select('iGroupId', 'strGroupName')
                    ->orderBy('strGroupName')
                    ->get();

                if ($allGroups->isEmpty()) {
                    $allGroups = collect([
                        (object)['iGroupId' => 1, 'strGroupName' => 'Sales Accounts', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 2, 'strGroupName' => 'Purchase Accounts', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 3, 'strGroupName' => 'Sundry Creditors', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 4, 'strGroupName' => 'Sundry Debtors', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 5, 'strGroupName' => 'Cash-in-Hand', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 6, 'strGroupName' => 'Bank Accounts', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 7, 'strGroupName' => 'Direct Incomes', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 8, 'strGroupName' => 'Direct Expenses', 'Closing' => 0, 'Opening' => 0],
                    ]);
                } else {
                    $allGroups = $allGroups->map(function ($group) {
                        $group->Closing = 0;
                        $group->Opening = 0;
                        return $group;
                    });
                }
            }

            // Get the IDs of the default financial groups
            $defaultGroupIds = $allGroups
                ->whereIn('strGroupName', $defaultGroupNames)
                ->pluck('iGroupId')
                ->toArray();

            if (empty($defaultGroupIds)) {
                $defaultGroupIds = $allGroups->take(8)->pluck('iGroupId')->toArray();
            }

            // Get selected groups from database or use defaults
            $preferences = DB::table('user_card_preferences')
                ->where('user_id', $user->id)
                ->where('party_id', $user->id)
                ->first();

            if ($preferences && $preferences->selected_groups) {
                $selectedGroups = json_decode($preferences->selected_groups, true);
                $selectedGroups = array_map('intval', $selectedGroups);
            } else {
                $selectedGroups = $defaultGroupIds;
            }

            // Ensure selected groups are valid
            $validSelectedGroups = [];
            foreach ($selectedGroups as $groupId) {
                if ($allGroups->contains('iGroupId', $groupId)) {
                    $validSelectedGroups[] = $groupId;
                }
            }

            if (empty($validSelectedGroups)) {
                $validSelectedGroups = $defaultGroupIds;
            }

            $selectedGroups = $validSelectedGroups;
            $selectedGroupsWithBalances = $allGroups->whereIn('iGroupId', $selectedGroups)
                ->map(function ($group) {
                    return [
                        'iGroupId' => (int)$group->iGroupId,
                        'strGroupName' => $group->strGroupName,
                        'Closing' => (float)($group->Closing ?? 0),
                        'Opening' => (float)($group->Opening ?? 0),
                    ];
                })
                ->values()
                ->toArray();

            // Get document counts for the client
            // $rows = DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$user->id]);
            $rows = $this->cachedDocumentSummary($user->id);
            $row  = $rows[0] ?? (object) [];

            $uploadedCount   = (int) ($row->uploaded_count    ?? 0);
            $inProgressCount = (int) ($row->in_progress_count ?? 0);
            $completedCount  = (int) ($row->completed_count   ?? 0);
            $rejectedCount   = (int) ($row->rejected_count    ?? 0);
            $acceptedCount   = (int) ($row->accepted_count    ?? 0);

            $activeTab = $r->get('tab', 'financial');

            // CRITICAL: Create group cards data from selected groups with balances
            // This is what the client dashboard uses to show all account cards
            $allGroupCards = [];
            foreach ($selectedGroupsWithBalances as $group) {
                $allGroupCards[] = [
                    'key' => 'group_' . $group['iGroupId'],
                    'iGroupId' => $group['iGroupId'],
                    'value' => $group['Closing'],
                    'name' => $group['strGroupName'],
                    'label' => $group['strGroupName'],
                    'accent' => $this->getAccentColor($group['strGroupName']),
                    'icon' => $this->getGroupIcon($group['strGroupName'])
                ];
            }

            // Keep the original allTotals for backward compatibility if needed
            if (empty($allTotals)) {
                $allTotals = [
                    'totalSale'     => ["iGroupId" => $allGroups->where('strGroupName', 'Sales Accounts')->first()->iGroupId ?? null, "value" => $sum($charts[0]['in'] ?? []), "name" => "Sales"],
                    'totalPurchase' => ["iGroupId" => $allGroups->where('strGroupName', 'Purchase Accounts')->first()->iGroupId ?? null, "value" => $sum($charts[0]['out'] ?? []), "name" => "Purchase"],
                    'totalCredit'   => ["iGroupId" => $allGroups->where('strGroupName', 'Sundry Creditors')->first()->iGroupId ?? null, "value" => $sum($charts[1]['in'] ?? []), "name" => "Creditors"],
                    'totalDebit'    => ["iGroupId" => $allGroups->where('strGroupName', 'Sundry Debtors')->first()->iGroupId ?? null, "value" => $sum($charts[1]['out'] ?? []), "name" => "Debtors"],
                    'totalCash'     => ["iGroupId" => $allGroups->where('strGroupName', 'Cash-in-Hand')->first()->iGroupId ?? null, "value" => $allGroups->where('strGroupName', 'Cash-in-Hand')->first()->Closing ?? 0, "name" => "Cash"],
                    'totalBank'     => ["iGroupId" => $allGroups->where('strGroupName', 'Bank Accounts')->first()->iGroupId ?? null, "value" => $allGroups->where('strGroupName', 'Bank Accounts')->first()->Closing ?? 0, "name" => "Bank"],
                ];
            }
            // $resp = $svc->pandl($user->id, $from, $to);
            $resp = $this->cachedPandl($svc, $user->id, $from, $to);
            $partyguid = $guid;                
            $partyId   = $user->id;
            
            // $respbalance = $svc->balanceSheet($partyguid, $partyId, $from, $to);
            $respbalance = $this->cachedBalanceSheet($svc, $partyguid, $partyId, $from, $to);
            $plData = $resp['data'] ?? [];
            $bsData = $respbalance['data'] ?? [];

            return view('admin.clients.reports.dashboard', compact(
                'summary',
                'type',
                'charts',
                'labelFY',
                'range',
                'allTotals',
                'fySel',
                'guid',
                'user',
                'allGroups',
                'selectedGroups',
                'selectedGroupsWithBalances',
                'defaultGroupIds',
                'uploadedCount',
                'inProgressCount',
                'completedCount',
                'rejectedCount',
                'acceptedCount',
                'activeTab',
                'allGroupCards', // This is the key variable for showing all account cards
                'plData',
                'bsData',
                'financialYears',
                'fyRangeSel',
                'activeType'
            ));
        } catch (\Throwable $e) {
            \Log::error("Dashboard error for GUID {$guid}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('clients.index')
                ->with('error', 'Something went wrong while loading the dashboard. Please try again.');
        }
    }

    protected function getAccentColor($groupName)
    {
        $colorMap = [
            'bank' => 'blue',
            'cash' => 'emerald',
            'sales' => 'green',
            'purchase' => 'orange',
            'debtors' => 'violet',
            'creditors' => 'rose',
            'assets' => 'indigo',
            'liabilities' => 'amber',
            'capital' => 'teal',
            'income' => 'lime',
            'expenses' => 'red',
            'tax' => 'purple',
            'stock' => 'cyan',
            'loan' => 'fuchsia',
            'investment' => 'sky',
        ];

        $groupName = strtolower(trim($groupName));
        
        foreach ($colorMap as $key => $color) {
            if (str_contains($groupName, $key)) {
                return $color;
            }
        }
        
        // Default colors rotation
        $defaultColors = ['blue', 'amber', 'violet', 'fuchsia', 'teal', 'indigo', 'emerald', 'rose'];
        $hash = crc32($groupName);
        return $defaultColors[$hash % count($defaultColors)];
    }

    protected function getGroupIcon($groupName)
    {   
        $iconMap = [
            'bank' => 'bank-od.png',
            'cash' => 'cash.png',
            'bank accounts' => 'bank-account.png',
            'cash-in-hand' => 'cash-in-hand.png',
            'current assets' => 'current-assets.png',
            'fixed assets' => 'fixed-assets.png',
            'investments' => 'investment.png',
            'current liabilities' => 'current-laibities.png',
            'deposits (asset)' => 'deposit.png',
            'provisions' => 'provision.png',
            'reserves & surplus' => 'reserve.png',
            'stock-in-hand' => 'stock-in-hand.png',
            'sundry creditors' => 'sundry-creditors.png',
            'sundry debtors' => 'sundry-debitors.png',
            'sales' => 'sales.png',
            'income' => 'income.png',
            'suspense a/c' => 'suspense-acc.png',
            'purchase' => 'purchase.png',
            'expenses' => 'expenses.png',
            'secured loans' => 'secured-loan.png',
            'unsecured loans' => 'unsecured-loan.png',
            'debtors' => 'debtors.png',
            'creditors' => 'creditors.png',

            'capital' => 'capital.png',

            'stock' => 'stock.png',

            'loans' => 'loan(laibility).png',
            'loans & advances (asset)' => 'loans_advance.png',

            'tax' => 'tax.png',
            'duties & taxes' => 'duties&taxes.png',
            'direct expenses' => 'direct_expense.png',
            'direct incomes' => 'direct_income.png',
            'indirect expense' => 'indirect_expense.png',
            'indirect incomes' => 'indirect_income.png',
            // fallback
            
        ];

        $groupName = strtolower(trim($groupName));

        // Exact match
        if (isset($iconMap[$groupName])) {
            // if($groupName == "loans & advances (asset)"){
            //     dd($iconMap[$groupName]);
            // }
            return asset('assets/images/' . $iconMap[$groupName]);
        }

        // Partial match
        foreach ($iconMap as $key => $icon) {
            if (str_contains($groupName, $key)) {
                return asset('assets/images/' . $icon);
            }
        }

        // Default fallback
        return asset('assets/images/document.png');
    }
    // protected function getGroupIcon($groupName)
    // {
    //     $iconMap = [
    //         // Financial & Banking
    //         'bank' => 'fa-solid fa-building-columns',
    //         'cash' => 'fa-solid fa-money-bill-wave',
    //         'bank accounts' => 'fa-solid fa-building-columns',
    //         'cash-in-hand' => 'fa-solid fa-money-bill-wave',
    //         'current assets' => 'fa-solid fa-chart-line',
    //         'fixed assets' => 'fa-solid fa-industry',
    //         'investments' => 'fa-solid fa-chart-pie',
            
    //         // Sales & Revenue
    //         'sales' => 'fa-solid fa-tags',
    //         'sales accounts' => 'fa-solid fa-tags',
    //         'income' => 'fa-solid fa-money-bill-trend-up',
    //         'revenue' => 'fa-solid fa-money-bill-wave',
            
    //         // Purchases & Expenses
    //         'purchase' => 'fa-solid fa-cart-shopping',
    //         'purchase accounts' => 'fa-solid fa-cart-shopping',
    //         'expenses' => 'fa-solid fa-receipt',
    //         'direct expenses' => 'fa-solid fa-truck',
    //         'indirect expenses' => 'fa-solid fa-file-invoice-dollar',
            
    //         // Debtors & Creditors
    //         'debtors' => 'fa-solid fa-hand-holding-dollar',
    //         'creditors' => 'fa-solid fa-hand-holding-hand',
    //         'sundry debtors' => 'fa-solid fa-hand-holding-dollar',
    //         'sundry creditors' => 'fa-solid fa-hand-holding-hand',
    //         'receivables' => 'fa-solid fa-arrow-down-to-line',
    //         'payables' => 'fa-solid fa-arrow-up-from-line',
            
    //         // Capital & Liabilities
    //         'capital' => 'fa-solid fa-landmark',
    //         'liabilities' => 'fa-solid fa-scale-balanced',
    //         'current liabilities' => 'fa-solid fa-clock-rotate-left',
    //         'long term liabilities' => 'fa-solid fa-calendar-day',
            
    //         // Stock & Inventory
    //         'stock' => 'fa-solid fa-boxes-stacked',
    //         'inventory' => 'fa-solid fa-warehouse',
    //         'stock-in-hand' => 'fa-solid fa-boxes-stacked',
            
    //         // Loans & Advances
    //         'loans' => 'fa-solid fa-hand-holding-dollar',
    //         'advances' => 'fa-solid fa-forward',
    //         'loan' => 'fa-solid fa-hand-holding-dollar',
    //         'advance' => 'fa-solid fa-forward',
            
    //         // Tax
    //         'tax' => 'fa-solid fa-percent',
    //         'duties' => 'fa-solid fa-scale-balanced',
    //         'taxes' => 'fa-solid fa-percent',
            
    //         // General
    //         'accounts' => 'fa-solid fa-book',
    //         'ledger' => 'fa-solid fa-book-open',
    //         'general' => 'fa-solid fa-gear',
    //         'miscellaneous' => 'fa-solid fa-cube',
    //         'profit' => 'fa-solid fa-chart-line',
    //         'loss' => 'fa-solid fa-chart-line-down',
            
    //         // Default fallbacks
    //         'assets' => 'fa-solid fa-chart-line',
    //         'equity' => 'fa-solid fa-scale-balanced',
    //         'revenue' => 'fa-solid fa-money-bill-wave',
    //     ];

    //     $groupName = strtolower(trim($groupName));
        
    //     // Exact match
    //     if (isset($iconMap[$groupName])) {
    //         return $iconMap[$groupName];
    //     }
        
    //     // Partial match
    //     foreach ($iconMap as $key => $icon) {
    //         if (str_contains($groupName, $key)) {
    //             return $icon;
    //         }
    //     }
        
    //     // Default icon based on group type
    //     if (str_contains($groupName, 'asset')) {
    //         return 'fa-solid fa-chart-line';
    //     } elseif (str_contains($groupName, 'liabilit')) {
    //         return 'fa-solid fa-scale-balanced';
    //     } elseif (str_contains($groupName, 'income') || str_contains($groupName, 'revenue')) {
    //         return 'fa-solid fa-money-bill-wave';
    //     } elseif (str_contains($groupName, 'expense') || str_contains($groupName, 'cost')) {
    //         return 'fa-solid fa-receipt';
    //     } elseif (str_contains($groupName, 'capital')) {
    //         return 'fa-solid fa-landmark';
    //     } elseif (str_contains($groupName, 'bank') || str_contains($groupName, 'cash')) {
    //         return 'fa-solid fa-building-columns';
    //     }
        
    //     // Ultimate fallback
    //     return 'fa-solid fa-cube';
    // }

    public function documentDashboard(Request $r, $guid = null, ReportsService $svc)
    {
        if (!$guid) {
            return redirect()
                ->route('clients.index')
                ->with('error', 'Invalid request. Client GUID is missing.');
        }
        $user = Client::where('guid', $guid)->first();
        $userId = (int) $user->id;

        // Summary labels (unchanged)
        $summary = [
            ['key' => '1', 'value' => "Sale & Purchase"],
            ['key' => '2', 'value' => "Credit & Debit"],
            ['key' => '3', 'value' => "Recepit & Payment"],
            ['key' => '4', 'value' => "Cash & Bank balance"]
        ];

        // ---- CALL STORED PROCEDURE ----
        // $rows = \DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$userId]);
        $rows = $this->cachedDocumentSummary($userId);
        $row  = $rows[0] ?? (object) [];

        $uploadedCount   = (int) ($row->uploaded_count    ?? 0);
        $inProgressCount = (int) ($row->in_progress_count ?? 0);
        $completedCount  = (int) ($row->completed_count   ?? 0);
        $rejectedCount   = (int) ($row->rejected_count    ?? 0);

        // ---- Chart logic (your existing code, unchanged) ----
        $type = (int) $r->input('type', 1);
        $from = $r->input('from');
        $to   = $r->input('to');

        $titles = [
            1 => 'Sales vs Purchase',
            2 => 'Creditors vs Debtors',
            3 => 'Receipt vs Payment',
            4 => 'Cash & Bank Flow',
        ];

        $charts      = [];
        $selectedRes = null;
        $sum         = fn($arr) => array_sum(array_map('floatval', $arr ?? []));

        for ($t = 1; $t <= 4; $t++) {
            // $res = $svc->monthlyGraph($userId, $from, $to, $t, [
            $res = $this->cachedMonthlyGraph($svc, $userId, $from, $to, $t, [
                'outflow_negative' => false,
                'groups'           => null,
                'exclude_types'    => null,
                'date_style'       => null,
            ]);

            if ($t === $type) {
                $selectedRes = $res;
            }

            $charts[] = [
                'key'    => $t,
                'title'  => $titles[$t],
                'months' => $res['months'] ?? [],
                'in'     => $res['cashIn']  ?? [],
                'out'    => $res['cashOut'] ?? [],
                'sumIn'  => $sum($res['cashIn']  ?? []),
                'sumOut' => $sum($res['cashOut'] ?? []),
            ];
        }

        $basis     = $selectedRes ?: $charts[0] ?? [];
        $labelFY   = $selectedRes['fy_label']  ?? ($basis['fy_label'] ?? '');
        $range     = $selectedRes['range']     ?? ($basis['range'] ?? ['from' => $from, 'to' => $to]);
        $allTotals = $selectedRes['allTotals'] ?? ($basis['allTotals'] ?? []);
        $fySel     = $r->input('fySel');

        return view('admin.clients.reports.documentDashboard', [
            // counts from SP
            'uploaded_count'    => $uploadedCount,
            'in_progress_count' => $inProgressCount,
            'completed_count'   => $completedCount,
            'rejected_count'    => $rejectedCount,

            // tiles + charts
            'summary'    => $summary,
            'activeType' => $type,
            'charts'     => $charts,
            'labelFY'    => $labelFY,
            'range'      => $range,
            'allTotals'  => $allTotals,
            'fySel'      => $fySel,
            'guid'      => $guid,
            'user'      => $user
        ]);
    }
    
    // public function suspense()
    // {
    //     $transactions = BankTransaction::where('is_suspense', 1)->latest()->get();
    //     return view('admin.clients.bank.suspense', compact('transactions'));
    // }

    public function suspense(Request $request)
    {
        $auth = auth()->user();
        $query = BankTransaction::where('is_suspense', 1)->where('iPartyId',$auth->id);
        // Date filter
        if ($request->from_date) {
            $query->whereDate('txn_date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('txn_date', '<=', $request->to_date);
        }
        $transactions = $query->latest()->paginate(10);
        return view('admin.clients.bank.suspense', compact('transactions'));
    }

    public function resolveSuspense(Request $request)
    {
        // 🔹 CASE 1: MULTIPLE
        if ($request->has('txn_ids')) {
            $ids = $request->txn_ids;
            BankTransaction::whereIn('id', $ids)->update([
                'is_suspense' => 0,
                'resolution_remark' => $request->remark
            ]);
            return response()->json(['status' => true, 'type' => 'bulk']);
        }

        // 🔹 CASE 2: SINGLE
        if ($request->has('txn_id')) {
            $row = BankTransaction::find($request->txn_id);
            if (!$row) {
                return response()->json(['status' => false]);
            }
            $row->update([
                'is_suspense' => 0,
                'resolution_remark' => $request->remark
            ]);
            return response()->json(['status' => true, 'type' => 'single']);
        }

        return response()->json(['status' => false, 'message' => 'No data']);
    }

    public function resolvedSuspense(Request $request)
    {
        $auth = auth()->user();
        $query = BankTransaction::where('is_suspense', 0)
            ->whereNotNull('resolution_remark')
            ->where('status', '<>','Synced')
            ->where('iPartyId', $auth->id);
        if ($request->from_date) {
            $query->whereDate('txn_date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('txn_date', '<=', $request->to_date);
        }
        $transactions = $query->latest()->paginate(10);

        return view(
            'admin.clients.bank.resolved_suspense',
            compact('transactions')
        );
    }

    public function updateRemark(Request $request)
    {
        // 🔹 CASE 1: MULTIPLE
        if ($request->has('txn_ids')) {
            $ids = $request->txn_ids;
            BankTransaction::whereIn('id', $ids)->update([
                //'is_suspense' => 0,
                'resolution_remark_new' => $request->remark
            ]);
            return response()->json(['status' => true, 'type' => 'bulk']);
        }

        // 🔹 CASE 2: SINGLE
        if ($request->has('txn_id')) {
            $row = BankTransaction::find($request->txn_id);
            if (!$row) {
                return response()->json(['status' => false]);
            }
            $row->update([
                //'is_suspense' => 0,
                'resolution_remark_new' => $request->remark
            ]);
            return response()->json(['status' => true, 'type' => 'single']);
        }

        return response()->json(['status' => false, 'message' => 'No data']);
    }

    // public function resolveSuspense(Request $request)
    // {
    //     $row = BankTransaction::find($request->txn_id);
    //     if (!$row) {
    //         return response()->json(['status' => false]);
    //     }
    //     $row->update([
    //         'is_suspense' => 0,
    //         //'status' => 'pending', // 👈 important
    //         'resolution_remark' => $request->remark
    //     ]);
    //     return response()->json(['status' => true]);
    // }

    public function Gstindex($guid)
    {
        $user = Client::where('guid', $guid)->first();
        $iPartyId = $user->id;
        $setting = GstSetting::where('iPartyId', $iPartyId)->first();
        
        $mappedLedgerIds = DB::table('LedgerMaster')
            ->where('iPartyId', $iPartyId)
            ->where(function($q){
                $q->whereNotNull('CGSTLedgerId')
                ->orWhereNotNull('SGSTLedgerId')
                ->orWhereNotNull('IGSTLedgerId');
            })
            ->pluck('iLedgerId');

        $availableLedgers = DB::table('LedgerMaster')
            ->where('iPartyId', $iPartyId)
            ->whereNotIn('iLedgerId', $mappedLedgerIds)
            ->whereIn('strParents', ['Sales Accounts','Purchase Accounts','Duties & Taxes', 'Direct Incomes', 'Direct Expenses', 'Indirect Incomes', 'Indirect Expenses'])
            ->orderBy('strCustomerName')
            ->get();

        $mappedItemIds = DB::table('StockItemMaster')
            ->where('iPartyId', $iPartyId)
            ->where(function($q){
                $q->whereNotNull('CGSTLedgerId')
                ->orWhereNotNull('SGSTLedgerId')
                ->orWhereNotNull('IGSTLedgerId');
            })
            ->pluck('iStockIdtemId');

        $mappedLedgers = DB::table('LedgerMaster as LM')
            ->leftJoin('LedgerMaster as CGST','LM.CGSTLedgerId','=','CGST.iLedgerId')
            ->leftJoin('LedgerMaster as SGST','LM.SGSTLedgerId','=','SGST.iLedgerId')
            ->leftJoin('LedgerMaster as IGST','LM.IGSTLedgerId','=','IGST.iLedgerId')
            ->where('LM.iPartyId', $iPartyId)
            ->where(function($q){
                $q->whereNotNull('LM.CGSTLedgerId')
                ->orWhereNotNull('LM.SGSTLedgerId')
                ->orWhereNotNull('LM.IGSTLedgerId');
            })
            ->select(
                'LM.iLedgerId',
                'LM.strCustomerName',
                'CGST.strCustomerName as CGSTLedgerName',
                'SGST.strCustomerName as SGSTLedgerName',
                'IGST.strCustomerName as IGSTLedgerName',
                'LM.CGSTLedgerId',
                'LM.SGSTLedgerId',
                'LM.IGSTLedgerId'
            )
            ->orderBy('LM.strCustomerName')
            ->get();
            
        $cgstLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strCustomerName', 'LIKE', '%CGST%')
            ->orderBy('strCustomerName')
            ->get();

        $sgstLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strCustomerName', 'LIKE', '%SGST%')
            ->orderBy('strCustomerName')
            ->get();

        $igstLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strCustomerName', 'LIKE', '%IGST%')
            ->orderBy('strCustomerName')
            ->get();

        $salesLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strParents', 'Sales Accounts')
            ->orderBy('strCustomerName')
            ->get();
        
        $purchaseLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strParents', 'Purchase Accounts')
            ->orderBy('strCustomerName')
            ->get();

        $indeirectIncometLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strParents', 'Indirect Incomes')
            ->orderBy('strCustomerName')
            ->get();

        $directIncometLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strParents', 'Direct Incomes')
            ->orderBy('strCustomerName')
            ->get();

        $indeirectExpensesLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strParents', 'Indirect Expenses')
            ->orderBy('strCustomerName')
            ->get();

        $directExpensesLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
            ->where('strParents', 'Direct Expenses')
            ->orderBy('strCustomerName')
            ->get();

        $stockItems = DB::table('StockItemMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName')
            ->get();

        $mappedItems = DB::table('StockItemMaster as SI')
            ->leftJoin('LedgerMaster as CGST','SI.CGSTLedgerId','=','CGST.iLedgerId')
            ->leftJoin('LedgerMaster as SGST','SI.SGSTLedgerId','=','SGST.iLedgerId')
            ->leftJoin('LedgerMaster as IGST','SI.IGSTLedgerId','=','IGST.iLedgerId')
            ->where('SI.iPartyId',$iPartyId)
            ->where(function($q){
                $q->whereNotNull('SI.CGSTLedgerId')
                ->orWhereNotNull('SI.SGSTLedgerId')
                ->orWhereNotNull('SI.IGSTLedgerId');
            })
            ->select(
                'SI.*',
                'CGST.strCustomerName as CGSTLedgerName',
                'SGST.strCustomerName as SGSTLedgerName',
                'IGST.strCustomerName as IGSTLedgerName'
            )
            ->get();

        $availableItems = DB::table('StockItemMaster')
            ->where('iPartyId',$iPartyId)
            // ->whereNull('CGSTLedgerId')
            // ->whereNull('SGSTLedgerId')
            // ->whereNull('IGSTLedgerId')
            ->whereNotIn('iStockIdtemId', $mappedItemIds)
            ->get();

        $roundOffLedgers = DB::table('LedgerMaster')
            ->where('iPartyId', $iPartyId)
            ->where(function ($query) {
                $query->where('strCustomerName', 'LIKE', '%round off%')
                    ->orWhere('strCustomerName', 'LIKE', '%roundoff%');
            })
            ->orderBy('strCustomerName')
            ->get();

        // $taxesLedgers = DB::table('LedgerMaster')->where('iPartyId', $iPartyId)
        //     ->where('strParents', 'Duties & Taxes')
        //     ->orderBy('strCustomerName')
        //     ->get();

        return view('admin.clients.settings.gst.index', compact('mappedItems','availableItems','mappedLedgers','mappedLedgerIds','availableLedgers','setting', 'cgstLedgers', 'sgstLedgers', 'igstLedgers', 'salesLedgers', 'purchaseLedgers', 'indeirectIncometLedgers', 'directIncometLedgers', 'indeirectExpensesLedgers', 'directExpensesLedgers','stockItems','user','roundOffLedgers'));
    }

    public function updateRoundoffSetting(Request $request)
    {
        $validated = $request->validate([
            'guid' => ['required', 'string'],
            'roundoff_side' => ['required', 'in:upper_side,lower_side,normal'],
            'roundoff_ledger_id' => ['nullable', 'integer'],
        ]);

        $client = Client::where('guid', $validated['guid'])->firstOrFail();
        $roundOffLedger = null;

        if (!empty($validated['roundoff_ledger_id'])) {
            $roundOffLedger = DB::table('LedgerMaster')
                ->where('iPartyId', $client->id)
                ->where('iLedgerId', $validated['roundoff_ledger_id'])
                ->first();
        }

        $client->profile()->updateOrCreate(
            ['user_id' => $client->id],
            [
                'roundoff_side' => $validated['roundoff_side'],
                'roundoff_ledger_id' => $roundOffLedger?->iLedgerId,
                'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Roundoff setting saved successfully.',
        ]);
    }

    public function saveGstMapping(Request $request)
    {
        $client = Client::where('guid',$request->guid)->firstOrFail();
        foreach($request->ledger_ids as $ledgerId)
        {
            DB::table('LedgerMaster')
                ->where('iPartyId',$client->id)
                ->where('iLedgerId',$ledgerId)
                ->update([
                    'CGSTLedgerId' => $request->cgst_id,
                    'SGSTLedgerId' => $request->sgst_id,
                    'IGSTLedgerId' => $request->igst_id,
                ]);
        }
        return response()->json([
            'success'=>true
        ]);
    }

    public function deleteGstMapping($id)
    {
        DB::table('LedgerMaster')
            ->where('iLedgerId', $id)
            ->update([
                'CGSTLedgerId' => null,
                'SGSTLedgerId' => null,
                'IGSTLedgerId' => null,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'GST Mapping deleted successfully.'
        ]);
    }

    public function saveItemGstMapping(Request $request)
    {
        $client = Client::where('guid',$request->guid)->firstOrFail();

        foreach($request->item_ids as $itemId)
        {
            DB::table('StockItemMaster')
                ->where('iPartyId',$client->id)
                ->where('iStockIdtemId',$itemId)
                ->update([
                    'CGSTLedgerId' => $request->cgst_id,
                    'SGSTLedgerId' => $request->sgst_id,
                    'IGSTLedgerId' => $request->igst_id,
                ]);
        }

        return response()->json([
            'success'=>true
        ]);
    }

    public function deleteItemGstMapping($id)
    {
        DB::table('StockItemMaster')
            ->where('iStockIdtemId',$id)
            ->update([
                'CGSTLedgerId'=>null,
                'SGSTLedgerId'=>null,
                'IGSTLedgerId'=>null,
            ]);

        return response()->json([
            'success'=>true
        ]);
    }

    public function GstSettingupdate(Request $request)
    {
        $client = Client::where('guid', $request->guid)->firstOrFail();
        $partyId = $client->id;
        // Update Ledger GST Mapping
        if ($request->filled('ledger_ids'))
        {
            foreach ($request->ledger_ids as $ledgerId)
            {
                DB::table('LedgerMaster')
                    ->where('iPartyId', $partyId)
                    ->where('iLedgerId', $ledgerId)
                    ->update([
                        'CGSTLedgerId' => !empty($request->ledger_cgst[$ledgerId])
                            ? $request->ledger_cgst[$ledgerId]
                            : null,

                        'SGSTLedgerId' => !empty($request->ledger_sgst[$ledgerId])
                            ? $request->ledger_sgst[$ledgerId]
                            : null,

                        'IGSTLedgerId' => !empty($request->ledger_igst[$ledgerId])
                            ? $request->ledger_igst[$ledgerId]
                            : null,
                    ]);
            }
        }

        // Update Item GST Mapping
        if ($request->filled('item_ids'))
        {
            foreach ($request->item_ids as $itemId)
            {
                DB::table('StockItemMaster')
                    ->where('iPartyId', $partyId)
                    ->where('iStockIdtemId', $itemId)
                    ->update([
                        'CGSTLedgerId' => !empty($request->item_cgst[$itemId])
                            ? $request->item_cgst[$itemId]
                            : null,

                        'SGSTLedgerId' => !empty($request->item_sgst[$itemId])
                            ? $request->item_sgst[$itemId]
                            : null,

                        'IGSTLedgerId' => !empty($request->item_igst[$itemId])
                            ? $request->item_igst[$itemId]
                            : null,
                    ]);
            }
        }

        return back()->with(
            'success',
            'GST Settings Updated Successfully.'
        );
    }
}
