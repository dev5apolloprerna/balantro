<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DataEntryOperator;
use App\Models\Group;
use App\Models\Manager;
use App\Models\Permission;
use App\Models\Supervisor;
use App\Services\DeoAssignmentService;
use App\Services\FilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\UserDevice;

class DataEntryOperatorsController extends Controller
{
    protected $dataEntryOperator;
    protected $dataEntryOperators;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->route('data_entry_operator') || $request->id) {
                $this->setDataEntryOperator($request);
            }
            if (in_array($request->route()->getActionMethod(), ['update', 'destroy', 'assignUsers', 'assignGroups', 'assignPermissions'])) {
                $this->setDataEntryOperators($request);
            }
            return $next($request);
        });
    }


    public function index(Request $r)
    {
        $auth = auth()->user();

        $q = DataEntryOperator::query()
            ->with(['managers', 'supervisors', 'groups'])
            ->orderByDesc('created_at');

        /* ── ROLE SCOPING ───────────────────────────────────────────── */
        if ($auth->role === User::ROLES['manager']) {
            // Only DEOs associated to this manager
            $q->whereHas('managers', fn($rel) => $rel->whereKey($auth->id));
        } elseif ($auth->role === User::ROLES['supervisor']) {
            // Only DEOs associated to this supervisor
            $q->whereHas('supervisors', fn($rel) => $rel->whereKey($auth->id));
        } elseif ($auth->role !== User::ROLES['super_admin']) {
            // (Optional) lock down other roles; customize if DEO should see self only, etc.
            // $q->whereRaw('1=0');
        }

        /* ── YOUR EXISTING FILTERS (search, etc.) GO HERE ───────────── */
        // e.g. if ($r->filled('query')) { ... }

        // Manager filter: only super admin can use this (no escalation for mgr/supervisor)
        if (
            $auth->role === User::ROLES['super_admin']
            && $r->filled('manager_id') && ctype_digit((string)$r->manager_id)
        ) {
            $mid = (int) $r->manager_id;
            $q->whereHas('managers', fn($rel) => $rel->whereKey($mid));
        }

        // Supervisor filter: allow super admin; managers may filter supervisors under them if you like
        if (
            in_array($auth->role, [User::ROLES['super_admin'], User::ROLES['manager']], true)
            && $r->filled('supervisor_id') && ctype_digit((string)$r->supervisor_id)
        ) {
            $sid = (int) $r->supervisor_id;
            $q->whereHas('supervisors', fn($rel) => $rel->whereKey($sid));
        }

        // Group filter: allowed for all (keeps result within already-scoped set)
        if ($r->filled('group_id') && ctype_digit((string)$r->group_id)) {
            $gid = (int) $r->group_id;
            $q->whereHas('groups', fn($rel) => $rel->where('groups.id', $gid));
        }

        $dataEntryOperators = $q->paginate(10)->appends($r->query());

        /* ── DROPDOWNS (ROLE-AWARE) ─────────────────────────────────── */
        if ($auth->role === User::ROLES['super_admin']) {
            $managers    = Manager::select('id', 'name')->orderBy('name')->get();
            $supervisors = Supervisor::select('id', 'name')->orderBy('name')->get();
            $mgrSupMap   = Manager::with(['supervisors:id,name'])
                ->get()
                ->mapWithKeys(fn($m) => [$m->id => $m->supervisors
                    ->map(fn($s) => ['id' => $s->id, 'name' => $s->name])])
                ->toArray();
        } elseif ($auth->role === User::ROLES['manager']) {
            // Manager sees self in Manager dropdown; supervisors only under them
            $managers    = Manager::whereKey($auth->id)->get(['id', 'name']);
            $supervisors = Supervisor::whereHas('managers', fn($rel) => $rel->whereKey($auth->id))
                ->select('id', 'name')->orderBy('name')->get();
            $mgrSupMap   = [
                $auth->id => $supervisors->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()->all(),
            ];
        } elseif ($auth->role === User::ROLES['supervisor']) {
            // Supervisor sees managers that are connected to them (optional), and self as the only supervisor
            $managers    = Manager::whereHas('supervisors', fn($rel) => $rel->whereKey($auth->id))
                ->select('id', 'name')->orderBy('name')->get();
            $supervisors = Supervisor::whereKey($auth->id)->get(['id', 'name']);
            $mgrSupMap   = Manager::with(['supervisors' => fn($q) => $q->select('users.id', 'users.name')
                ->whereKey($auth->id)])
                ->whereHas('supervisors', fn($rel) => $rel->whereKey($auth->id))
                ->get()
                ->mapWithKeys(fn($m) => [$m->id => $m->supervisors
                    ->map(fn($s) => ['id' => $s->id, 'name' => $s->name])])
                ->toArray();
        } else {
            $managers = collect();
            $supervisors = collect();
            $mgrSupMap = [];
        }

        $groups      = Group::select('id', 'name')->orderBy('name')->get();
        $permissions = Permission::select('id', 'name', 'action', 'subject')
            ->whereNotIn('subject', ['managers','supervisors','user_devices','themes','data_entry_operators'])
            ->orderBy('name')->get();

        /* ── VIEW FLAGS ─────────────────────────────────────────────── */
        $canAdd = $auth->role === User::ROLES['super_admin'];                 // show “Add”
        $canAllocateSupervisor = in_array($auth->role, [                      // show “Allocate Supervisor”
            User::ROLES['manager'],
            User::ROLES['supervisor'],
        ], true);
        $user = $auth;
        return view('admin.data_entry_operators.index', compact(
            'dataEntryOperators',
            'managers',
            'supervisors',
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
        return $this->createUser(DataEntryOperator::class, $request);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $request->merge([
            'role' => 4,            // your default role id
            'type' => User::ROLES['data_entry_operator'],    // or whatever value fits your schema
            'password' => Hash::make('Dataentryoperator1!'), // random 12-char password
            'confirmation_token' => Str::uuid()->toString(), // random unique token

        ]);
        // ✅ Correct: first the model class, then the request object
        return $this->createUser(\App\Models\DataEntryOperator::class, $request);

        //return $this->createUser(\App\Models\DataEntryOperator::class, new Request($data));
    }

    public function update(Request $request, DataEntryOperator $dataEntryOperator)
    {
        if ($dataEntryOperator->update($this->dataEntryOperatorParams($request))) {
            if ($request->filled('fcm_token')) {
                UserDevice::where([
                    'user_id' => $dataEntryOperator->id,
                    'device_type' => $request->device_type,
                    'browser_name' => $request->browser_name
                ])->updateOrCreate(
                    [
                        'device_token' => $request->fcm_token,
                    ],
                    [
                        'user_id'      => $dataEntryOperator->id,
                        'device_type'  => $request->device_type ?? 'pc',
                        'browser_name' => $request->browser_name,
                        'os_name'      => $request->os_name,
                        'is_active'    => true,
                    ]
                );
            }
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('admin.data_entry_operators.flash.operator_update_msg')
                ]);
            }

            return redirect()->back()
                ->with('notice', __('admin.data_entry_operators.flash.operator_update_msg'));
        }

        return response()->json([
            'status' => 'error',
            'errors' => $dataEntryOperator->errors()
        ], 422);
    }
    
    public function destroy(Request $request, \App\Models\DataEntryOperator $dataEntryOperator)
    {
        $deoId = (int) $dataEntryOperator->getKey();

        // ===== 1) guardrails: block if relationships exist =====
        $clientCount   = DB::table('clients_data_entry_operators')
            ->where('data_entry_operator_id', $deoId)
            ->count();

        $hasManagers    = $dataEntryOperator->managers()->exists();
        $hasSupervisors = $dataEntryOperator->supervisors()->exists();

        if ($clientCount > 0 || $hasManagers || $hasSupervisors) {
            $reasons = [];
            if ($clientCount > 0)   $reasons[] = "{$clientCount} client(s) still assigned";
            if ($hasManagers)       $reasons[] = "manager(s) still attached";
            if ($hasSupervisors)    $reasons[] = "supervisor(s) still attached";

            $msg = 'Cannot delete operator: ' . implode(', ', $reasons) . '. Unassign first, then retry.';

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'blocked',
                    'message' => $msg,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return redirect()
                ->route('data_entry_operators.index', ['page' => $request->page])
                ->with('alert', $msg);
        }

        // ===== 2) do work inside a transaction =====
        DB::beginTransaction();
        try {
            // 2a) Prefix email with "deleted_" (and id) BEFORE soft delete to avoid unique index conflicts
            $originalEmail = (string) $dataEntryOperator->email;

            // Only prefix if not already prefixed
            $alreadyPrefixed = Str::startsWith($originalEmail, 'deleted_');

            if (!$alreadyPrefixed) {
                // Build a unique, safe email (deleted_{id}_{local}@{domain})
                $prefix = "deleted_{$deoId}_";

                // If email is malformed (no "@"), just prefix whole thing
                if (!Str::contains($originalEmail, '@')) {
                    $newEmail = $prefix . $originalEmail;
                } else {
                    [$local, $domain] = explode('@', $originalEmail, 2);

                    // Compose and trim to a conservative 255 characters (SQL Server NVARCHAR(255) is common)
                    // Reserve room for "@{domain}"
                    $maxTotal   = 255;
                    $suffix     = '@' . $domain;
                    $maxLocal   = $maxTotal - strlen($prefix) - strlen($suffix);
                    if ($maxLocal < 1) {
                        // fallback: keep at least 1 char
                        $maxLocal = 1;
                    }
                    $localTrimmed = Str::limit($local, $maxLocal, '');

                    $newEmail = $prefix . $localTrimmed . $suffix;
                }

                // As an extra guard against uniques, append a microtime hash if it still collides
                $collision = DB::table('users')->where('email', $newEmail)->exists();
                if ($collision) {
                    $tag = '_' . substr(sha1((string) microtime(true) . $deoId), 0, 8);
                    if (Str::contains($newEmail, '@')) {
                        [$l, $d] = explode('@', $newEmail, 2);
                        $newEmail = Str::limit($l . $tag, 245, '') . '@' . $d; // keep under 255 total
                    } else {
                        $newEmail = Str::limit($newEmail . $tag, 255, '');
                    }
                }

                // Save quietly to avoid touching updated_at hooks you might not want here
                $dataEntryOperator->forceFill(['email' => $newEmail])->saveQuietly();
            }

            // 2b) Clean up pivots that don’t make sense to keep after deletion (optional but you had it)
            DB::table('user_permissions')->where('user_id', $deoId)->delete();
            DB::table('group_users')->where('user_id', $deoId)->delete();

            // 2c) Soft delete (sets deleted_at)
            $dataEntryOperator->delete();

            DB::commit();

            $msg = __('admin.data_entry_operators.flash.operator_delete_msg') ?? 'Data entry operator deleted.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => $msg]);
            }

            return redirect()
                ->route('data_entry_operators.index', ['page' => $request->page])
                ->with('notice', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();

            $errMsg = __('admin.data_entry_operators.flash.operator_delete_alert') ?? 'Delete failed.';
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $errMsg,
                    'error'   => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()
                ->route('data_entry_operators.index', ['page' => $request->page])
                ->with('error', $errMsg);
        }
    }

    public function assignUsers(Request $request, DataEntryOperator $dataEntryOperator)
    {
        $currentPage = $request->page ?? 1;

        $deoAssignmentService = new DeoAssignmentService(Auth::user(), $dataEntryOperator, $request->all());
        $deoAssignmentService->call();

        $request->validate([
            'manager_id'    => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 2)],
            'supervisor_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 3)],
        ]);

        $dataEntryOperator->managers()->sync($request->filled('manager_id') ? [(int)$request->manager_id] : []);
        $dataEntryOperator->supervisors()->sync($request->filled('supervisor_id') ? [(int)$request->supervisor_id] : []);


        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => __('admin.data_entry_operators.flash.users_assigned_msg')
            ]);
        }
        return redirect()->route('data_entry_operators.index', ['page' => $currentPage])
            ->with('notice', __('admin.data_entry_operators.flash.users_assigned_msg'));
        // return redirect()->route('data_entry_operators.index', ['page' => $currentPage])
        //     ->with('success', __('admin.data_entry_operators.flash.users_assigned_msg'));
    }

    public function managerSupervisors(Request $request)
    {
        $manager = Manager::findOrFail($request->manager_id);
        $supervisors = $manager->supervisors;

        return response()->json($supervisors);
    }

    public function assignGroups(Request $request, DataEntryOperator $dataEntryOperator)
    {
        $dataEntryOperator->groups()->sync($request->group_ids);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => __('admin.assign_groups.messages.update_success')
            ]);
        }

        return redirect()->route('data_entry_operators.index')
            ->with('notice', __('admin.assign_groups.messages.update_success'));
    }

    public function getGroups(Request $request, DataEntryOperator $dataEntryOperator)
    {
        $groups = Group::all();

        return response()->json([
            'data_entry_operator_id' => $dataEntryOperator->id,
            'groups' => $groups->map(function ($g) {
                return [
                    'id' => $g->id,
                    'name' => $g->name,
                    'permissions_count' => $g->permissions->count()
                ];
            }),
            'assigned_group_ids' => $dataEntryOperator->groups->pluck('id')
        ]);
    }

    public function assignPermissions(Request $request, DataEntryOperator $dataEntryOperator)
    {
        $userId = (int) ($dataEntryOperator->user_id ?? $dataEntryOperator->getKey() ?? 0);

        $assignedPermissionIds = collect($request->input('permission_ids', []))
            ->map(fn($id) => (int) $id)->unique()->values()->all();

        $deniedPermissionIds = collect($request->input('denied_permission_ids', []))
            ->map(fn($id) => (int) $id)->unique()->values()->all();

        $groupPermissionIds = Permission::whereHas('groups.users', function ($q) use ($dataEntryOperator) {
            $q->where('users.id', $dataEntryOperator->id);
        })
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Remove explicit ALLOWs that are no longer checked
        $dataEntryOperator->userPermissions()
            ->where('is_negative', false)
            ->whereNotIn('permission_id', $assignedPermissionIds)
            ->delete();

        // Add new explicit ALLOWs (skip ones already granted via group)
        foreach ($assignedPermissionIds as $pid) {
            if (!in_array($pid, $groupPermissionIds, true)) {
                $dataEntryOperator->assignPermission($pid, false);
            }
        }

        // Remove explicit DENYs no longer checked
        $dataEntryOperator->userPermissions()
            ->where('is_negative', true)
            ->whereIn('permission_id', $groupPermissionIds)
            ->whereNotIn('permission_id', $deniedPermissionIds)
            ->delete();

        // Add explicit DENYs (only meaningful if permission is in group set)
        foreach ($groupPermissionIds as $pid) {
            if (in_array($pid, $deniedPermissionIds, true)) {
                $dataEntryOperator->assignPermission($pid, true);
            }
        }


        return back()->with('notice', __('admin.assign_permissions.messages.update_success'));
    }

    public function getPermissions(Request $request, \App\Models\DataEntryOperator $operator)
    {
        $groupPermissionIds = $operator->groups()->with('permissions:id')->get()
            ->flatMap(fn($g) => $g->permissions->pluck('id'))
            ->unique()->values()->all();

        $assignedPermissions = $operator->userPermissions()
            ->where('is_negative', false)
            ->pluck('permission_id')->map(fn($id) => (int)$id)->all();

        $deniedPermissions = $operator->userPermissions()
            ->where('is_negative', true)
            ->pluck('permission_id')->map(fn($id) => (int)$id)->all();

        return response()->json([
            'data_entry_operator_id' => (int) $operator->id,
            'permissions'           => \App\Models\Permission::orderBy('name')->get(['id', 'name', 'action', 'subject']),
            'group_permission_ids'  => $groupPermissionIds,
            'assigned_permissions'  => $assignedPermissions,
            'denied_permissions'    => $deniedPermissions,
        ]);
    }

    protected function setDataEntryOperator($request)
    {
        $this->dataEntryOperator = DataEntryOperator::find($request->id ?? $request->data_entry_operator_id);
    }

    protected function setDataEntryOperators($request)
    {
        $this->dataEntryOperators = DataEntryOperator::with(['groups', 'managers', 'supervisors'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $request->page);
    }

    protected function dataEntryOperatorParams(Request $request)
    {
        return $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);
    }



    public function assignOwners(Request $r, DataEntryOperator $deo)
    {
        $r->validate([
            'manager_id'    => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 2)],
            'supervisor_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 3)],
        ]);

        // Enforce single selections
        $deo->managers()->sync($r->manager_id ? [(int)$r->manager_id] : []);
        $deo->supervisors()->sync($r->supervisor_id ? [(int)$r->supervisor_id] : []);

        return back()->with('notice', 'Owners updated.');
    }

    public function bulkuploadpurchase(Request $r)
    {
        $auth = auth()->user();
        return view('admin.data_entry_operators.bulkuploadpurchase', compact('auth'));
    }

    public function bulkuploadcompletelist(Request $r)
    {
        $auth = auth()->user();
        return view('admin.data_entry_operators.bulkuploadcompletelist', compact('auth'));
    }

    public function bulkuploadbankstatement(Request $r)
    {
        $auth = auth()->user();
        return view('admin.data_entry_operators.bulkuploadbankstatement', compact('auth'));
    }

    public function bulkuploadbankingcompletelist(Request $r)
    {
        $auth = auth()->user();
        return view('admin.data_entry_operators.bulkuploadbankingcompletelist', compact('auth'));
    }
    
}
