<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $districts = \Laravolt\Indonesia\Models\District::all();
            return response()->json($districts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch districts'], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
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
}
