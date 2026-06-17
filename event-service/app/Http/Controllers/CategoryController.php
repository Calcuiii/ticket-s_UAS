<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->withCount('events')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $categories
        ]);
    }
    // POST /api/categories  (admin only)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Category created',
            'data'    => $category
        ], 201);
    }
    // GET /api/categories/{id}
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Kategori dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $category
        ]);
    }

    // PUT /api/categories/{id}
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Kategori dengan ID ' . $id . ' tidak ditemukan, tidak bisa melakukan edit'
            ], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil diupdate',
            'data'    => $category
        ]);
    }

    // DELETE /api/categories/{id}
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Kategori dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        }

        // Cek apakah kategori masih punya event
        if ($category->events()->count() > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Kategori tidak bisa dihapus karena masih memiliki event'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
