<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Services\BatchService;
use App\Http\Requests\Admin\StoreProductBatchRequest;
use App\Http\Requests\Admin\UpdateProductBatchRequest;

class ProductBatchController extends Controller
{
    public function __construct(
        private readonly BatchService $batches
    ) {}

    public function create(Product $product)
    {
        $this->authorize('create', ProductBatch::class);

        return view('admin.batches.create', [
            'product' => $product,
        ]);
    }

    public function store(StoreProductBatchRequest $request, Product $product)
    {
        $this->authorize('create', ProductBatch::class);

        $this->batches->createBatch($product, $request->validated());

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Batch added successfully.');
    }

    public function edit(ProductBatch $batch)
    {
        $this->authorize('update', $batch);

        $batch->load('product');

        return view('admin.batches.edit', [
            'batch' => $batch,
        ]);
    }

    public function update(UpdateProductBatchRequest $request, ProductBatch $batch)
    {
        $this->authorize('update', $batch);

        $this->batches->updateBatch($batch, $request->validated());

        return redirect()
            ->route('admin.products.show', $batch->product)
            ->with('success', 'Batch updated successfully.');
    }

    public function destroy(ProductBatch $batch)
    {
        $this->authorize('delete', $batch);

        $product = $batch->product;
        $this->batches->deleteBatch($batch);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Batch deleted successfully.');
    }
}
