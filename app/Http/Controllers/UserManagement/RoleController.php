<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);

        return Inertia::render('user-management/role-permission/Index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $permissions = $this->groupedPermissions();

        return Inertia::render('user-management/role-permission/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('user-management.role.index')
            ->with('success', 'Role berhasil ditambahkan');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = $this->groupedPermissions();

        return Inertia::render('user-management/role-permission/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('user-management.role.index')
            ->with('success', 'Role berhasil diupdate');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('user-management.role.index')
            ->with('success', 'Role berhasil dihapus');
    }

    private function groupedPermissions(): array
    {
        $grouped = [];

        Permission::all()->each(function ($permission) use (&$grouped) {
            $parts = explode('.', $permission->name);
            array_pop($parts);
            $module = implode('.', $parts);
            $grouped[$module][] = $permission->name;
        });

        return $grouped;
    }
}
