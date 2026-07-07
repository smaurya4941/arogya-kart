<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function paginateWithStats(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()
            ->with('category')
            ->withCount('batches')
            ->withSum('batches as total_stock', 'quantity');

        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['sku'])) {
            $query->where('sku', 'like', "%{$filters['sku']}%");
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['drug_type'])) {
            $query->where('drug_type', $filters['drug_type']);
        }

        return $query
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($filters);
    }

    public function loadWithBatches(Product $product): Product
    {
        $product->load(['batches' => function ($query) {
            $query->orderBy('expiry_date');
        }]);

        return $product;
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
