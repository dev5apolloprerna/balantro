<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{

    public function index(Request $request)
    {
        //$groups = Group::orderBy('created_at', 'asc')->paginate(10);
        $groups = Group::withCount(['users', 'permissions'])  // Eager load the counts
            ->orderBy('created_at', 'asc')
            ->paginate(10);


        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        $group = new Group();
        return view('admin.groups.create', compact('group'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Group::create($data);

        return redirect()->route('groups.index')
            ->with('notice', __('admin.group.flash.create_success'));
    }

    public function edit(Request $request, Group $group)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $html = view('admin.groups.partials.edit', compact('group'))->render();

            return response()->json([
                'group' => $group,
                'html'  => $html,
            ]);
        }

        return view('admin.groups.partials.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($data);

        return redirect()->route('groups.index')
            ->with('notice', __('admin.group.flash.update_success'));
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(Group $group)
    {
        DB::table('group_permissions')->where('group_id', $group->id)->delete();
        $group->delete();

        return redirect()->route('groups.index')
            ->with('notice', __('admin.group.flash.delete_success'));
    }

    /**
     * Assign permissions to a group.
     *
     * Handles both GET (show form) and POST (assign permissions) like Rails controller's assign_permissions.
     */
    public function assignPermissions(Request $request, Group $group)
    {
        if ($request->isMethod('post')) {
            // Accept permission_ids as array, default to empty array.
            $permissionIds = $request->input('permission_ids', []);
            if (!is_array($permissionIds)) {
                $permissionIds = [$permissionIds];
            }

            // Sync permissions
            $group->permissions()->sync($permissionIds);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => __('admin.group.flash.permissions_assigned')], 200);
            }

            return redirect()->route('groups.index')
                ->with('notice', __('admin.group.flash.permissions_assigned'));
        }

        // GET: show available permissions and assigned ones
        $permissions = Permission::orderBy('name')->whereNotIn('subject', ['managers','supervisors','user_devices','themes','data_entry_operators','clients','groups'])->get();
        $assigned_permissions = $group->permissions()->pluck('permissions.id')->toArray(); // 👈 qualified

        return view('admin.groups.assign_permissions', compact('group', 'permissions', 'assigned_permissions'));
    }

    /**
     * Get permissions for a group as JSON.
     */
    public function getPermissions(Request $request, Group $group)
    {
        $all_permissions = Permission::select(['id', 'name', 'action', 'subject'])->whereIn('subject', ['managers','supervisors','data_entry_operators','clients'])->get();
        $assigned_permissions = $group->permissions()->pluck('id')->toArray();

        $permissions_array = $all_permissions->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'action' => $p->action,
                'subject' => $p->subject,
            ];
        })->values();

        return response()->json([
            'permissions' => $permissions_array,
            'assigned_permissions' => $assigned_permissions,
            'group_permission_ids' => [],
            'denied_permissions' => [],
        ]);
    }
}
