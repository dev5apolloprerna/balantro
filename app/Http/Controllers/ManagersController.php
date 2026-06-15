<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use App\Models\Manager;
use App\Models\Group;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\QueryException;
use App\Models\UserDevice;

class ManagersController extends Controller
{
    protected $manager;
    protected $managers;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->route('manager') || $request->id || $request->manager_id) {
                $this->manager = Manager::find($request->route('manager') ?? $request->id ?? $request->manager_id);
            }
            $this->setManagers();
            return $next($request);
        })->only(['update', 'destroy', 'assignGroups', 'getGroups', 'assignPermissions', 'getPermissions']);
    }

    public function index()
    {
        // $managers = Manager::with('groups')
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);
        $managers = Manager::with('groups')->orderBy('created_at', 'desc')->paginate(10);
        $groups   = Group::with('permissions')->get(); // for the modal list
        $permissions = Permission::select('id', 'name', 'action', 'subject')->get(); // used by Permissions modal
        return view('admin.managers.index', compact('managers', 'groups', 'permissions'));
    }

    public function create()
    {
        return $this->createUser(Manager::class);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $request->merge([
            'role' => 2,            // your default role id
            'type' => User::ROLES['manager'],    // or whatever value fits your schema
            'password' => Hash::make('Manager1!'), // random 12-char password
            'confirmation_token' => Str::uuid()->toString(), // random unique token
        ]);
        // ✅ Correct: first the model class, then the request object
        return $this->createUser(\App\Models\Manager::class, $request);
    }

    public function update(Request $request, Manager $manager)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $manager->id],
        ]);
        $data = $request->only(['name', 'email', 'device', 'origin']);
        if ($request->filled('fcm_token')) {
            $data['token'] = $request->input('fcm_token');
        }

        $manager->update($data);
        if ($request->filled('fcm_token')) {
            UserDevice::where([
                'user_id' => $manager->id,
                'device_type' => $request->device_type,
                'browser_name' => $request->browser_name
            ])->updateOrCreate(
                [
                    'device_token' => $request->fcm_token,
                ],
                [
                    'user_id'      => $manager->id,
                    'device_type'  => $request->device_type ?? 'pc',
                    'browser_name' => $request->browser_name,
                    'os_name'      => $request->os_name,
                    'is_active'    => true,
                ]
            );
        }
        // If request is AJAX/JSON, send JSON back (no streams!)
        if ($request->ajax() || $request->wantsJson()) {
            // Re-render just the updated row (or the whole table if you prefer)
            $rowHtml = view('admin.managers.partials.manager_row', compact('manager'))->render();

            return response()->json([
                'status'  => 'success',
                'message' => __('admin.managers.flash.manager_update_msg') ?? 'Manager updated.',
                'rowHtml' => $rowHtml,
                'id'      => $manager->id,
            ]);
        }

        // Normal browser form post: redirect back with flash
        return redirect()
            ->route('managers.index')
            ->with('notice', __('admin.managers.flash.manager_update_msg') ?? 'Manager updated.');
    }

    // public function destroy(Request $request, Manager $manager)
    // {
    //     try {
    //         DB::table('group_users')->where('user_id', $manager->id)->delete();
    //         DB::table('user_permissions')->where('user_id', $manager->id)->delete();
    //         $manager->delete();

    //         // Success responses (unchanged)
    //         if ($request->wantsJson()) {
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => __('admin.managers.flash.manager_delete_msg')
    //             ]);
    //         }
    //         if ($request->header('Accept') === 'text/vnd.turbo-stream.html') {
    //             return response()->streamDownload(function () use ($manager) {
    //                 echo sprintf('<turbo-stream action="remove" target="manager_%d"></turbo-stream>', $manager->id);
    //                 echo view('shared.flash_messages')->render();
    //             }, null, 200, ['Content-Type' => 'text/vnd.turbo-stream.html']);
    //         }
    //         return redirect()->route('managers.index', ['page' => $request->page])
    //             ->with('notice', __('admin.managers.flash.manager_delete_msg'));
    //     } catch (QueryException $e) {
    //         // Make the error actionable: surface the blocking FK/table if present
    //         $msg = 'Cannot delete manager: permissions or other records still reference this user.';
    //         if (str_contains($e->getMessage(), 'fk_user_permissions_user_id')) {
    //             $msg .= ' (Blocked by user_permissions.user_id)';
    //         }
    //         return $request->wantsJson()
    //             ? response()->json(['status' => 'error', 'message' => $msg], 422)
    //             : back()->with('alert', $msg);
    //     } catch (\Throwable $e) {
    //         return $request->wantsJson()
    //             ? response()->json(['status' => 'error', 'message' => 'Delete failed.'], 500)
    //             : back()->with('alert', 'Delete failed.');
    //     }
    // }

    public function destroy(Request $request, Manager $manager)
    {
        try {
            DB::transaction(function () use ($manager) {
                $deoId = (int) $manager->getKey();
                // --- 1) Prefix unique columns BEFORE soft delete ---
                // Build a stable, readable prefix like: del:123:20250901T154500__
                $originalEmail = (string) $manager->email;

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
                    $manager->forceFill(['email' => $newEmail])->saveQuietly();
                }

                // --- 2) Optional clean-ups (not required for FK with soft deletes) ---
                DB::table('group_users')->where('user_id', $manager->id)->delete();
                DB::table('user_permissions')->where('user_id', $manager->id)->delete();

                // If your schema links manager in pivots, you can also clean them:
                DB::table('clients_managers')->where('manager_id', $manager->id)->delete();
                DB::table('managers_supervisors')->where('manager_id', $manager->id)->delete();

                // --- 3) Soft delete (DO NOT forceDelete) ---
                $manager->delete();

                // (Optional) log
                if (method_exists($manager, 'logActivity')) {
                    $manager->logActivity('soft_deleted');
                }
            });

            // --- success responses (same style as yours) ---
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => __('admin.managers.flash.manager_delete_msg'),
                ]);
            }

            if ($request->header('Accept') === 'text/vnd.turbo-stream.html') {
                return response()->streamDownload(function () use ($manager) {
                    echo sprintf('<turbo-stream action="remove" target="manager_%d"></turbo-stream>', $manager->id);
                    echo view('shared.flash_messages')->render();
                }, null, 200, ['Content-Type' => 'text/vnd.turbo-stream.html']);
            }

            return redirect()
                ->route('managers.index', ['page' => $request->page])
                ->with('notice', __('admin.managers.flash.manager_delete_msg'));
        } catch (QueryException $e) {
            // Unlikely with soft deletes, but keep a clear message
            $msg = 'Could not delete the manager due to related records. (soft delete)';
            return $request->wantsJson()
                ? response()->json(['status' => 'error', 'message' => $msg], 422)
                : back()->with('alert', $msg);
        } catch (Throwable $e) {
            return $request->wantsJson()
                ? response()->json(['status' => 'error', 'message' => 'Delete failed.'], 500)
                : back()->with('alert', 'Delete failed.');
        }
    }

    public function assignGroups(\Illuminate\Http\Request $request, Manager $manager)
    {
        $ids = collect($request->input('group_ids', []))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $manager->groups()->sync($ids);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => __('admin.assign_groups.messages.update_success'),
            ]);
        }

        return redirect()
            ->route('managers.index')
            ->with('notice', __('admin.assign_groups.messages.update_success'));
    }

    public function getGroups(Manager $manager)
    {
        $groups = Group::query()
            ->select('id', 'name')
            ->withCount('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn($g) => [
                'id' => (int) $g->id,
                'name' => $g->name,
                'permissions_count' => (int) $g->permissions_count,
            ]);

        $assignedIds = $manager->groups()
            ->pluck('groups.id')
            ->map(fn($id) => (int) $id)
            ->values(); // ensure plain array

        return response()->json([
            'groups' => $groups,
            'assigned_group_ids' => $assignedIds,
        ]);
    }

    public function assignPermissions(Request $request, Manager $manager)
    {
        $assignedPermissionIds = collect($request->input('permission_ids', []))
            ->map(fn($id) => (int) $id)->unique()->values()->all();

        $deniedPermissionIds = collect($request->input('denied_permission_ids', []))
            ->map(fn($id) => (int) $id)->unique()->values()->all();

        $groupPermissionIds = Permission::whereHas('groups.users', function ($q) use ($manager) {
            $q->where('users.id', $manager->id);
        })
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Remove explicit ALLOWs that are no longer checked
        $manager->userPermissions()
            ->where('is_negative', false)
            ->whereNotIn('permission_id', $assignedPermissionIds)
            ->delete();

        // Add new explicit ALLOWs (skip ones already granted via group)
        foreach ($assignedPermissionIds as $pid) {
            if (!in_array($pid, $groupPermissionIds, true)) {
                $manager->assignPermission($pid, false);
            }
        }

        // Remove explicit DENYs no longer checked
        $manager->userPermissions()
            ->where('is_negative', true)
            ->whereIn('permission_id', $groupPermissionIds)
            ->whereNotIn('permission_id', $deniedPermissionIds)
            ->delete();

        // Add explicit DENYs (only meaningful if permission is in group set)
        foreach ($groupPermissionIds as $pid) {
            if (in_array($pid, $deniedPermissionIds, true)) {
                $manager->assignPermission($pid, true);
            }
        }

        return redirect()
            ->route('managers.index')
            ->with('notice', __('admin.assign_permissions.messages.update_success'));
    }

    public function getPermissions(Manager $manager)
    {
        $groupPermissionIds = $manager->groups()->with('permissions')
            ->get()
            ->flatMap(fn($g) => $g->permissions->pluck('id'))
            ->map(fn($id) => (int) $id)   // <-- cast
            ->unique()
            ->values()
            ->toArray();

        $assignedPermissions = $manager->userPermissions()
            ->where('is_negative', false)
            ->pluck('permission_id')
            ->map(fn($id) => (int) $id)   // <-- cast
            ->toArray();

        $deniedPermissions = $manager->userPermissions()
            ->where('is_negative', true)
            ->pluck('permission_id')
            ->map(fn($id) => (int) $id)   // <-- cast
            ->toArray();

        $permissions = Permission::query()
            ->select('id', 'name', 'action', 'subject')
            ->whereNotIn('subject', ['managers','user_devices','themes'])
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id' => (int) $p->id,      // <-- cast
                'name' => $p->name,
                'action' => $p->action,
                'subject' => $p->subject,
            ]);

        return response()->json([
            'manager_id' => (int) $manager->id,
            'permissions' => $permissions,
            'group_permission_ids' => $groupPermissionIds,
            'assigned_permissions' => $assignedPermissions,
            'denied_permissions' => $deniedPermissions,
        ]);
    }

    protected function setManagers()
    {
        $this->managers = Manager::with('groups')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function showAssignGroups(Manager $manager)
    {
        $groups = \App\Models\Group::withCount('permissions')->get();

        return view('admin.managers.pages.assign_groups', [
            'manager' => $manager->load('groups'),
            'groups'  => $groups,
        ]);
    }

    public function showAssignPermissions(Manager $manager)
    {
        // What the page needs
        $permissions = \App\Models\Permission::select('id', 'name', 'action', 'subject')->get();

        // Based on your existing logic
        $groupPermissionIds = $manager->groups()->with('permissions')
            ->get()
            ->flatMap(fn($g) => $g->permissions->pluck('id'))
            ->unique()
            ->values()
            ->toArray();

        $assignedPermissions = $manager->userPermissions()
            ->where('is_negative', false)
            ->pluck('permission_id')
            ->toArray();

        $deniedPermissions = $manager->userPermissions()
            ->where('is_negative', true)
            ->pluck('permission_id')
            ->toArray();

        return view('admin.managers.pages.assign_permissions', [
            'manager'              => $manager,
            'permissions'          => $permissions,
            'groupPermissionIds'   => $groupPermissionIds,
            'assignedPermissions'  => $assignedPermissions,
            'deniedPermissions'    => $deniedPermissions,
        ]);
    }
}
