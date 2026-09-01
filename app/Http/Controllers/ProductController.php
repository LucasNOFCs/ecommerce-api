<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function index(
        ProductFilterRequest $request,
        ProductService $productService
    ) {
        $filters = $request->validated();

        $perPage = $filters['per_page'] ?? 10;

        $products = $productService->getProducts(
            $filters,
            (int) $perPage
        );

        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        $productService = new ProductService;
        $product = $productService->createProduct($validatedData);

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $productService = new ProductService;
        $product = $productService->getProductById((int) $id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully.',
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $productService = new ProductService;
        $product = $productService->getProductById((int) $id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
            ], 404);
        }

        $validatedData = $request->validated();
        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => $product,
        ]);
    }

    public function destroy(string $id)
    {
        $productService = new ProductService;
        $product = $productService->getProductById((int) $id);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
            ], 404);
        }

        $productService->deleteProduct((int) $id);

        return response()->json([
            'message' => 'Product deleted successfully.',
            'data' => null,
        ]);
    }

    public function vinculateProductToCategory(int $productId, int $categoryId)
    {
        $productService = new ProductService;
        $product = $productService->vinculateProductToCategory((int) $productId, (int) $categoryId);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Product vinculated to category successfully.',
            'data' => $product,
        ]);
    }
}
