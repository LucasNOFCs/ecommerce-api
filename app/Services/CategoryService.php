<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    public function getAllCategories()
    {
        return Category::all();
    }

    public function getCategoryById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function updateCategory(int $id, array $data): ?Category
    {
        $category = Category::find($id);
        if ($category) {
            $category->update($data);
        }

        return $category;
    }

    public function deleteCategory(int $id): bool
    {
        $category = Category::find($id);
        if ($category) {
            return $category->delete();
        }

        return false;
    }
}
