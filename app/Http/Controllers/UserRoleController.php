<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.user_roles_permissions.index', [
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    // Get roles assigned to a specific user
    public function getUserRoles(User $user)
    {
        return response()->json([
            'roles' => $user->roles->pluck('id')
        ]);
    }

    // Update permissions for roles
    public function updateRolePermissions(Request $request)
    {
        $request->validate([
            'role_permissions' => 'array',
            'role_permissions.*' => 'array'
        ]);

        $rolePermissions = $request->role_permissions ?? [];
        $allRoles = Role::all();
        
        foreach ($allRoles as $role) {
            $permissionIds = $rolePermissions[$role->id] ?? [];
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);
        }

        return response()->json([
            'message' => 'Role permissions updated successfully'
        ]);
    }

    // Assign roles to user
    public function assignRolesToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_ids' => 'array',
            'role_ids.*' => 'exists:roles,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles($request->role_ids);

        return response()->json([
            'message' => 'User roles updated successfully'
        ]);
    }
}