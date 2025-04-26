<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\User;
use App\Models\Employee;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::whereHas('employee')->with(['employee', 'roles'])->get();
        // $data->roles = User::findOrFail($data->user_id)->getRoleNames();
        if ($data->isEmpty()) {
            return response()->json('data tidak ada', 404);
        }
        return new EmployeeResource('data ditemukan', $data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate request
        $validated = $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nik' => 'required|unique:employees,nik',
            'address' => 'required',
            'phone' => 'required|numeric|min:10',
        ]);
        // run transaction
        DB::beginTransaction();

        try {
            // create user
            $user = User::create([
                'name' => $request->first_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            // create employee
            $user->employee()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nik' => $request->nik,
                'address' => $request->address,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
            ]);
            // assign role if any roles are provided
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }
            // commit transaction
            DB::commit();

            return new EmployeeResource('data berhasil disimpan', $user->load(['employee', 'roles']), 201);
        } catch (ValidationException $e) {
            // pass the validation error to the response
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = User::whereHas('employee')->with(['employee', 'roles'])->findOrFail($id);
            return new EmployeeResource('data ditemukan', $data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // validate request
        $validated = $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'nik' => 'required|unique:employees,nik,' . $id,
            'address' => 'required',
            'phone' => 'required|numeric|min:10',
        ]);
        // run transaction
        DB::beginTransaction();

        try {
            // find user
            $user = User::findOrFail($id);
            // update user
            $user->update([
                'name' => $request->first_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            // update employee
            $user->employee()->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nik' => $request->nik,
                'address' => $request->address,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'zip_code' => $request->zip_code,
            ]);
            // assign role if any roles are provided
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }
            // commit transaction
            DB::commit();

            return new EmployeeResource('data berhasil diubah', $user->load(['employee', 'roles']), 200);
        } catch (ValidationException $e) {
            // pass the validation error to the response
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json('data tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();
            return response()->json('terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
