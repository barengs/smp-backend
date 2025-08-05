<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\Hostel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HostelResource;
use Illuminate\Validation\ValidationException;

class HostelController extends Controller
{
    /**
     * Menampilkan daftar semua asrama
     *
     * Method ini digunakan untuk mengambil semua data asrama dari database
     * beserta relasi parent asrama. Asrama mencakup tempat tinggal santri
     * di pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "message": "Data retrieved successfully",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Asrama Putra A",
     *       "parent_id": null,
     *       "description": "Asrama untuk santri putra kelas 7-9",
     *       "parent": null,
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Asrama Putra B",
     *       "parent_id": 1,
     *       "description": "Sub-asrama dari Asrama Putra A",
     *       "parent": {
     *         "id": 1,
     *         "name": "Asrama Putra A"
     *       },
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 500 {
     *   "message": "Error retrieving data",
     *   "error": "Error details"
     * }
     */
    public function index()
    {
        try {
            $data = Hostel::with('parent')->get();
            return new HostelResource('Data retrieved successfully', $data, 200);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error retrieving data',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable',
                'description' => 'nullable',
            ]);

            $hostel = Hostel::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'description' => $request->description,
            ]);
            return new HostelResource('Hostel created successfully', $hostel, 201);
        } catch (ValidationException $e) {
            return response([
                'message' => 'Validation error',
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error creating hostel',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = Hostel::with('parent')->findOrFail($id);
            return new HostelResource('Data retrieved successfully', $data, 200);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error retrieving data',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable',
                'description' => 'nullable',
            ]);

            $hostel = Hostel::findOrFail($id);
            $hostel->update([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'description' => $request->description,
            ]);
            return new HostelResource('Hostel updated successfully', $hostel, 200);
        } catch (ValidationException $e) {
            return response([
                'message' => 'Validation error',
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response([
                'message' => 'Hostel not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error updating hostel',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $hostel = Hostel::findOrFail($id);
            $hostel->delete();
            return new HostelResource('Hostel deleted successfully', null, 200);
        } catch (ModelNotFoundException $e) {
            return response([
                'message' => 'Hostel not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error deleting hostel',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
