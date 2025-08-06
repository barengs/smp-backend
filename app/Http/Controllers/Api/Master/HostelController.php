<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\Hostel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HostelResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

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

    /**
     * Import data asrama dari file Excel/CSV.
     *
     * Method ini digunakan untuk mengimpor data asrama secara massal dari file Excel atau CSV.
     * File harus memiliki kolom: name, parent_id (opsional), description (opsional).
     *
     * @group Master Data
     * @authenticated
     *
     * @bodyParam file file required File Excel/CSV yang berisi data asrama.
     *
     * @response 200 {
     *   "message": "Import successful",
     *   "imported": 10,
     *   "failed": 2,
     *   "errors": [
     *     {"row": 3, "error": "Kolom name wajib diisi"}
     *   ]
     * }
     *
     * @response 422 {
     *   "message": "Validation error",
     *   "errors": {
     *     "file": ["The file field is required."]
     *   }
     * }
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,csv,txt',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $imported = 0;
        $failed = 0;
        $errors = [];

        try {
            $rows = [];
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'csv' || $extension === 'txt') {
                $handle = fopen($file->getRealPath(), 'r');
                $header = fgetcsv($handle);
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = array_combine($header, $data);
                }
                fclose($handle);
            } else {
                // Excel (xlsx) support
                if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                    return response([
                        'message' => 'Excel import requires phpoffice/phpspreadsheet package.',
                        'error' => 'Package not installed.',
                    ], 500);
                }
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $header = [];
                foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                    if ($rowIndex === 1) {
                        $header = $cells;
                    } else {
                        $rows[] = array_combine($header, $cells);
                    }
                }
            }

            DB::beginTransaction();
            foreach ($rows as $i => $row) {
                $rowNumber = $i + 2; // +2 for header and 1-based index
                $rowValidator = Validator::make($row, [
                    'name' => 'required|string|max:255',
                    'parent_id' => 'nullable|integer|exists:hostels,id',
                    'description' => 'nullable|string',
                ]);
                if ($rowValidator->fails()) {
                    $failed++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => $rowValidator->errors()->first(),
                    ];
                    continue;
                }
                Hostel::create([
                    'name' => $row['name'],
                    'parent_id' => $row['parent_id'] ?? null,
                    'description' => $row['description'] ?? null,
                ]);
                $imported++;
            }
            DB::commit();

            return response([
                'message' => 'Import successful',
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors,
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response([
                'message' => 'Error importing data',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
