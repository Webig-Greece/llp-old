<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:create-permission', ['only' => ['store']]);
        $this->middleware('permission:update-permission', ['only' => ['update']]);
        $this->middleware('permission:delete-permission', ['only' => ['destroy']]);
    }

    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions',
            'description' => 'nullable|string'
        ]);

        $permission = new Permission([
            'name' => $request->name,
            'description' => $request->description
        ]);

        $permission->save();

        return response()->json(['message' => 'Permission created successfully', 'permission' => $permission], 201);
    }

    public function show($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        return response()->json($permission);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $request->validate([
            'name' => 'string|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string'
        ]);

        $permission->fill($request->all());
        $permission->save();

        return response()->json(['message' => 'Permission updated successfully', 'permission' => $permission]);
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
