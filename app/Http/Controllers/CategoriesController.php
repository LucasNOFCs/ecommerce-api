<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategorieRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $categoryService = new CategoryService;
        $categories = $categoryService->getAllCategories();

        return response()->json([
            'message' => 'Categories retrieved successfully.',
            'data' => $categories,
        ]);
    }

    public function store(StoreCategorieRequest $request)
    {
        $validatedData = $request->validated();

        $categoryService = new CategoryService;
        $category = $categoryService->createCategory($validatedData);

        return response()->json([
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    public function show(string $id)
    {
        $categoryService = new CategoryService;
        $category = $categoryService->getCategoryById((int) $id);

        if (! $category) {
            return response()->json([
                'message' => 'Category not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Category retrieved successfully.',
            'data' => $category,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $categoryService = new CategoryService;
        $category = $categoryService->updateCategory((int) $id, $validatedData);

        if (! $category) {
            return response()->json([
                'message' => 'Category not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Category updated successfully.',
            'data' => $category,
        ]);
    }

    public function destroy(string $id)
    {
        $categoryService = new CategoryService;
        $deleted = $categoryService->deleteCategory((int) $id);

        if (! $deleted) {
            return response()->json([
                'message' => 'Category not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Category deleted successfully.',
            'data' => null,
        ]);
    }
}
