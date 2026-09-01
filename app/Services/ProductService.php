<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }

    public function getProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['in_stock'])) {
            if ($filters['in_stock']) {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '=', 0);
            }
        }

        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';

        $query->orderBy($sort, $direction);

        return $query->paginate($perPage);
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
