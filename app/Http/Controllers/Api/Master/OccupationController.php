<?php

namespace App\Http\Controllers\Api\Master;

use App\Imports\OccupationImport;
use App\Models\Occupation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OccupationResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class OccupationController extends Controller
{
    /**
     * Menampilkan daftar semua pekerjaan
     *
     * Method ini digunakan untuk mengambil semua data pekerjaan dari database.
     * Data pekerjaan digunakan untuk mengisi informasi pekerjaan orang tua
     * dan data demografis pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "message": "Occupations retrieved successfully",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "PNS",
     *       "description": "Pegawai Negeri Sipil",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Wiraswasta",
     *       "description": "Pengusaha atau wiraswasta",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 500 {
     *   "status": "error",
     *   "message": "Failed to fetch occupations: Error details"
     * }
     */
    public function index()
    {
        try {
            // Fetch all occupations from the database
            $occupations = Occupation::all();
            // Return a successful response with the occupations
            return new OccupationResource('Occupations retrieved successfully', $occupations, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch occupations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Create a new occupation using the validated data
            $occupation = Occupation::create($request->all());

            // Return a successful response with the created occupation
            return new OccupationResource('Occupation created successfully', $occupation, 201);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create occupation: ' . $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the occupation by ID
            $occupation = Occupation::findOrFail($id);

            // Return a successful response with the occupation
            return new OccupationResource('Occupation retrieved successfully', $occupation, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch occupation: ' . $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Find the occupation by ID
            $occupation = Occupation::findOrFail($id);

            // Update the occupation with the validated data
            $occupation->update($request->all());

            // Return a successful response with the updated occupation
            return new OccupationResource('Occupation updated successfully', $occupation, 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update occupation: ' . $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the occupation by ID
            $occupation = Occupation::findOrFail($id);

            // Delete the occupation
            $occupation->delete();

            // Return a successful response indicating deletion
            return new OccupationResource('Occupation deleted successfully', null, 204);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete occupation: ' . $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        }
    }
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv'
            ]);

            DB::beginTransaction();

            Excel::import(new OccupationImport, $request->file('file'));

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data imported successfully',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->validator->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to import data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
