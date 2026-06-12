<?php

namespace App\GraphQL\Mutations;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryMutations
{
    public function create($root, array $args): Category
    {
        $input = $args['input'];

        return Category::create([
            'name'        => $input['name'],
            'slug'        => Str::slug($input['name']),
            'description' => $input['description'] ?? null,
            'is_active'   => true,
        ]);
    }

    public function update($root, array $args): Category
    {
        $category = Category::findOrFail($args['id']);

        $input = $args['input'];
        if (isset($input['name'])) {
            $input['slug'] = Str::slug($input['name']);
        }

        $category->update($input);
        return $category;
    }

    public function delete($root, array $args): Category
    {
        $category = Category::find($args['id']);

        if (!$category) {
            throw new \Exception('Kategori dengan ID ' . $args['id'] . ' tidak ditemukan');
        }

        if ($category->events()->count() > 0) {
            throw new \Exception('Kategori tidak bisa dihapus karena masih memiliki event');
        }

        $category->delete();
        return $category;
    }
}