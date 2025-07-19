<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all roles from the database
        $roles = Role::with('permissions')->get();

        // Return a response with the roles
        return response()->json([
            'message' => 'Roles retrieved successfully',
            'data' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'nullable|string',
        ]);

        try {
            //code...
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name ?? 'api',
            ]);

            return response()->json([
                'message' => 'Role created successfully',
                'data' => $role,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to create role',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
