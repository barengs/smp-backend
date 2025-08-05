<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;

class ActivityController extends Controller
{
    /**
     * Menampilkan daftar semua kegiatan pesantren
     *
     * Method ini digunakan untuk mengambil semua data kegiatan pesantren dari database.
     * Kegiatan mencakup berbagai aktivitas yang dilakukan di pesantren.
     *
     * @group Activities
     * @authenticated
     *
     * @response 200 {
     *   "message": "Success",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Kajian Kitab Kuning",
     *       "description": "Kajian kitab kuning setiap malam Jumat",
     *       "date": "2024-01-01",
     *       "status": "active",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 500 {
     *   "message": "Error",
     *   "status": 500,
     *   "data": null
     * }
     */
    public function index()
    {
        try {
            $data = Activity::all();
            return new ActivityResource('Success', $data, 200);
        } catch (\Exception $e) {
            return new ActivityResource('Error', null, 500);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 404);
        }
    }

    /**
     * Menyimpan kegiatan pesantren baru
     *
     * Method ini digunakan untuk membuat kegiatan pesantren baru dengan validasi input
     * yang ketat. Kegiatan akan disimpan dengan status aktif secara default.
     *
     * @group Activities
     * @authenticated
     *
     * @bodyParam name string required Nama kegiatan (maksimal 255 karakter). Example: Kajian Kitab Kuning
     * @bodyParam description string Deskripsi kegiatan. Example: Kajian kitab kuning setiap malam Jumat
     * @bodyParam date date Tanggal kegiatan. Example: 2024-01-01
     * @bodyParam status string Status kegiatan (active, inactive). Example: active
     *
     * @response 201 {
     *   "message": "Activity created successfully",
     *   "status": 201,
     *   "data": {
     *     "id": 1,
     *     "name": "Kajian Kitab Kuning",
     *     "description": "Kajian kitab kuning setiap malam Jumat",
     *     "date": "2024-01-01",
     *     "status": "active",
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
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'nullable|date',
                'status' => 'nullable|in:active,inactive',
            ]);

            $activity = Activity::create([
                'name' => $request->name,
                'description' => $request->description,
                'date' => $request->date,
                'status' => $request->status ?? 'active',
            ]);

            return new ActivityResource('Activity created successfully', $activity, 201);
        } catch (\Exception $e) {
            return new ActivityResource($e->getMessage(), null, 500);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 404);
        }
    }

    /**
     * Menampilkan detail kegiatan berdasarkan ID
     *
     * Method ini digunakan untuk mengambil detail kegiatan spesifik berdasarkan ID.
     *
     * @group Activities
     * @authenticated
     *
     * @urlParam id integer required ID kegiatan yang akan ditampilkan. Example: 1
     *
     * @response 200 {
     *   "message": "Success",
     *   "status": 200,
     *   "data": {
     *     "id": 1,
     *     "name": "Kajian Kitab Kuning",
     *     "description": "Kajian kitab kuning setiap malam Jumat",
     *     "date": "2024-01-01",
     *     "status": "active",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Activity not found",
     *   "status": 404,
     *   "data": null
     * }
     */
    public function show(string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            return new ActivityResource('Success', $activity, 200);
        } catch (\Exception $e) {
            return new ActivityResource('Activity not found', null, 404);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 500);
        }
    }

    /**
     * Mengupdate data kegiatan yang ada
     *
     * Method ini digunakan untuk mengubah data kegiatan yang sudah ada
     * dengan validasi input yang ketat.
     *
     * @group Activities
     * @authenticated
     *
     * @urlParam id integer required ID kegiatan yang akan diupdate. Example: 1
     * @bodyParam name string required Nama kegiatan (maksimal 255 karakter). Example: Kajian Kitab Kuning Updated
     * @bodyParam description string Deskripsi kegiatan. Example: Kajian kitab kuning setiap malam Jumat
     * @bodyParam date date Tanggal kegiatan. Example: 2024-01-01
     * @bodyParam status string required Status kegiatan (active, inactive). Example: active
     *
     * @response 200 {
     *   "message": "Activity updated successfully",
     *   "status": 200,
     *   "data": {
     *     "id": 1,
     *     "name": "Kajian Kitab Kuning Updated",
     *     "description": "Kajian kitab kuning setiap malam Jumat",
     *     "date": "2024-01-01",
     *     "status": "active",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Activity not found",
     *   "status": 404,
     *   "data": null
     * }
     */
    public function update(Request $request, string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'nullable|date',
                'status' => 'required|in:active,inactive',
            ]);

            $activity->update($request->all());

            return new ActivityResource('Activity updated successfully', $activity, 200);
        } catch (\Exception $e) {
            return new ActivityResource('Error updating activity', null, 500);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 404);
        }
    }

    /**
     * Menghapus kegiatan berdasarkan ID
     *
     * Method ini digunakan untuk menghapus kegiatan berdasarkan ID.
     * Perlu diperhatikan bahwa penghapusan kegiatan harus dilakukan dengan hati-hati.
     *
     * @group Activities
     * @authenticated
     *
     * @urlParam id integer required ID kegiatan yang akan dihapus. Example: 1
     *
     * @response 200 {
     *   "message": "Activity deleted successfully",
     *   "status": 200,
     *   "data": null
     * }
     *
     * @response 404 {
     *   "message": "Activity not found",
     *   "status": 404,
     *   "data": null
     * }
     */
    public function destroy(string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            $activity->delete();
            return new ActivityResource('Activity deleted successfully', null, 200);
        } catch (\Exception $e) {
            return new ActivityResource('Activity not found', null, 404);
        } catch (\Throwable $th) {
            return new ActivityResource($th->getMessage(), null, 500);
        }
    }
}
