<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EducationalLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $data = EducationalLevel::all();

        // if ($data->isEmpty()) {
        //     return response()->json([
        //         'message' => 'Data tidak ditemukan',
        //     ], 404);
        // }
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data educational level',
        //     'data' => $data,
        // ]);
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
