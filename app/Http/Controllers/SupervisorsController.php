<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supervisor;
use App\Models\Manager;
use App\Models\Group;
use App\Models\Permission;
use App\Services\FilterService;
use App\Services\SupervisorAssignmentService;
use App\Http\Requests\SupervisorRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\UserDevice;

class SupervisorsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Supervisor::query()
            ->with(['groups', 'managers'])
            ->orderByDesc('created_at');

        /* ---------- ROLE SCOPING ---------- */
        if ($user->role === User::ROLES['manager']) {
            // Managers only see supervisors associated to them
            $query->whereHas('managers', fn($q) => $q->whereKey($user->id));
        } elseif ($user->role !== User::ROLES['super_admin']) {
            // (Optional) lock down everyone else; or customize per your policy
            // $query->whereRaw('1=0'); // show nothing
        }

        /* ---------- SEARCH ---------- */
        if ($request->filled('query')) {
            $q = trim((string) $request->input('query'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        /* ---------- FILTERS ---------- */
        // Manager filter: only super admin can use this (managers can’t escalate)
        if (
            $user->role === User::ROLES['super_admin'] &&
            $request->filled('manager_id') &&
            ctype_digit((string) $request->manager_id)
        ) {
            $managerId = (int) $request->manager_id;
            $query->whereHas('managers', fn($q) => $q->whereKey($managerId));
        }

        // Group filter: allowed for both roles
        if ($request->filled('group_id') && ctype_digit((string) $request->group_id)) {
            $groupId = (int) $request->group_id;
            $query->whereHas('groups', fn($q) => $q->where('groups.id', $groupId));
        }

        $supervisors = $query->paginate(10)->appends($request->query());
    
        /* ---------- DROPDOWN DATA ---------- */
        // Super admin gets all managers; manager sees only self; others none.
        if ($user->role === User::ROLES['super_admin']) {
            $managers = Manager::select('id', 'name')->orderBy('name')->get();
        } elseif ($user->role === User::ROLES['manager']) {
            $managers = Manager::whereKey($user->id)->get(['id', 'name']);
        } else {
            $managers = collect();
        }
        
        $groups = Group::select('id', 'name')->orderBy('name')->get();

        /* ---------- VIEW FLAGS (for buttons) ---------- */
        $canAdd       = $user->role === User::ROLES['super_admin'];   // show “Add”
        $canAllocate  = $user->role === User::ROLES['manager'];       // show “Allocate Supervisor”
        
        return view('admin.supervisors.index', compact(
            'supervisors',
            'managers',
            'groups',
            'canAdd',
            'canAllocate',
            'user'
        ));
    }

    public function store(SupervisorRequest $request)
    {
        // Accept either "supervisor" payload or "user" or plain name.
        $name = $this->extractName($request);
        $request->merge([
            'role' => 3,            // your default role id
            'type' => User::ROLES['supervisor'],    // or whatever value fits your schema
            'password' => Hash::make('Supervisor1!'), // random 12-char password
            'confirmation_token' => Str::uuid()->toString(), // random unique token
        ]);
        //$supervisor = Supervisor::create(['name' => $name]);
        return $this->createUser(\App\Models\Supervisor::class, $request);
        // if ($request->wantsJson()) {
        //     return response()->json([
        //         'status' => 'success',
        //         'supervisor' => $supervisor,
        //         'message' => __('admin.supervisors.flash.supervisor_create_msg', [], 'en')
        //     ]);
        // }

        //return redirect()->route('supervisors.index')->with('notice', __('admin.supervisors.flash.supervisor_create_msg'));
    }

    public function update(Request $request, Supervisor $supervisor)
    {
        $this->setSupervisors($request); // For returning updated partials

        $name = $this->extractName($request);
        $supervisor->name = $name;

        if ($supervisor->save()) {
            $flash = __('admin.supervisors.flash.supervisor_update_msg');
            if ($request->filled('fcm_token')) {
                UserDevice::where([
                    'user_id' => $supervisor->id,
                    'device_type' => $request->device_type,
                    'browser_name' => $request->browser_name
                ])->updateOrCreate(
                    [
                        'device_token' => $request->fcm_token,
                    ],
                    [
                        'user_id'      => $supervisor->id,
                        'device_type'  => $request->device_type ?? 'pc',
                        'browser_name' => $request->browser_name,
                        'os_name'      => $request->os_name,
                        'is_active'    => true,
                    ]
                );
            }
            // For AJAX requests emulate TurboStream by returning html fragments
            if ($request->ajax() || $request->wantsJson()) {
                $supervisorTable = View::make('admin.supervisors._supervisor_table', ['supervisors' => $this->supervisors])->render();
                $flashMessages = View::make('shared._flash_messages')->render();

                return response()->json([
                    'status' => 'success',
                    'message' => $flash,
                    'fragments' => [
                        'supervisor-table' => $supervisorTable,
                        'flashMessages' => $flashMessages,
                    ]
                ]);
            }

            return redirect()->route('supervisors.index')->with('notice', $flash);
        }

        return response()->json([
            'status' => 'error',
            'errors' => $supervisor->getErrors ?? []
        ], 422);
    }

    public function destroy(Request $request, Supervisor $supervisor)
    {
        $sid = (int) $supervisor->getKey();
        DB::beginTransaction();
        try {
            // dd($sid);
            // 1) Prefix unique columns BEFORE soft delete so new users can reuse them
            $originalEmail = (string) ($supervisor->email ?? '');
            if ($originalEmail !== '' && !\Illuminate\Support\Str::startsWith($originalEmail, 'deleted_')) {
                // Build "deleted_{id}_{local}@{domain}" and keep under 255 chars
                $prefix = "deleted_{$sid}_";
                if (!\Illuminate\Support\Str::contains($originalEmail, '@')) {
                    $newEmail = \Illuminate\Support\Str::limit($prefix . $originalEmail, 255, '');
                } else {
                    [$local, $domain] = explode('@', $originalEmail, 2);
                    $suffix   = '@' . $domain;
                    $maxLocal = max(1, 255 - strlen($prefix) - strlen($suffix));
                    $newEmail = $prefix . \Illuminate\Support\Str::limit($local, $maxLocal, '') . $suffix;
                }
                // collision guard
                if (DB::table('users')->where('email', $newEmail)->exists()) {
                    $tag = '_' . substr(sha1((string) microtime(true) . $sid), 0, 8);
                    if (\Illuminate\Support\Str::contains($newEmail, '@')) {
                        [$l, $d] = explode('@', $newEmail, 2);
                        $newEmail = \Illuminate\Support\Str::limit($l . $tag, 245, '') . '@' . $d;
                    } else {
                        $newEmail = \Illuminate\Support\Str::limit($newEmail . $tag, 255, '');
                    }
                }
                $supervisor->forceFill(['email' => $newEmail])->saveQuietly();
            }

            // (optional) tokens that might be unique
            $mut = [];
            foreach (['confirmation_token' => 128, 'reset_password_token' => 128] as $col => $max) {
                $val = (string) ($supervisor->{$col} ?? '');
                if ($val !== '' && !\Illuminate\Support\Str::startsWith($val, 'deleted_')) {
                    $mut[$col] = \Illuminate\Support\Str::limit("deleted_{$sid}_" . $val, $max, '');
                }
            }
            if ($mut) $supervisor->forceFill($mut)->saveQuietly();

            // 2) Clean up pivots/refs that point to this supervisor
            DB::table('clients_supervisors')->where('supervisor_id', $sid)->delete();
            DB::table('managers_supervisors')->where('supervisor_id', $sid)->delete();
            DB::table('group_users')->where('user_id', $sid)->delete();
            DB::table('user_permissions')->where('user_id', $sid)->delete();

            // 3) Documents: delete or unlink (choose ONE policy)
            DB::table('documents')->where('user_id', $sid)->delete();
            // If FK allows NULL instead of delete, use this instead:
            // DB::table('documents')->where('user_id', $sid)->update(['user_id' => null]);

            // 4) Profile row
            //$supervisor->profile()->delete();

            // 5) Soft delete the supervisor (DO NOT forceDelete)
            $supervisor->delete();

            DB::commit();

            $message = __('admin.supervisors.flash.supervisor_delete_msg');
            if ($request->ajax() || $request->wantsJson()) {
                $flash = \Illuminate\Support\Facades\View::make('shared._flash_messages')->render();
                return response()->json([
                    'status'   => 'success',
                    'message'  => $message,
                    'remove'   => "supervisor_{$sid}",
                    'fragments' => ['flashMessages' => $flash],
                ]);
            }

            return redirect()
                ->route('supervisors.index', ['page' => $request->get('page')])
                ->with('notice', $message);
        } catch (QueryException $e) {
            DB::rollBack();
            $alert = __('admin.supervisors.flash.supervisor_delete_alert');
            // helpful FK hinting
            $hint = '';
            foreach (
                [
                    'fk_clients_supervisors_supervisor_id' => 'clients_supervisors.supervisor_id',
                    'fk_managers_supervisors_supervisor_id' => 'managers_supervisors.supervisor_id',
                    'fk_group_users_user_id'               => 'group_users.user_id',
                    'fk_user_permissions_user_id'          => 'user_permissions.user_id',
                    'fk_documents_user_id'                 => 'documents.user_id',
                ] as $needle => $tblcol
            ) {
                if (str_contains($e->getMessage(), $needle)) {
                    $hint = " (blocked by {$tblcol})";
                    break;
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $alert . $hint], 422);
            }
            return redirect()
                ->route('supervisors.index', ['page' => $request->get('page')])
                ->with('alert', $alert . $hint);
        } catch (\Throwable $e) {
            DB::rollBack();
            $alert = __('admin.supervisors.flash.supervisor_delete_alert');
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $alert], 500);
            }
            return redirect()
                ->route('supervisors.index', ['page' => $request->get('page')])
                ->with('alert', $alert);
        }
    }

    public function assignManagers(Request $request, Supervisor $supervisor)
    {
        $this->setSupervisors($request);

        $request->validate([
            'manager_id' => [
                'nullable',
                'integer',
                // manager must be a user.id AND have role=2
                Rule::exists('users', 'id')
                    ->where(fn($q) => $q->where('role', 2) /* ->whereNull('deleted_at') if soft deletes */),
            ],
        ]);

        $managerId = $request->input('manager_id');

        // keep the existing many-to-many table but restrict to one
        $supervisor->managers()->sync($managerId ? [$managerId] : []);

        $message = __('admin.supervisors.flash.manager_assigned_msg');

        if ($request->ajax() || $request->wantsJson()) {
            $supervisorTable = \View::make('admin.supervisors._supervisor_table', [
                'supervisors' => $this->supervisors
            ])->render();
            $flashMessages = \View::make('shared._flash_messages')->render();

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'fragments' => [
                    'supervisor-table' => $supervisorTable,
                    'flashMessages' => $flashMessages,
                ]
            ]);
        }

        return redirect()
            ->route('supervisors.index', ['page' => $request->get('page', 1)])
            ->with('notice', $message);
    }

    public function assignGroups(Request $request, Supervisor $supervisor)
    {
        $this->setSupervisors($request);

        $groupIds = array_map('intval', (array)$request->input('group_ids', []));

        $supervisor->groups()->sync($groupIds);

        $message = __('admin.assign_groups.messages.update_success');

        if ($request->ajax() || $request->wantsJson()) {
            $supervisorTable = View::make('admin.supervisors._supervisor_table', ['supervisors' => $this->supervisors])->render();
            $flashMessages = View::make('shared._flash_messages')->render();

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'fragments' => [
                    'supervisor-table' => $supervisorTable,
                    'flashMessages' => $flashMessages,
                ]
            ]);
        }

        return redirect()->route('supervisors.index')->with('notice', $message);
    }

    public function getGroups(Supervisor $supervisor)
    {
        $groups = Group::all()->map(function ($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'permissions_count' => $g->permissions()->count(),
            ];
        });

        return response()->json([
            'supervisor_id' => $supervisor->id,
            'groups' => $groups,
            'assigned_group_ids' => $supervisor->groups()->pluck('groups.id')->toArray(),
        ]);
    }

    public function assignPermissions(Request $request, Supervisor $supervisor)
    {
        $this->setSupervisors($request);

        $assignedPermissionIds = array_map('intval', (array)$request->input('permission_ids', []));
        $deniedPermissionIds = array_map('intval', (array)$request->input('denied_permission_ids', []));

        // group_permission_ids: permissions that come from supervisor's groups
        $groupPermissionIds = $supervisor->groups()->with('permissions')->get()
            ->flatMap(function ($g) {
                return $g->permissions->pluck('id');
            })->unique()->values()->all();

        // Remove positive (is_negative = false) user permissions not in assignedPermissionIds
        $toRemovePositive = $supervisor->userPermissions()
            ->where('is_negative', false)
            ->whereNotIn('permission_id', $assignedPermissionIds)
            ->pluck('id')
            ->toArray();

        $supervisor->removePermissions($toRemovePositive);

        // Assign positive permissions for assignedPermissionIds unless they come from groups
        foreach ($assignedPermissionIds as $pid) {
            if (!in_array($pid, $groupPermissionIds, true)) {
                $supervisor->assignPermission($pid, false);
            }
        }

        // Remove negative permissions for group_permission_ids that are no longer denied
        $toRemoveNegative = $supervisor->userPermissions()
            ->whereIn('permission_id', $groupPermissionIds)
            ->where('is_negative', true)
            ->whereNotIn('permission_id', $deniedPermissionIds)
            ->pluck('id')
            ->toArray();

        $supervisor->removePermissions($toRemoveNegative);

        // For each group permission, if it's denied then ensure a negative user permission exists
        foreach ($groupPermissionIds as $pid) {
            if (in_array($pid, $deniedPermissionIds, true)) {
                $supervisor->assignPermission($pid, true);
            }
        }

        $message = __('admin.assign_permissions.messages.update_success');

        if ($request->ajax() || $request->wantsJson()) {
            $supervisorTable = View::make('admin.supervisors._supervisor_table', ['supervisors' => $this->supervisors])->render();
            $flashMessages = View::make('shared._flash_messages')->render();

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'fragments' => [
                    'supervisor-table' => $supervisorTable,
                    'flashMessages' => $flashMessages,
                ]
            ]);
        }

        return redirect()->route('supervisors.index')->with('notice', $message);
    }

    public function getPermissions(Supervisor $supervisor)
    {
        $groupPermissionIds = $supervisor->groups()->with('permissions')->get()
            ->flatMap(function ($g) {
                return $g->permissions->pluck('id');
            })->unique()->values()->all();

        $assignedPermissions = $supervisor->userPermissions()->where('is_negative', false)->pluck('permission_id')->toArray();
        
        $deniedPermissions = $supervisor->userPermissions()->where('is_negative', true)->pluck('permission_id')->toArray();
        
        return response()->json([
            'supervisor_id' => $supervisor->id,
            'permissions' => Permission::select(['id', 'name', 'action', 'subject'])->whereNotIn('subject', ['managers','supervisors','user_devices','themes'])->get()->toArray(),
            'group_permission_ids' => $groupPermissionIds,
            'assigned_permissions' => $assignedPermissions,
            'denied_permissions' => $deniedPermissions,
        ]);
    }

    // Helpers

    /**
     * Load supervisors list used to render partials (called for update/destroy/assign* actions)
     */
    protected function setSupervisors(Request $request)
    {
        $query = Supervisor::with(['groups', 'managers'])->orderByDesc('created_at');
        $query = (new FilterService($query, $request))->applyFilters();
        $this->supervisors = $query->paginate(10)->appends($request->query());
    }

    protected function extractName(Request $request)
    {
        if ($request->has('supervisor.name')) {
            return $request->input('supervisor.name');
        } elseif ($request->has('user.name')) {
            return $request->input('user.name');
        } elseif ($request->has('name')) {
            return $request->input('name');
        }
        return '';
    }
}
