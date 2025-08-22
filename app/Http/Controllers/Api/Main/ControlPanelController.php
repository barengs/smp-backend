<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Models\ControlPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
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
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'app_theme' => 'in:light,dark,system',
            'app_language' => 'in:indonesia,english,arabic',
        ]);

        if ($request->hasFile('app_logo')) {
            $this->uploadImage($request->file('app_logo'), $validatedData, 'app_logo');
        }

        if ($request->hasFile('app_favicon')) {
            $validatedData['app_favicon'] = $this->uploadFavicon($request->file('app_favicon'));
        }

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
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('app_logo')) {
            $this->uploadImage($request->file('app_logo'), $validatedData, 'app_logo');
        }

        if ($request->hasFile('app_favicon')) {
            $validatedData['app_favicon'] = $this->uploadFavicon($request->file('app_favicon'));
        }

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

    /**
     * Update the specified resource in storage by column.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByColumn(Request $request, string $id)
    {
        $controlPanel = ControlPanel::findOrFail($id);

        $column = $request->input('column');
        $value = $request->input('value');

        if (!$column || !$value) {
            return response()->json(['message' => 'Column and value are required.'], 422);
        }

        $fillable = $controlPanel->getFillable();
        if (!in_array($column, $fillable)) {
            return response()->json(['message' => 'Column is not fillable.'], 422);
        }

        $controlPanel->{$column} = $value;
        $controlPanel->save();

        return response()->json($controlPanel);
    }
    private function uploadImage($file, &$validatedData, $columnName)
    {
        $image = new ImageManager(new Driver());
        $timestamp = now()->timestamp;
        $fileName = $timestamp . '_' . $file->getClientOriginalName();

        // Large logo
        $largeImage = $image->read($file->getRealPath());
        $largeImage->cover(512, 512);
        Storage::disk('public')->put('uploads/logos/large/' . $fileName, (string) $largeImage->encode());

        // Small logo
        $smallImage = $image->read($file->getRealPath());
        $smallImage->scaleDown(128, 128);
        Storage::disk('public')->put('uploads/logos/small/' . $fileName, (string) $smallImage->encode());

        $validatedData[$columnName] = $fileName;
    }

    private function uploadFavicon($file)
    {
        $fileName = $file->getClientOriginalName();

        // Store favicon in a separate directory without resizing
        $file->storeAs('public/uploads/favicons', $fileName);

        return $fileName;
    }
}
