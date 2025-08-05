<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar semua menu
     *
     * Method ini digunakan untuk mengambil semua data menu dari database
     * beserta relasi child menu. Menu digunakan untuk mengatur navigasi
     * dan struktur menu aplikasi pesantren.
     *
     * @group Security Management
     * @authenticated
     *
     * @response 200 {
     *   "message": "success",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Dashboard",
     *       "description": "Halaman utama dashboard",
     *       "icon": "fas fa-tachometer-alt",
     *       "route": "/dashboard",
     *       "parent_id": null,
     *       "type": "link",
     *       "position": "side",
     *       "status": "active",
     *       "order": 1,
     *       "child": [
     *         {
     *           "id": 2,
     *           "title": "Sub Menu",
     *           "icon": "fas fa-cog",
     *           "route": "/dashboard/settings"
     *         }
     *       ],
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 500 {
     *   "message": "Data tidak ditemukan",
     *   "error": "Error details"
     * }
     */
    public function index()
    {
        try {
            $data = Menu::with(['child'])->get();

            return new MenuResource('success', $data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'route' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:menus,id',
        ]);

        try {
            $maxOrder = Menu::max('order') ?? 0;
            $menu = Menu::create([
                'title' => $request->title,
                'description' => $request->description,
                'icon' => $request->icon,
                'route' => $request->route,
                'parent_id' => $request->parent_id,
                'type' => $request->type ?? 'link',
                'position' => $request->position ?? 'side',
                'status' => $request->status ?? 'active',
                'order' => $maxOrder + 1,
            ]);

            return new MenuResource('Menu created successfully', $menu, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to create menu',
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
            $menu = Menu::with(['child'])->findOrFail($id);

            return new MenuResource('success', $menu, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Menu not found',
                'error' => $th->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'route' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:menus,id',
        ]);

        try {
            $menu = Menu::findOrFail($id);
            $menu->update($request->all());

            return new MenuResource('Menu updated successfully', $menu, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update menu',
                'error' => $th->getMessage(),
            ], 500);
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
