<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;

class NewsController extends Controller
{
    /**
     * Menampilkan daftar semua berita dan pengumuman
     *
     * Method ini digunakan untuk mengambil semua data berita dan pengumuman dari database.
     * Berita mencakup informasi penting dan pengumuman pesantren.
     *
     * @group Master Data
     * @authenticated
     *
     * @response 200 {
     *   "message": "Success",
     *   "status": 200,
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Pengumuman Libur Sekolah",
     *       "content": "Sekolah akan libur pada tanggal 25 Desember 2024",
     *       "image": "images/news1.jpg",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ]
     * }
     *
     * @response 500 {
     *   "message": "Error fetching news",
     *   "status": 500,
     *   "data": null
     * }
     */
    public function index()
    {
        try {
            $data = News::all();
            return new NewsResource('Success', $data, 200);
        } catch (\Throwable $th) {
            return new NewsResource('Error fetching news', null, 500);
        } catch (\Exception $e) {
            return new NewsResource('An unexpected error occurred', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|max:2048',
            ]);

            $data = $request->only(['title', 'content']);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                $data['image'] = $imagePath;
            }

            $news = News::create($data);

            return new NewsResource('News created successfully', $news, 201);
        } catch (\Throwable $th) {
            return new NewsResource($th->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $news = News::findOrFail($id);
            return new NewsResource('Success', $news, 200);
        } catch (\Throwable $th) {
            return new NewsResource('News not found', null, 404);
        } catch (\Exception $e) {
            return new NewsResource('An unexpected error occurred', null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|max:2048',
            ]);

            $news = News::findOrFail($id);

            $data = $request->only(['title', 'content']);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                $data['image'] = $imagePath;
            }

            $news->update($data);

            return new NewsResource('News updated successfully', $news, 200);
        } catch (\Throwable $th) {
            return new NewsResource($th->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $news = News::findOrFail($id);
            $news->delete();
            return new NewsResource('News deleted successfully', null, 200);
        } catch (\Throwable $th) {
            return new NewsResource('News not found', null, 404);
        } catch (\Exception $e) {
            return new NewsResource('An unexpected error occurred', null, 500);
        }
    }
}
