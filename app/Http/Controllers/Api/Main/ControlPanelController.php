<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Models\ControlPanel;
use Illuminate\Http\Request;

class ControlPanelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $controlPanel = ControlPanel::latest()->first();
        return response()->json($controlPanel);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_version' => 'nullable|string|max:255',
            'app_description' => 'nullable|string',
            'app_logo' => 'nullable|string',
            'app_favicon' => 'nullable|string',
            'app_url' => 'nullable|string',
            'app_email' => 'nullable|email',
            'app_phone' => 'nullable|string',
            'app_address' => 'nullable|string',
            'is_maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string',
            'app_theme' => 'in:light,dark,system',
            'app_language' => 'in:indonesia,english,arabic',
        ]);

        $controlPanel = ControlPanel::create($validatedData);

        return response()->json($controlPanel, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $controlPanel = ControlPanel::findOrFail($id);
        return response()->json($controlPanel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $controlPanel = ControlPanel::findOrFail($id);

        $validatedData = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_version' => 'nullable|string|max:255',
            'app_description' => 'nullable|string',
            'app_logo' => 'nullable|string',
            'app_favicon' => 'nullable|string',
            'app_url' => 'nullable|string',
            'app_email' => 'nullable|email',
            'app_phone' => 'nullable|string',
            'app_address' => 'nullable|string',
            'is_maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string',
            'app_theme' => 'in:light,dark,system',
            'app_language' => 'in:indonesia,english,arabic',
        ]);

        $controlPanel->update($validatedData);

        return response()->json($controlPanel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $controlPanel = ControlPanel::findOrFail($id);
        $controlPanel->delete();

        return response()->json(null, 204);
    }
}
