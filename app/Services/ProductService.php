<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;

class ProductService
{
    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }

    public function getProducts(int $perPage = 10)
    {
        return Product::query()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getProductById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function updateProduct(int $id, array $data): ?Product
    {
        $product = Product::find($id);
        if ($product) {
            $product->update($data);
        }

        return $product;
    }

    public function deleteProduct(int $id): bool
    {
        $product = Product::find($id);
        if ($product) {
            return $product->delete();
        }

        return false;
    }

    public function vinculateProductToCategory(int $productId, int $categoryId): ?Product
    {
        $product = Product::find($productId);

        if (! $product) {
            return null;
        }

        $category = Category::find($categoryId);

        if (! $category) {
            return null;
        }

        $product->category_id = $category->id;
        $product->save();

        return $product;
    }
}
