<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interfaces\BatchRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly BatchRepositoryInterface $batches,
        private readonly InventoryService $inventory,
        private readonly AuditLogService $audit
    ) {}

    public function listProducts(array $filters = [], int $perPage = 15): array
    {
        return [
            'products' => $this->products->paginateWithStats($filters, $perPage),
            'expiringBatches' => $this->batches->getExpiringBatches(),
            'categories' => Category::orderBy('name')->get(),
        ];
    }

    public function createProduct(array $data): Product
    {
        $data = $this->handleImage($data);

        $product = $this->products->create($data);
        $this->audit->log(auth()->user(), 'product_created', $product, [
            'name' => $product->name,
            'sku' => $product->sku,
        ]);

        return $product;
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $data = $this->handleImage($data, $product);

        $product = $this->products->update($product, $data);
        $this->audit->log(auth()->user(), 'product_updated', $product, [
            'name' => $product->name,
            'sku' => $product->sku,
        ]);

        return $product;
    }

    public function deleteProduct(Product $product): void
    {
        $this->products->delete($product);
        $this->audit->log(auth()->user(), 'product_deleted', $product, [
            'name' => $product->name,
            'sku' => $product->sku,
        ]);
    }

    public function getProductDetail(Product $product): array
    {
        return [
            'product' => $this->products->loadWithBatches($product),
            'expiringBatches' => $this->batches->getExpiringBatchesForProduct($product),
        ];
    }

    public function issueStock(Product $product, int $quantity): void
    {
        $this->inventory->issueStock($product, $quantity);
    }

    private function handleImage(array $data, ?Product $product = null): array
    {
        if (empty($data['image'])) {
            return $data;
        }

        $path = $data['image']->store('products', 'public');
        $data['image_path'] = $path;
        unset($data['image']);

        if ($product && $product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        return $data;
    }
}
