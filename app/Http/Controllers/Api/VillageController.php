<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravolt\Indonesia\Models\Village;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VillageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $defaultPerPage = 5;

        $perPage = $request->input('per_page', $defaultPerPage);

        $maxPerPage = 100; // Set a maximum limit for per_page
        if ($perPage > $maxPerPage) {
            $perPage = $maxPerPage;
        }

        try {
            $villages = Village::with('district')->paginate($perPage);
            return response()->json($villages, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch villages'], 500);
        } catch (ModelNotFoundException $th) {
            return response()->json(['error' => 'Model not found'], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

    public function villageByNik($nik)
    {
        $distCode = substr($nik, 0, 6); // Ensure the district code is 6 characters long
        try {
            $villages = Village::where('district_code', $distCode)->get();
            return response()->json($villages, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch villages for district'], 500);
        }
    }
}
