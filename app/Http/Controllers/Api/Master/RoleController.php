<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua role dan permission
     *
     * Method ini digunakan untuk mengambil semua data role dari database
     * beserta permission yang terkait. Role digunakan untuk mengatur akses
     * user dalam sistem pesantren.
     *
     * @group Security Management
     * @authenticated
     *
     * @response 200 {
     *   "message": "Roles retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "admin",
     *       "guard_name": "api",
     *       "permissions": [
     *         {
     *           "id": 1,
     *           "name": "create-users",
     *           "guard_name": "api"
     *         }
     *       ],
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
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
     * Menyimpan role baru
     *
     * Method ini digunakan untuk membuat role baru dengan validasi input
     * yang ketat. Role harus memiliki nama yang unik.
     *
     * @group Security Management
     * @authenticated
     *
     * @bodyParam name string required Nama role (harus unik). Example: admin
     * @bodyParam guard_name string Guard name untuk role (default: api). Example: api
     *
     * @response 200 {
     *   "message": "Role created successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "admin",
     *     "guard_name": "api",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Validation error",
     *   "errors": {
     *     "name": ["The name field is required."]
     *   }
     * }
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

            $role->syncPermissions($request->permissions);

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
     * Menampilkan detail role berdasarkan ID
     *
     * Method ini digunakan untuk mengambil detail role spesifik berdasarkan ID
     * beserta permission yang terkait.
     *
     * @group Security Management
     * @authenticated
     *
     * @urlParam id integer required ID role yang akan ditampilkan. Example: 1
     *
     * @response 200 {
     *   "message": "Role retrieved successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "admin",
     *     "guard_name": "api",
     *     "permissions": [
     *       {
     *         "id": 1,
     *         "name": "create-users",
     *         "guard_name": "api"
     *       }
     *     ],
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Role not found"
     * }
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Mengupdate data role yang ada
     *
     * Method ini digunakan untuk mengubah data role yang sudah ada
     * dengan validasi input yang ketat.
     *
     * @group Security Management
     * @authenticated
     *
     * @urlParam id integer required ID role yang akan diupdate. Example: 1
     * @bodyParam name string required Nama role (harus unik). Example: admin
     * @bodyParam guard_name string Guard name untuk role (default: api). Example: api
     *
     * @response 200 {
     *   "message": "Role updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "admin",
     *     "guard_name": "api",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Role not found"
     * }
     */
    public function update(Request $request, string $id)
    {
        try {
            $role = Role::findOrFail($id);

            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $role->id,
                'guard_name' => 'nullable|string',
            ]);

            $role->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name ?? 'api',
            ]);

            $role->syncPermissions($request->permission);

            return response()->json([
                'message' => 'Role updated successfully',
                'data' => $role,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update role',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus role berdasarkan ID
     *
     * Method ini digunakan untuk menghapus role berdasarkan ID.
     * Perlu diperhatikan bahwa penghapusan role harus dilakukan dengan hati-hati
     * karena dapat mempengaruhi akses user yang terkait.
     *
     * @group Security Management
     * @authenticated
     *
     * @urlParam id integer required ID role yang akan dihapus. Example: 1
     *
     * @response 200 {
     *   "message": "Role deleted successfully"
     * }
     *
     * @response 404 {
     *   "message": "Role not found"
     * }
     */
    public function destroy(string $id)
    {
        //
    }
}
